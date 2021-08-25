<?php

namespace App\Http\Controllers;

use App\Imports\ItemImport;
use App\Imports\PriceListImport;
use App\Imports\ProductsImport;
use App\Imports\VendorImport;
use App\Models\Attributes;
use App\Models\Attributes_variations;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\Faqs;
use App\Models\ProductDetail;
use App\Models\Products;
use App\Models\Product_feature_videos;
use App\Models\Product_training_videos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Session;

class ImportController extends Controller
{
    public function index()
    {
        $page_title = 'Products Import';
        $page_description = 'Products Import List';
        return view('admin.import.import_form', compact('page_title', 'page_description'));
    }
    public  function getUniqueSlug($tableName,$name)
    {
         
        $slugCount = 0;
        $slug = $name;
        do {
            if ($slugCount == 0) {
                $currentSlug = slugify($slug);
            } else {
                $currentSlug = slugify($name . '-' . $slugCount);
            }
            if ( DB::table($tableName)->select('id')->where('slug','=',$currentSlug)->first()){
                $slugCount++;
            } else {
                $slug = $currentSlug;
                $slugCount = 0;
            }
        } while ($slugCount > 0);
      return $slug;  
    }
    public function importCsv(Request $request)
    {
        $this->validate($request,
            ['products_import' => 'required']
        );
        $importModel = new Products();
        //$file = public_path('file/test.csv');
        $file = request()->file('products_import');
        $tempName = $file->getPathName();
        $fileExtension = $file->getClientOriginalExtension();
        $errors = $file->getError();
        
        if ($errors == 0) {
            if (($fileExtension == "csv") && (!empty($tempName))) {
                $i = 0;
                $delimiter = ',';
                $data = [];
                $headersArray = [];
                if (($handle = fopen($tempName, 'r')) !== false) {
                    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                        $row = array_map("utf8_encode", $row);
                        if ($i == 0) {
                            $headersArray = $row;
                        }
                        $importArrayKeys = array_keys($importModel->import['fields']);
                           
                        if ($i != 0) {

                            if (count($row) == count($row)) {

                                foreach ($row as $key => $value) {
                                         
                                    if (isset($value)) {
                                        if (in_array('sku', $headersArray)
                                            && in_array('product_name', $headersArray)
                                            && in_array('description', $headersArray)
                                            && in_array('short_description', $headersArray)
                                            && in_array('category_id', $headersArray)
                                            && in_array('brand_id', $headersArray)
                                            && in_array('main_image', $headersArray)
                                            && in_array('regular_price', $headersArray)
                                            && in_array('sale_price', $headersArray)
                                            && in_array('Inventory', $headersArray)
                                            && in_array('IsCommited', $headersArray)
                                            && in_array('OnOrder', $headersArray)
                                            && in_array('specification', $headersArray)
                                            && in_array('tech_documents', $headersArray)
                                            && in_array('video', $headersArray)
                                            && in_array('gallery', $headersArray)
                                            && in_array('download_datasheet', $headersArray)
                                            && in_array('Attributes', $headersArray)
                                            && in_array('packaging_delivery_descr', $headersArray)
                                            && in_array('packaging_delivery_images', $headersArray)
                                            && in_array('trending_product', $headersArray)
                                            && in_array('best_selling', $headersArray)
                                            && in_array('bid_quote', $headersArray)
                                            && in_array('seo_title', $headersArray)
                                            && in_array('seo_description', $headersArray)
                                            && in_array('seo_keyword', $headersArray)
                                            && in_array('status', $headersArray)
                                            && in_array('VatGourpSa', $headersArray)
                                            && in_array('VatGroupPu', $headersArray)
                                            && in_array('U_Size', $headersArray)
                                            && in_array('SizeName', $headersArray)
                                            && in_array('U_SCartQty', $headersArray)
                                            && in_array('U_CBM', $headersArray)
                                            && in_array('OnHand', $headersArray)
                                            && in_array('U_Itemgrp', $headersArray)
                                            && in_array('U_Itemgrpname', $headersArray)
                                            && in_array('U_OrgCountCod', $headersArray)
                                            && in_array('U_OrgCountNam', $headersArray)
                                            && in_array('U_CartQty', $headersArray)
                                            && in_array('SuppCatNum', $headersArray)
                                            && in_array('BuyUnitMsr', $headersArray)
                                            && in_array('SalUnitMsr', $headersArray)
                                            && in_array('FirmCode', $headersArray)
                                            && in_array('FirmName', $headersArray)
                                            && in_array('U_HsCode', $headersArray)
                                            && in_array('U_HsName', $headersArray)
                                            && in_array('QryGroup1', $headersArray)
                                            && in_array('QryGroup2', $headersArray)
                                            && in_array('QryGroup3', $headersArray)
                                            && in_array('QryGroup4', $headersArray)
                                            && in_array('QryGroup5', $headersArray)
                                            && in_array('QryGroup6', $headersArray)
                                            && in_array('QryGroup7', $headersArray)
                                            && in_array('QryGroup8', $headersArray)
                                            && in_array('QryGroup9', $headersArray)
                                            && in_array('QryGroup10', $headersArray)
                                            && in_array('QryGroup11', $headersArray)
                                            && in_array('QryGroup12', $headersArray)
                                            && in_array('QryGroup13', $headersArray)
                                            && in_array('QryGroup14', $headersArray)
                                            && in_array('QryGroup15', $headersArray)
                                            && in_array('QryGroup16', $headersArray)
                                            && in_array('QryGroup17', $headersArray)
                                            && in_array('QryGroup18', $headersArray)
                                            && in_array('QryGroup19', $headersArray)
                                            && in_array('QryGroup20', $headersArray)
                                            && in_array('QryGroup21', $headersArray)
                                            && in_array('QryGroup22', $headersArray)
                                            && in_array('QryGroup23', $headersArray)
                                            && in_array('QryGroup24', $headersArray)
                                            && in_array('QryGroup25', $headersArray)
                                            && in_array('QryGroup26', $headersArray)
                                            && in_array('QryGroup27', $headersArray)
                                            && in_array('QryGroup28', $headersArray)
                                            && in_array('QryGroup29', $headersArray)
                                            && in_array('QryGroup30', $headersArray)
                                            && in_array('QryGroup31', $headersArray)
                                            && in_array('QryGroup32', $headersArray)
                                            && in_array('QryGroup33', $headersArray)
                                            && in_array('QryGroup34', $headersArray)
                                            && in_array('QryGroup35', $headersArray)
                                            && in_array('QryGroup36', $headersArray)
                                            && in_array('QryGroup37', $headersArray)
                                            && in_array('QryGroup38', $headersArray)
                                            && in_array('QryGroup39', $headersArray)
                                            && in_array('QryGroup40', $headersArray)
                                            && in_array('QryGroup41', $headersArray)
                                            && in_array('QryGroup42', $headersArray)
                                            && in_array('QryGroup43', $headersArray)
                                            && in_array('QryGroup44', $headersArray)
                                            && in_array('QryGroup45', $headersArray)
                                            && in_array('QryGroup46', $headersArray)
                                            && in_array('QryGroup47', $headersArray)
                                            && in_array('QryGroup48', $headersArray)
                                            && in_array('QryGroup49', $headersArray)
                                            && in_array('QryGroup50', $headersArray)
                                            && in_array('QryGroup51', $headersArray)
                                            && in_array('QryGroup52', $headersArray)
                                            && in_array('QryGroup53', $headersArray)
                                            && in_array('QryGroup54', $headersArray)
                                            && in_array('QryGroup55', $headersArray)
                                            && in_array('QryGroup56', $headersArray)
                                            && in_array('QryGroup57', $headersArray)
                                            && in_array('QryGroup58', $headersArray)
                                            && in_array('QryGroup59', $headersArray)
                                            && in_array('QryGroup60', $headersArray)
                                            && in_array('QryGroup61', $headersArray)
                                            && in_array('QryGroup62', $headersArray)
                                            && in_array('QryGroup63', $headersArray)
                                            && in_array('QryGroup64', $headersArray)
                                        ) {

                                            $data[$i][$importArrayKeys[$key]] = $value;
                                        }
                                    }
                                }
                            }
                        }
                        $i++;
                    }
                    fclose($handle);
                }
               
                $length = count($data);
                $totalInsert = 0;
                $totalError = 0;
                $totalUpdate= 0;
                
                $fileNotValid = 0;
                if ($length != 0) {
                    for ($i = 1; $i <= $length; $i++) {
                        try {
                            $sku = trim($data[$i]['sku']);
                            $product_name = trim($data[$i]['product_name']);
                            $description = trim($data[$i]['description']);
                            $short_description = trim($data[$i]['short_description']);
                            $category_id = trim($data[$i]['category_id']);
                            $brand_id = trim($data[$i]['brand_id']);
                            $main_image = trim($data[$i]['main_image']);
                            $regular_price = trim($data[$i]['regular_price']);
                            $sale_price = trim($data[$i]['sale_price']);
                            $inventory = trim($data[$i]['inventory']);
                            $IsCommited = trim($data[$i]['IsCommited']);
                            $OnOrder = trim($data[$i]['OnOrder']);
                            $specification = trim($data[$i]['specification']);
                            $tech_documents = trim($data[$i]['tech_documents']);
                            $video = trim($data[$i]['video']);
                            $gallery = trim($data[$i]['gallery']);
                            $download_datasheet = trim($data[$i]['download_datasheet']);
                            $attributes = trim($data[$i]['Attributes']);
                            $packaging_delivery_descr = trim($data[$i]['packaging_delivery_descr']);
                            $packaging_delivery_images = trim($data[$i]['packaging_delivery_images']);
                            $trending_product = trim($data[$i]['trending_product']);
                            $best_selling = trim($data[$i]['best_selling']);
                            $bid_quote = trim($data[$i]['bid_quote']);
                            
                            $seo_title = trim($data[$i]['seo_title']);
                            $seo_description = trim($data[$i]['seo_description']);
                            $seo_keyword = trim($data[$i]['seo_keyword']);
                            $status = trim($data[$i]['status']);
                            $vatGourpSa = trim($data[$i]['VatGourpSa']);
                            $VatGroupPu = trim($data[$i]['VatGroupPu']);
                            $U_Size = trim($data[$i]['U_Size']);
                            $sizeName = trim($data[$i]['SizeName']);
                            $u_SCartQty = trim($data[$i]['U_SCartQty']);
                            $U_CBM = trim($data[$i]['U_CBM']);
                            $OnHand = trim($data[$i]['OnHand']);
                            $U_Itemgrp = trim($data[$i]['U_Itemgrp']);
                            $U_Itemgrpname = trim($data[$i]['U_Itemgrpname']);
                            $U_OrgCountCod = trim($data[$i]['U_OrgCountCod']);
                            $U_OrgCountNam = trim($data[$i]['U_OrgCountNam']);
                            $U_CartQty = trim($data[$i]['U_CartQty']);
                            $SuppCatNum = trim($data[$i]['SuppCatNum']);
                            $BuyUnitMsr = trim($data[$i]['BuyUnitMsr']);
                            $SalUnitMsr = trim($data[$i]['SalUnitMsr']);
                            $FirmCode = trim($data[$i]['FirmCode']);
                            $FirmName = trim($data[$i]['FirmName']);
                            $U_HsCode = trim($data[$i]['U_HsCode']);
                            $U_HsName = trim($data[$i]['U_HsName']);
                            $QryGroup1 = trim($data[$i]['QryGroup1']);
                            $QryGroup2 = trim($data[$i]['QryGroup2']);
                            $QryGroup3 = trim($data[$i]['QryGroup3']);
                            $QryGroup4 = trim($data[$i]['QryGroup4']);
                            $QryGroup5 = trim($data[$i]['QryGroup5']);
                            $QryGroup6 = trim($data[$i]['QryGroup6']);
                            $QryGroup7 = trim($data[$i]['QryGroup7']);
                            $QryGroup8 = trim($data[$i]['QryGroup8']);
                            $QryGroup9 = trim($data[$i]['QryGroup9']);
                            $QryGroup10 = trim($data[$i]['QryGroup10']);
                            $QryGroup11 = trim($data[$i]['QryGroup11']);
                            $QryGroup12 = trim($data[$i]['QryGroup12']);
                            $QryGroup13 = trim($data[$i]['QryGroup13']);
                            $QryGroup14 = trim($data[$i]['QryGroup14']);
                            $QryGroup15 = trim($data[$i]['QryGroup15']);
                            $QryGroup16 = trim($data[$i]['QryGroup16']);
                            $QryGroup17 = trim($data[$i]['QryGroup17']);
                            $QryGroup18 = trim($data[$i]['QryGroup18']);
                            $QryGroup19 = trim($data[$i]['QryGroup19']);
                            $QryGroup20 = trim($data[$i]['QryGroup20']);
                            $QryGroup21 = trim($data[$i]['QryGroup21']);
                            $QryGroup22 = trim($data[$i]['QryGroup22']);
                            $QryGroup23 = trim($data[$i]['QryGroup23']);
                            $QryGroup24 = trim($data[$i]['QryGroup24']);
                            $QryGroup25 = trim($data[$i]['QryGroup25']);
                            $QryGroup26 = trim($data[$i]['QryGroup26']);
                            $QryGroup27 = trim($data[$i]['QryGroup27']);
                            $QryGroup28 = trim($data[$i]['QryGroup28']);
                            $QryGroup29 = trim($data[$i]['QryGroup29']);
                            $QryGroup30 = trim($data[$i]['QryGroup30']);
                            $QryGroup31 = trim($data[$i]['QryGroup31']);
                            $QryGroup32 = trim($data[$i]['QryGroup32']);
                            $QryGroup33 = trim($data[$i]['QryGroup33']);
                            $QryGroup34 = trim($data[$i]['QryGroup34']);
                            $QryGroup35 = trim($data[$i]['QryGroup35']);
                            $QryGroup36 = trim($data[$i]['QryGroup36']);
                            $QryGroup37 = trim($data[$i]['QryGroup37']);
                            $QryGroup38 = trim($data[$i]['QryGroup38']);
                            $QryGroup39 = trim($data[$i]['QryGroup39']);
                            $QryGroup40 = trim($data[$i]['QryGroup40']);
                            $QryGroup41 = trim($data[$i]['QryGroup41']);
                            $QryGroup42 = trim($data[$i]['QryGroup42']);
                            $QryGroup43 = trim($data[$i]['QryGroup43']);
                            $QryGroup44 = trim($data[$i]['QryGroup44']);
                            $QryGroup45 = trim($data[$i]['QryGroup45']);
                            $QryGroup46 = trim($data[$i]['QryGroup46']);
                            $QryGroup47 = trim($data[$i]['QryGroup47']);
                            $QryGroup48 = trim($data[$i]['QryGroup48']);
                            $QryGroup49 = trim($data[$i]['QryGroup49']);
                            $QryGroup50 = trim($data[$i]['QryGroup50']);
                            $QryGroup51 = trim($data[$i]['QryGroup51']);
                            $QryGroup52 = trim($data[$i]['QryGroup52']);
                            $QryGroup53 = trim($data[$i]['QryGroup53']);
                            $QryGroup54 = trim($data[$i]['QryGroup54']);
                            $QryGroup55 = trim($data[$i]['QryGroup55']);
                            $QryGroup56 = trim($data[$i]['QryGroup56']);
                            $QryGroup57 = trim($data[$i]['QryGroup57']);
                            $QryGroup58 = trim($data[$i]['QryGroup58']);
                            $QryGroup59 = trim($data[$i]['QryGroup59']);
                            $QryGroup60 = trim($data[$i]['QryGroup60']);
                            $QryGroup61 = trim($data[$i]['QryGroup61']);
                            $QryGroup62 = trim($data[$i]['QryGroup62']);
                            $QryGroup63 = trim($data[$i]['QryGroup63']);
                            $QryGroup64 = trim($data[$i]['QryGroup64']);
                        } catch (\Exception $e) {
                            $fileNotValid = 1;
                        }

                       $model = Products::where('sku', $sku)->first();
                        if (!empty($model)){
                            
                            if(!empty($description)){
                                $model->description = $description;
                            }
                            if(!empty($description)){
                                $model->short_description = $short_description;
                            }
                            if(!empty($regular_price)){
                                $model->regular_price = $regular_price;
                            }
                            if(!empty($sale_price)){
                                $model->sale_price = $sale_price;
                            }
                            if(!empty($specification)){
                                $model->specification = $specification;
                            }
                            
                            $model->seo_title = $seo_title;
                            $model->seo_description = $seo_description;
                            $model->seo_keyword = $seo_keyword;
                            if(!empty($main_image)){
                                if (!empty($model->main_image)) {
                                    $destinationPath = $model->main_image;
                                    $fileExists = file_exists($destinationPath);
                                    if ($fileExists) {
                                        unlink($destinationPath);
                                    }
                                }
                                if (!empty($model->thumbnail_image)) {
                                    $destinationPath = $model->thumbnail_image;
                                    $fileExists = file_exists($destinationPath);
                                    if ($fileExists) {
                                        unlink($destinationPath);
                                    }
                                }
                                if (!empty($model->medium_image)) {
                                    $destinationPath = $model->medium_image;
                                    $fileExists = file_exists($destinationPath);
                                    if ($fileExists) {
                                        unlink($destinationPath);
                                    }
                                }
                                if (!empty($model->large_image)) {
                                    $destinationPath = $model->large_image;
                                    $fileExists = file_exists($destinationPath);
                                    if ($fileExists) {
                                        unlink($destinationPath);
                                    }
                                }
                                //medium image
                                $model->thumbnail_image = resizeImageByURL($main_image,100,100,'thumbnail');
                                //medium image
                                $model->medium_image = resizeImageByURL($main_image,300,300,'medium');
                                //large image
                                $model->large_image = resizeImageByURL($main_image,600,600,'large',true);
                               $model->main_image  = uplodImageByURL($main_image,true);
                            }
                            if(!empty($video)){
                                if (!empty($model->video)) {
                                    $destinationPath = $model->video;
                                    $fileExists = file_exists($destinationPath);
                                    if ($fileExists) {
                                        unlink($destinationPath);
                                    }
                                }
                                $model->video  = uplodImageByURL($video);
                            }
                            if(!empty($download_datasheet)){
                                if (!empty($model->download_datasheet)) {
                                    $destinationPath = $model->download_datasheet;
                                    $fileExists = file_exists($destinationPath);
                                    if ($fileExists) {
                                        unlink($destinationPath);
                                    }
                                }
                                $model->download_datasheet  = uplodImageByURL($download_datasheet);
                            }
                            
                            $explodeCats = explode('>', $category_id );
                            $parentid = 0;
                            foreach($explodeCats as $cat){
                                $catData = Categories::where('category_name', trim($cat))->first('id');
                                if(!empty( $catData)){
                                    $catId = $catData->id;
                                    $parentid = $catData->id;
                                }else{
                                    $newCat = new Categories;
                                    $newCat->category_name = $cat;
                                    if(!empty( $parentid)){
                                        $newCat->parent_category = $parentid;
                                    }
                                    $newCat->slug = $this->getUniqueSlug('categories', $cat);
                                    $newCat->save();
                                    $catId = $newCat->id;
                                    $parentid = $newCat->id;
                                }
                            }
                           $model->category_id = $catId;

                           
                            $brandData = Brand::where('brand_name', $brand_id)->first('id');
                            if(!empty( $brandData)){
                                $brandId = $brandData->id;
                            }else{
                                $newBrand = new Brand;
                                $newBrand->brand_name = $brand_id;
                                $newBrand->save();
                                $brandId = $newBrand->id;
                            }
                            $model->brand_id = $brandId;

                            $tech_docs = array();
                            if(!empty($tech_documents)){
                                if (!empty($model->tech_documents)) {
                                    $exited_techdocs =  explode(',', $model->tech_documents);
                                   if(!empty($exited_techdocs)){
                                       foreach($exited_techdocs as $destinationPath){
                                        $fileExists = file_exists($destinationPath);
                                        if ($fileExists) {
                                            unlink($destinationPath);
                                        }
                                       }
                                   }
                                  
                                } 

                                $tech_documentsArr =  explode('|', $tech_documents);
                                foreach($tech_documentsArr as $doc){
                                    $tech_docs[] = uplodImageByURL($doc);
                                }
                             }  

                            if(!empty($tech_docs)){
                                $model->tech_documents = implode(',', $tech_docs);  
                            }

                           $gallery_names = array();
                           if(!empty($gallery)){
                                if (!empty($model->gallery)) {
                                    $exited_gallery =  explode(',', $model->gallery);
                                   if(!empty($exited_gallery)){
                                       foreach($exited_gallery as $destinationPath){
                                        $fileExists = file_exists($destinationPath);
                                        if ($fileExists) {
                                            unlink($destinationPath);
                                        }
                                       }
                                   }
                                }

                                $galleryArr =  explode('|', $gallery);
                                foreach($galleryArr as $image){
                                    $gallery_names[] = uplodImageByURL($image,true);
                                }
                            }   
                            if(!empty($gallery_names)){
                               $model->gallery = implode(',', $gallery_names);  
                            }

                            /* packaging  */
                            if(!empty($packaging_delivery_descr)){
                                $model->packaging_delivery_descr = $packaging_delivery_descr; 
                            }
                            $pd_names = array();
                            if(!empty($packaging_delivery_images)){
                                if (!empty($model->packaging_delivery_images)) {
                                    $exited_pdImages =  explode(',', $model->packaging_delivery_images);
                                   if(!empty($exited_pdImages)){
                                       foreach($exited_pdImages as $destinationPath){
                                        $fileExists = file_exists($destinationPath);
                                        if ($fileExists) {
                                            unlink($destinationPath);
                                        }
                                       }
                                   }
                                }
                                $pdImageArr =  explode('|', $packaging_delivery_images);
                                foreach($pdImageArr as $image){
                                    $pd_names[] = uplodImageByURL($image);
                                }
                            }  
                            if(!empty($pd_names)){
                                $model->packaging_delivery_images = implode(',', $pd_names);  
                            }
                          
                            $model->trending_product = $trending_product;
                            $model->best_selling = $best_selling;
                            $model->bid_quote = $bid_quote;
                            $model->save();
                            $totalUpdate++;
                        }else{
                            $totalInsert++;

                          /*   $model = new Products;
                      
                            $catId = Categories::where('category_name', $category_id)->first('id');
                            $brandId = Brand::where('brand_name', $brand_id)->first('id');
                            $splitAttributes = preg_split("/[|]/", $attributes);

                            $attId = Attributes::first();

                            foreach ($splitAttributes as $key => $value) {

                                //$attName = strtok($value, '>');
                                $explodeAtt = explode('>', $value);
                                $attName = $explodeAtt[0];
                                $variationName = $explodeAtt[1];
                                $attTrimName = trim($attName);
                                $variationTrim = trim($variationName);


                                $attId = NULL;
                                $attData = Attributes::where('attribute_name', $attTrimName)->first('id');
                                if (empty($attData)) {
                                    $attNew = new Attributes();
                                    $attNew->attribute_name = $attTrimName;
                                    $attNew->save();
                                    $attId = $attNew->id;
                                } else {
                                    $attId = $attData->id;
                                }

                                $variationId = NULL;
                                $variationData = Attributes_variations::where('variation_name', $variationTrim)->where('attribute_id', $attId)->first('id');

                                if (empty($variationData)) {
                                    $variationNew = new Attributes_variations();
                                    $variationNew->attribute_id = $attId;
                                    $variationNew->variation_name = $variationTrim;
                                    $variationNew->save();
                                    $variationId = $variationNew->id;
                                } else {
                                    $variationId = $variationData->id;
                                }
                            }
 

                            $info = pathinfo($main_image);
                            $contents = filter_var($main_image, FILTER_SANITIZE_URL);
                            // $file = '/tmp/' . $info['basename'];
                            $path = date('Y') . '/' . date('m');
                            $destinationPath = 'uploads/' . $path . '/' . $info['basename'];
                            file_put_contents($destinationPath, $contents);
                            new UploadedFile($destinationPath, $info['basename']);
                          


                            $model->product_name = $product_name;
                            $model->description = $description;
                            $model->short_description = $short_description;
                            $model->category_id = $catId->id;
                            $model->brand_id = $brandId->id;
                            $model->sku = $sku;
                            $model->main_image = $destinationPath;
                            $model->regular_price = $regular_price;
                            $model->sale_price = $sale_price;
                            $model->inventory = $inventory;
                            $model->IsCommited = $IsCommited;
                            $model->OnOrder = $OnOrder;
                            $model->specification = $specification;
                            $model->tech_documents = $tech_documents;
                            $explodeVideo = explode('|', $video);
                            $implodeVideo = implode(", ", (array)$explodeVideo);
                            $model->video = $implodeVideo;
                            //$model->video = $video;
                            //$model->gallery = $gallery;
                            $explodeGallery = explode('|', $gallery);
                            $implodeGallery = implode(", ", (array)$explodeGallery);
                            $model->gallery = $implodeGallery;
                            $model->download_datasheet = $download_datasheet;
                            $model->seo_title = $seo_title;
                            $model->seo_description = $seo_description;
                            $model->seo_keyword = $seo_keyword;
                            $model->status = $status;
                            $model->save();

                            $productsDetailsData = ProductDetail::where('product_id', $model->id)->first('id');

                            if (empty($productsDetailsData)) {
                                $productDetails = new ProductDetail();
                                $productDetails->product_id = $model->id;
                                $productDetails->VatGourpSa = trim($vatGourpSa);
                                $productDetails->U_Size = trim($U_Size);
                                $productDetails->SizeName = trim($sizeName);
                                $productDetails->U_SCartQty = trim($u_SCartQty);
                                $productDetails->U_CBM = trim($U_CBM);
                                $productDetails->OnHand = trim($OnHand);
                                $productDetails->U_Itemgrp = trim($U_Itemgrp);
                                $productDetails->U_Itemgrpname = trim($U_Itemgrpname);
                                $productDetails->U_OrgCountCod = trim($U_OrgCountCod);
                                $productDetails->U_OrgCountNam = trim($U_OrgCountNam);
                                $productDetails->U_CartQty = trim($U_CartQty);
                                $productDetails->SuppCatNum = trim($SuppCatNum);
                                $productDetails->BuyUnitMsr = trim($BuyUnitMsr);
                                $productDetails->SalUnitMsr = trim($SalUnitMsr);
                                $productDetails->FirmCode = trim($FirmCode);
                                $productDetails->FirmName = trim($FirmName);
                                $productDetails->U_HsCode = trim($U_HsCode);
                                $productDetails->U_HsName = trim($U_HsName);
                                $productDetails->QryGroup1 = trim($QryGroup1);
                                $productDetails->QryGroup2 = trim($QryGroup2);
                                $productDetails->QryGroup3 = trim($QryGroup3);
                                $productDetails->QryGroup4 = trim($QryGroup4);
                                $productDetails->QryGroup5 = trim($QryGroup5);
                                $productDetails->QryGroup6 = trim($QryGroup6);
                                $productDetails->QryGroup7 = trim($QryGroup7);
                                $productDetails->QryGroup8 = trim($QryGroup8);
                                $productDetails->QryGroup9 = trim($QryGroup9);
                                $productDetails->QryGroup10 = trim($QryGroup10);
                                $productDetails->QryGroup11 = trim($QryGroup11);
                                $productDetails->QryGroup12 = trim($QryGroup12);
                                $productDetails->QryGroup13 = trim($QryGroup13);
                                $productDetails->QryGroup14 = trim($QryGroup14);
                                $productDetails->QryGroup15 = trim($QryGroup15);
                                $productDetails->QryGroup16 = trim($QryGroup16);
                                $productDetails->QryGroup17 = trim($QryGroup17);
                                $productDetails->QryGroup18 = trim($QryGroup18);
                                $productDetails->QryGroup19 = trim($QryGroup19);
                                $productDetails->QryGroup20 = trim($QryGroup20);
                                $productDetails->QryGroup21 = trim($QryGroup21);
                                $productDetails->QryGroup22 = trim($QryGroup22);
                                $productDetails->QryGroup23 = trim($QryGroup23);
                                $productDetails->QryGroup24 = trim($QryGroup24);
                                $productDetails->QryGroup25 = trim($QryGroup25);
                                $productDetails->QryGroup26 = trim($QryGroup26);
                                $productDetails->QryGroup27 = trim($QryGroup27);
                                $productDetails->QryGroup28 = trim($QryGroup28);
                                $productDetails->QryGroup29 = trim($QryGroup29);
                                $productDetails->QryGroup30 = trim($QryGroup30);
                                $productDetails->QryGroup30 = trim($QryGroup30);
                                $productDetails->QryGroup31 = trim($QryGroup31);
                                $productDetails->QryGroup32 = trim($QryGroup32);
                                $productDetails->QryGroup33 = trim($QryGroup33);
                                $productDetails->QryGroup34 = trim($QryGroup34);
                                $productDetails->QryGroup35 = trim($QryGroup35);
                                $productDetails->QryGroup36 = trim($QryGroup36);
                                $productDetails->QryGroup37 = trim($QryGroup37);
                                $productDetails->QryGroup38 = trim($QryGroup38);
                                $productDetails->QryGroup39 = trim($QryGroup39);
                                $productDetails->QryGroup39 = trim($QryGroup39);
                                $productDetails->QryGroup40 = trim($QryGroup40);
                                $productDetails->QryGroup41 = trim($QryGroup41);
                                $productDetails->QryGroup42 = trim($QryGroup42);
                                $productDetails->QryGroup43 = trim($QryGroup43);
                                $productDetails->QryGroup44 = trim($QryGroup44);
                                $productDetails->QryGroup45 = trim($QryGroup45);
                                $productDetails->QryGroup46 = trim($QryGroup46);
                                $productDetails->QryGroup47 = trim($QryGroup47);
                                $productDetails->QryGroup48 = trim($QryGroup48);
                                $productDetails->QryGroup49 = trim($QryGroup49);
                                $productDetails->QryGroup50 = trim($QryGroup50);
                                $productDetails->QryGroup51 = trim($QryGroup51);
                                $productDetails->QryGroup52 = trim($QryGroup52);
                                $productDetails->QryGroup53 = trim($QryGroup53);
                                $productDetails->QryGroup54 = trim($QryGroup54);
                                $productDetails->QryGroup55 = trim($QryGroup55);
                                $productDetails->QryGroup56 = trim($QryGroup56);
                                $productDetails->QryGroup57 = trim($QryGroup57);
                                $productDetails->QryGroup58 = trim($QryGroup58);
                                $productDetails->QryGroup59 = trim($QryGroup59);
                                $productDetails->QryGroup60 = trim($QryGroup60);
                                $productDetails->QryGroup61 = trim($QryGroup61);
                                $productDetails->QryGroup62 = trim($QryGroup62);
                                $productDetails->QryGroup63 = trim($QryGroup63);
                                $productDetails->QryGroup64 = trim($QryGroup64);
                                $productDetails->save();
                            } else {
                                $proID = $productsDetailsData->id;
                            } */
                        }

                    }
                    $message = 'Successfully updated '.$totalUpdate.', and skipped  '.$totalInsert.' from the total of '.$length.'  records!';

                    Session::flash('message',  $message);
                    return redirect('/admin/import');
                }else{
                    Session::flash('message', 'Error occured');
                    return redirect('/admin/import');
                }

            }
        }
    }

    public function faqImportCsv(Request $request)
    {
        $this->validate($request,
            ['faq_file' => 'required']
        ); 
        $faqImportModel = new Faqs();
        $file = request()->file('faq_file');
        $tempName = $file->getPathName();
        $fileExtension = $file->getClientOriginalExtension();
        $errors = $file->getError();

        if ($errors == 0) {
            if (($fileExtension == "csv") && (!empty($tempName))) {
                $i = 0;
                $delimiter = ',';
                $data = [];
                $headersArray = [];
                if (($handle = fopen($tempName, 'r')) !== false) {
                    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                        $row = array_map("utf8_encode", $row);
                        if ($i == 0) {
                            $headersArray = $row;
                        }
                        $importArrayKeys = array_keys($faqImportModel->import['fields']);
                      
                        if ($i != 0) {
                            if (count($row) == count($row)) {
                                foreach ($row as $key => $value) {
                                    if (isset($value)) {
                                        if (in_array('sku', $headersArray)
                                            && in_array('title', $headersArray)
                                            && in_array('description', $headersArray)
                                        ) {
                                            $data[$i][$importArrayKeys[$key]] = $value;
                                        }
                                    }
                                }
                            }
                        }
                        $i++;
                    }
                    fclose($handle);
                }
                $length = count($data);
                $totalInsert = 0;
                $totalError = 0;
                $totalUpdate= 0;
                $fileNotValid = 0;
                if ($length != 0) {
                    for ($i = 1; $i <= $length; $i++) {
                        try {
                            $sku = $data[$i]['sku'];
                            $title = $data[$i]['title'];
                            $description = $data[$i]['description'];
                        } catch (\Exception $e) {
                            $fileNotValid = 1;
                        }
                        $trimSKU = trim($sku);
                        $trimTitle = trim($title);
                        $trimDesc = trim($description);
                        $productData = Products::where('sku', $trimSKU)->first('id');
                        if (!empty($productData) && !empty( $trimTitle)){
                            $trimProid = $productData->id;
                            $faqData = Faqs::where('title', $trimTitle)->where('proid', $trimProid)->first('id');
                            if(!empty( $faqData)){
                                $faqData->title =  $trimTitle;
                                $faqData->description =  $trimDesc;
                                $faqData->save();
                                $totalUpdate ++;
                            }else{
                                $newFAQ = new Faqs;
                                $newFAQ->title =  $trimTitle;
                                $newFAQ->description =  $trimDesc;
                                $newFAQ->proid =  $trimProid;
                                $newFAQ->save();
                                $totalInsert ++;
                            }

                        }else{
                            $totalError ++;
                        }
                      
                    }
                     
                    $message = 'Successfully inserted '.$totalInsert.', updated '.$totalUpdate.', and skipped  '.$totalError.' from the total of '.$length.'  records!';

                    Session::flash('message',  $message);
                    return redirect('/admin/import');
                }else{
                    Session::flash('message', 'Error occured');
                    return redirect('/admin/import');
                }
            }
        }
    }

    public function trainingVideoImportCsv(Request $request)
    {
        $this->validate($request,
            ['training_video_file' => 'required']
        );
        $trainingVideoModel = new Product_training_videos();
        $file = request()->file('training_video_file');
        $tempName = $file->getPathName();
        $fileExtension = $file->getClientOriginalExtension();
        $errors = $file->getError();

        if ($errors == 0) {
            if (($fileExtension == "csv") && (!empty($tempName))) {
                $i = 0;
                $delimiter = ',';
                $data = [];
                $headersArray = [];
                if (($handle = fopen($tempName, 'r')) !== false) {
                    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                        $row = array_map("utf8_encode", $row);
                        if ($i == 0) {
                            $headersArray = $row;
                        }
                        $importArrayKeys = array_keys($trainingVideoModel->import['fields']);
                        if ($i != 0) {
                            if (count($row) == count($row)) {
                                foreach ($row as $key => $value) {
                                    if (isset($value)) {
                                        if (in_array('sku', $headersArray)
                                            && in_array('name', $headersArray)
                                            && in_array('video', $headersArray)
                                        ) {
                                            $data[$i][$importArrayKeys[$key]] = $value;
                                        }
                                    }
                                }
                            }
                        }
                        $i++;
                    }
                    fclose($handle);
                }
                $length = count($data);
                $totalInsert = 0;
                $totalError = 0;
                $totalUpdate= 0;
                $fileNotValid = 0;
                if ($length != 0) {
                    for ($i = 1; $i <= $length; $i++) {
                        try {
                            $sku = $data[$i]['sku'];
                            $name = $data[$i]['name'];
                            $video = $data[$i]['video'];
                        } catch (\Exception $e) {
                            $fileNotValid = 1;
                        }
                        $trimSKU = trim($sku);
                        $trimName = trim($name);
                        $trimvideo = trim($video);

                        $productData = Products::where('sku', $trimSKU)->first('id');
                        if (!empty($productData)  && !empty( $trimName)  && !empty( $trimvideo)){
                            $trimvideoProid = $productData->id;
                           
                            $trainingVideoData = Product_training_videos::where('name', $trimName)->where('proid', $trimvideoProid)->first();
                            
                            if(!empty( $trainingVideoData)){
                               
                                $trainingVideoData->name = $trimName;
                                if (!empty($trainingVideoData->video)) {
                                    $destinationPath = $trainingVideoData->video;
                                    $fileExists = file_exists($destinationPath);
                                    if ($fileExists) {
                                        unlink($destinationPath);
                                    }
                                }
                               if(!empty($trimvideo)){
                                    $trainingVideoData->video  = uplodImageByURL($trimvideo);
                                }
                                $trainingVideoData->save();
                                $totalUpdate++;
                            }else{
                                $newTV = new Product_training_videos;
                                $newTV->name =  $trimName;
                                if(!empty($trimvideo)){
                                    $newTV->video  = uplodImageByURL($trimvideo);
                                }
                                $newTV->proid =  $trimvideoProid;
                                $newTV->save();
                                $totalInsert++;
                            }
                          
                        }else{
                            $totalError++;
                        }
                      
                    }
                    $message = 'Successfully inserted '.$totalInsert.', updated '.$totalUpdate.', and skipped  '.$totalError.' from the total of '.$length.'  records!';
                    
                    Session::flash('message',  $message);
                    return redirect('/admin/import');
                }else{
                    Session::flash('message', 'Error occured');
                    return redirect('/admin/import');
                }
            }
        }
    }

    public function featureVideoImportCsv(Request $request)
    {
        $this->validate($request,
            ['feature_video_file' => 'required']
        );
        $featureVideoModel = new Product_feature_videos();
        $file = request()->file('feature_video_file');
        $tempName = $file->getPathName();
        $fileExtension = $file->getClientOriginalExtension();
        $errors = $file->getError();

        if ($errors == 0) {
            if (($fileExtension == "csv") && (!empty($tempName))) {
                $i = 0;
                $delimiter = ',';
                $data = [];
                $headersArray = [];
                if (($handle = fopen($tempName, 'r')) !== false) {
                    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                        $row = array_map("utf8_encode", $row);
                        if ($i == 0) {
                            $headersArray = $row;
                        }
                        $importArrayKeys = array_keys($featureVideoModel->import['fields']);
                        if ($i != 0) {
                            if (count($row) == count($row)) {
                                foreach ($row as $key => $value) {
                                    if (isset($value)) {
                                        if (in_array('sku', $headersArray)
                                            && in_array('name', $headersArray)
                                            && in_array('video', $headersArray)
                                        ) {
                                            $data[$i][$importArrayKeys[$key]] = $value;
                                        }
                                    }
                                }
                            }
                        }
                        $i++;
                    }
                    fclose($handle);
                }
                $length = count($data);
                $totalInsert = 0;
                $totalError = 0;
                $totalUpdate= 0;
                $fileNotValid = 0;
                if ($length != 0) {
                    for ($i = 1; $i <= $length; $i++) {
                        try {
                            $sku = $data[$i]['sku'];
                            $name = $data[$i]['name'];
                            $video = $data[$i]['video'];
                        } catch (\Exception $e) {
                            $fileNotValid = 1;
                        }

                        $trimSKU = trim($sku);
                        $trimName = trim($name);
                        $trimvideo = trim($video);

                        $productData = Products::where('sku', $trimSKU)->first('id');
                        if (!empty($productData)  && !empty( $trimName)  && !empty( $trimvideo)){
                            $trimvideoProid = $productData->id;
                           
                            $featureVideoData = Product_feature_videos::where('name', $trimName)->where('proid', $trimvideoProid)->first();
 
                            if(!empty( $featureVideoData)){
                               $featureVideoData->name = $trimName;
                                if (!empty($featureVideoData->video)) {
                                    $destinationPath = $featureVideoData->video;
                                    $fileExists = file_exists($destinationPath);
                                    if ($fileExists) {
                                        unlink($destinationPath);
                                    }
                                }
                               if(!empty($trimvideo)){
                                    $featureVideoData->video  = uplodImageByURL($trimvideo);
                                }
                                $featureVideoData->save();
                                $totalUpdate++;
                            }else{
                                $newFV = new Product_feature_videos;
                                $newFV->name =  $trimName;
                                if(!empty($trimvideo)){
                                    $newFV->video  = uplodImageByURL($trimvideo);
                                }
                                $newFV->proid =  $trimvideoProid;
                                $newFV->save();
                                $totalInsert++;
                            }

                        }else{
                            $totalError++;
                        }
                      
                    }
                    $message = 'Successfully inserted '.$totalInsert.', updated '.$totalUpdate.', and skipped  '.$totalError.' from the total of '.$length.'  records!';
                    
                    Session::flash('message',  $message);
                    return redirect('/admin/import');
                }else{
                    Session::flash('message', 'Error occured');
                    return redirect('/admin/import');
                }

            }
        }
    }
}