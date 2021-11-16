<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\LikedProduct;
use Illuminate\Support\Facades\Auth;
use App\Models\Faqs;
use App\Models\Product_feature_videos;
use App\Models\Product_training_videos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Brand;
use App\Http\Controllers\API\BaseController;
use App\Models\Categories;
use App\Models\Order_products;
use App\Models\Settings;
use Illuminate\Support\Arr;
use Response;

class ProductsController extends BaseController
{
    public function __construct(Products $product)
    {
        $this->product = $product;
    }
    /**
     * Get all products
     *
     * @return \Illuminate\Http\Response
     */
    public function peoductList(Request $request)
    {
        $orderby = (isset($request->orderby)) ?   $request->orderby : "id";
        $order =  (isset($request->order)) ?   $request->order : "ASC";
        $perPage = (isset($request->per_page)) ?  intval($request->per_page) : 10;
        $limit = (isset($request->limit)) ?  intval($request->limit) : 0;
        $cat_id = (isset($request->cat_id)) ?  intval($request->cat_id) : 0;
        $brand_id = (isset($request->brand_id)) ? intval($request->brand_id) : 0;
        $brand_ids = (isset($request->brand_ids)) ? $request->brand_ids : 0;
        $badges = (isset($request->badges)) ? $request->badges : 0;
        $search_text =  (isset($request->searchText)) ? $request->searchText : '';  
      
         
        if ($orderby == "topsales") {
            // $orderby =  'OnOrder';
            $order = 'DESC';
            $orderby =  'price';
        }
        if ($orderby != "price" && $orderby != "topsales") {
            $orderby = 'products.'.$orderby;
        }
       
        // $user_id = $request->user()->id;
            
             if (Auth::guard('api')->check()) {
                $user_id = $request->user('api')->id;
                $query = DB::table('products')
                    ->join('price_lists', 'products.sku', '=', 'price_lists.item_no') 
                    ->join('users', 'price_lists.price_list_no', '=', 'users.price_list_no')
                  /*   ->leftJoin('product_variant_combinations', 'products.id', '=', 'product_variant_combinations.product_id')  */
                    ->select(
                        'products.id',
                        'products.product_name',
                        'products.product_type',
                        'products.short_description',
                        'products.regular_price',
                        'products.sale_price',
                        // 'products.category_id',
                        'products.main_image',
                        'products.medium_image',
                        'products.thumbnail_image',                        
                        'products.sku',
                        'products.slug',
                        'products.specification',
                        'products.trending_product',
                        'products.best_selling',
                        'price_lists.list_price AS uprice', 
                        DB::raw('COALESCE(price_lists.list_price, products.regular_price) as price'), 
                        DB::raw('COALESCE(CAST(((products.regular_price - price_lists.list_price) * 100 / products.regular_price) as decimal(5,2)),0) discount')
                        )
                     ->where('users.id', '=', $user_id)
                     ->where('products.status', 1);
                     
                 
             }else{
                $query =   DB::table('products')
                    ->select(
                    'products.id',
                    'product_name',
                    'product_type',
                    'regular_price',
                    'sale_price',
                    // 'category_id',
                    'main_image',
                    'medium_image',
                    'thumbnail_image',
                    'sku',
                    'products.slug',
                    'short_description',
                    'specification',
                    'products.trending_product',
                    'products.best_selling',
                    DB::raw('COALESCE(sale_price, regular_price) as price'),
                    DB::raw('COALESCE(CAST(((products.regular_price - products.sale_price) * 100 / products.regular_price) as decimal(5,2)),0) discount')) 
                    ->where('products.status', 1);
             }
            
            if ( !empty($search_text)){
                 $query->where(function ($qp) use($search_text) {
                    $qp->where('products.product_name', 'LIKE', "%{$search_text}%"); 
                    $qp->orWhere('products.sku', 'LIKE', "%{$search_text}%"); 
                });
                $orderby =  'products.sku';
            } 

                /* badges */
                if(!empty($badges)){
                    $badges = collect(explode(',', $request->badges))
                    ->map(fn($i) => trim($i))
                    ->all();
                    $best_selling = 0;
                    $best_deal = 0;
                    foreach($badges as $badge){
                        if ($badge == "best_selling") {
                            //$query->where('products.best_selling', 1);
                            $best_selling = 1 ; 
                             
                        } else if ($badge == "sale_price") {
                            $best_deal = 1 ; 
                           /*  $query->where(DB::raw('COALESCE(CAST(((products.regular_price - products.sale_price) * 100 / products.regular_price) as decimal(5,2)),0) '), '>',0);
                            $orderby =  'discount';
                            $order = 'DESC'; */
                        }
                    }
                    if($best_selling > 0 && $best_deal > 0){
                        $query->where(function ($qp) {
                            $qp->where('products.best_selling', 1);
                            $qp->orwhere('products.trending_product', 1);
                        });
                    }else if($best_selling > 0 && $best_deal == 0){
                        $query->where('products.best_selling', 1);
                    }else if($best_deal > 0 && $best_selling == 0){
                        $query->where('products.trending_product', 1);
                    }
                }   
                
                $query->orderby($orderby, $order);
                
                /* brand */
                if(!empty($brand_ids)){
                    $brand_ids = collect(explode(',', $request->brand_ids))
                    ->map(fn($i) => trim($i))
                    ->all();
                    $query->whereIn('products.brand_id', $brand_ids);
                }
                /* brand */
                if(!empty($request->in_stock)){
                    $query->where('products.inventory', '>', 0);
                }

                if (isset($request->prices)){
                    $data_prices = array();
                    $data_prices =   $request->prices;
                    if(is_array($data_prices)){
    
                        $query->where(function ($qp) use($data_prices) {
                        for($i=0; $i < count($data_prices); $i++){
                            $values = collect(explode(',', $data_prices[$i]))
                            ->map(fn($i) => trim($i))
                            ->all();
                        
                            if (Auth::guard('api')->check()) {
                                if(($i == 0) && ($values[1]  > 0)){
                                $qp->whereBetween(DB::raw('COALESCE(price_lists.list_price, products.regular_price)'), [$values[0], $values[1]]);  
                                
                                }else if(($i != 0) && ($values[1] > 0)){
                                    $qp->orwhereBetween(DB::raw('COALESCE(price_lists.list_price, products.regular_price)'),[$values[0], $values[1]]);
                                }else if(($values[1] < 0)){
                                    $qp->where(DB::raw('COALESCE(price_lists.list_price, products.regular_price) '), '>=', $values[0]);
                                }
                            }else{
                                if(($i == 0) && ($values[1]  > 0)){
                                $qp->whereBetween(DB::raw('COALESCE(products.sale_price, products.regular_price)'), [$values[0], $values[1]]);  
                                
                                }else if(($i != 0) && ($values[1] > 0)){
                                    $qp->orwhereBetween(DB::raw('COALESCE(sale_price, regular_price) '),[$values[0], $values[1]]);
                                }else if(($values[1] < 0)){
                                    $qp->where(DB::raw('COALESCE(products.sale_price, products.regular_price) '), '>=', $values[0]);
                                }
                            }
                        }
                      });
                   }
                }
                if (isset($request->discounts)){
                    $data_discounts = array();
                    $data_discounts =   $request->discounts; 
                    if(is_array($data_discounts)){
    
                       $query->where(function ($q) use($data_discounts) {
                            for($i=0; $i < count($data_discounts); $i++){
                                $values = collect(explode(',', $data_discounts[$i]))
                                ->map(fn($i) => trim($i))
                                ->all();
                                if (Auth::guard('api')->check()) {
                                    if($i == 0){
                                        $q->whereBetween(DB::raw('COALESCE(CAST(((products.regular_price - price_lists.list_price) * 100 / products.regular_price)  as decimal(5,2)),0)'), [$values[0], $values[1]]);   
                                    
                                    }else{
                                        $q->orwhereBetween(DB::raw('COALESCE(CAST(((products.regular_price - price_lists.list_price) * 100 / products.regular_price)  as decimal(5,2)),0)'),[$values[0], $values[1]]); 
                                    }
                                }else{
                                    if($i == 0){
                                        $q->whereBetween(DB::raw('COALESCE(CAST(((products.regular_price - products.sale_price) * 100 / products.regular_price) as decimal(5,2)),0) '), [$values[0], $values[1]]);   
                                    
                                    }else{
                                        $q->orwhereBetween(DB::raw('COALESCE(CAST(((products.regular_price - products.sale_price) * 100 / products.regular_price) as decimal(5,2)),0) '),[$values[0], $values[1]]); 
                                    }
                                }
                            }
                        });
                   }
                } 

        $parent_cats = array();
        if ($cat_id > 0) {
            $categoryIds = Categories::where('parent_category', $cat_id)->pluck('id')->all(); 
            $childCatData = Categories::whereIn('parent_category',$categoryIds)->pluck('id')->all();
         
            // $query->join('categories','categories.id','=','products.category_id');                        
            $query->join('product_categories', function($join) use($cat_id, $categoryIds, $childCatData){
                // $join->where('categories.id', '=', 'product_categories.category_id');
                $join->on('product_categories.product_id', '=', 'products.id');
                $join->where('product_categories.category_id', '=', $cat_id);
                // $join->orWhereIn('product_categories.category_id',$categoryIds);
                // $join->orWhereIn('product_categories.category_id',$childCatData);
            });
            
            // $query->where(function ($q) use($cat_id, $categoryIds, $childCatData ) {
            //    // $q->where('categories.parent_category',$cat_id)
            //     $q->where('products.category_id',$cat_id)
            //     ->orWhereIn('products.category_id',$categoryIds)
            //     ->orWhereIn('products.category_id',$childCatData); 
            // });

            $cat_data = Categories::select('id','parent_category')->where('id', $cat_id)->first();
            if(!empty($cat_data->parent_category)){
                $parent_cats = Categories::select('id','category_name AS name','slug')->where('parent_category', $cat_data->parent_category)->where('id','!=',$cat_id)->get();
            }
           
           
        }
        if ($brand_id > 0) {
            $query->where('products.brand_id', $brand_id);
        }
       
        if ($limit > 0) {
            $result = $query->limit($limit)->get();
        } elseif ($perPage > 0 && $limit ==  0) {
            $result = $query->paginate($perPage);
        } elseif ($perPage < 0) {
            $result = $query->get();
        }
        
        $frontend_url =  Settings::select('value')->where('name','frontend_url')->first();
           
        foreach ($result as $item) {
            $item->fronturl = $frontend_url->value.'/product/'.$item->slug;
            if (!empty($item->main_image) && file_exists($item->main_image)) {
                    $item->main_image = asset($item->main_image);
            }else{
                $item->main_image = asset('uploads/placeholder-medium.jpg');
            }

            if (!empty($item->medium_image) && file_exists($item->medium_image)) {
                $item->medium_image = asset($item->medium_image);
            }else{
                $item->medium_image = asset('uploads/placeholder-medium.jpg');
            }

            if (!empty($item->thumbnail_image) && file_exists($item->thumbnail_image)) {
                $item->thumbnail_image = asset($item->thumbnail_image);
            }else{
                $item->thumbnail_image = asset('uploads/placeholder-medium.jpg');
            }
            // if (!empty($item->medium_image) && file_exists($item->medium_image)) {
            //     $item->main_image = asset($item->medium_image);
                
            // }elseif (!empty($item->main_image) && file_exists($item->main_image) && empty($item->medium_image)) {
            //     $item->main_image = asset($item->main_image);
            // } else {   
            //     $item->main_image = asset('uploads/placeholder-medium.jpg');
            // }
            if (!empty($item->video) && file_exists($item->video)) {
                $item->video = asset($item->video);
            }
            if (empty($item->regular_price)) {
                $item->regular_price = 0.00;
            }
            $wishlist = false;
           
        /*     $uprice = null; */
            if (Auth::guard('api')->check()) {
                $user_id = $request->user('api')->id; 
                $likedProduct = new LikedProduct;
                $wishlistData =  $likedProduct->checkProductExist($item->id, $user_id);
                if (!empty($wishlistData)) {
                    $wishlist = true;
                } 

                /* $userPriceNoData =  DB::table('users')->select('price_list_no')->where('id','=',$user_id)->first();
                 $upriceData = DB::table('price_lists')
                 ->select('list_price')
                 ->where([['item_no', '=',  $item->sku],['price_list_no', '=',  $userPriceNoData->price_list_no]]) 
                ->first(); 
                if(!empty($upriceData)){
                    $uprice = $upriceData->list_price;
                } */
               
            
            } 
          /*   $item->uprice =  $uprice; */
            $item->wishlist = $wishlist;
        }
      /*   return $this->sendResponse($result, 'Show all Products'); */
        $response = [
            'success' => true,
            'data'    => $result,
            'parent_cats'    => $parent_cats,
            'message' => 'Show all Products',
        ];
        return response()->json($response, 200);
    }

    /**
     * Get Product Details Using Slug
     *
     * @return \Illuminate\Http\Response
     */
    public function productDetails($product_slug, Request $request)
    {
        # code...
        $data = Products::where('status', 1)
            ->where('slug', $product_slug)
            ->first();
       
        if (!empty($data)) {

            if (!empty($data->tech_documents)) {
                $documents  =  explode(',', $data->tech_documents);
                $icount = 0;
                foreach ($documents as $document) {
                    $documents[$icount] = asset($document);
                    $icount++;
                }
                $data->tech_documents =  $documents;
            } else {
                $data->tech_documents = null;
            }

            $data->product_icons  =  (!empty($data->product_icons) && $data->product_icons) ? explode('|', $data->product_icons) : [];

            if (!empty($data->main_image) && file_exists($data->main_image)) {
                $data->main_image = asset($data->main_image);
            } else { 
                $data->main_image = asset('uploads/placeholder-large.jpg'); 
            }
            if (!empty($data->thumbnail_image) && file_exists($data->thumbnail_image)) {
                $data->thumbnail_image = asset($data->thumbnail_image);
            } else { 
                $data->thumbnail_image = asset('uploads/placeholder-large.jpg'); 
            }
            if (!empty($data->medium_image) && file_exists($data->medium_image)) {
                $data->medium_image = asset($data->medium_image);
            } else { 
                $data->medium_image = asset('uploads/placeholder-large.jpg'); 
            }
            if (!empty($data->large_image) && file_exists($data->large_image)) {
                $data->large_image = asset($data->large_image);
            } else { 
                $data->large_image = asset('uploads/placeholder-large.jpg'); 
            }
            if (!empty($data->video) && file_exists($data->video)) {
                $data->video = asset($data->video);;
            }
            if (!empty($data->download_datasheet) && file_exists($data->download_datasheet)) {
                $data->download_datasheet = asset($data->download_datasheet);
            }
            if (!empty($data->gallery) ) {
                $gallery  =  explode(',', $data->gallery);
                $galleryArr = array();
                $icount = 0;
                foreach ($gallery as $image) {
                    if(file_exists($image)){
                        $galleryArr[] = asset($image);
                    }    
                    $icount++;
                }
                $data->gallery =  $galleryArr;
             
            } else {
                $data->gallery = null;
            }
           
            if (!empty($data->category_id)) {
                $data->productCategory;
            }
            if (!empty($data->brand_id)) {
                $data->productBrand;
            }
            if (empty($data->regular_price)) {
                $data->regular_price = 0.00;
            }

            $pdicount = 0;
            $pdi_data =   explode(',', $data['packaging_delivery_images']);
            $pdi_data_new = array();  

            foreach ($pdi_data  as $pdimage) {
                if (!empty($pdimage) && file_exists($pdimage)) {
                    $pdi_data_new[$pdicount] = asset($pdimage);
                    $pdicount++;
                }
            } 
            if(!empty($pdi_data_new)){
                $data->packaging_delivery_images =  $pdi_data_new;
            }else{
                $data->packaging_delivery_images =  null;
            }
            $wishlist = false;
            $uprice = null;
            if (Auth::guard('api')->check()) {
                $user_id = $request->user('api')->id;
                $likedProduct = new LikedProduct;
                $wishlistData =  $likedProduct->checkProductExist($data->id, $user_id);
                if (!empty($wishlistData)) {
                    $wishlist = true;
                }
                $userPriceNoData =  DB::table('users')->select('price_list_no')->where('id','=',$user_id)->first();
                $upriceData = DB::table('price_lists')
                ->select('list_price')
                ->where([['item_no', '=',  $data->sku],['price_list_no', '=',  $userPriceNoData->price_list_no]]) 
               ->first(); 
               if(!empty($upriceData)){
                   $uprice = $upriceData->list_price;
               }
            }
            $data->uprice = $uprice;
            $data->wishlist = $wishlist;
            //Get Product_feature_videos 
            $fcount = 0;
            $featurevideos = Product_feature_videos::select('id', 'name', 'video')->where('proid', $data->id)->get();
            // $data->featurevideos = $featurevideos;
            foreach ($featurevideos as $featurevideo) {
                $featurevideos[$fcount]['video'] = asset($featurevideo->video);
                $fcount++;
            }
            $data->featurevideos = $featurevideos;
            //Get Product_feature_videos 
            $tcount = 0;
            $tranningVideos = Product_training_videos::select('id', 'name', 'video')->where('proid', $data->id)->get();

            foreach ($tranningVideos as $tranningVideo) {
                $tranningVideos[$tcount]['video'] = asset($tranningVideo->video);
                $tcount++;
            }
            $data->tranningVideos = $tranningVideos;

            $faqDetails = new Faqs;
            if (!empty($faqDetails->faqDetails($data->id))) {
                $data->faqDetails = $faqDetails->faqDetails($data->id);
            }
          $data->products_attributes = DB::table('product_variant_combinations')
            ->select('id','item_name','item_code','OnHand as stock', 'IsCommited')
            ->where([['product_id', '=', $data->id]])
            ->whereNull('deleted_at') 
            ->orderby('product_variant_combinations.id','DESC')->get();   
             /*    if(!empty($products_variants) ){
                    foreach($products_variants as  $products_variant){
                        if(!empty($products_variant->product_variant_data)){
                            $pv_options = json_decode($products_variant->product_variant_data);
                   
                            foreach($pv_options as $option){
                                echo $option;
                            }
                        }
                    }
                }
                dd($products_variants); */
            return $this->sendResponse($data, 'Product Details');
        } else {
            return $this->sendError('404 page not found.', $data);
        }
    }


    public function ProductInfo($id, Request $request)
    {
        
        $data = Products::where('status', 1)
            ->where('id', $id)
            ->first();

        if ($data) {
            $documents  =  explode(',', $data->tech_documents);
            $icount = 0;
            foreach ($documents as $document) {
                $documents[$icount] = asset($document);
                $icount++;
            }

            $data->tech_documents =  $documents;
            if (!empty($data->main_image) && file_exists($data->main_image)) {
                $data->main_image = asset($data->main_image);
            } else {
                $data->main_image = asset('uploads/placeholder-large.jpg');
            }
            if (!empty($data->medium_image) && file_exists($data->medium_image)) {
                $data->medium_image = asset($data->medium_image);
            } else {
                $data->medium_image = asset('uploads/placeholder-large.jpg');
            }
            if (!empty($data->thumbnail_image) && file_exists($data->thumbnail_image)) {
                $data->thumbnail_image = asset($data->thumbnail_image);
            } else {
                $data->thumbnail_image = asset('uploads/placeholder-large.jpg');
            }
            if (!empty($data->large_image) && file_exists($data->large_image)) {
                $data->large_image = asset($data->large_image);
            } else {
                $data->large_image = asset('uploads/placeholder-large.jpg');
            }

            if (!empty($data->video) && file_exists($data->video)) {
                $data->video = asset($data->video);;
            }
            if (!empty($data->download_datasheet) && file_exists($data->download_datasheet)) {
                $data->download_datasheet = asset($data->download_datasheet);
            }
           /*  if (empty($data->gallery) && file_exists($data->gallery)) {

                $data->gallery = asset($data->gallery);
            }  */ 
            if (!empty($data->category_id)) {
                $data->productCategory;
            }
            if (!empty($data->brand_id)) {
                $data->productBrand;
            }

            $wishlist = false;
            $uprice = null;
            if (Auth::guard('api')->check()) {
                $user_id = $request->user('api')->id;
                $likedProduct = new LikedProduct;
                $wishlistData =  $likedProduct->checkProductExist($data->id, $user_id);
                if (!empty($wishlistData)) {
                    $wishlist = true;
                }
                $userPriceNoData =  DB::table('users')->select('price_list_no')->where('id','=',$user_id)->first();
                $upriceData = DB::table('price_lists')
                ->select('list_price')
                ->where([['item_no', '=',  $data->sku],['price_list_no', '=',  $userPriceNoData->price_list_no]]) 
               ->first(); 
               if(!empty($upriceData)){
                   $uprice = $upriceData->list_price;
               }
            }
            $data->uprice = $uprice;
            $data->wishlist = $wishlist;

            //Get Product_feature_videos 
            $fcount = 0;
            $featurevideos = Product_feature_videos::select('id', 'name', 'video')->where('proid', $id)->get();
            // $data->featurevideos = $featurevideos;
            foreach ($featurevideos as $featurevideo) {
                $featurevideos[$fcount]['video'] = asset($featurevideo->video);
                $fcount++;
            }
            $data->featurevideos = $featurevideos;


            //gallery array set asset path
            if (!empty($data->gallery) ) {
                $gallery  =  explode(',', $data->gallery);
                $galleryArr = array();
                $icount = 0;
                foreach ($gallery as $image) {
                    if(file_exists($image)){
                        $galleryArr[] = asset($image);
                    }    
                    $icount++;
                }
                $data->gallery =  $galleryArr;
             
            } else {
                $data->gallery = null;
            }

          /*   $gcount = 0;
            $gallery_data =   explode(',', $data['gallery']);
            $gallery_data_new = array();  

            foreach ($gallery_data  as $gallery) {
                if (empty($gallery) && file_exists($gallery)) {
                    $gallery_data_new[$gcount] = asset($gallery);
                    $gcount++;
                }
            } 
            $data->gallery =  $gallery_data_new;*/

            $pdicount = 0;
            $pdi_data =   explode(',', $data['packaging_delivery_images']);
            $pdi_data_new = array();  

            foreach ($pdi_data  as $pdimage) {
                if (!empty($pdimage) && file_exists($pdimage)) {
                    $pdi_data_new[$pdicount] = asset($pdimage);
                    $pdicount++;
                }
            } 
            if(!empty($pdi_data_new)){
                $data->packaging_delivery_images =  $pdi_data_new;
            }else{
                $data->packaging_delivery_images =  null;
            }


            //Get Product_feature_videos 
            $tcount = 0;
            $tranningVideos = Product_training_videos::select('id', 'name', 'video')->where('proid', $id)->get();

            foreach ($tranningVideos as $tranningVideo) {
                $tranningVideos[$tcount]['video'] = asset($tranningVideo->video);
                $tcount++;
            }
            $data->tranningVideos = $tranningVideos;

            $faqDetails = new Faqs;
            if (!empty($faqDetails->faqDetails($id))) {
                $data->faqDetails = $faqDetails->faqDetails($id);
            }
      

            $data->products_attributes = DB::table('product_variant_combinations')
            ->select('id','item_name','item_code','OnHand as stock', 'IsCommited')
            ->where([['product_id', '=', $id]])
            ->whereNull('deleted_at') 
            ->orderby('product_variant_combinations.id','DESC')->get(); 

            $products_variants = DB::table('product_variant_combinations')
                ->where([['product_id', '=', $id]])
                ->whereNull('deleted_at') 
                ->orderby('product_variant_combinations.id','DESC')->get();   

                $fronturl = Settings::where('id',21)->select('value')->get();
                    
                $data->fronturl = $fronturl[0]['value'].'/product/'.$data['slug'];
          
                return $this->sendResponse($data, 'Product Details');
        } else {
            return $this->sendError('404 page not found.', $data);
        }
    }

    /**
     * View getPorductVariant
     *
     * @return \Illuminate\Http\Response
     */
    public function getPorductDetailsBySKU(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sku' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {
           $product_data =   DB::table('products')->select('id','product_name','sku','product_type','regular_price','main_image','medium_image')->where('sku','=',$request->sku)->first(); 
           if(!empty($product_data)){

                if(file_exists($product_data->main_image) || file_exists($product_data->medium_image)){
                    $product_data->main_image = asset($product_data->main_image);
                    $product_data->medium_image = asset($product_data->medium_image);
                }else{
                    $product_data->medium_image = asset('uploads/placeholder-medium.jpg');
                }
                
            // if (!empty($product_data->medium_image) && file_exists($product_data->medium_image)) {
            //     $product_data->medium_image = asset($product_data->medium_image);
            // }elseif (!empty($product_data->main_image) && file_exists($product_data->main_image) && empty($product_data->medium_image)) {
            //     $product_data->main_image = asset($product_data->main_image);
            // } else {   
            //     $product_data->main_image = asset('uploads/placeholder-medium.jpg');
            // }
            
           $uprice = null;
           if (Auth::guard('api')->check()) {
                 $user_id = $request->user('api')->id;
                $userPriceNoData =  DB::table('users')->select('price_list_no')->where('id','=',$user_id)->first();
                $upriceData = DB::table('price_lists')
                ->select('list_price')
                ->where([['item_no', '=',  $request->sku],['price_list_no', '=',  $userPriceNoData->price_list_no]]) 
                ->first(); 
                if(!empty($upriceData)){
                    $uprice = $upriceData->list_price;
                }
            }
            $product_data->uprice = $uprice;
            $result = DB::table('product_variant_combinations')
            ->select('id','item_name','item_code','OnHand as stock', 'IsCommited')
            ->where([['product_id', '=', $product_data->id]])
            ->whereNull('deleted_at') 
            ->orderby('product_variant_combinations.id','DESC')->get();
            
            if ($result) {
                $product_data->variant =  $result;
                return $this->sendResponse($product_data, 'Product and variations has been found.');
            } else {
                return $this->sendError('Product not found, please try again.', $result);
            }
            }else{
                return $this->sendError('Product not found, please try again.', false);
            }
        }
    }
    /**
     * View getPorductVariant
     *
     * @return \Illuminate\Http\Response
     */
    public function getPorductVariant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);

        if (Auth::guard('api')->check()) {
            $user_id = $request->user('api')->id;
            $product_id = $request->product_id;
            $result = DB::table('product_variant_combinations AS pvc')
                ->leftJoin('customers_basket_attributes AS cba', function($join) use ($user_id,$product_id){
                    $join->on('pvc.id', '=', 'cba.variant_id')
                          ->where('cba.product_id', '=', $product_id)
                          ->where('cba.customer_id', '=',  $user_id);
                }) 
             ->select('pvc.id','pvc.item_name','pvc.item_code','pvc.OnHand as stock', 'pvc.IsCommited', 'cba.quantity AS ccart')
             ->where([['pvc.product_id', '=', $request->product_id]])
             ->whereNull('pvc.deleted_at') 
             ->orderby('pvc.id','DESC')->get();
        }else{
            $result = DB::table('product_variant_combinations AS pvc')
            ->select('pvc.id','pvc.item_name','pvc.item_code','pvc.OnHand as stock', 'pvc.IsCommited', 'cba.quantity AS ccart')
             ->where([['pvc.product_id', '=', $request->product_id]])
             ->whereNull('pvc.deleted_at') 
             ->orderby('pvc.id','DESC')->get();
        }
     
  
         


        if ($result) {
            return $this->sendResponse($result, 'Product variations');
        } else {
            return $this->sendError('404 page not found.', $result);
        }
    }

    public function filterList()
    {
        # code...
        $filterdata = $this->product->filterProducts();
        return $this->sendResponse($filterdata, 'Filter List');
    }

    public function filterProducts(Request $request)
    {   
        $orderby = (isset($request->orderby)) ?   $request->orderby : "id";
        $order =  (isset($request->order)) ?   $request->order : "ASC";
        $perPage = (isset($request->per_page)) ?  intval($request->per_page) : 10;
        $limit = (isset($request->limit)) ?  intval($request->limit) : 0;

        $query =  DB::table('products') 
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id') 
          //  ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
          //  ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
           ->where('products.status', 1);

            if(!empty($request->cat_ids)){
                $query->whereIn('category_id', $request->cat_ids);
            }
            if(!empty($request->brand_ids)){
                $brand_ids = collect(explode(',', $request->brand_ids))
                ->map(fn($i) => trim($i))
                ->all();
                $query->whereIn('brand_id', $brand_ids);
            }  
            if(!empty($request->in_stock)){
                $query->where('products.inventory', '>', 0);
            } 

            if (isset($orderby) && ($orderby == "best-selling") )
            { 
                $orderby =  'OnOrder';
                $order = 'DESC';
                  
            } 
               /*  $query->select('products.id', 'products.product_name', 'products.regular_price', 'products.sale_price', 'products.category_id','categories.category_name', 'products.main_image', 'products.sku', 'products.slug', 'products.short_description', 'products.specification', DB::raw('COALESCE(sum(order_products.product_quantity),0) total_sales') );
                $query->groupBy('products.id', 'products.product_name', 'products.regular_price', 'products.sale_price', 'products.category_id','categories.category_name', 'products.main_image', 'products.sku', 'products.slug', 'products.short_description', 'products.specification' );
                $query->orderBy('total_sales', 'DESC'); */
           if (isset($orderby) && ($orderby == "best-deal") )
            {    
                $query->select('products.id', 'products.product_name', 'products.regular_price', 'products.sale_price', 'products.category_id','categories.category_name', 'products.main_image', 'products.sku', 'products.slug', 'products.short_description', 'products.specification', DB::raw('COALESCE(CAST(((products.regular_price - products.sale_price) * 100 / products.regular_price) as decimal(5,2)),0) discount') );
                $query->groupBy('products.id', 'products.product_name', 'products.regular_price', 'products.sale_price', 'products.category_id','categories.category_name', 'products.main_image', 'products.sku', 'products.slug', 'products.short_description', 'products.specification' );
                $query->orderBy('discount', 'DESC');
            }else{
                $query->select('products.id', 'products.product_name', 'products.regular_price', 'products.sale_price', 'products.category_id','categories.category_name', 'products.main_image', 'products.sku', 'products.slug', 'products.short_description', 'products.specification', DB::raw('COALESCE(CAST(((products.regular_price - products.sale_price) * 100 / products.regular_price) as decimal(5,2)),0) discount'), DB::raw('COALESCE(sale_price, regular_price) as price'));
                $query->orderby($orderby, $order);
            } 
           if (isset($request->prices)){
                $data_prices = array();
                $data_prices =   $request->prices;
                if(is_array($data_prices)){

                    $query->where(function ($qp) use($data_prices) {
                    for($i=0; $i < count($data_prices); $i++){
                        $values = collect(explode(',', $data_prices[$i]))
                        ->map(fn($i) => trim($i))
                        ->all();
                    
                        if(($i == 0) && ($values[1]  > 0)){
                          $qp->whereBetween(DB::raw('COALESCE(sale_price, regular_price)'), [$values[0], $values[1]]);  
                        
                        }else if(($i != 0) && ($values[1] > 0)){
                            $qp->orwhereBetween(DB::raw('COALESCE(sale_price, regular_price) '),[$values[0], $values[1]]);
                        }else if(($values[1] < 0)){
                            $qp->where(DB::raw('COALESCE(sale_price, regular_price) '), '>=', $values[0]);
                        }
                    }
                  });
               }
            }
            if (isset($request->discounts)){
                $data_discounts = array();
                $data_discounts =   $request->discounts; 
                if(is_array($data_discounts)){

                   $query->where(function ($q) use($data_discounts) {
                        for($i=0; $i < count($data_discounts); $i++){
                            $values = collect(explode(',', $data_discounts[$i]))
                            ->map(fn($i) => trim($i))
                            ->all();
                         if($i == 0){
                                $q->whereBetween(DB::raw('COALESCE(CAST(((regular_price - sale_price) * 100 / regular_price) as decimal(5,2)),0) '), [$values[0], $values[1]]);   
                            
                            }else{
                                $q->orwhereBetween(DB::raw('COALESCE(CAST(((regular_price - sale_price) * 100 / regular_price) as decimal(5,2)),0) '),[$values[0], $values[1]]); 
                            }
                        }
                    });
               }
            } 
            $result = $query->get();
            return $this->sendResponse($result,'filter Product list.');
           



        # code...
       /*  $query = Products::join('order_products','order_products.product_id','=','products.id')
        ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->select('products.id', 'products.product_name', 'products.regular_price', 'products.brand_id', 'products.sale_price', 'products.category_id', 'products.main_image', 'products.sku', 'products.slug', 'products.short_description', 'products.specification')
            ->where('products.status', 1);

        // return $final_price;
        if (isset($request->data) || isset($orderby) == "topsales" )
        {
            $result =  $query->where('products.product_name', 'LIKE', "%{$request->data}%")
                ->orwhere('categories.category_name', 'LIKE', '%' . $request->data . '%')
                ->orwhere('brands.brand_name', 'LIKE', '%' . $request->data . '%')
               ->selectRaw('sum(order_products.product_quantity) as topsale')
               ->groupBy('products.id','products.product_name','products.regular_price','products.brand_id','products.sale_price','products.category_id','products.main_image','products.sku','products.slug','products.short_description','products.specification')
                // ->whereBetween('sale_price',$final_price)
                ->where('products.in_stock', '!=', 0)
                ->WhereNotNull('products.in_stock')
                ->get();
                return $this->sendResponse($result,'Filter Product List');
        } 
        */

        


        // if (is_array($request->data)) {
        //     $data =   implode('-', $request->data);
        //     $final_price = explode('-', $data);
        //     $query->whereBetween('sale_price', $final_price)
        //         ->get();
        // }
        // return $this->sendResponse($result,'filter Product list.');
    }

    //     #price
    //     if (isset($request->min_price) && isset($request->max_price)) {
    //         $result = $query->whereBetween('sale_price', [$request->min_price, $request->max_price])
    //         ->where('products.in_stock', '!=', 0)
    //         ->WhereNotNull('products.in_stock') 
    //         ->get();
    //     }
    //     foreach ($result as $item) {
    //         if (!empty($item->main_image) && file_exists($item->main_image)) {
    //             $item->main_image = asset($item->main_image);
    //         } else {
    //             $item->main_image = asset('uploads/product-placeholder.png');
    //         }
    //     }
    //     if (!$result->isEmpty()) {
    //         return $this->sendResponse($result, 'Seach Products List');
    //     } else {
    //         return $this->sendError('Product Not Found');
    //     }
    //     #discount
    //     // if ($request->discount) {
    //     //     $result = $query->get();
    //     //     $i = 0;
    //     //     foreach ($result as $result) {

    //     //         if ($result->regular_price != 0 && $result->sale_price != 0) {
    //     //             $dis[$i] = (($result->regular_price - $result->sale_price) * 100)
    //     //                 / $result->regular_price . '%';
    //     //         } else {
    //     //             $dis[$i] = 0 . ' ' . '%';
    //     //         }
    //     //         $i++;
    //     //     }

    //     //     $discount =  explode('-', $request->discount);
    //     //     return $discount;
    //     //     $data =  $query->whereBetween($dis, $discount)
    //     //         ->get();
    //     // }
    //     return $this->sendResponse($result,"Filter Products List");
    // }

    public function getAllProductsSKU(Request $request)
    {
        $data = Products::select('sku')
            ->where('status', 1)
            ->orderby('sku','ASC')
            ->get();
        if (!$data->isEmpty()) {
            return $this->sendResponse($data, 'All List of products skus.');
        }else {
            return $this->sendError('Product Not Found');
        }

    }
    public function searchProducts(Request $request)
    {
        # code...
        $serch_data =  $request->search_data;
        $validator = Validator::make($request->all(), [
            'search_data' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {
            $orderby = (isset($request->orderby)) ?   $request->orderby : "id";
            $order =  (isset($request->order)) ?   $request->order : "ASC";
            $data = Products::select('id', 'product_name', 'regular_price', 'sale_price', 'category_id', 'main_image', 'sku', 'slug', 'short_description', 'specification')
                ->Where('product_name', 'like', DB::raw("'%$serch_data%'"))
                ->where('status', 1)
                ->orderby($orderby, $order)
                ->get();
            foreach ($data as $item) {
                if (!empty($item->main_image) && file_exists($item->main_image)) {
                    $item->main_image = asset($item->main_image);
                } else {
                    $item->main_image = asset('uploads/product-placeholder.png');
                }
            }
            if (!$data->isEmpty()) {
                return $this->sendResponse($data, 'Seach Products List');
            }else {
                return $this->sendError('Product Not Found');
            }
        }
    }
    /* get products by products ids */
    function getProductsByIds(Request $request)
    {
        if (isset($request->include)){

            $ids_ordered = implode(',', array_reverse($request->include));
           
            $query =  Products::select(
                'id',
                'product_name',
                'product_type',
                'regular_price',
                'sale_price',
                // 'category_id',                
                'main_image',
                'medium_image',
                'thumbnail_image',
                'sku',
                'slug',
                'short_description',
                'specification',
                DB::raw('COALESCE(sale_price, regular_price) as price'),
                DB::raw('COALESCE(CAST(((products.regular_price - products.sale_price) * 100 / products.regular_price) as decimal(5,2)),0) discount'))
                ->selectRaw('LEAST(regular_price,sale_price) AS price')
                ->where('status', 1);
             $query->whereIn('id', $request->include);
             $query->orderByRaw("FIELD(id, $ids_ordered)");
             $result = $query->take(10)->get();

            if (!$result->isEmpty()) {
                foreach ($result as $item) {
                    if (!empty($item->main_image) && file_exists($item->main_image)) {
                        $item->main_image = asset($item->main_image);
                    } else {
                        $item->main_image = asset('uploads/product-placeholder.png');
                    }
                    if (!empty($item->medium_image) && file_exists($item->medium_image)) {
                        $item->medium_image = asset($item->medium_image);
                    } else {
                        $item->medium_image = asset('uploads/placeholder-medium.jpg');
                    }
                    if (!empty($item->thumbnail_image) && file_exists($item->thumbnail_image)) {
                        $item->thumbnail_image = asset($item->thumbnail_image);
                    } else {
                        $item->thumbnail_image = asset('uploads/placeholder-medium.jpg');
                    }
                    $wishlist = false;
                    $uprice = null;
                    if (Auth::guard('api')->check()) {
                        $user_id = $request->user('api')->id;
                        $likedProduct = new LikedProduct;
                        $wishlistData =  $likedProduct->checkProductExist($item->id, $user_id);
                        if (!empty($wishlistData)) {
                            $wishlist = true;
                        }

                        $userPriceNoData =  DB::table('users')->select('price_list_no')->where('id','=',$user_id)->first();
                        $upriceData = DB::table('price_lists')
                        ->select('list_price')
                        ->where([['item_no', '=',  $item->sku],['price_list_no', '=',  $userPriceNoData->price_list_no]]) 
                       ->first(); 
                       if(!empty($upriceData)){
                           $uprice = $upriceData->list_price;
                       }
                    }
                    $item->wishlist = $wishlist;
                    $item->uprice = $uprice;
                 }
              
                return $this->sendResponse($result, 'Product List');
            }else {
                return $this->sendError('Product Not Found');
            }
        }
    }


    public function truncateProductTables()
    {
        // $tables = [
        //     // Product Imported Tables
        //     'attributes_variations',
        //     'product_details',
        //     'product_attributes',
        //     'product_variant_combinations',
        //     'products',
        //     // Product Related Tables
        //     'product_feature_videos',
        //     'product_training_videos',
        //     'user_product_videos',
        //     'liked_products',
        //     'orders',
        //     'order_products',
        //     'order_product_attributes',
        //     'manage_order_status',
        //     'customers_basket',
        //     'customers_basket_attributes',
        //     'price_lists'
        // ];

        // // It do truncate given array tables in database
        // $arr = [];
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // foreach($tables as $table){
        //     $arr[$table] = DB::table($table)->truncate();            
        // }
        // $arr['vendor_deleted'] =  DB::table('users')->where('user_role', 3)->DELETE();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // return $this->sendResponse($arr, 'Data Truncated.');
    }

    public function exportProductData()
    {

        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=exported-products.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];        

        $list = Products::SELECT(
            'products.sku', 
            'products.product_name',
            'products.description',
            'products.short_description',
            'categories.category_name as category_id',
            'brands.brand_name as brand_id',
            DB::raw("'' as main_image"),
            'products.regular_price',
            'products.sale_price',
            'products.Inventory',
            'products.IsCommited',
            'products.OnOrder',
            'products.specification',
            'products.tech_documents',
            'products.video',
            DB::raw("'' as gallery"),
            DB::raw("'' as download_datasheet"),
            // DB::raw("(GROUP_CONCAT(product_attributes.attribute_variation_id SEPARATOR '|')) as Attributes"),
            // DB::raw("(GROUP_CONCAT(attributes.attribute_name SEPARATOR '|')) as attributeName"),
            // DB::raw("(GROUP_CONCAT(product_attributes.attribute_variation_id SEPARATOR '|')) as attributesVariantName"),
            DB::raw('CONCAT(GROUP_CONCAT(attributes.attribute_name), \'|\', GROUP_CONCAT(product_attributes.attribute_variation_id)) as Attributes'),
            // DB::raw('CONCAT("attribute_name", variation_name) AS Attributes'),
            // DB::raw("'' as Attributes"),
            // DB::raw("(GROUP_CONCAT(attributes_variations.variation_name SEPARATOR '|')) as Attributes"),
            'products.packaging_delivery_descr',
            'products.packaging_delivery_images',
            'products.trending_product',
            'products.best_selling',
            'products.bid_quote',
            'products.seo_title',
            'products.seo_description',
            'products.seo_keyword',
            'products.status',
            'product_details.VatGourpSa',
            'product_details.VatGroupPu',
            'product_details.U_Size',
            'product_details.SizeName',
            'product_details.U_SCartQty',
            'product_details.U_CBM',
            'product_details.OnHand',
            'product_details.U_Itemgrp',
            'product_details.U_Itemgrpname',
            'product_details.U_OrgCountCod',
            'product_details.U_OrgCountNam',
            'product_details.U_CartQty',
            'product_details.SuppCatNum',
            'product_details.BuyUnitMsr',
            'product_details.SalUnitMsr',
            'product_details.FirmCode',
            'product_details.FirmName',
            'product_details.U_HsCode',
            'product_details.U_HsName',
            'product_details.QryGroup1',
            'product_details.QryGroup2',
            'product_details.QryGroup3',
            'product_details.QryGroup4',
            'product_details.QryGroup5',
            'product_details.QryGroup6',
            'product_details.QryGroup7',
            'product_details.QryGroup8',
            'product_details.QryGroup9',
            'product_details.QryGroup10',
            'product_details.QryGroup11',
            'product_details.QryGroup12',
            'product_details.QryGroup13',
            'product_details.QryGroup14',
            'product_details.QryGroup15',
            'product_details.QryGroup16',
            'product_details.QryGroup17',
            'product_details.QryGroup18',
            'product_details.QryGroup19',
            'product_details.QryGroup20',
            'product_details.QryGroup21',
            'product_details.QryGroup22',
            'product_details.QryGroup23',
            'product_details.QryGroup24',
            'product_details.QryGroup25',
            'product_details.QryGroup26',
            'product_details.QryGroup27',
            'product_details.QryGroup28',
            'product_details.QryGroup29',
            'product_details.QryGroup30',
            'product_details.QryGroup31',
            'product_details.QryGroup32',
            'product_details.QryGroup33',
            'product_details.QryGroup34',
            'product_details.QryGroup35',
            'product_details.QryGroup36',
            'product_details.QryGroup37',
            'product_details.QryGroup38',
            'product_details.QryGroup39',
            'product_details.QryGroup40',
            'product_details.QryGroup41',
            'product_details.QryGroup42',
            'product_details.QryGroup43',
            'product_details.QryGroup44',
            'product_details.QryGroup45',
            'product_details.QryGroup46',
            'product_details.QryGroup47',
            'product_details.QryGroup48',
            'product_details.QryGroup49',
            'product_details.QryGroup50',
            'product_details.QryGroup51',
            'product_details.QryGroup52',
            'product_details.QryGroup53',
            'product_details.QryGroup54',
            'product_details.QryGroup55',
            'product_details.QryGroup56',
            'product_details.QryGroup57',
            'product_details.QryGroup58',
            'product_details.QryGroup59',
            'product_details.QryGroup60',
            'product_details.QryGroup61',
            'product_details.QryGroup62',
            'product_details.QryGroup63',
            'product_details.QryGroup64'
            )
            ->leftJoin('product_details', 'product_details.product_id', '=', 'products.id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'products.id')
            ->join('attributes', 'attributes.id', '=', 'product_attributes.attribute_id')
            // ->leftJoin('attributes_variations', 'attributes_variations.attribute_id', '=', 'attributes.id')
            ->groupBy('products.id')
            ->GET()
            ->toArray();


        if(count($list) > 0){

            // For display Attributes with CSV formatted
            foreach ($list as $listkey => $updatedData) {                   
                $attributes = explode('|', $updatedData['Attributes']);
                $attributesNameArr = explode(',', $attributes[0]);
                $attributesVariantArr = explode(',', $attributes[1]);
                $allAttributesArr = [];
                foreach ($attributesNameArr as $key => $attrName) {
                    $allAttributesArr[] = $attrName.'>'.$attributesVariantArr[$key];
                }
                $list[$listkey]['Attributes'] = (implode('|', $allAttributesArr));
            }

            # add headers for each column in the CSV download
            array_unshift($list, array_keys($list[0]));
      
            $callback = function() use ($list) 
            {
                $FH = fopen('php://output', 'w');
                foreach ($list as $row) {                   
                    fputcsv($FH, $row);
                }
                fclose($FH);
            };

            return Response::stream($callback, 200, $headers);
        } else {
            return $this->sendResponse([], 'No Data Available.');
        }
    }

        
}