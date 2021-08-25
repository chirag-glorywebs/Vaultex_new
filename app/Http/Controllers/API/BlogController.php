<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Models\Blog_review;
use App\Models\Blog;    
use App\Models\BlogCategory;      
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Constraint\IsNull;
use Illuminate\Validation\Rule;
use App\Models\Settings;

   
class BlogController extends BaseController
{
    /**
     * Get All Blog api
     *
     * @return \Illuminate\Http\Response
     */
    public function getBlogList(Request $request)
    {
        $orderby = (isset($request->orderby)) ?   $request->orderby : "id";
        $order =  (isset($request->order)) ?   $request->order : "ASC";
        $perPage = (isset($request->per_page)) ?  intval($request->per_page) : 10 ;
        $limit = (isset($request->limit)) ?  intval($request->limit) : 0 ;   
        $catId = (isset($request->cat_id)) ?  intval($request->cat_id) : 0 ;   
        $query =  Blog::select('id','blog_name','blog_image','blog_description','blog_date','category_id','slug')
                    ->where('status',1)
                    ->orderby($orderby,$order); 
        if($catId > 0){
            $query->where('category_id',$catId);
        }       
        if($limit > 0){
            $result = $query->limit($limit)->get();
        }elseif($perPage > 0 && $limit ==  0){
            $result = $query->paginate($perPage);
        }elseif($perPage < 0){
            $result = $query->get();
        }
        
        foreach( $result as $item){
            $furl = Settings::where('id',21)->select('value')->get();
            $item->fronturl = $furl[0]['value'].'/blog/'.$item['slug'];
            $item->blog_image = asset('uploads/blog-detail.png');
            $item->blogCategory;
        }  
        return $this->sendResponse($result, 'Blog List'); 
    }

    /**
     * Get Single Blog 
     *
     * @return \Illuminate\Http\Response
     */
    public function getBlogDetailsByID($id)
    {
        $blog =  Blog::where('status',1)
        ->where('id',$id)
        ->get(); 
        if(!empty($blog[0]['category_id'])){
            $blog[0]['category_name'] =  isset($blog[0]->blogCategory->category_name);
            $blog[0]['blog_image'] = asset('uploads/blog-detail.png');
            $blog[0]['banner'] =  asset('uploads/blog-detail.png');
            $fronturl = Settings::where('id',21)->select('value')->get();
            $blog[0]['fronturl'] = $fronturl[0]['value'].'/blog/'.$blog[0]['slug'];
             
        }   
        return $this->sendResponse($blog, 'Blog Details page');
    }
    
    /**
     * Get Single Blog For Web
     *
     * @return \Illuminate\Http\Response
     */ 
    public function getBlogDetails($blog_slug)
    {
        $result =  Blog::where('status',1)
        ->where('slug',$blog_slug)
        ->first();
        $result->banner = asset($result->banner);
        $result->blog_image = asset($result->blog_image);
        return $this->sendResponse($result, 'Blog List');
    }
}