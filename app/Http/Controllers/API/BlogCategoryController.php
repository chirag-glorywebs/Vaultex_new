<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory; 
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; 
use Yajra\DataTables\Facades\DataTables;

class BlogCategoryController extends BaseController
{
    /**
     * Get All Blog Categories api
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        $orderby = (isset($request->orderby)) ?   $request->orderby : "id";
        $order =  (isset($request->order)) ?   $request->order : "ASC";
        $perPage = (isset($request->per_page)) ?  intval($request->per_page) : 10 ;
        $limit = (isset($request->limit)) ?  intval($request->limit) : 0 ;    
        $query =  BlogCategory::select('id','category_name','slug')
                    ->where('status',1)
                    ->orderby($orderby,$order); 
             
        if($limit > 0){
            $result = $query->limit($limit)->get();
        }elseif($perPage > 0 && $limit ==  0){
            $result = $query->paginate($perPage);
        }elseif($perPage < 0){
            $result = $query->get();
        }
        
        foreach( $result as $item){
            $item->brand_logo = asset('uploads/product_image.png');
        
        }  
        return $this->sendResponse($result, 'Show all Product brands'); 
    }
    
}
