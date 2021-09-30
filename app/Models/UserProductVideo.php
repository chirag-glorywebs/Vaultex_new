<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserProductVideo extends Model
{
    use HasFactory;

    public static function getNonWatermarkVideos(){
        return UserProductVideo::SELECT('user_product_videos.*', 'users.business_logo', 'products.video as product_video')
        ->LEFTJOIN('products', 'products.id', '=', 'user_product_videos.product_id')        
        ->LEFTJOIN('users', 'users.id', '=', 'user_product_videos.user_id')        
        ->WHERE('user_product_videos.created_at','>=',Carbon::now()->subdays(15))
        ->WHERE('user_product_videos.status', 0)
        ->ORDERBY('user_product_videos.created_at', 'DESC')
        ->GET();
    }

    public static function getExpiredWatermarkVideos(){
        return UserProductVideo::SELECT('user_product_videos.*')
        ->WHERE('user_product_videos.created_at','<',Carbon::now()->subdays(15))
        ->GET();
    }
}
