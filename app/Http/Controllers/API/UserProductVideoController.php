<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\UserProductVideo;
use App\Models\Products;
use App\Models\User;
use FFMpeg\FFProbe;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Format\Video\X264;
use Illuminate\Database\Eloquent\Model;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Image;
use File;

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


    public function waterMarkVideo(Request $request)
    {        
        try {
            // Remove expired videos
            $expiredProductVideos = UserProductVideo::getExpiredWatermarkVideos();            
            foreach ($expiredProductVideos as $key => $value) {
                if($value->video){
                    if (File::exists(public_path($value->video))) {
                        File::delete(public_path($value->video));
                    }            
                } 
                $removeExpireWatermarkVideos = UserProductVideo::destroy($value->id); 
            }

            // Create watermark videos
            $result = '';
            $userProductVideo = UserProductVideo::getNonWatermarkVideos(); 
            if($userProductVideo->isNotEmpty()) {
                foreach ($userProductVideo as $key => $upvItem) {
                    if($upvItem && ($upvItem->business_logo && file_exists(public_path($upvItem->business_logo))) && ($upvItem->product_video && file_exists(public_path($upvItem->product_video)))){

                        $splitLogo = explode('.', $upvItem->business_logo);
                        $logo = trim($splitLogo[0]);
                        $watermakVideoTitle = $logo.'-'.$upvItem->id.'-watermark-video.mp4';
                        $ffmpeg_string = getenv('FFMPEG_BINARIES');
                        $videotmp = public_path($upvItem->product_video);
                        $businessLogo = public_path($upvItem->business_logo);
                        $watermarkvideo = public_path($watermakVideoTitle);
                        $cmd = $ffmpeg_string . " -i " . $videotmp . " -i " . $businessLogo;
                        $cmd .= " -filter_complex \"";
                        // $cmd .= " scale2ref=(W/H)*ih/8/sar:ih/8[wm][base];[base][wm]\""; // closing double quotes
                        //$cmd .= " overlay=x=(main_w-overlay_w):y=(main_h-overlay_h)\""; // closing double quotes
                        $cmd .= " overlay=10:main_h-overlay_h-10 \""; // closing double quotes
                        $cmd .= " -pix_fmt yuv420p -c:a copy " . $watermarkvideo;
                        system($cmd);
                        $productsVideo = UserProductVideo::find($upvItem->id);
                        $productsVideo->video = $watermakVideoTitle;
                        $productsVideo->status = 1;
                        $result = $productsVideo->save();
                    }
                }
                $message = 'Product videos created successfully.';
                // return $this->sendResponse($result, 'Product videos created successfully.');
            } else {
                $message = 'Product not available.';
                // return $this->sendResponse($result, 'Product not available.');
            }

            $responseData = [
                'message'=>$message,
                'error'=>'',
                'function' => 'waterMarkVideo'            
            ];
            $send = User::sendNotificationForCron($responseData);
            // return response()->json($responseData,200); 
            return $this->sendResponse($result, $message);

        } catch (\Exception $e) {
            $responseData = [
                'message'=>'Something went wrong.',
                'error'=>$e->getLine().' - '.$e->getFile().' - '.$e->getMessage(),
                'function' => 'waterMarkVideo'
            ];
            $send = User::sendNotificationForCron($responseData);
            return $this->sendError('Something Went Wrong.');
        }
        
    }
}