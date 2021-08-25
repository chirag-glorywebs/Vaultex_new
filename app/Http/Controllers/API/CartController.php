<?php

namespace App\Http\Controllers\API;

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
use Illuminate\Validation\Rules\Exists;
use PhpParser\Node\Expr\AssignOp\Concat;

class CartController extends BaseController
{

    public function __construct(Cart $cart,Coupon $coupon)
    {
        $this->cart = $cart;
        $this->coupon = $coupon;
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
                    ->select('price_lists.list_price AS uprice', 'products.regular_price', 'products.sale_price','sku')->where('users.id', $user_id)->where('products.id', $request->product_id)->first();
         /*  $productData = Products::select('regular_price', 'sale_price','sku')->where('id', $request->product_id)->first(); */
            if(!empty($productData)){
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
            }else{
                return $this->sendError(false, 'Product data not found');
            }
        }
        }else{
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
        $cartItems = $this->cart->myCart($user_id,$request);
         
        if ($cartItems['grandTotal'] > 0) {
            return $this->sendResponse($cartItems, 'Cart Product List');
        } else {
            return $this->sendError('You have no items in your shopping cart.');
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
            $product_attribute =   CustomerBasket::where('product_id', $request->product_id)
                ->where('user_id', $user_id)
                ->first();

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
            $CustomerBasket =  CustomerBasket::where('product_id', $product_id)->where('user_id', $user_id)->first();
             
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
}
