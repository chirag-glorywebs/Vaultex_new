<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Product_training_videos;
use App\Models\Product_feature_videos;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Attributes;
use App\Models\Attributes_variations;
use App\Models\Reviews;
use App\Models\Faqs;
use App\Models\User;
use App\Models\ProductAttribute;
use Auth;
use DB;
use Image;
use File;
use Illuminate\Validation\Rule;
use Session;
use Yajra\DataTables\Facades\DataTables; 

class ProductsController extends Controller
{
    /* get the all products*/
    public function get(Request $request)
    {
        $page_title = 'Products';
        $page_description = 'All products list page';
        if ($request->ajax()) {
             $products = Products::leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                ->select('products.*', 'categories.category_name', 'brands.brand_name');
                // ->get();  
            

         /*    $products = Products::join('brands', 'brands.id', '=', 'products.brand_id')
            ->select('products.*', 'brands.brand_name')
            ->get(); */

            return DataTables::of($products)
                ->addIndexColumn()
                ->editColumn('status', function ($productList) {
                    $status = '';
                    if ($productList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($productList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->editColumn('mainImage', function ($image) {
                    $imageUrl = asset($image->main_image);
                    $placeHolderUrl = asset('uploads/product-placeholder.png');
                    if ($image->main_image != null || $image->main_image != '') {
                        $destinationPath = $image->main_image;
                        $fileExists = file_exists($destinationPath);
                        if ($fileExists) {
                            return '<img src="' . $imageUrl . '" width="50" height="50"/>';
                        } else {
                            return '<img src="' . $placeHolderUrl . '" width="50" height="50"/>';
                        }
                    } else {
                        return '<img src="' . $placeHolderUrl . '" width="50" height="50"/>';
                    }
                })
                ->addColumn('action', function ($productList) {
                    return view('admin.products.products_datatable', compact('productList'))->render();
                })
                ->rawColumns(['mainImage', 'status', 'action'])
                ->make(true);
        }
        return view('admin.products.products', compact('page_title', 'page_description'));
    }

    /* add new product page*/
    public function add()
    {
        $page_title = 'Add category';
        $page_description = 'Add category here';
        $category = Categories::with('childCategoires')->whereNull('parent_category')->select('id','category_name','parent_category')->where('status',1)->orderby('category_name','ASC')->get();  
        $brand = Brand::all();
        return view('admin.products.add_product', compact('page_title', 'page_description', 'category', 'brand'));
    }

    /* add new product in db*/
    public function create(Request $req)
    {
        $req->validate([
            'product_name' => 'required',
            'product_type' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'main_image' => 'required',
            'sku' => 'required',
            'regular_price' => 'required|numeric',
            'inventory' => 'required',
            'description' => 'required',
            'short_description' => 'required',
             'specification' => 'required',
            'video' => 'required', 
            'download_datasheet' => 'required', 
        ]);
        $products = new Products;

        $products->product_name = $req->product_name;
        $products->product_type = $req->product_type;
        $products->category_id = $req->category_id;
        $products->brand_id = $req->brand_id;
       
        if ($req->hasFile('main_image')) {
            $mainImage = $req->file('main_image');
            //thumbnail image
            $thumbnail_image = resizeImage($mainImage,100,100,'thumbnail', true);
            //medium image
            $medium_image = resizeImage($mainImage,600,600,'medium', true);
            //large image
            $large_image = resizeImage($mainImage,600,600,'large', true);
            $mainImageSave = uplodImage($mainImage,true);
        }
        if (isset($mainImageSave) && $mainImageSave != '') {
            $products->main_image = $mainImageSave;
        }
        if (isset($large_image) && !empty($large_image)) {
            $products->large_image = $large_image;
        }
        if (isset($medium_image) && !empty($medium_image)) {
            $products->medium_image = $medium_image;
        }
        if (isset($thumbnail_image) && !empty($thumbnail_image)) {
            $products->thumbnail_image = $thumbnail_image;
        }
       
        
        $products->sku = $req->sku;
        $products->regular_price = $req->regular_price;
        $products->sale_price = $req->sale_price; 
        $products->inventory = $req->inventory;
        $products->description = $req->description;
        $products->short_description = $req->short_description;
        $products->specification = $req->specification;
        if ($req->hasFile('video')) {
            $video = $req->file('video');
            $videoSave = uplodImage($video);
        }
        if (isset($videoSave) && $videoSave != '') {
            $products->video = $videoSave;
        }
        if ($req->hasFile('download_datasheet')) {
            $dataSheetDwnd = $req->file('download_datasheet');
            $dataSheetDwndSave = uplodImage($dataSheetDwnd);
        }
        if (isset($dataSheetDwndSave) && $dataSheetDwndSave != '') {
            $products->download_datasheet = $dataSheetDwndSave;
        }
        //For Unique slug Start
        $slug_count = 0;
        $slug = $productName = $req->product_name;
        do {
            if ($slug_count == 0) {
                $currentSlug = slugify($slug);
            } else {
                $currentSlug = slugify($productName . '-' . $slug_count);
            }
            if (Products::where('slug', $currentSlug)->first()) {
                $slug_count++;
            } else {
                $slug = $currentSlug;
                $slug_count = 0;
            }
        } while ($slug_count > 0);
        $products->slug = $slug;
        //For Unique slug End

        $products->seo_name = $req->seo_name;
        $products->seo_description = $req->seo_description;
        $products->seo_title = $req->seo_title;
        $products->seo_keyword = $req->seo_keyword;
        $products->status = $req->status;
        $user = Auth::user();
        $products->userid = $user->id;
        $products->save();
        Session::flash('message', 'Successfully added!');
        return redirect('/admin/products/edit/' . $products->id . '');
    }

    /* delete products*/
    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $productDelete = Products::findOrFail($id);
            if ($productDelete->main_image != null || $productDelete->main_image != '') {
                $destinationPath = $productDelete->main_image;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    File::delete($destinationPath);
                }
            }
            if ($productDelete->video != null || $productDelete->video != '') {
                $destinationPath = $productDelete->video;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    File::delete($destinationPath);
                }
            }
            if ($productDelete->download_datasheet != null || $productDelete->download_datasheet != '') {
                $destinationPath = $productDelete->download_datasheet;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    File::delete($destinationPath);
                }
            }
            $productDelete->delete();
            DB::commit();
            return redirect()->route('products.index')->with('success', 'Products Deleted successfully !!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something Went Wrong.');
        }
    }


    /**
     * Delete Multiple Products
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        dd($ids);
        // Products::whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>"Products Deleted successfully."]);
    }

    /* edit products information*/
    public function edit($id)
    {
        $page_title = 'Edit product';
        $page_description = 'Edit product info';
        $products = Products::select( 'product_details.id as pd_id', 'product_details.*','products.*')->leftJoin('product_details','products.id','=','product_details.product_id')->find($id);
         
        $category = Categories::with('childCategoires')->whereNull('parent_category')->select('id','category_name','parent_category')->where('status',1)->orderby('category_name','ASC')->get(); 
        $brand = Brand::all();
        $attribute = Attributes::where([['status', '=',1]])->get();
        $variation = Attributes_variations::where([['status', '=',1]])->get();
        $review = Reviews::join('users', 'users.id', '=', 'reviews.userid')
            ->select('reviews.*', 'users.first_name', 'users.last_name')
            ->where([['reviews.proid', '=', $id]])
            ->get();
        $faqs = Faqs::where([['proid', '=', $id]])->get();
        $training_videos = product_training_videos::where([['proid', '=', $id]])->get();
        $feature_videos = product_feature_videos::where([['proid', '=', $id]])->get();
        $products_attributes = ProductAttribute::where([['product_id', '=', $id]])->get();
        $products_attributes_ids = array(); 
        $products_attributes_options = array();
        foreach($products_attributes as $row){
            if (!in_array($row->attribute_id, $products_attributes_ids)) {
                $products_attributes_ids[] = $row->attribute_id; 
            }
            if ( !isset($products_attributes_options[$row->attribute_id]) || 
            ( isset($products_attributes_options[$row->attribute_id]) && 
                !in_array($row->attribute_variation_id, $products_attributes_options[$row->attribute_id])
            ) 
            ) {
                $products_attributes_options[$row->attribute_id][] = $row->attribute_variation_id;
            }
        }  
        $products_attr_var = DB::table('product_attributes')->join('attributes', 'product_attributes.attribute_id', '=', 'attributes.id')
        ->join('attributes_variations', 'product_attributes.attribute_variation_id', '=', 'attributes_variations.id')
        ->select('product_attributes.id','product_attributes.attribute_id', 'product_attributes.attribute_variation_id', 'attributes.attribute_name', 'attributes_variations.variation_name')
        ->where([['product_id', '=', $id]])
        ->orderby('product_attributes.attribute_id','ASC')->get();

        $products_variants = DB::table('product_variant_combinations')
         ->where([['product_id', '=', $id]])
         ->whereNull('deleted_at') 
         ->orderby('product_variant_combinations.id','DESC')->get();
        

        return view('admin.products.add_product', compact('page_title', 'page_description', 'products', 'category', 'brand', 'attribute', 'variation', 'review', 'faqs', 'training_videos', 'feature_videos','products_attributes','products_attributes_ids','products_attributes_options','products_attr_var','products_variants'));

    }

    /* update product information*/
    public function update(Request $req)
    {
        $id = $req->id;
        $req->validate([
            'product_name' => 'required',
            'product_type' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'sku' => 'required',
            'regular_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'inventory' => 'required',
            'description' => 'required',
            'short_description' => 'required',
            'specification' => 'required',
            'slug' => [
            'required',
                Rule::unique('products')->where(function ($query) use ($id) {
                    return $query->where('id', '!=', $id);
                })
            ],
        ]);
        $products = Products::find($req->id);
        $products->product_name = $req->product_name;
        $products->product_type = $req->product_type;
        //For Unique slug Start
         $products->slug = slugify($req->slug);
        //For Unique slug End
        $products->category_id = $req->category_id;
        $products->brand_id = $req->brand_id;
        $mainImage = $req->get('old_main_image');
        if ($req->hasFile('main_image')) {
            if ($products->main_image != null || $products->main_image != '') {
                $destinationPath = $products->main_image;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    File::delete($destinationPath);
                }
            }
            if ($products->large_image != null || $products->large_image != '') {
                $destinationPath = $products->large_image;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    File::delete($destinationPath);
                }
            }
            if ($products->main_image != null || $products->main_image != '') {
                $destinationPath = $products->main_image;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    File::delete($destinationPath);
                }
            }
            if ($products->thumbnail_image != null || $products->thumbnail_image != '') {
                $destinationPath = $products->thumbnail_image;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    File::delete($destinationPath);
                }
            }
            if ($req->hasFile('main_image')) {
                $mainImage = $req->file('main_image');
                //thumbnail image
                $thumbnail_image = resizeImage($mainImage,100,100,'thumbnail', true);
                //medium image
                $medium_image = resizeImage($mainImage,300,300,'medium', true);
                //large image
                $large_image = resizeImage($mainImage,600,600,'large',true);
                $mainImage = uplodImage($mainImage,true);
                $products->large_image = $large_image;
                $products->medium_image = $medium_image;
                $products->thumbnail_image = $thumbnail_image;
            }
           /*  $maineFile = $req->file('main_image');
            $mainImage = uplodImage($maineFile); */
        }
        $products->sku = $req->sku;
        $products->regular_price = $req->regular_price;
        $products->sale_price = $req->sale_price; 
        $products->inventory = $req->inventory;
        $products->description = $req->description;
        $products->short_description = $req->short_description;
        $products->specification = $req->specification;
        $videoImage = $req->get('old_video');
        if ($req->hasFile('video')) {
            if ($products->video != null || $products->video != '') {
                $destinationPath = $products->video;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    File::delete($destinationPath);
                }
            }
            $videoFile = $req->file('video');
            $videoImage = uplodImage($videoFile);
        }
        $downloadDatasheet = $req->get('old_download_datasheet');
        if ($req->hasFile('download_datasheet')) {
            if ($products->download_datasheet != null || $products->download_datasheet != '') {
                $destinationPath = $products->download_datasheet;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    File::delete($destinationPath);
                }
            }
            $downloadDatasheetSave = $req->file('download_datasheet');
            $downloadDatasheet = uplodImage($downloadDatasheetSave);
        }
        $products->seo_name = $req->seo_name;
        $products->seo_description = $req->seo_description;
        $products->seo_title = $req->seo_title;
        $products->seo_keyword = $req->seo_keyword;
        $products->status = $req->status;
        $products->main_image = $mainImage;
        
        $products->video = $videoImage;
        $products->download_datasheet = $downloadDatasheet;
        $products->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/products/edit/' . $req->id . '');
    }

    
/* update product gallery*/
    public function updateGallery(Request $req)
    {
        $products = Products::find($req->id);
        if (empty($products->gallery)) {
            $req->validate([
                'galleryfiles' => 'required',
            ]);
            if ($req->hasFile('galleryfiles')) {
                $gallery_image = $req->file('galleryfiles');
                $images = array();
                foreach ($gallery_image as $filenew) {
                    $images[]= uplodImage($filenew,true);
                }
                $products->gallery = implode(',', $images);
                
            }
        } else {
            if ($req->hasFile('galleryfiles')) {
                $gallery_image = $req->file('galleryfiles');
                $gallery_name = $req->galleryname;
                $path = date('Y') . '/' . date('m').'/';
                $destinationPath = 'uploads/' . $path;
                $images = array();
                $no = 1;
                foreach ($gallery_image as $filenew) {
                    $finalename = $filenew->getClientOriginalName();
                    if (in_array($finalename, explode(",", $products->gallery))) {
                        $newname = basename($finalename, '.' . $filenew->getClientOriginalExtension());
                        $finalname = $newname . $no . '.' . $filenew->getClientOriginalExtension();
                        $filenew->move($destinationPath, $finalname);
                        $images[] = $destinationPath.$finalname;
                    } else if (in_array($finalename, $gallery_name)) {
                        $filenew->move($destinationPath, $finalename);
                        $images[] = $destinationPath.$finalename;
                    }
                    $no++;
                }
                
                $dbdata = explode(",", $products->gallery);
                $imagesnew = array();
                foreach ($dbdata as $setname) {
                    if (in_array($setname, $gallery_name)) {
                        $imagesnew[] = $setname;
                    }
                }
               
                $products->gallery = implode(',', array_merge($images, $imagesnew));
            } else {
                $exited_gallery =  explode(',', $products->gallery);
                if(!empty($exited_gallery)){
                    foreach($exited_gallery as $destinationPath){
                        $fileExists = file_exists($destinationPath);
                        if ($fileExists) {
                            File::delete($destinationPath);
                        }
                    }
                }
                $products->gallery = null; 
            }
        }


        $products->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/products/edit/' . $req->id . '?tab=2');
    }
    /* update product attributes */
    public function updateAttribute(Request $req)
    {  
        if(!empty($req->selattributes)){
            foreach($req->selattributes as $attr){
             
               $prodcut_attributes= DB::table('product_attributes')
               ->select('attribute_variation_id')
               ->where('product_id',$req->id)
               ->where('attribute_id',$attr)->get();
            
               if(!empty($prodcut_attributes)){
                   foreach($prodcut_attributes as $ext_attr){
                       if(!empty($req->selvariations[$attr]) && !in_array($ext_attr->attribute_variation_id, $req->selvariations[$attr])){
                           DB::table('product_attributes')->where('attribute_variation_id', $ext_attr->attribute_variation_id) 
                            ->where('product_id',$req->id)
                            ->where('attribute_id',$attr)->delete();
                       
                       } 
                      
                   }
               } 
                 
                if(!empty($req->selvariations[$attr])){
                    
                    foreach($req->selvariations[$attr] as $variation){
                       $prodcut_attribute = ProductAttribute::where('product_id', '=', $req->id)
                                            ->where('attribute_id', '=', $attr)
                                            ->where('attribute_variation_id', '=', $variation)->first();
                                            
                        if(!$prodcut_attribute){

                            $prodcut_attribute = new ProductAttribute;
                            $prodcut_attribute->product_id = $req->id;
                            $prodcut_attribute->attribute_id = $attr;
                            $prodcut_attribute->attribute_variation_id = $variation;
                            $prodcut_attribute->save();
                        } 
                    }
                    
                }
            }
        }
        
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/products/edit/' . $req->id . '?tab=3');
    }

    /* update product techdoc*/
    public function updateTechDoc(Request $req)
    {
        $products = Products::find($req->id);
        if (empty($products->tech_documents)) {
            $req->validate([
                'techdocumentsfiles' => 'required',
            ]);
            if ($req->hasFile('techdocumentsfiles')) {
                $tech_doc = $req->file('techdocumentsfiles');
                $tech_name = $req->techdocumentsname;
                $path = date('Y') . '/' . date('m');
                $destinationPath = 'uploads/' . $path;
                // $destinationPath = 'uploads/products/techdocs/';

                $images = array();
                foreach ($tech_doc as $filenew) {
                    $finalename = $filenew->getClientOriginalName();
                    if (in_array($finalename, $tech_name)) {
                        $filenew->move($destinationPath, $finalename);
                        $images[] = $finalename;
                    }
                }
                $products->tech_documents ='uploads/'. date('Y').'/' .date('m').'/'. implode(',', $images);
            } else {
                $tech_name = $req->techdocumentsname;
                $products->tech_documents = 'uploads/'. date('Y').'/' .date('m').'/'. implode(',', $tech_name);
            }
        } else {
            if ($req->hasFile('techdocumentsfiles')) {
                $tech_doc = $req->file('techdocumentsfiles');
                $tech_name = $req->techdocumentsname;
                $path = date('Y') . '/' . date('m');
                $destinationPath = 'uploads/' . $path;
                // $destinationPath = 'uploads/products/techdocs/';
                $images = array();
                $no = 1;
                foreach ($tech_doc as $filenew) {
                    $finalename = $filenew->getClientOriginalName();
                    if (in_array($finalename, explode(",", $products->tech_documents))) {
                        $newname = basename($finalename, '.' . $filenew->getClientOriginalExtension());
                        $finalname = $newname . $no . '.' . $filenew->getClientOriginalExtension();
                        // return $finalname;
                        $filenew->move($destinationPath, $finalname);
                  
                        $images[] = $finalname;
                    } else if (in_array($finalename, $tech_name)) {
                        $filenew->move($destinationPath, $finalename);
                        $images[] = $finalename;
                    }
                    $no++;
                }
              
                //print_r($tech_name);
                $dbdata = explode(",", $products->tech_documents);
                $imagesnew = array();
                foreach ($dbdata as $setname) {
                    if (in_array($setname, $tech_name)) {
                        $imagesnew[] = $setname;
                    }
                }
                //print_r($imagesnew);
                //exit;
                $products->tech_documents ='uploads/'. date('Y').'/' .date('m').'/'.implode(',', array_merge($images, $imagesnew));  
            } else {
                $tech_name = $req->techdocumentsname;
                $products->tech_documents = 'uploads/'. date('Y').'/' .date('m').'/'.implode(',', $tech_name);
            }
        }
        // return $products->tech_documents;    
        $products->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/products/edit/' . $req->id . '?tab=4');
    }

    /* update product 360 images*/
    public function updateThreeSixty(Request $req)
    {
        $products = Products::find($req->id);
        if (empty($products->threesixty_images)) {
            $req->validate([
                'threesixtyfiles' => 'required',
            ]);
            if ($req->hasFile('threesixtyfiles')) {
                $threesixty_image = $req->file('threesixtyfiles');
                $threesixty_name = $req->threesixtyname;
                $destinationPath = 'uploads/products/threesixty/';
                $images = array();
                foreach ($threesixty_image as $filenew) {
                    $finalename = $filenew->getClientOriginalName();
                    if (in_array($finalename, $threesixty_name)) {
                        $filenew->move($destinationPath, $finalename);
                        $images[] = $finalename;
                    }
                }
                $products->threesixty_images = implode(',', $images);
            } else {
                $threesixty_name = $req->threesixtyname;
                $products->threesixty_images = implode(',', $threesixty_name);
            }
        } else {
            if ($req->hasFile('threesixtyfiles')) {
                $threesixty_image = $req->file('threesixtyfiles');
                $threesixty_name = $req->threesixtyname;
                $destinationPath = 'uploads/products/threesixty/';
                $images = array();
                $no = 1;
                foreach ($threesixty_image as $filenew) {
                    $finalename = $filenew->getClientOriginalName();
                    if (in_array($finalename, explode(",", $products->threesixty_images))) {
                        $newname = basename($finalename, '.' . $filenew->getClientOriginalExtension());
                        $finalname = $newname . $no . '.' . $filenew->getClientOriginalExtension();
                        $filenew->move($destinationPath, $finalname);
                        $images[] = $finalname;
                    } else if (in_array($finalename, $threesixty_name)) {
                        $filenew->move($destinationPath, $finalename);
                        $images[] = $finalename;
                    }
                    $no++;
                }
                $dbdata = explode(",", $products->threesixty_images);
                $imagesnew = array();
                foreach ($dbdata as $setname) {
                    if (in_array($setname, $threesixty_name)) {
                        $imagesnew[] = $setname;
                    }
                }
                $products->threesixty_images = implode(',', array_merge($images, $imagesnew));
            } else {
                $threesixty_name = $req->threesixtyname;
                $products->threesixty_images = implode(',', $threesixty_name);
            }
        }
        $products->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/products/edit/' . $req->id . '?tab=5');
    }

    /* add faq page*/
    public function addFaqs($id)
    {
        $page_title = 'Add faq';
        $page_description = 'Add faq info';
        $proid = $id;
        return view('admin.products.add_faq', compact('page_title', 'page_description', 'proid'));
    }

    /* add faq in db*/
    public function createFaqs(Request $req)
    {
        $req->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        $faqs = new Faqs;
        $faqs->title = $req->title;
        $faqs->description = $req->description;
        $faqs->proid = $req->proid;
        $faqs->save();
        /* if (count($ftitle) > 0 && count($fcontent) > 0) {
             foreach($ftitle as $key => $value)
             {
                 if (!empty($value) && !empty($fcontent[$key])){
                     $input['title'] = $value;
                     $input['content'] = $fcontent[$key];
                     $input['proid'] = $fproid;
                     Faqs::create($input);
                 }
             }
         }*/
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/products/edit/' . $req->proid . '?tab=7');
    }

    /* edit faq page*/
    public function editFaqs($id, $fid)
    {
        $page_title = 'Edit faq';
        $page_description = 'Edit faq info';
        $faqs = Faqs::find($fid);
        $proid = $id;
        return view('admin.products.add_faq', compact('page_title', 'page_description', 'faqs', 'proid'));
    }

    /* update faq in db*/
    public function updateFaqs(Request $req)
    {
        $req->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        $faqs = Faqs::find($req->id);
        $faqs->title = $req->title;
        $faqs->description = $req->description;
        $faqs->proid = $req->proid;
        $faqs->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/products/edit/' . $req->proid . '?tab=7');
    }

    /* delete products faq*/
    public function deleteFaq($id, $fid)
    {
        $faqs = Faqs::find($fid);
        $faqs->delete();
        Session::flash('message', 'Successfully delete!');
        return redirect('/admin/products/edit/' . $id . '?tab=7');
    }


    /* add product training videos.*/
    public function createTrainingVideo(Request $req)
    {
        $trainingvideo = new Product_training_videos;
        $req->validate([
            'trainingvideoname' => 'required',
            'trainingvideofiles' => 'required',
        ]);
        if ($req->hasFile('trainingvideofiles')) {
            $video = $req->file('trainingvideofiles');
            // $videoPath = 'uploads/products/trainingvideos/';
            $path = date('Y') . '/' . date('m');
            $vPath = 'uploads/' . $path;
            $video->move($vPath, $video->getClientOriginalName());
            $trainingvideo->video = $vPath.'/'.$video->getClientOriginalName();
            // return $trainingvideo;
        }
        $trainingvideo->name = $req->trainingvideoname;
        $trainingvideo->display_order = $req->display_order;
        $trainingvideo->proid = $req->proid;
        // return $trainingvideo;
        $trainingvideo->save();
        Session::flash('message', 'Successfully added!');
        return redirect('/admin/products/edit/' . $req->proid . '?tab=8');
    }

    /* add training video page*/
    public function addTrainingVideo($id)
    {
        $page_title = 'Add training video';
        $page_description = 'Add training video info';
        $proid = $id;
        return view('admin.products.add_training_video', compact('page_title', 'page_description', 'proid'));
    }

    /* edit training video page*/
    public function editTrainingVideo($id, $tvid)
    {
        $page_title = 'Edit training video';
        $page_description = 'Edit training video info';
        $trainingvideo = Product_training_videos::find($tvid);
        $proid = $id;
        return view('admin.products.add_training_video', compact('page_title', 'page_description', 'trainingvideo', 'proid'));
    }

    /* update training video in db*/
    public function updateTrainingVideo(Request $req)
    {
        $req->validate([
            'trainingvideoname' => 'required',
        ]);
        $trainingvideo = Product_training_videos::find($req->id);
        $trainingvideo->name = $req->trainingvideoname;
        if ($req->hasFile('trainingvideofiles')) {
            $video = $req->file('trainingvideofiles');
            // $videoPath = 'uploads/products/trainingvideos/';
            $path = date('Y') . '/' . date('m');
            $vPath = 'uploads/' . $path;
            // DD($vPath.'/'.$video->getClientOriginalName());
            $video->move($vPath, $video->getClientOriginalName());
            $trainingvideo->video = $vPath.'/'. $video->getClientOriginalName();
            // dd($trainingvideo);
        }
        $trainingvideo->display_order = $req->display_order;
        $trainingvideo->proid = $req->proid;
        $trainingvideo->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/products/edit/' . $req->proid . '?tab=8');
    }

    /* delete products training video*/
    public function deleteTrainingVideo($id, $tvid)
    {
        $trainingvideo = Product_training_videos::find($tvid);
        $trainingvideo->delete();
        Session::flash('message', 'Successfully delete!');
        return redirect('/admin/products/edit/' . $id . '?tab=8');
    }

    /* here set the feature video tab code */
    /* add product training videos.*/
    public function createFeatureVideo(Request $req)
    {
        $featurevideo = new Product_feature_videos;
        $req->validate([
            'featurevideoname' => 'required',
            'featurevideofiles' => 'required',
        ]);
        if ($req->hasFile('featurevideofiles')) {
            $video = $req->file('featurevideofiles');
            // $videoPath = 'uploads/products/featurevideos/';
            $path = date('Y') . '/' . date('m');
            $videoPath = 'uploads/' . $path;
            // return $videoPath;
            $video->move($videoPath, $video->getClientOriginalName());
            $featurevideo->video =$videoPath.'/'. $video->getClientOriginalName();
            // return $featurevideo;
        }
        $featurevideo->name = $req->featurevideoname;
        $featurevideo->display_order = $req->display_order;
        $featurevideo->proid = $req->proid;
        $featurevideo->save();
        Session::flash('message', 'Successfully added!');
        return redirect('/admin/products/edit/' . $req->proid . '?tab=9');
    }

    /* add feature video page*/
    public function addFeatureVideo($id)
    {
        $page_title = 'Add feature video';
        $page_description = 'Add feature video info';
        $proid = $id;
        return view('admin.products.add_feature_video', compact('page_title', 'page_description', 'proid'));
    }

    /* edit feature video page*/
    public function editFeatureVideo($id, $tvid)
    {
        $page_title = 'Edit feature video';
        $page_description = 'Edit feature video info';
        $featurevideo = Product_feature_videos::find($tvid);
        $proid = $id;
        return view('admin.products.add_feature_video', compact('page_title', 'page_description', 'featurevideo', 'proid'));
    }

    /* update feature video in db*/
  public function updateFeatureVideo(Request $req)
    {
        $req->validate([
            'featurevideoname' => 'required',
        ]);
        $featurevideo = Product_feature_videos::find($req->id);
        
        $featurevideo->name = $req->featurevideoname;
        if ($req->hasFile('featurevideofiles')) {
            $video = $req->file('featurevideofiles');
            // $videoPath = 'uploads/products/featurevideos/';
            $path = date('Y') . '/' . date('m');
            $videoPath = 'uploads/' . $path;


            $video->move($videoPath, $video->getClientOriginalName());
            // $featurevideo->video = $video->getClientOriginalName();
            $featurevideo->video =$videoPath.'/'. $video->getClientOriginalName();
        }
        $featurevideo->display_order = $req->display_order;
        $featurevideo->proid = $req->proid;
        $featurevideo->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/products/edit/' . $req->proid . '?tab=9');
    }

    /* delete products feature video*/
    public function deleteFeatureVideo($id, $tvid)
    {
        $featurevideo = Product_feature_videos::find($tvid);
        $featurevideo->delete();
        Session::flash('message', 'Successfully delete!');
        return redirect('/admin/products/edit/' . $id . '?tab=9');
    }
    
}
