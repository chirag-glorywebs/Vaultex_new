<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cart;
use Illuminate\Support\Facades\DB; 
use App\Models\order_statuses;
use Illuminate\Support\Str;


class Order extends Model
{
    use HasFactory;

    //place_order
    public function place_order($request)
    {
        $cart = new Cart();  
        $result = array();
        $user_id = $request->user()->id; 
        $cart_data = $cart->myCart($user_id,$request);  
    
    // return $cart_data;
        if (count($cart_data['items']) ==  0) {
            return count($cart_data['items']);
        }
        
      if (count($cart_data['items']) > 0) {
        /* foreach ($cart_data['items'] as $products) {
            $req = array();
            $attr = array();
            $req['products_id'] = $products->id;
            if (isset($products->products_attributes)) {
                foreach ($products->products_attributes as   $value) {
                    $attr[] = $value->attribute_id;
                }
                $req['attributes'] = $attr;

            }
            return    $req; */

           /*  $check = Products::getquantity($req);
            if ($products->customers_basket_quantity > $check['stock']) {
                session(['out_of_stock' => 1]);
                session(['out_of_stock_product' => $products->id]);
                return redirect('viewcart');
            } 
        }*/
       
    }
    $order_information = array();
    $date_added = date('Y-m-d h:i:s');
    if (!empty($request->cc_type)) {
        $cc_type = $request->cc_type;
        $cc_owner = $request->cc_owner;
        $cc_number = $request->cc_number;
        $cc_expires = $request->cc_expires;
    } else {
        $cc_type = '';
        $cc_owner = '';
        $cc_number = '';
        $cc_expires = '';
    }
     

    $shipping_address =  DB::table('user_addresses')->where('id',$request->shipping_address)->first(); 
    $total_amount = $cart_data['grandTotal']; 
    $vat = 5;
    // $on_freeshipping = 200.00;
    // $shipping_cost = 100.00;

    //Apply Promo code place order 
    $user_id =  $request->user()->id;
    $current_date = date('Y-m-d H:i:s');
    if(isset($request->coupon_code)){
    $coupon =  Coupon::where('code', $request->coupon_code)
                ->where('expiry_date', '>=', $current_date)
                ->where('start_date', '<=', $current_date)
                ->where('status', 1)
                ->first();
               
 
if (!$coupon == NULL &&  !$coupon == '') {
        # used_by field is empty
        if ($coupon->used_by == NULL  ||  $coupon->used_by == '')  {
            $coupon->used_by = $user_id;
            $coupon->save();
        // return ['success' => true,'amount'=> $coupon->amount,'message' => 'Coupon applied successfully!'];
        }
        #This coupon has been reached to its maximum usage limit
        $coupon->increment('usage_count', 1);
        if ($coupon->usage_count > 100) {
            return ['success'=>false,"message"=>"This coupon has been reached to its maximum usage limit"];
        }
        # user not in used_by field 
        $user =  Coupon::select('used_by')->where('code', $request->coupon_code)->where('status',1)->first();
        $user_data =  $user['used_by'];
        $data_co =  explode(',', $user_data);
        if(!in_array($user_id, $data_co)){
                $coupon->used_by =  $user->used_by.','.$user_id; 
                $coupon->save();
            // return ['success' => true, 'amount'=> $coupon->amount,'message' => 'Coupon applied successfully!'];
        }
        
        else if (!$coupon->used_by == null) {

            $user =  Coupon::select('used_by')->where('code', $request->coupon_code)->where('status',1)->first();
        
            $user_data =  $user['used_by'];
            $data_co =  explode(',', $user_data);
            
            $count_values =  array_count_values($data_co);  
            $serch_data = $count_values[$user_id];

            if($serch_data == $coupon->usage_limit_per_user) {
                return ['success'=>false,"message"=>"sorry your coupon limit is over"];
            } else {
                $data = $user_id;
                $coupon->used_by = $data .  ',' . $coupon->used_by;
                $coupon->save();
                // return ['success' => true, 'amount'=> $coupon->amount,'message' => 'Coupon applied successfully!'];
            }
        }
    //  return ['success' => true, 'amount'=> $coupon->amount,'message' => 'Coupon applied successfully!'];
        // return   $coupon;
    }
    else{
        return ['success'=>false,'message'=>'You have entered invalied coupon code'];
    } 
}


    $coupon =  DB::table('coupons')->where('code',$request->coupon_code)->first();
 
    if(!empty($coupon)){
        $user_data =  $coupon->used_by;
        $coupon_data =  explode(',', $user_data);
        if (in_array($user_id, $coupon_data)  && $total_amount > $coupon->minimum_amount)  {
        $discount = number_format((float) $coupon->amount, 2, '.', '');
        $discount_percent_amount = number_format((float)$coupon->amount,2,'.','');
        $discount_percent = ($total_amount * $discount_percent_amount)/100;
         $coupon_amount = number_format((float) $coupon->amount, 2, '.', '');
    }
    else{
    $discount = 0.00; 
   
    $coupon_amount = 0.00;
 }
 
    } 
    $total_tax =  ($total_amount * $vat) / 100 ;
    $total_tax = number_format((float) $total_tax, 2, '.', '');
    $payment_method = $request->payment_method; 
    
    if(Str::contains(!empty($coupon->discount_type),'fixed_cart')){
        if(isset($request->coupon_code) && isset($discount)){
            $order_price =  $total_amount - $discount;
        }else{
            $order_price =  $total_amount;
        }
    }else{
        if(isset($request->coupon_code) && isset($discount_percent)){
            $order_price = $total_amount - $discount_percent;
        }else{
            $order_price =  $total_amount;
        }
    }

    // if($order_price > $on_freeshipping){
    //     $shipping_cost = 0;
    // }     
    // $order_price =  $order_price + $total_tax + $shipping_cost;
    
    $order_price =  $order_price + $total_tax;
    $order_price = number_format((float) $order_price, 2, '.', '');
    $payment_status = 'success';

    $orders_status = 1;
    $last_modified = date('Y-m-d H:i:s');
    $date_purchased = date('Y-m-d H:i:s');

    //check if order is verified
   
    if ($payment_status == 'success') {   
         
           $order_id = DB::table('orders')->insertGetId( 
            ['userid' => $user_id,
            'customer_name' => $shipping_address->name,
            'customer_email' => $shipping_address->email,
            'customers_phone' => $shipping_address->email,
            'customer_street_address' => $shipping_address->address,
            'customer_landmark' => $shipping_address->landmark,
            'customers_city' => $shipping_address->city,
            'customers_state' => $shipping_address->state,
            'customers_country' => $shipping_address->country,
            'customers_postcode' => $shipping_address->pincode,
            'addressid' =>$shipping_address->id,
            'payment_method' => $payment_method,
            'cc_type' => $cc_type,
            'cc_owner' => $cc_owner,
            'cc_number' => $cc_number,
            'cc_expires' => $cc_expires,
            'last_modified' => $last_modified,
            'date_purchased' => $date_purchased,
            'order_price' => $order_price,
            // 'shipping_cost' => $shipping_cost,
            'orders_status' => $orders_status,
            
            //'orders_date_finished'  => $orders_date_finished,
          
            'order_information' => json_encode($order_information),
            'coupon_code' => json_encode($coupon),
            'coupon_amount' => isset($coupon_amount)?$coupon_amount:0.00,
            'total_tax' => $total_tax,
            'ordered_source' => '1'

        ]);
      
        foreach ($cart_data['items'] as $product) {
            $product_tax = 0;
            $final_price = $product->regular_price;
            if(!empty($product->sale_price)){
                $final_price = $product->sale_price;
            }
            $order_product_id = DB::table('order_products')->insertGetId(
                [
                    'order_id' => $order_id,
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'product_price' => $product->regular_price,
                    'final_price' => $final_price,
                    'sub_total' =>$product->subtotal,
                    'product_tax' => $product_tax,
                    'product_quantity' => $product->quantity,
                ]);
                
                $pro_data = [];
                $pro_row = DB::table('products')->where('id', $product->id)->first();
                $pro_data['inventory'] =   intval($pro_row->inventory - $product->quantity);
                $pro_data['OnOrder'] = intval($pro_row->OnOrder + $product->quantity);
                DB::table('products')
                    ->where('id',$product->id)
                    ->update($pro_data);
             
        
         DB::table('customers_basket')->where('user_id',$user_id)->where('product_id',$product->id)->update(['is_order'=>1]);
          if (!empty($product->products_attributes) and count($product->products_attributes) >0) {
                foreach ($product->products_attributes as $attribute) {
                    DB::table('order_product_attributes')->insert(
                        [
                            'order_id' => $order_id, 
                            'product_id' => $product->id,
                            'order_product_id' => $order_product_id,
                            'variant_id' => $attribute->variant_id,
                            'item_name' => $attribute->item_name,
                            'item_code' => $attribute->item_code,
                            'variant_data' => $attribute->product_variant_data,
                            'quantity' => $attribute->quantity,
                            'product_price' => $product->price
                        ]);

                     
                        $pvc_data = [];
                        $pvc_row = DB::table('product_variant_combinations')->where('id', $attribute->variant_id)->first();
                        $pvc_data['OnHand'] =   intval($pvc_row->OnHand - $attribute->quantity);
                        $pvc_data['OnOrder'] = intval($pvc_row->OnOrder + $attribute->quantity);
                        DB::table('product_variant_combinations')
                            ->where('id',$attribute->variant_id)
                            ->update($pvc_data);
                      
                }
            }

        }

       // $orderStatus = order_statuses::whereBetween('id', [2, 4])->get();
       $orderStatus = DB::table('manage_order_status')->whereBetween('id', [2, 4])->get();

        DB::table('manage_order_status')->insert(
              [
                  'orderid' => $order_id,
                  'order_status_id' => 1,
                  'order_status_date' => date('Y-m-d'),

              ]
          );
  
              foreach($orderStatus as $data){

           $order_status = DB::table('manage_order_status')->insert(
                  [
                      'orderid' => $order_id,
                      'order_status_id' => $data->id,
                  ]
              );

                  
              }
             
          DB::table('manage_order_status')->insert(
              [
                  'orderid' => $order_id,
                  'order_status_id' => 5,
                  'order_status_date' =>  date('Y-m-d H:i:s', strtotime(' +7 day')),

              ]
          );
      
          
        return ['order_id'=>$order_id];
        $responseData = array('success' => '1','data' => array(),'message' => "Order has been placed successfully.");
      return $payment_status;
    } else if ($payment_status == "failed") {
        return $payment_status;
    }   
    
    }
}
