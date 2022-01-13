<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\CustomerBasket;
use App\Models\CustomerBasketAttribute;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\Products;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use App\Models\Coupon;
use App\Models\Cart;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Exists;
use PhpParser\Node\Expr\AssignOp\Concat;

class CartController extends BaseController
{

    protected $apiBaseUrl;

    public function __construct(Cart $cart, Coupon $coupon)
    {
        $this->cart = $cart;
        $this->coupon = $coupon;
        $this->apiBaseUrl = 'http://192.168.22.8/IndusAPI/api';
    }
    /**
     * Add to Cart api
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        # code...
        if (Auth::guard('api')->check()) {
            $user_id = $request->user('api')->id;

            /*   $user = Auth::user(); 
        $user_id =  $user->id; */
            $validator = Validator::make($request->all(), [
                'product_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            } else {

                $productData = DB::table('products')
                    ->join('price_lists', 'products.sku', '=', 'price_lists.item_no')
                    ->join('users', 'price_lists.price_list_no', '=', 'users.price_list_no')
                    ->select('price_lists.list_price AS uprice', 'products.regular_price', 'products.sale_price', 'sku')->where('users.id', $user_id)->where('products.id', $request->product_id)->first();
                /*  $productData = Products::select('regular_price', 'sale_price','sku')->where('id', $request->product_id)->first(); */
                if (!empty($productData)) {
                    $regular =  $productData->regular_price;
                    $sale =  $productData->sale_price;
                    $uprice = $productData->uprice;
                    $sale = $uprice;


                    $basketExist = CustomerBasket::where(['product_id' => $request->product_id, 'user_id' => $user_id])->first();
                    if ($basketExist) {
                        if (!empty($request->product_option)) {
                            foreach ($request->product_option as $data) {
                                $optionCart = CustomerBasketAttribute::where(['product_id' => $request->product_id, 'customer_id' => $user_id, 'variant_id' => $data["variant_id"]])->first();
                                if ($optionCart) {
                                    $optionCart->quantity =  $optionCart->quantity +  $data['quantity'];
                                    $optionCart->save();
                                } else {
                                    $cba = new CustomerBasketAttribute;
                                    $cba->customers_basket_id = $basketExist->id;
                                    $cba->customer_id = $user_id;
                                    $cba->product_id = $request->product_id;
                                    $cba->variant_id = $data['variant_id'];
                                    $cba->quantity = $data['quantity'];
                                    $cba->save();
                                }
                                $basketExist->quantity = $basketExist->quantity +  $data['quantity'];
                            }
                            $basketExist->sub_total = $basketExist->quantity * $basketExist->price;
                            $cartDetails = $basketExist->save();
                            return $this->sendResponse($cartDetails, 'Product  has been added to your cart');
                        }
                    } else {
                        $CustomerBasket = new CustomerBasket;
                        $CustomerBasket->product_id = $request->product_id;
                        /*   $CustomerBasket->quantity = $request->quantity; */
                        $CustomerBasket->user_id = $user_id;
                        /*  if (!empty($sale != 0)) {
                    $CustomerBasket->price = $sale;
                } else {
                    $CustomerBasket->price = $regular;
                } */
                        $CustomerBasket->price = $sale;
                        $cartDetails = $CustomerBasket->save();
                        $CustomerBasketID = $CustomerBasket->id;
                        $totalQty = 0;
                        if (!empty($request->product_option)) {
                            foreach ($request->product_option as $data) {
                                $cba = new CustomerBasketAttribute;
                                $cba->customers_basket_id = $CustomerBasketID;
                                $cba->customer_id = $user_id;
                                $cba->product_id = $request->product_id;
                                $cba->variant_id = $data['variant_id'];
                                $cba->quantity = $data['quantity'];
                                $cba->save();
                                $totalQty = $data['quantity'] + $totalQty;
                            }
                            $CustomerBasket->quantity = $totalQty;
                            $CustomerBasket->sub_total = $totalQty * $CustomerBasket->price;
                            $CustomerBasket->save();
                        }
                        return $this->sendResponse($cartDetails, 'Product  has been added to your cart');
                    }
                } else {
                    return $this->sendError(false, 'Product data not found');
                }
            }
        } else {
            return $this->sendError(false, "Unauthorised user.");
        }
    }

    /**
     * View Cart API
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $cartItems = $this->cart->myCart($user_id, $request);

        if ($cartItems['grandTotal'] > 0) {
            return $this->sendResponse($cartItems, 'Cart Product List');
        } else {
            return $this->sendResponse([], 'You have no items in your shopping cart.');
            // return $this->sendError('You have no items in your shopping cart.');
        }

        /* 

        $totalPrice = 0;
        $user_id = $request->user()->id;
        
        $orderby = (isset($request->orderby)) ?   $request->orderby : "id";
        $order =  (isset($request->order)) ?   $request->order : "ASC";
        
        $result = Products::join('customers_basket', 'products.id', '=', 'customers_basket.product_id')
            ->where('user_id', $user_id)
            ->select([
                'customers_basket.id', 'customers_basket.quantity', 'customers_basket.price', 'products.id', 'customers_basket.user_id', 'products.product_name', 'products.regular_price', 'products.sale_price',
                'products.main_image', 'products.sku', 'products.slug', 'products.short_description', 'products.specification'
            ])->orderby('products.'.$orderby, $order)->get();
                

        if (!$result->isEmpty()) {
            # SubTotal  
            foreach ($result as $data) {
                $data->subtotal = $data->price * $data->quantity;
                if (!empty($data->main_image) && file_exists($data->main_image)) {
                    $data->main_image = asset($data->main_image);
                } else {
                    $data->main_image = asset('uploads/product-placeholder.png');
                }
                if (!empty($data->tech_documents)) {
                    $data->tech_documents = asset($data->tech_documents);
                }
                if (!empty($data->video) && file_exists($data->video)) {
                    $data->video = asset($data->video);;
                }

                $data->products_attributes =  DB::table('customers_basket_attributes')->join('attributes', 'customers_basket_attributes.attribute_id', '=', 'attributes.id')
                ->join('attributes_variations', 'customers_basket_attributes.attribute_variation_id', '=', 'attributes_variations.id')
                ->select('customers_basket_attributes.attribute_id', 'customers_basket_attributes.attribute_variation_id','customers_basket_attributes.quantity', 'attributes.attribute_name', 'attributes_variations.variation_name') 
               ->where([['product_id', '=', $data->id],['customer_id', '=', $user_id]])
               ->get(); 
            }
        }
        # Additional total
        foreach ($result as $data) {
            $totalPrice = $totalPrice + ($data->price * $data->quantity);
            $totalPrice  = number_format((float)$totalPrice, 2, '.', '');
        }
       */
    }

    /**
     * Update Cart API
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity' => 'required|numeric|gt:0',
            'variant_id' => 'required|integer'

        ]);
        $user_id = $request->user()->id;

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {

            $product_variation =   CustomerBasketAttribute::where('product_id', $request->product_id)
                ->where('customer_id', $user_id)
                ->where('variant_id', $request->variant_id)
                ->first();
            $product_attribute = CustomerBasket::query()
                ->with('product', 'user')
                ->where('product_id', $request->product_id)
                ->where('user_id', $user_id)
                ->first();

            $email = Helper::adminEmail();
            if ($request->sendMail) {
                Mail::send('API.email.cart_product_update', [
                    'cart' => $product_attribute,
                    'quantity' => $request->quantity,
                    'fromQuantity' => $request->fromQuantity,
                ], function ($message) use ($email) {
                    $message->subject('Cart product updated beacuse insufficient quantity!');
                    $message->to($email);
                });
            }

            if (!empty($product_attribute) && !empty($product_variation)) {

                $total_qty = $product_attribute->quantity;
                $total_qty =  ($total_qty - $product_variation->quantity) + $request->quantity;

                $product_variation->quantity  = $request->quantity;
                $product_attribute->quantity  = $total_qty;
                $product_attribute->sub_total  = number_format((float)$total_qty * $product_attribute->price, 2, '.', '');

                $product_attribute->save();
                $product_variation->save();
                $data['sub_total']  =  $product_attribute->sub_total;

                return $this->sendResponse($data, "Cart quantity updated successfully");
            } else {
                return $this->sendError('prodcut not found.', $product_variation);
            }
        }
    }


    /**
     * Update Cart API
     *
     * @return \Illuminate\Http\Response
     */
    public function multiupdate(Request $request)
    {

        $requestData = $request->all();
        return $requestData;
        foreach ($requestData as $key => $cartItem) {
            $user_id = $request->user()->id;
            $product_variation =   CustomerBasketAttribute::where('product_id', $cartItem['product_id'])
                ->where('customer_id', $user_id)
                ->where('variant_id', $cartItem['variant_id'])
                ->first();

            $product_attribute =   CustomerBasket::where('product_id', $cartItem['product_id'])
                ->where('user_id', $user_id)
                ->first();
            if (!empty($product_attribute) && !empty($product_variation)) {
                $total_qty = $product_attribute->quantity;
                $total_qty =  ($total_qty - $product_variation->quantity) + $cartItem['quantity'];
                if ($total_qty <= 0) {
                    $product_variation->quantity  = $cartItem['quantity'];
                    $product_attribute->quantity  = $total_qty;
                    $product_attribute->sub_total  = number_format((float)$total_qty * $product_attribute->price, 2, '.', '');
                    $product_attribute->save();
                    $product_variation->save();
                    $data['sub_total']  =  $product_attribute->sub_total;
                } else {
                    $CustomerBasket =  CustomerBasket::where('product_id', $cartItem['product_id'])->where('user_id', $user_id)->first();
                    $data = CustomerBasketAttribute::where('customers_basket_id', $CustomerBasket->id)->delete();
                    if (($key + 1) == count($requestData)) {
                        $data =  CustomerBasket::where('product_id',  $cartItem['product_id'])->where('user_id', $user_id)->delete();
                    }
                }
            }
        }
        return $this->sendResponse([], "Cart quantity updated successfully");

        // $validator = Validator::make($request->all(), [
        //     'product_id' => 'required|integer',
        //     'quantity' => 'required|numeric|gt:0',
        //     'variant_id' => 'required|integer'

        // ]);
        // $user_id = $request->user()->id;

        // if ($validator->fails()) {
        //     return $this->sendError('Validation Error.', $validator->errors());
        // } else {

        //     $product_variation =   CustomerBasketAttribute::where('product_id', $request->product_id)
        //         ->where('customer_id', $user_id)
        //         ->where('variant_id', $request->variant_id)
        //         ->first();
        //     $product_attribute =   CustomerBasket::where('product_id', $request->product_id)
        //         ->where('user_id', $user_id)
        //         ->first();

        //     if (!empty($product_attribute) && !empty($product_variation)) {

        //         $total_qty = $product_attribute->quantity;
        //         $total_qty =  ($total_qty - $product_variation->quantity) + $request->quantity;

        //         $product_variation->quantity  = $request->quantity;
        //         $product_attribute->quantity  = $total_qty;
        //         $product_attribute->sub_total  = number_format((float)$total_qty * $product_attribute->price, 2, '.', '');

        //         $product_attribute->save();
        //         $product_variation->save();
        //         $data['sub_total']  =  $product_attribute->sub_total;

        //         return $this->sendResponse($data, "Cart quantity updated successfully");
        //     } else {
        //         return $this->sendError('prodcut not found.', $product_variation);
        //     }
        // }
    }


    /**
     * Delete Cart API
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);
        $product_id =  $request->product_id;
        $user_id = $request->user()->id;

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {
            $CustomerBasket =  CustomerBasket::query()
                ->with('product', 'user')
                ->where('product_id', $product_id)
                ->where('user_id', $user_id)
                ->first();

            $email = Helper::adminEmail();
            if ($request->sendMail) {
                Mail::send('API.email.cart_product_destroy', [
                    'cart' => $CustomerBasket,
                ], function ($message) use ($email) {
                    $message->subject('Cart product deleted beacuse insufficient quantity!');
                    $message->to($email);
                });
            }

            $data = CustomerBasketAttribute::where('customers_basket_id', $CustomerBasket->id)->delete();
            $data =  CustomerBasket::where('product_id', $product_id)->where('user_id', $user_id)->delete();

            if ($data) {
                return $this->sendResponse($data, 'Product has been successfully removed form Cart.');
            } else {
                return $this->sendError('Product Not Found in cart.', $data);
            }
        }
    }



    /**
     *  Apply counpon code
     *
     * @return \Illuminate\Http\Response
     */
    public function applyCoupon(Request $request)
    {
        # code...
        $data =   $this->coupon->applyCoupon($request);
        return $data;
    }


    // public function applyCoupon(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'coupon_code' => 'required'
    //     ]);
    //     $user_id = $request->user()->id;
    //     $current_date = date('Y-m-d H:i:s');
    //     // return $current_date;
    //     $coupon_data = Coupon::where('code', $request->coupon_code)
    //                 ->where('expiry_date', '>=', $current_date)
    //                 ->where('start_date', '<=', $current_date)
    //                 ->where('status', 1)
    //                 ->first();

    //     if ($coupon_data->usage_limit > 0 &&  $coupon_data->usage_limit == $coupon_data->usage_count) {
    //         return $this->sendError('This coupon has been reached to its maximum usage limit',false);
    //     }else{
    //         $coupon_data->usage_count += 1;
    //         $data['coupon_code'] = $coupon_data->code;
    //         $data['description'] = $coupon_data->description;
    //         $data['amount'] = $coupon_data->amount;
    //         return $this->sendResponse($data, 'Applied Promo Code');
    //     }

    // }


    /**
     *  
     *
     * @return \Illuminate\Http\Response
     */
    public function getCoupons(Request $request)
    {
    }


    /**
     *  Check cart items available in stock
     *
     * @return \Illuminate\Http\Response
     */
    public function checkItemInStock(Request $request)
    {

        try {
            $requestData = $request->all();
            $outOfStockProductResult = [];
            $user_id = $request->user()->id;

            foreach ($requestData['items'] as $itemKey => $itemValue) {
                $sku = ($itemValue['sku']) ? $itemValue['sku'] : '';
                $xml_data = $this->getXMLData($this->apiBaseUrl . '/ItemMaster/GetItemStock?ItemGrp=' . $sku);
                $xml = simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
                $apiStockArr = json_decode($xml);

                $stockResponse = [];
                // foreach ($itemValue['products_attributes'] as $key => $productAttr) {
                // $stockResponse[] = SELF::getOutOfStockProduct($productAttr, $apiStockArr, $itemValue['id']);
                $stockResponse[] = SELF::getOutOfStockProduct($itemValue['products_attributes'], $apiStockArr, $itemValue['id'], $user_id);
                // }

                // $stockResponse = SELF::getOutOfStockProduct($itemValue, $apiStockArr);
                if (count($stockResponse) > 0) {
                    $stockResponse = array_filter($stockResponse);
                    $outOfStockProductResult[] = $stockResponse;
                }
            }


            $outOfStockProduct = [];
            $outOfStockProduct = $outOfStockProductResult;

            // ********************************
            // return $outOfStockProduct;

            $outOfStockData = [];
            if (count($outOfStockProduct) > 0) {
                $outOfStockData = $outOfStockProduct;
                return $this->sendResponse($outOfStockData, 'Out of stock data.');
            }
            return $this->sendResponse($outOfStockData, 'All products available in stock.');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    public function getOutOfStockProduct($productAttr, $apiStockArr, $productId, $user_id)
    {

        $result = [];
        foreach ($productAttr as $key => $productAttrValue) {
            $found_key = false;
            if ($apiStockArr) {
                $found_key = array_search($productAttrValue['item_code'], array_column($apiStockArr, 'ItemCode'));
            }
            if ($found_key === false) {
                // No attribute available then delete Product Cart Attribute                    
                $dataArr = [
                    // 'product_name' => $product['product_name'],
                    'product_id' => $productId,
                    'item_code' => $productAttrValue['item_code'],
                    'quantity' => 0,
                    'not_in_stock_quantity' => $productAttrValue['quantity'],
                    'variant_id' => $productAttrValue['variant_id'],
                    'user_id' => $user_id,
                ];
                $result[] = $dataArr;

                // Delete Product Attribute
                $deleted = SELF::deleteCartProductAttribute($dataArr);
            } else {
                $apiItem = $apiStockArr[$found_key];
                if ($apiItem->ItemCode == $productAttrValue['item_code']) {
                    $inStockProducts = ($apiItem->AvailableStock - $apiItem->Committed);
                    if ((int)$productAttrValue['quantity'] > (int)$inStockProducts) {
                        $totalAvailableQty = $inStockProducts;
                        $totalNotAvailableQty = $productAttrValue['quantity'] - $inStockProducts;
                        $dataArr = [
                            'product_id' => $productId,
                            'item_code' => $productAttrValue['item_code'],
                            'quantity' => $totalAvailableQty,
                            'not_in_stock_quantity' => $totalNotAvailableQty,
                            'variant_id' => $productAttrValue['variant_id'],
                            'user_id' => $user_id
                        ];
                        $result[] = $dataArr;

                        // check product attribute in stock then update in cart otherwise remove in cart
                        if ($totalAvailableQty <= 0) {
                            // Delete Product Attribute
                            $deleted = SELF::deleteCartProductAttribute($dataArr);
                        } else {
                            // Update Product Attribute Quantity
                            $updated = SELF::updateCartProductAttribute($dataArr);
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function deleteCartProductAttribute($cartItem)
    {

        $customerBasket =  CustomerBasket::where('product_id', $cartItem['product_id'])->where('user_id', $cartItem['user_id'])->first();
        if ($customerBasket) {
            $deleteProductAttribute = CustomerBasketAttribute::where('customers_basket_id', $customerBasket->id)->delete();
            if ($deleteProductAttribute) {
                $attrCount = CustomerBasketAttribute::where('customers_basket_id', $customerBasket->id)->count();
                if ($attrCount == 0) {
                    $deleteProduct =  CustomerBasket::where('product_id',  $cartItem['product_id'])->where('user_id', $cartItem['user_id'])->delete();
                }
            }
        }
        return true;
    }

    public function updateCartProductAttribute($cartItem)
    {

        $product_variation =   CustomerBasketAttribute::where('product_id', $cartItem['product_id'])
            ->where('customer_id', $cartItem['user_id'])
            ->where('variant_id', $cartItem['variant_id'])
            ->first();

        $product_attribute =   CustomerBasket::where('product_id', $cartItem['product_id'])
            ->where('user_id', $cartItem['user_id'])
            ->first();

        if (!empty($product_attribute) && !empty($product_variation)) {
            $total_qty = $product_attribute->quantity;
            $total_qty =  ($total_qty - $product_variation->quantity) + $cartItem['quantity'];
            if ($total_qty <= 0) {
                $product_variation->quantity  = $cartItem['quantity'];
                $product_attribute->quantity  = $total_qty;
                $product_attribute->sub_total  = number_format((float)$total_qty * $product_attribute->price, 2, '.', '');
                $product_attribute->save();
                $product_variation->save();
                $data['sub_total']  =  $product_attribute->sub_total;
            }
        }
        return true;
    }



    // /**
    //  *  Check cart items available in stock
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function checkItemInStock(Request $request){        
    //     try{
    //         $requestData = $request->all();
    //         $outOfStockProductResult = [];            
    //         foreach ($requestData['items'] as $itemKey => $itemValue) {
    //             $sku = ($itemValue['sku']) ? $itemValue['sku']: '';
    //             $xml_data = $this->getXMLData($this->apiBaseUrl.'/ItemMaster/GetItemStock?ItemGrp='.$sku);            
    //             $xml = simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
    //             $apiStockArr = json_decode($xml);                        
    //             $stockResponse = SELF::getOutOfStockProduct($itemValue, $apiStockArr);
    //             if($stockResponse){
    //                 $outOfStockProductResult[] = $stockResponse;
    //             }
    //         }
    //         $outOfStockProduct = [];
    //         $outOfStockProduct = $outOfStockProductResult;
    //         $outOfStockData = [];
    //         if(count($outOfStockProduct) > 0){
    //             $outOfStockData = $outOfStockProduct;
    //             return $this->sendResponse($outOfStockData, 'Unfortunately we have just been informed that the below cart item\'s are discontinued and no longer available. Please update your quantities.');
    //         }
    //         return $this->sendResponse($outOfStockData, 'All products available in stock.');
    //     } catch(Exception $exception) {
    //         return $this->sendError($exception->getMessage());
    //     }
    // }



    // public function getOutOfStockProduct($product, $apiStockArr){

    //     foreach ($product['products_attributes'] as $key => $productAttr) {

    //         // $productAttr = $product['products_attributes'][0];  

    //         $found_key = false;
    //         if($apiStockArr){
    //             $found_key = array_search($productAttr['item_code'], array_column($apiStockArr, 'ItemCode'));
    //         }
    //         if($found_key===false){
    //             $result = [
    //                 'totalAvailableStock' => 0,
    //                 'item_code' => $productAttr['item_code'],
    //                 'product_name' => $product['product_name'],
    //             ];
    //         } else {
    //             $apiItem = $apiStockArr[$found_key];
    //             $result = null;
    //             if($apiItem->ItemCode==$productAttr['item_code']){
    //                 $inStockProducts = ($apiItem->AvailableStock - $apiItem->Committed);            
    //                 if( $productAttr['quantity'] > $inStockProducts ) {
    //                     $result = [
    //                         'totalAvailableStock' => $inStockProducts,
    //                         'item_code' => $productAttr['item_code'],
    //                         'product_name' => $product['product_name']
    //                     ];
    //                 } 
    //             } 
    //         }
    //         return $result;
    //     }
    // }    

    public  function getXMLData($url)
    {
        $curl = curl_init();
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "Accept: application/xml",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }
}
