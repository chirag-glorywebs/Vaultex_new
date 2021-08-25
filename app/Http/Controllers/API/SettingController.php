<?php

namespace App\Http\Controllers\API;

use App\Models\Settings;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\DB;


class SettingController extends BaseController
{
   public function get()
   {
 
      $setting = DB::table('settings')->pluck('value','name');
      $setting['favicon'] = asset($setting['favicon']);
      $setting['logo'] = asset($setting['logo']);
      $setting['footer_image'] = asset($setting['footer_image']);

      $cat_ids = array(9,10,11,12,13,14);
      $cat_datas =  DB::table('categories')->select('id','category_name','slug')->where('status','=','1')->whereIn('id',$cat_ids)->get();
      $cat_products = array();
      foreach($cat_datas as $cat){
        $childCats =  DB::table('categories')->select('id','category_name','slug')->where('status','=','1')->where('parent_category',$cat->id)->take(6)->get();
        
          $cat_arr = array('id'=>$cat->id,'category_name'=>$cat->category_name,'slug'=>$cat->slug,'childCats'=>$childCats);
        array_push($cat_products,$cat_arr); 
      }
      $setting['footer_cats'] = $cat_products;
    return $this->sendResponse($setting, 'General Details');
 
}


}
