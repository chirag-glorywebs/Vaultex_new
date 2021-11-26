<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Products;
use App\Models\ProductAttribute;
use App\Models\OrderProductAttribute;
use App\Models\CustomerBasketAttribute;
use App\Models\Order_products;
use App\Mail\PlaceOrderMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Mail\AdminPlaceOrderMail;
use App\Models\Settings;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\API\BaseController;
use App\Models\CustomerBasket;

class OrderController extends BaseController
{
    public function __construct(
        Cart $cart,
        Order $order
    ) {
        $this->cart = $cart;
        $this->order = $order;
    }


    /* Return readble string */
    public function readableStringModify($str)
    {
        return ucwords(str_replace("_", " ", $str));
    }

    /**
     * Get all products
     *
     * @return \Illuminate\Http\Response
     */
    public function getInfo(Request $request)
    {
        $user_id = $request->user()->id;
        $user_data = User::select('vendor_credit_limit','address_id')->where('user_role', 3)->where('status', 1)
            ->where('id', $user_id)
            ->first();

        $response = array();
        $paymentsMenthods  = paymentsMenthods();
        $icount = 0;
        foreach ($paymentsMenthods as $data) {
            $paymentsMenthods[$icount]['payment_method'] =  $this->readableStringModify($data['payment_method']);
            $icount++;
        }
        // $response['shipping_cost'] = 100.00;
        $response['vat'] = 5;
        $response['payment_methods'] = $paymentsMenthods;
        if (!empty($user_data)) {
            $response['vendor_credit_limit'] = $user_data['vendor_credit_limit'];
            $response['address_id'] = $user_data['address_id'];
        }
        return $this->sendResponse($response, 'Check out page detials');
    }
    /**
     * Get all products
     *
     * @return \Illuminate\Http\Response
     */

    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|integer',
            'payment_method' => 'required|integer',
            'items' => 'required|array|min:1'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {
            $user_id = $request->user()->id;
            $payment_status = $this->order->place_order($request);
            //  return response()->json($payment_status);
            if ($payment_status) {
                $placeorder_data = CustomerBasket::where('user_id', $user_id)->first();
                if (!empty($placeorder_data->is_order) == 1) {
                    DB::table('customers_basket_attributes')->where('customer_id', $user_id)
                        ->delete();
                    CustomerBasket::where('user_id', $user_id)->delete();
                }
                
                $cart_data = DB::table('orders')
                ->join('order_products','orders.id','=','order_products.order_id')
                ->join('products','order_products.product_id','=','products.id')
                ->join('price_lists', 'products.sku', '=', 'price_lists.item_no')  
                ->select('orders.date_purchased','products.sale_price','products.regular_price',
                'order_products.sub_total','order_products.product_id','order_products.product_name',
                'order_products.product_quantity','order_products.product_price','products.main_image',
                DB::raw('COALESCE(price_lists.list_price, products.regular_price) as uprice'),
                )
                ->where('orders.id',$payment_status['order_id'])
                ->where('orders.userid',$user_id)
                ->get();
                foreach($cart_data as $mainimg){
                    if (!empty($mainimg->main_image) && file_exists($mainimg->main_image)) {
                        $mainimg->main_image = asset($mainimg->main_image);
                    } else {
                        $mainimg->main_image = asset('uploads/product-placeholder.jpg');
                    }
                }
            
                    $order = DB::table('orders')
                    ->select('id','date_purchased','coupon_amount','shipping_cost','total_tax','order_price')
                    ->where('orders.id',$payment_status['order_id'])->first();

                    $estdate = date('Y-m-d', strtotime(' +6 day'));
                    $order->estimate_delivery_date  = $estdate;
                    
                $currancy = Settings::select('value')
                ->where('id',17)
                 ->first();

            $user_info = User::where('id',$user_id)->get();
            $email_send  =User::where('id',$user_id)->select('email')->first();
            Mail::to($email_send)->send(new PlaceOrderMail($user_info,$cart_data,$order,$currancy));
            $adminEmail = Settings::select('value')->where('id',25)->get();
                 $place_order =  $adminEmail[0]['value'];
                 $admin = explode(',',$place_order);

        
        
            Mail::to($admin)->send(new AdminPlaceOrderMail($user_info,$cart_data,$order,$currancy));
                
        //Mail::to($email_send)->send(new PlaceOrderMail($placeorder_data));
                return $this->sendResponse($payment_status, 'Order Success');
            } else {
                return $this->sendError('Your cart is empty.', $payment_status);
            }
        }
        /*   if($payment_status == 'success'){
            return $this->sendResponse($request, 'Order Success');
        }else{
            return $this->sendError('oops something went wrong', $payment_status);
        } */
    }


    public function orderList(Request $request)
    {
        # code...
        $user_id = $request->user()->id;
        // return $user_id;

        $data =  DB::table('orders')->join('order_statuses', 'order_statuses.id', '=', 'orders.orders_status')
            /* ->join('order_products', 'order_products.order_id', '=', 'orders.id') */
            ->where('orders.userid', $user_id)
            ->select(
                'orders.id',
                'orders.payment_method',
                'order_statuses.status',
                'orders.date_purchased',
                'orders.order_price',
                'orders.coupon_amount AS total_coupon_discount',
            )->orderBy('orders.id', "DESC")
            ->get();
        // return $data;
       
        foreach ($data as $items) {
        if ($items->status != 'Completed') {
                $track_order = DB::table('manage_order_status')
                    ->join('order_statuses', 'order_statuses.id', '=', 'manage_order_status.order_status_id')
                    ->where('manage_order_status.orderid', $items->id)
                    ->whereBetween('manage_order_status.order_status_id', [1, 5])
                    ->select(
                        'manage_order_status.orderid',
                        'manage_order_status.order_status_id',
                        'manage_order_status.order_status_date',
                        'order_statuses.status'
                    )
                    ->get();
                $items->track_order = $track_order;
            } else {

                $track_order = DB::table('manage_order_status')
                    ->join('order_statuses', 'order_statuses.id', '=', 'manage_order_status.order_status_id')
                    ->where('manage_order_status.orderid', $items->id)
                    ->whereNotIn('manage_order_status.order_status_id', [5])
                    ->select(
                        'manage_order_status.orderid',
                        'manage_order_status.order_status_id',
                        'manage_order_status.order_status_date',
                        'order_statuses.status'
                    )
                    ->get();
                $items->track_order = $track_order;
            }
            $items->payment_method = 'Cash On Delivery';
            
        }
         # Additional total
        $grandTotal =  Order::where('userid', $user_id)->sum('order_price');
        return $this->sendResponse(['orders' => $data, 'grandTotal' => $grandTotal], 'order list');
    }


    public function orderItemList(Request $request)
    {
        # code...
        $totalPrice = 0;
        $user_id = $request->user()->id;
        $orderby = (isset($request->orderby)) ?   $request->orderby : "id";
        $order =  (isset($request->order)) ?   $request->order : "ASC";
    
            $data = DB::table('orders')
            ->join('order_products','orders.id','=', 'order_products.order_id') 
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('price_lists', 'products.sku', '=', 'price_lists.item_no')  
           /*  ->join('users', 'price_lists.price_list_no', '=', 'users.price_list_no')   */
            ->join('users', function($join){
                $join->on('price_lists.price_list_no', '=', 'users.price_list_no')
                     ->on('orders.userid', '=', 'users.id');
            })  
            ->leftJoin('liked_products', function($join){
                $join->on('products.id', '=', 'liked_products.liked_products_id')
                     ->on('users.id', '=', 'liked_products.liked_customers_id');
            }) 
            ->select(
                'products.id',
                'products.product_name',
                'products.regular_price',
                'products.sale_price',
                // 'products.category_id',
                'products.main_image',
                'products.medium_image',
                'products.thumbnail_image',
                'products.sku',
                'products.slug',
                'products.short_description',
                'products.specification',
                'products.trending_product',
                'products.best_selling',
                'order_products.product_price AS order_price',
                'order_products.sub_total',
                'liked_products.id as wishlist',
                
                  DB::raw('COALESCE(price_lists.list_price, products.regular_price)as price'),  
                  DB::raw('COALESCE(price_lists.list_price, products.regular_price) as uprice'),
                   DB::raw('COALESCE(CAST(((products.regular_price - price_lists.list_price) * 100 / products.regular_price) as decimal(5,2)),0) discount')    
              )
            ->where('products.status', 1)
            ->where('orders.userid', $user_id)
            ->orderby($orderby, $order)
            ->get();
            
        foreach ($data as $mainimg) {

            
            if(!empty($mainimg->wishlist) && ($mainimg->wishlist) != NULL){
                $mainimg->wishlist = TRUE;
            }

            $estdate = date('Y-m-d H:i:s', strtotime(' +2 day'));
            $mainimg->estimate_delivery_date  = $estdate;
            if (!empty($mainimg->main_image) && file_exists($mainimg->main_image)) {
                $mainimg->main_image = asset($mainimg->main_image);
            } else {
                $mainimg->main_image = asset('uploads/placeholder-medium.jpg');
            } 
            if (!empty($mainimg->medium_image) && file_exists($mainimg->medium_image)) {
                $mainimg->medium_image = asset($mainimg->medium_image);
            } else {
                $mainimg->medium_image = asset('uploads/placeholder-medium.jpg');
            } 
            if (!empty($mainimg->thumbnail_image) && file_exists($mainimg->thumbnail_image)) {
                $mainimg->thumbnail_image = asset($mainimg->thumbnail_image);
            } else {
                $mainimg->thumbnail_image = asset('uploads/placeholder-medium.jpg');
            } 
        }
        return $this->sendResponse($data, 'Order items list');
    }

     public function viewOrder($id, Request $request)
    {
        $user_id  = $request->user()->id;
        $data =   Order::join('order_statuses', 'order_statuses.id', '=', 'orders.orders_status')
            ->where('orders.id', $id)->where('userid', $user_id)->select([
                'orders.id', 'orders.userid', 'orders.payment_method', 'orders.addressid',
                'orders.last_modified', 'orders.date_purchased', 'orders.order_price',
                'orders.shipping_cost', 'order_statuses.id AS status_id ', 'order_statuses.status', 'orders.order_information',
                'orders.coupon_code', 'orders.coupon_amount', 'orders.total_tax',
                'orders.ordered_source'
            ])->first();
        $totalPrice = 0;
        $products = Order_products::join('products', 'order_products.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->select(
                'products.id',
                'products.product_name',
                'products.regular_price',
                'products.sale_price',
                // 'products.category_id',                
                'products.main_image',
                'products.medium_image',
                'products.thumbnail_image',
                'products.sku',
                'products.slug',
                'products.short_description',
                'products.specification',
                'order_products.product_quantity AS quantity',
                'order_products.sub_total AS subtotal',
                'order_products.final_price AS price',
            )
            ->where('products.status', 1)
            ->where('orders.id', $id)
            ->where('orders.userid', $user_id)            
            ->get();

        if ($data->status != 'Completed') {
            $track_order = DB::table('manage_order_status')
                ->join('order_statuses', 'order_statuses.id', '=', 'manage_order_status.order_status_id')
                ->where('manage_order_status.orderid', $data->id)
                ->whereBetween('manage_order_status.order_status_id', [1, 5])
                ->select(
                    'manage_order_status.orderid',
                    'manage_order_status.order_status_id',
                    'manage_order_status.order_status_date',
                    'order_statuses.status'
                )
                ->get();
            $data->track_order = $track_order;
        } else {

            $track_order = DB::table('manage_order_status')
                ->join('order_statuses', 'order_statuses.id', '=', 'manage_order_status.order_status_id')
                ->where('manage_order_status.orderid', $data->id)
                ->whereNotIn('manage_order_status.order_status_id', [5])
                ->select(
                    'manage_order_status.orderid',
                    'manage_order_status.order_status_id',
                    'manage_order_status.order_status_date',
                    'order_statuses.status'
                )
                ->get();
            $data->track_order = $track_order;
        }

        foreach ($products as $product) {

            if (!empty($product->main_image) && file_exists($product->main_image)) {
                $product->main_image = asset($product->main_image);
            } else {
                $product->main_image = asset('uploads/placeholder-medium.jpg');
            }

            if (!empty($product->medium_image) && file_exists($product->medium_image)) {
                $product->medium_image = asset($product->medium_image);
            } else {
                $product->medium_image = asset('uploads/placeholder-medium.jpg');
            } 

            if (!empty($product->thumbnail_image) && file_exists($product->thumbnail_image)) {
                $product->thumbnail_image = asset($product->thumbnail_image);
            } else {
                $product->thumbnail_image = asset('uploads/placeholder-medium.jpg');
            } 
           
            $attributes = DB::table('order_product_attributes')->join('order_products', 'order_product_attributes.order_product_id', '=', 'order_products.id')
                ->where('order_product_attributes.order_id', $id)
                ->where('order_product_attributes.product_id', $product->id)
                ->select('order_product_attributes.variant_id', 
                'order_product_attributes.item_code',
                'order_product_attributes.item_name',
                'order_product_attributes.variant_data',
                'order_product_attributes.quantity',
                'order_product_attributes.product_price')
                ->get();  
            $product->products_attributes = $attributes;

            $productPriceWithCategories = Products::getProductPrice($user_id, $product->id);            
            $product->uprice = $productPriceWithCategories->uprice;
            $product->productCategories = $productPriceWithCategories->productCategories;
        }
        $data->products   = $products;

        $data->payment_method = 'Cash On Delivery';
        $data->coupon_code =  json_decode($data->coupon_code);

        if (isset($data->coupon_code)) {
            $data->coupon_code = array([
                'id' => $data->coupon_code->id, 'code' => $data->coupon_code->code,
                'amount' => $data->coupon_code->amount, 'description' => $data->coupon_code->description
            ]);
        }
        # Additional total
        foreach ($products as $items) {
            $final_price = $items->price;
            //    return $items;
            if (!empty($items->price)) {
                $final_price = $items->price;
                // return $items->quantity;
                $totalPrice = $totalPrice + ($final_price * $items->quantity);
                $totalPrice  = number_format((float)$totalPrice, 2, '.', '');
            }
        }
        // return $totalPrice;
        $data->grandTotal = $totalPrice;
        return $this->sendResponse($data, 'View order Details.');
    }
}
