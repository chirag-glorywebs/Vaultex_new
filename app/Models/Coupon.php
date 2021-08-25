<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class Coupon extends Model
{
    use HasFactory;
    protected $table = 'coupons';
    // protected $fillable = ['used_by'];
    public function getcode($code){

     //   $couponInfo = DB::table('coupons')->where('code','=', $code)->get();
        $couponInfo = Coupon::where('code','=', $code)->get();
        return $couponInfo;
    }

    /**
     *  Apply coupon code
     *
     * @return \Illuminate\Http\Response
     */

    public function applyCoupon(Request $request)
    {
        # code...
        $user_id =  $request->user()->id;

        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required'
        ]);

        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {
            $coupon  = new Coupon;
            $current_date = date('Y-m-d H:i:s');
            $code = $request->coupon_code;
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
              
                return ['success' => true,'amount'=> $coupon->amount,'message' => 'Coupon applied successfully!'];
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
                return ['success' => true, 'amount'=> $coupon->amount, 'description'=> $coupon->description,'message' => 'Coupon applied successfully!'];
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
                    return ['success' => true, 'amount'=> $coupon->amount,'description'=> $coupon->description, 'message' => 'Coupon applied successfully!'];
                }
             }
         return ['success' => true, 'amount'=> $coupon->amount,'message' => 'Coupon applied successfully!'];
            return   $coupon;
        }
        else{
            return ['success'=>false,'message'=>'You have entered invalied coupon code'];
        } 
    }
      
}

    
}
