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
         $watermark_path = public_path('/uploads').'/500px.png';  
        // $watermark_path= public_path('/uploads/2021/06').'/logo.png';   
         $watermarkImg = Image::make($watermark_path);
         $imgFile = Image::make($blogImage->getRealPath());
         $wmarkWidth=$watermarkImg->width();
         $wmarkHeight=$watermarkImg->height();
         $imgWidth=$imgFile->width();
         $imgHeight=$imgFile->height();
         $x=0;
         $y=0;
         while($y<=$imgHeight){
             $imgFile->insert($watermark_path,'top-left',$x,$y);
             $x+=$wmarkWidth;
             if($x>=$imgWidth){
                 $x=0;
                 $y+=$wmarkHeight;
             }
         }
         $imgFile->save(public_path($destinationPath . '/' . $finalImage));
         $watermarkImg->destroy();
    }else{
        $blogImage->move($destinationPath, $moveImage);
    }
    $finalImagePath = $destinationPath . '/' . $finalImage;
    return $finalImagePath;
}
function uplodImageByURL($url, $watermark = false)
{
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
    $info = pathinfo($url); 
    $contents = file_get_contents($url); 
    $splitName = $info['filename'];
    $splitExt = $info['extension'];
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
    $finalImagePath = $destinationPath . '/' . $finalImage;

    if($watermark == true){
        $watermark_path = public_path('/uploads').'/500px.png';  
       // $watermark_path= public_path('/uploads/2021/06').'/logo.png';   
        $watermarkImg = Image::make($watermark_path);
        $imgFile = Image::make($url);
        $wmarkWidth=$watermarkImg->width();
        $wmarkHeight=$watermarkImg->height();
        $imgWidth=$imgFile->width();
        $imgHeight=$imgFile->height();
        $x=0;
        $y=0;
        while($y<=$imgHeight){
            $imgFile->insert($watermark_path,'top-left',$x,$y);
            $x+=$wmarkWidth;
            if($x>=$imgWidth){
                $x=0;
                $y+=$wmarkHeight;
            }
        }
        $imgFile->save(public_path($finalImagePath));
        $watermarkImg->destroy();
    }else{
        file_put_contents($finalImagePath, $contents);
    }
    return $finalImagePath;
}

function resizeImageByURL($url,$width,$height,$type, $watermark = false)
{
    $year = 'uploads/' . date('Y');
    $destinationPath =  $year. '/' . date('m');
    if(file_exists($year)){
        if(file_exists($destinationPath)==false){
            mkdir($destinationPath,777, true);
            }
    }else{
        mkdir($year, 777,true);
        mkdir($destinationPath,777, true);
    }
    $info = pathinfo($url); 
    $splitBlogImage = explode('.', $info['basename']);
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
    $finalImagePath = $destinationPath . '/' . $finalData;

    if($watermark == true){
        $watermark_path = public_path('/uploads').'/500px.png';  
       // $watermark_path= public_path('/uploads/2021/06').'/logo.png';   
        $watermarkImg = Image::make($watermark_path);
        $imgFile = Image::make($url);
        $wmarkWidth=$watermarkImg->width();
        $wmarkHeight=$watermarkImg->height();
        $imgWidth=$imgFile->width();
        $imgHeight=$imgFile->height();
        $x=0;
        $y=0;
        while($y<=$imgHeight){
            $imgFile->insert($watermark_path,'top-left',$x,$y);
            $x+=$wmarkWidth;
            if($x>=$imgWidth){
                $x=0;
                $y+=$wmarkHeight;
            }
        }
        $imgFile->fit($width,$height);
        $imgFile->save(public_path($finalImagePath));
        $watermarkImg->destroy();
   }else{
        $image_resize = Image::make($url);
        $image_resize->fit($width,$height);
        $image_resize->save(public_path($finalImagePath));
   }
   return $finalImagePath;
}
function resizeImage($blogImage,$width,$height,$type , $watermark = false)
{ 
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
        $watermark_path = public_path('/uploads').'/500px.png';  
       // $watermark_path= public_path('/uploads/2021/06').'/logo.png';   
        $watermarkImg = Image::make($watermark_path);
        $imgFile = Image::make($blogImage->getRealPath());
        $wmarkWidth=$watermarkImg->width();
        $wmarkHeight=$watermarkImg->height();
        $imgWidth=$imgFile->width();
        $imgHeight=$imgFile->height();
        $x=0;
        $y=0;
        while($y<=$imgHeight){
            $imgFile->insert($watermark_path,'top-left',$x,$y);
            $x+=$wmarkWidth;
            if($x>=$imgWidth){
                $x=0;
                $y+=$wmarkHeight;
            }
        }
        $imgFile->fit($width,$height);
        $imgFile->save(public_path($destinationPath . '/' . $finalData));
        $watermarkImg->destroy();
   }else{
        $image_resize = Image::make($blogImage->getRealPath());
        // $image_resize->orientate(); 
        $image_resize->fit($width,$height);
        $image_resize->save(public_path($destinationPath.'/' .$finalData));
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
        [ 'id' => 2, 'payment_method' => 'stripe']
    ];
    return $methodArray;
}


?>
