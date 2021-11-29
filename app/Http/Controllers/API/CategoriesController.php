<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Models\Categories;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Support\Facades\DB;


class CategoriesController extends BaseController
{
    /**
     * Get all Categories  
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderby = (isset($request->orderby)) ?   $request->orderby : "id";
        $order =  (isset($request->order)) ?   $request->order : "ASC";
        $perPage = (isset($request->per_page)) ?  intval($request->per_page) : 10 ;
        $limit = (isset($request->limit)) ?  intval($request->limit) : 0 ;   
        $parent = (isset($request->parent)) ?  intval($request->parent) : 0 ; 
     
    /*     $query =  Categories::whereNull('parent_category')->with('childCategoires')->select('id','category_name','parent_category','logo','category_description','slug')
                    ->where('status',1)
                    ->orderby($orderby,$order);  */
        
        $query = Categories::with('childCategoires')
        ->whereNull('parent_category')
        ->select('id','category_name','parent_category','logo','category_description','slug')
        ->where('status',1)->orderby($orderby,$order); 

        if($parent > 0){
            $query->where('parent_category',$parent);
        }       
        if($limit > 0){
            $result = $query->limit($limit)->get();
        }elseif($perPage > 0 && $limit ==  0){
            $result = $query->paginate($perPage);
        }elseif($perPage < 0){
            $result = $query->get();
        }
       foreach($result as $item){
            if (!empty($item->logo) && file_exists($item->logo)) {
                $item->logo = asset($item->logo);
            } else {
                $item->logo = asset('uploads/cat_image.png');
            }
            if(!empty($item->childCategoires)){
               foreach($item->childCategoires as $child){
                if (!empty($child->logo) && file_exists($child->logo)) {
                    $child->logo = asset($child->logo);
                } else {
                    $child->logo = asset('uploads/cat_image.png');
                }
               
                if(!empty($child->childCategoires)){
                    foreach($child->childCategoires as $nestedChild){
                     if (!empty($nestedChild->logo) && file_exists($nestedChild->logo)) {
                         $nestedChild->logo = asset($nestedChild->logo);
                     } else {
                         $nestedChild->logo = asset('uploads/cat_image.png');
                     }
                    }
                 }
               }
            } 
        
        }    
        return $this->sendResponse($result, 'Show all Product Categories'); 
    }
# product category using slug
function productCategoryList($slug)
    {
        # code...
        $item =  Categories::select('id','category_name','parent_category','logo','category_description','slug')
                    ->with('parents', 'catParents')
                    ->with('childCategoires')
                    ->where('status',1)
                    ->where('slug',$slug)
                    ->first();
            if (!empty($item->logo) && file_exists($item->logo)) {
                $item->logo = asset($item->logo);
            } else {
                $item->logo = asset('uploads/cat_image.png');
            }
            if(!empty($item->childCategoires)){
               foreach($item->childCategoires as $child){
                if (!empty($child->logo) && file_exists($child->logo)) {
                    $child->logo = asset($child->logo);
                } else {
                    $child->logo = asset('uploads/cat_image.png');
                }
                if(!empty($child->categories)){
                    foreach($child->categories as $nestedChild){
                     if (!empty($nestedChild->logo) && file_exists($nestedChild->logo)) {
                         $nestedChild->logo = asset($nestedChild->logo);
                     } else {
                         $nestedChild->logo = asset('uploads/cat_image.png');
                     }
                    }
                 }
               }
            } 
        
        return $this->sendResponse($item,"product category");
    
    }
    

}