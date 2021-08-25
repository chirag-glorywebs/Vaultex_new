<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Models\Blog_review;
use App\Models\Blog;   
use Illuminate\Support\Facades\Validator;

class BlogReviewController extends BaseController
{
     /**
     * Create Review api
     *
     * @return \Illuminate\Http\Response
     */
    public function createReviews(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comments' => 'required',  
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }else{
            $blog_review = new Blog_review;
            $blog_review->name = $request->name;
            $blog_review->email = $request->email;
            $blog_review->comments = $request->comments;
            $blog_review->blog_id = $request->blog_id;
            $blog_review->user_id = $request->user_id; 
            $restult = $blog_review->save();
            
            return $this->sendResponse($restult, 'Your review has been post successfully');
        }
    
    }
     /**
     * Get Sigle Review api
     *
     * @return \Illuminate\Http\Response
     */
    public function reviews($id)
    {   
        $restult =  Blog_review::where('id',$id)->get();
        return $this->sendResponse($restult, 'show single blog review');
         
    }
    /**
     * Get All Reviews api
     *
     * @return \Illuminate\Http\Response
     */
    public function showReviews(Request $request)
    { 
        $blog_id = (isset($request->blog_id)) ?  intval($request->blog_id) : 0 ; 

        $query =  Blog_review::select('id','name','email','comments','blog_id','user_id');

        if(!empty($blog_id)){
            $query->where('blog_id',$blog_id);
        }
        $result = $query->get();
        return $this->sendResponse($result, 'All reviews of this blog post');
        
    }
}
