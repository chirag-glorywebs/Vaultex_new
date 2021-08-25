<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use App\Models\Products;
use Illuminate\Http\Request;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;

class Cart extends Model
{
    use HasFactory;

     //mycart
     public function myCart($user_id,Request $request)
     {
         
        $totalPrice = 0;
        $orderby = "id";
        $order =  "ASC";
        $result = Products::join('customers_basket', 'products.id', '=', 'customers_basket.product_id')
            ->where('user_id', $user_id)
            ->select([
                'customers_basket.id', 'customers_basket.quantity', 'customers_basket.price', 'products.id', 'customers_basket.user_id', 'products.product_name', 'products.regular_price', 'products.sale_price',
                'products.main_image', 'products.sku', 'products.slug', 'products.specification'
            ])
            ->where('is_order', 0)
            ->orderby('products.'.$orderby, $order)->get();
 
        if (!$result->isEmpty()) {
            # SubTotal  
            foreach ($result as $data) {
                $data->subtotal = number_format((float)$data->price * $data->quantity, 2, '.', '');
                if (!empty($data->main_image) && file_exists($data->main_image)) {
                    $data->main_image = asset($data->main_image);
                } else {
                    $data->main_image = asset('uploads/product-placeholder.png');
                }
                if (!empty($data->tech_documents)) {
                    $data->tech_documents = asset($data->tech_documents);
                }
                if (!empty($data->video) && file_exists($data->video)) {
                    $data->video = asset($data->video);
                }

               /*  $data->products_attributes =  DB::table('customers_basket_attributes')->join('attributes', 'customers_basket_attributes.attribute_id', '=', 'attributes.id')
                ->join('attributes_variations', 'customers_basket_attributes.attribute_variation_id', '=', 'attributes_variations.id')
                ->select('customers_basket_attributes.attribute_id', 'customers_basket_attributes.attribute_variation_id','customers_basket_attributes.quantity', 'attributes.attribute_name', 'attributes_variations.variation_name') 
               ->where([['product_id', '=', $data->id],['customer_id', '=', $user_id]]) */

                $data->products_attributes =  DB::table('customers_basket_attributes')
                ->join('product_variant_combinations', 'customers_basket_attributes.variant_id', '=', 'product_variant_combinations.id')
                //->join('attributes_variations', 'customers_basket_attributes.attribute_variation_id', '=', 'attributes_variations.id')
                ->select('customers_basket_attributes.variant_id', 
                'product_variant_combinations.item_code',
                'product_variant_combinations.product_variant_data',
                'product_variant_combinations.item_name',
                'customers_basket_attributes.quantity',
                'product_variant_combinations.OnHand as stock',
                'product_variant_combinations.IsCommited') 
               ->where([['customers_basket_attributes.product_id', '=', $data->id],['customer_id', '=', $user_id]])
               
               ->get(); 
            }
           
        }
         # Additional total
         foreach ($result as $data) {
            $totalPrice = $totalPrice + ($data->price * $data->quantity);
            $totalPrice  = number_format((float)$totalPrice, 2, '.', '');
        }
        $settings = Settings::select('value')->where('id',15)->orWhere('id',18)->get();
        $vat  = $settings[0]['value'];
        $shipping_cost  = $settings[1]['value'];
        $vatAmount = ($totalPrice / 100) * 5;
        $vat_amount = number_format((float)$vatAmount,2,'.','');
        $amount_payable = $vat_amount + $shipping_cost + $totalPrice;
       
     # coupon code discount
    //    $data =  Coupon::select('used_by','amount','id','code','minimum_amount')->where('code',$request->coupon_code)->first();
    // //  return $data;
    //    if(isset($data)){
    //            $user_data =  $data['used_by'];
    //            $coupon =  explode(',', $user_data);
    //         // return $data['minimum_amount'];
    //         if (in_array($user_id, $coupon) && $totalPrice > $data->minimum_amount) {
    //              $total_coupon_discount  =  $totalPrice - $data->amount;
        //    }else{
    //         $total_coupon_discount  =  "00.00";
      
    //     }
    // }    
        $responseData = array('items'=>$result,'grandTotal'=>$totalPrice,'vat'=>$vat,'shipping_cost'=>$shipping_cost,
        'vat_amount'=>$vat_amount,'amount_payable'=>$amount_payable);
        return $responseData;
    }
}