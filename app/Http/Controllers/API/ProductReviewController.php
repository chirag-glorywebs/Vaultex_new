<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController;
use App\Models\Reviews;

class ProductReviewController extends BaseController
{
  /**
     * Create Review api
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',  
            'comment' => 'required',  
            'proid' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }else{
            if (Auth::guard('api')->check() && (!isset($request->userid))) {
                $user_id = $request->user('api')->id;
            }else{
                $user_id = $request->userid; 
            }
            $review = new Reviews;
            $review->title = $request->title;
            $review->comment = $request->comment;
            $review->rating = $request->rating;
            $review->proid = $request->proid;
            $review->userid = $user_id; 
            $restult = $review->save();
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
        $restult =  Reviews::where('id',$id)->get();
        return $this->sendResponse($restult, 'show single review');
         
    }

    /**
     * Get All Reviews api
     *
     * @return \Illuminate\Http\Response
     */
    public function showReviews(Request $request)
    { 
        $proid = (isset($request->proid)) ?  intval($request->proid) : 0 ; 

        $query =  Reviews::select('id','title','comment','rating','proid','userid');

        if(!empty($proid)){
            $query->where('proid',$proid);
        }
        $result = $query->get();
        return $this->sendResponse($result, 'All reviews of this  post');
        
    }
}
