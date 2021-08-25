<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\UserProductVideo;

class UserProductVideoController extends BaseController
{
    /**
     * show list api
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::guard('api')->check()) {
             $user_id = $request->user('api')->id;
            $result = DB::table('user_product_videos')
                     ->join('products', 'user_product_videos.product_id', '=', 'products.id') 
                     ->join('users', 'user_product_videos.user_id', '=', 'users.id')
                     ->select('user_product_videos.id','products.product_name','products.slug','products.sku','user_product_videos.product_id','user_product_videos.video','user_product_videos.status','user_product_videos.created_at')
                     ->where('user_product_videos.user_id',$user_id) 
                     ->orderby('id', 'DESC')
                     ->get();
                     
            if(!empty($result)){
                foreach($result as $item){
                    $item->video = asset($item->video);
                    $item->placeholder = asset('uploads/video-placeholder.jpg');
                    $currentDateTime = Carbon::now();
                    $lastDate = Carbon::parse($item->created_at)->addDays(15);
                    $item->left_days = $lastDate->diffInDays($currentDateTime);
                }
                return $this->sendResponse($result, 'Product downloadable videos');
            }else{
                return $this->sendResponse($result, 'Not found any downloadable video of products');
            }
        }else{
            return $this->sendError('Unauthenticated user.', 422);
        }
    }

    /**
     * Store api
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        if (Auth::guard('api')->check()) {
            $user_id = $request->user('api')->id;
            $product_id = $request->product_id;
             $product_video_data =  DB::table('user_product_videos')->where('product_id', $product_id)->where('user_id', $user_id)->first(); 
            if(!empty($product_video_data)){
                return $this->sendResponse(true, 'You already submited before. We will do proceed with your request as soon as possible Thank you.');
            }else{
                $new = new UserProductVideo;
                $new->user_id = $user_id;
                $new->product_id = $product_id;
                $new->save();
                return $this->sendResponse(true, 'Your request has been sucessfully submited we will create video with your logo image. You can see and download your requested product video from my account page.');
            }
        }else{
            return $this->sendError('Unauthenticated user.', 422);
        }
    }
    /**
     * destroy record 
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       $videos =  DB::table('user_product_videos')->get();
    //    return response()->json($videos);
       $count = 0; 
       if(!empty( $videos)){
           foreach( $videos as $item){
            if (Carbon::parse($item->created_at)->addDays(15)->isPast()) {
                   $expiredVideo =   UserProductVideo::select('id')->where('id','=',$item->id)->first();
                if(!empty($expiredVideo)){
                    $result = $expiredVideo->delete();
                    $count ++ ; 
                }
               }
           }
       }
       return $this->sendResponse(true, 'Total '.$count.' Records Deleted');
    }
}