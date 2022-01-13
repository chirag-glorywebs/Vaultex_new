<?php

use Illuminate\Support\Arr; 
use Illuminate\Support\Facades\File;

 
function slugify($slug)
{    
    // replace non letter or digits by -
    $slug = preg_replace('~[^\pL\d]+~u', '-', $slug);
    // transliterate
    if (function_exists('iconv')) {
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
    }
    // remove unwanted characters
    $slug = preg_replace('~[^-\w]+~', '', $slug);
    // trim
    $slug = trim($slug, '-');
    // remove duplicate -
    // $slug = preg_replace('~-+~', '-', $slug);
    // lowercase
    $slug = strtolower($slug);
    if (empty($slug)) {
        return 'n-a';
    }
    return $slug;
}

function uplodImage($blogImage, $watermark = false)
{
    $watermarkImageName = 'watermark-large.png';
    $year = 'uploads/' . date('Y');
    $destinationPath =  $year. '/' . date('m');
    if(file_exists($year)){
        if(file_exists($destinationPath)==false){
            mkdir($destinationPath,0777, true);
            }
    }else{
        mkdir($year, 0777,true);
        mkdir($destinationPath,0777, true);
    }
    
    $imageFullName = $blogImage->getClientOriginalName();
    $splitBlogImage = explode('.', $imageFullName);
    $splitName = $splitBlogImage[0];
    $splitExt = $splitBlogImage[1];
    $slug = $splitName;
    $slugCount = 0;
    do {
        if ($slugCount == 0) {
            $currentSlug = slugify($slug);
        } else {
            $currentSlug = slugify($slug . '-' . $slugCount);
        }
        $checkImagePath = $destinationPath . '/' . $currentSlug . '.' . $splitExt;
        if (file_exists($checkImagePath)) {
            $slugCount++;
        } else {
            $slug = $currentSlug;
            $slugCount = 0;
        }
    } while ($slugCount > 0);
    $finalImage = $slug . '.' . $splitExt;
    $moveImage = $finalImage;
    //Insert image path
  
    if($watermark == true){
         $watermark_path = 'uploads/'.$watermarkImageName;  
        // // $watermark_path= public_path('/uploads/2021/06').'/logo.png';   
         $watermarkImg = Image::make($watermark_path);
        $imgFile = waterMarkImage($blogImage->getRealPath(), $watermark_path);
         $imgFile->save($destinationPath . '/' . $finalImage);
         $watermarkImg->destroy();
    }else{
        $blogImage->move($destinationPath, $moveImage);
    }
    $finalImagePath = $destinationPath . '/' . $finalImage;
    return $finalImagePath;
}

function checkRemoteFileAvailable($url, $videoFile=false)
{
    if($videoFile){
        $urlHeader = get_headers($url);
        $urlExist = stripos($urlHeader[0],"200 OK")?true:false;
    } else {
        $urlExist = @getimagesize($url);
    }
    return $urlExist;
    // if ($urlExist) {
    //     return true;
    // } else {
    //    return false;
    // }    
}

function uplodImageByURL($url, $watermark = false)
{
    ini_set('max_execution_time', 600); 
    ini_set('memory_limit','-1');
   
    $watermarkImageName = 'watermark-large.png';
    $year = 'uploads/' . date('Y');
    $destinationPath =  $year. '/' . date('m');
    if(file_exists($year)){
        if(file_exists($destinationPath)==false){
            mkdir($destinationPath,0777, true);
            }
    }else{
        mkdir($year, 0777,true);
        mkdir($destinationPath,0777, true);
    }  
   
    $contents = @file_get_contents($url);

    $info = pathinfo($url);
    $splitName = (isset($info['filename']) && $info['filename']) ? $info['filename'] : '';
    $splitExt = (isset($info['extension']) && $info['extension']) ? $info['extension'] : '';
    $slug = $splitName;
    
    $videoFile = false;
    $extArr = ['MP4','MOV','WMV','AVI','AVCHD','MKV','MPEG-2','WEBM','HTML5','FLV','F4V','SWF'];
    if(in_array(strtoupper($splitExt), $extArr)){
    // if($splitExt=='MP4'){
        $videoFile = true;
    }    
    $isRemoteFileAvailable = checkRemoteFileAvailable($url, $videoFile);

    if($contents && $isRemoteFileAvailable){       
        
        $slugCount = 0;
        do {
            if ($slugCount == 0) {
                $currentSlug = slugify($slug);
            } else {
                $currentSlug = slugify($slug . '-' . $slugCount);
            }
            $checkImagePath = $destinationPath . '/' . $currentSlug . '.' . $splitExt;
            if (file_exists($checkImagePath)) {
                $slugCount++;
            } else {
                $slug = $currentSlug;
                $slugCount = 0;
            }
        } while ($slugCount > 0);
        $finalImage = $slug . '.' . $splitExt;
        $finalImagePath = $destinationPath . '/' . $finalImage;
        if($watermark == true){
            $watermark_path = 'uploads/'.$watermarkImageName;  
            if(@file_get_contents($watermark_path) && @file_get_contents($url)){
                $watermarkImg = Image::make($watermark_path);                
                $imgFile = waterMarkImage($url, $watermark_path);
                if($imgFile){
                    $imgFile->save($finalImagePath);
                }
                $watermarkImg->destroy();
            } 
        
        }else{
            file_put_contents($finalImagePath, $contents);
        }
        return $finalImagePath;
    } else {        
        return '';
    }
}

function resizeImageByURL($url,$width,$height,$type, $watermark = false)
{
    ini_set('max_execution_time', 600); 
    ini_set('memory_limit','-1');
    
    $watermarkImageName = 'watermark-large.png';
    $year = 'uploads/' . date('Y');
    $destinationPath =  $year. '/' . date('m');
    if(file_exists($year)){
        if(file_exists($destinationPath)==false){
            mkdir($destinationPath,0777, true);
            }
    }else{
        mkdir($year, 0777,true);
        mkdir($destinationPath,0777, true);
    }

    $contents = @file_get_contents($url);
 

    $info = pathinfo($url); 
    $splitBlogImage = explode('.', $info['basename']);
    $splitName = (isset($splitBlogImage[0]) && $splitBlogImage[0]) ? $splitBlogImage[0] : '';
    $splitExt = (isset($splitBlogImage[1]) && $splitBlogImage[1]) ? $splitBlogImage[1] : '';

    // $splitName = $splitBlogImage[0];
    // $splitExt = $splitBlogImage[1];
    $slug = $splitName;

    $videoFile = false;

    $extArr = ['MP4','MOV','WMV','AVI','AVCHD','MKV','MPEG-2','WEBM','HTML5','FLV','F4V','SWF'];
    if(in_array(strtoupper($splitExt), $extArr)){
    // if($splitExt=='MP4'){   
        $videoFile = true;
    }
    $isRemoteFileAvailable = checkRemoteFileAvailable($url, $videoFile );
    
        
    // if($url!="https://sbmmarketplace.com/ProductImages/SafetyShoes/RBW12/RBW%2012.jpg"){
    //     print_r($isRemoteFileAvailable);
    //     echo '===============';
    //     print_r($url);
    //     exit;

    // }

    if($contents && $isRemoteFileAvailable){
        
            
            $slugCount = 0;
            do {
                if ($slugCount == 0) {
                    $currentSlug = slugify($slug);
                } else {
                    $currentSlug = slugify($slug . '-' . $slugCount);
                }
                $checkImagePath = $destinationPath . '/' . $currentSlug . '.' . $splitExt;
                if (file_exists($checkImagePath)) {
                    $slugCount++;
                } else {
                    $slug = $currentSlug;
                    $slugCount = 0;
                }
            } while ($slugCount > 0);
            $finalImage = $slug . '.' . $splitExt;

            $splitFinalImage = explode('.', $finalImage);
            $splitFinalImageName = $splitFinalImage[0];
            $splitFinalImageExt = $splitFinalImage[1];
            $slug = $splitFinalImageName.'-'.$type;
            $finalData = $slug . '.' . $splitFinalImageExt;
            $finalImagePath = $destinationPath . '/' . $finalData;

            if($watermark == true){
                $watermark_path = 'uploads/'.$watermarkImageName;  
                //    // $watermark_path= public_path('/uploads/2021/06').'/logo.png';   
                $watermarkImg = Image::make($watermark_path);
                $imgFile = waterMarkImage($url, $watermark_path);

                    $imgFile->fit($width,$height);
                    $imgFile->save($finalImagePath);
                    $watermarkImg->destroy();
            }else{
                    $image_resize = Image::make($url);
                    $image_resize->fit($width,$height);
                    $image_resize->save($finalImagePath);
            }
            return $finalImagePath;
    } else {
        return '';
    }
}
function resizeImage($blogImage,$width,$height,$type , $watermark = false)
{ 
    $watermarkImageName = 'watermark-large.png';
    $year = 'uploads/' . date('Y');
    $destinationPath =  $year. '/' . date('m');
    if(file_exists($year)){
        if(file_exists($destinationPath)==false){
            mkdir($destinationPath,0777, true);
            }
    }else{
        mkdir($year, 0777,true);
        mkdir($destinationPath,0777, true);
    }

    $imageFullName = $blogImage->getClientOriginalName();
    $splitBlogImage = explode('.', $imageFullName);
    $splitName = $splitBlogImage[0];
    $splitExt = $splitBlogImage[1];
    $slug = $splitName;
    $slugCount = 0;
    do {
        if ($slugCount == 0) {
            $currentSlug = slugify($slug);
        } else {
            $currentSlug = slugify($slug . '-' . $slugCount);
        }
        $checkImagePath = $destinationPath . '/' . $currentSlug . '.' . $splitExt;
        if (file_exists($checkImagePath)) {
            $slugCount++;
        } else {
            $slug = $currentSlug;
            $slugCount = 0;
        }
    } while ($slugCount > 0);
    $finalImage = $slug . '.' . $splitExt;
 
    $splitFinalImage = explode('.', $finalImage);
    $splitFinalImageName = $splitFinalImage[0];
    $splitFinalImageExt = $splitFinalImage[1];
    $slug = $splitFinalImageName.'-'.$type;
    $finalData = $slug . '.' . $splitFinalImageExt;
     

    if($watermark == true){
        $watermark_path = 'uploads/'.$watermarkImageName;  
        //    // $watermark_path= public_path('/uploads/2021/06').'/logo.png';   
        $watermarkImg = Image::make($watermark_path);
    
    $imgFile = waterMarkImage($blogImage->getRealPath(), $watermark_path);

        $imgFile->fit($width,$height);
        $imgFile->save($destinationPath . '/' . $finalData);
        $watermarkImg->destroy();
   }else{
        $image_resize = Image::make($blogImage->getRealPath());
        // $image_resize->orientate(); 
        $image_resize->fit($width,$height);
        $image_resize->save($destinationPath.'/' .$finalData);
   }
    $finalImagePath = $destinationPath . '/' . $finalData;
    return $finalImagePath;
}

/**
     * View getPorductVariant
     *
     * @return \Illuminate\Http\Response
     */
function paymentsMenthods()
{
    $methodArray = array();
   
    $methodArray = [
        [ 'id' => 1, 'payment_method' => 'cash_on_delivery'],
        // [ 'id' => 2, 'payment_method' => 'stripe']
        [ 'id' => 3, 'payment_method' => 'pay_online']
    ];
    return $methodArray;
}


function waterMarkImage($mainImage, $watermark_path)
{
    
    try{
        $watermark =  Image::make($watermark_path);
        $imgFile = Image::make($mainImage);
    
        //#1
        $watermarkSize = $imgFile->width() - 20; //size of the image minus 20 margins
        //#2
        $watermarkSize = $imgFile->width() / 2; //half of the image size
        //#3
        $resizePercentage = 40;//40% less then an actual image (play with this value)
        $watermarkSize = round($imgFile->width() * ((100 - $resizePercentage) / 100), 2); //watermark will be $resizePercentage less then the actual width of the image

        // resize watermark width keep height auto
        $watermark->resize($watermarkSize, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        //insert resized watermark to image center aligned
        $imgFile->insert($watermark, 'center');
    

        // $watermarkImg = Image::make($watermark_path);
        // $imgFile = Image::make($mainImage);
        // $wmarkWidth=$watermarkImg->width();
        // $wmarkHeight=$watermarkImg->height();
        // $imgWidth=$imgFile->width();
        // $imgHeight=$imgFile->height();
        // $x=0;
        // $y=0;
        // $imgFile->insert($watermark_path,'center',$x,$y);
        // // while($y<=$imgHeight){
        // //     $imgFile->insert($watermark_path,'center',$x,$y);
        // //     $x+=$wmarkWidth;
        // //     if($x>=$imgWidth){
        // //         $x=0;
        // //         $y+=$wmarkHeight;
        // //     }
        // // }
        return $imgFile;  
    
    } catch(\Exception $e){
        return false;
        // Session::flash('message', $mainImage.' - '.$e->getFile().' - '.$e->getMessage());
        // return redirect('/admin/import');
    }
}

?>
