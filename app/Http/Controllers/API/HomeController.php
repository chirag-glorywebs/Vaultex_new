<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Products;
use Illuminate\Support\Facades\Auth;
use App\Models\LikedProduct;
use Illuminate\Support\Facades\DB;

class HomeController extends BaseController
{
    /**
     * Home Page
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {

        $cats =  Categories::select('id', 'category_name', 'logo', 'category_description')->where('status', 1)->take(10)->get();

        $brands = Brand::select('id', 'brand_name', 'brand_logo')->where('status', 1)->take(10)->get();

        $products = Products::select('id', 'product_name', 'sku', 'regular_price', 'sale_price', 'main_image','medium_image', 'specification')->where('status', 1)->take(10)->get();

        $trending_cat_title = "Trending Categories";
        $prodyct_new_arrivals = "New Arrivals";
        $new_products_title = "New Products";
        $trending_product_title = "Trending Product";
        $brand_title =  "Our Top Brands";
        $selling_product_title = "Best Selling Products";

        $result = array();
        /* Category  */
        if (!empty($cats)) {
            foreach ($cats as $item) {
                $item->logo = asset('uploads/cat_image.png');
            }
            $catTitle[] = array("title" => $trending_cat_title);
            array_push($result, array("type" => 1, "data" => $catTitle));
            $catsList = array("type" => 4, "data" => $cats);
            array_push($result, $catsList);
        }
        /* New arrivals */
        $new_arrival_Product[0] = $products[0];
        $new_arrival_Product[0]['title'] = $prodyct_new_arrivals;
        $arrival_Product = array("type" => 5, "data" => $new_arrival_Product);
        array_push($result, $arrival_Product);

        /* New Product */
        if (!empty($products)) {
            foreach ($products as $item) {
                $item->main_image = asset('uploads/product_image.png');
            }
            $newProductsTitle[] = array("title" => $new_products_title);
            array_push($result, array("type" => 1, "data" => $newProductsTitle));
            $newproducts = array("type" => 2, "data" => $products);
            array_push($result, $newproducts);
        }
        /* tranding Product */
        if (!empty($products)) {
            // foreach ($products as $item) {
            //     $item->main_image = asset('uploads/product_image.png');
            // }
            foreach($products as $item){
            if (!empty($item->main_image) && file_exists($item->main_image)) {
                $item->main_image = asset($item->main_image);
            } else {
                $item->main_image = asset('uploads/product-placeholder.png');
            }
            if (!empty($item->medium_image) && file_exists($item->medium_image)) {
                $item->medium_image = asset($item->medium_image);
            } else {
                $item->medium_image = asset('uploads/product-placeholder.png');
            }
            }
            $trandingProductsTitle[] = array("title" => $trending_product_title);
            array_push($result, array("type" => 1, "data" => $trandingProductsTitle));
            $trandingProducts = array("type" => 2, "data" => $products);
            array_push($result, $trandingProducts);
        }
        /* brands */
        if (!empty($brands)) {
            foreach ($brands as $item) {
                $item->brand_logo = asset('uploads/brand_image.png');
            }
            $brandTitle[] = array("title" => $brand_title);
            array_push($result, array("type" => 1, "data" => $brandTitle));
            $brands = array("type" => 3, "data" => $brands);
            array_push($result, $brands);
        }
        /* sale Products */
        if (!empty($products)) {
            foreach ($products as $item) {
                $item->main_image = asset('uploads/product_image.png');
            }
            $sellingProductTitle[] = array("title" => $selling_product_title);
            array_push($result, array("type" => 1, "data" => $sellingProductTitle));
            $sellingProducts = array("type" => 2, "data" => $products);
            array_push($result, $sellingProducts);
        }

        return $this->sendResponse($result, 'Home Page Details');
    }

    public function homePage( Request $request)
    {
        # code...
        $result = array();
        $slider_data = DB::table('sliders')->select('image', 'contents')->where('page_id', 1)->where('status', 1)->get();
        foreach ($slider_data as $item) {
            if (!empty($item->image) && file_exists($item->image)) {
                $item->image = asset($item->image);
            } else {
                $item->image = asset('uploads/product-placeholder.png');
               
            }
        }
 
        $Categories =  Categories::whereNull('parent_category')->with('childCategoires')->select('id', 'category_name', 'parent_category', 'logo', 'category_description', 'slug')
            ->where('status', 1)->take(10)->get();
        
        foreach ($Categories as $items) {
            if (!empty($items->logo) && file_exists($items->logo)) {
                $items->logo = asset($items->logo);
            } else {
                $items->logo = asset('uploads/cat_image.png');
            }
        }

        $homepage_image = DB::table('home_pages')->select('offer_top_1', 'offer_top_1_url', 'offer_top_2', 'offer_top_2_url', 'offer_top_3', 'offer_top_3_url')->first();
        $topOffer['offer_top_1'] =  asset($homepage_image->offer_top_1);
        $topOffer['offer_top_1_url'] = $homepage_image->offer_top_1_url;
        $topOffer['offer_top_2'] =  asset($homepage_image->offer_top_2);
        $topOffer['offer_top_2_url'] = $homepage_image->offer_top_2_url;
        $topOffer['offer_top_3'] =  asset($homepage_image->offer_top_3);
        $topOffer['offer_top_3_url'] = $homepage_image->offer_top_3_url;

        /* main categories product */
        $cat_ids = array(9,10,11,12,13,14);
        $cat_datas =  DB::table('categories')->select('id','category_name','slug')->where('status','=','1')->whereIn('id',$cat_ids)->get();
        $cat_products = array();
        foreach($cat_datas as $data){
            $cat_arr = array('id'=>$data->id,'category_name'=>$data->category_name,'slug'=>$data->slug,'products'=>[]);
            array_push($cat_products,$cat_arr); 
        }  
         /* $cat_products = array( 
                array('id'=> 9,'name'=>'Industrial Tools & Equipment','products'=>[]),
                array('id'=> 10,'name'=>'Office Stationery & Supplies','products'=>[]),
                array('id'=> 11,'name'=>'Electrical Tools & Equipment','products'=>[]),
                array('id'=> 12,'name'=>'Safety & PPE Supplies','products'=>[])
                );  */  
        if (Auth::guard('api')->check()) {
            $user_id = $request->user('api')->id;
            
            foreach($cat_products as  $key=>$cat_data ){
                $cc_products = null;
                $cat_id = $cat_data['id'];
                $cat_products_query = DB::table('products')
                ->join('price_lists', 'products.sku', '=', 'price_lists.item_no') 
                ->join('users', 'price_lists.price_list_no', '=', 'users.price_list_no')
                ->leftJoin('liked_products', function($join){
                    $join->on('products.id', '=', 'liked_products.liked_products_id')
                         ->on('users.id', '=', 'liked_products.liked_customers_id');
                }) 
                ->select('products.id','products.category_id','products.product_name','products.product_type','products.medium_image','products.main_image','products.sku','products.slug','price_lists.list_price AS uprice', DB::raw('COALESCE(price_lists.list_price, products.regular_price) as price'),'liked_products.id as wishlist')
                ->where('users.id', '=', $user_id) 
                ->where('products.status', 1)
                ->orderBy('id', 'ASC');

                $categoryIds = Categories::where('parent_category', $cat_id)->where('status', 1)->pluck('id')->all(); 
                $childCatData = Categories::whereIn('parent_category',$categoryIds)->where('status',1)->pluck('id')->all();
                $cat_products_query->where(function ($q) use($cat_id, $categoryIds, $childCatData ) { 
                    $q->where('products.category_id',$cat_id)
                    ->orWhereIn('products.category_id',$categoryIds)
                    ->orWhereIn('products.category_id',$childCatData); 
                });
                $cc_products = $cat_products_query->get();
                foreach ($cc_products as $items) {
                    if (!empty($items->medium_image) && file_exists($items->medium_image)) {
                       $items->main_image = asset($items->medium_image);
                    }elseif (!empty($items->main_image) && file_exists($items->main_image)&& empty($items->medium_image) ) {
                        $items->main_image = asset($items->main_image);
                    } else {
                        $items->main_image = asset('uploads/placeholder-medium.jpg');
                    }
                    if(isset( $items->wishlist) &&  ($items->wishlist > 0)){
                        $items->wishlist = true;
                    }else{
                        $items->wishlist = false;
                    }

                }
                if(!empty($cc_products)){  
                    $cat_products[$key]['products'] =  $cc_products;
                   /*  array_merge($cat_products[$key]['products'],$cat_products);  */
                 }
            }    
        }else{
            foreach($cat_products as  $key=>$cat_data ){
                $cc_products = null;
                $cat_id = $cat_data['id'];
                $cat_products_query = DB::table('products')->select('products.id', 'products.product_name', 'products.sku', 'products.regular_price', 'products.sale_price', 'products.medium_image','products.main_image', 'products.slug')->where('products.status', 1)->take(10)->orderBy('id', 'DESC');
                $categoryIds = Categories::where('parent_category', $cat_id)->where('status', 1)->pluck('id')->all(); 
                $childCatData = Categories::whereIn('parent_category',$categoryIds)->where('status', 1)->pluck('id')->all();
                $cat_products_query->where(function ($q) use($cat_id, $categoryIds, $childCatData ) { 
                    $q->where('products.category_id',$cat_id)
                    ->orWhereIn('products.category_id',$categoryIds)
                    ->orWhereIn('products.category_id',$childCatData); 
                });
                $cc_products = $cat_products_query->get();
                foreach ($cc_products as $items) {
                    if (!empty($items->medium_image) && file_exists($items->medium_image)) {
                       $items->main_image = asset($items->medium_image);
                    }elseif (!empty($items->main_image) && file_exists($items->main_image)&& empty($items->medium_image) ) {
                        $items->main_image = asset($items->main_image);
                    } else {
                        $items->main_image = asset('uploads/placeholder-medium.jpg');
                    }
                    $items->wishlist = false;
                    $items->uprice = null;
                }
                if(!empty($cc_products)){  
                    $cat_products[$key]['products'] =  $cc_products;
                   /*  array_merge($cat_products[$key]['products'],$cat_products);  */
                 }
            }   
        }
         
        

        
        $bottom_images = DB::table('home_pages')->select('offer_bottom_1', 'offer_bottom_1_contents', 'offer_bottom_2', 'offer_bottom_2_contents')
            ->first();
        $bottom_images->offer_bottom_1 =  asset($bottom_images->offer_bottom_1);
        $bottom_images->offer_bottom_2 =  asset($bottom_images->offer_bottom_2);

        $Brands = Brand::select('id', 'brand_name', 'brand_logo', 'slug')->where('status', 1)->take(10)->get();

        foreach ($Brands as $items) {
            if (!empty($items->brand_logo) && file_exists($items->brand_logo)) {
                $items->brand_logo = asset($items->brand_logo);
            } else {
                $items->brand_logo = asset('uploads/product-placeholder.png');
                
            }
        }
        
        /* Best salling product */
        if (Auth::guard('api')->check()) {
            $user_id = $request->user('api')->id;
            $best_selling = DB::table('products')
            ->join('price_lists', 'products.sku', '=', 'price_lists.item_no') 
            ->join('users', 'price_lists.price_list_no', '=', 'users.price_list_no')
            ->leftJoin('liked_products', function($join){
                $join->on('products.id', '=', 'liked_products.liked_products_id')
                     ->on('users.id', '=', 'liked_products.liked_customers_id');
            }) 
            ->select('products.id','products.category_id','products.product_name','products.product_type','products.medium_image','products.main_image','products.sku','products.slug','price_lists.list_price AS uprice', DB::raw('COALESCE(price_lists.list_price, products.regular_price) as price'),'liked_products.id as wishlist')
            ->where('users.id', '=', $user_id) 
            ->where('products.status', 1)
            ->take(10)->orderBy('products.id', 'ASC')->get();
        }else{  
            $best_selling =  DB::table('products')->select('id', 'product_name', 'sku', 'regular_price', 'sale_price', 'medium_image','main_image', 'slug')->where('status', 1)->take(10)->orderBy('id', 'DESC')->get();
        }   
        foreach ($best_selling as $items) {
            if (!empty($items->medium_image) && file_exists($items->medium_image)) {
               $items->main_image = asset($items->medium_image);
            }elseif (!empty($items->main_image) && file_exists($items->main_image)&& empty($items->medium_image) ) {
                $items->main_image = asset($items->main_image);
            } else {
                $items->main_image = asset('uploads/placeholder-medium.jpg');
            }
            
            if (Auth::guard('api')->check()) {
              if(isset( $items->wishlist) &&  ($items->wishlist > 0)){
                    $items->wishlist = true;
                }
            }else{  
                $items->wishlist = false;
                $items->uprice = null;
            }
        } 
        return $this->sendResponse(['slider' => $slider_data, 'categories' => $Categories, 'topOffer' => $topOffer, 'catProductsList' => $cat_products, 'bottomOffer' => $bottom_images, 'topsaleProducts' => $best_selling, 'brands' => $Brands], 'Home page Details');
    }
}
