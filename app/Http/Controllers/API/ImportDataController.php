<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Products;
use App\Models\Brand;
use App\Models\PriceList;
use App\Models\ProductDetail;
use App\Models\ProductVariantCombination;
use App\Models\Attributes_variations;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\Log;

class ImportDataController extends Controller
{

    public function __construct()
    {
        ini_set('max_execution_time', 600); 
        ini_set('memory_limit','-1');
    }

    public function vendorImport()
    {
      
        $xml_data = $this->getXMLData('http://192.168.10.20/api/BPMaster/GetBPMaster');
        $xml = simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);   
        $vendorArr = json_decode($xml); 
      
        $totalCount = count($vendorArr);
        $errors=  array();
        $skipRow = 0;
        $icout = 0;
        $ucout = 0;
          foreach($vendorArr as $vendor){
            $salesperson = User::select('id')->where('vendor_code', '=',  $vendor->SlpCode)
            ->where('user_role', '=',  2)->first();
            
            if(isset($vendor->CardCode) && !empty($vendor->CardCode) 
                && isset($vendor->CardName) && !empty($vendor->CardName) 
                && isset($vendor->ListNum) && !empty($vendor->ListNum) && (!empty($salesperson))){

               $user = User::where('vendor_code', '=',  $vendor->CardCode)->where('user_role', '=',  3)->first();
            
                if ($user === null) {
                     $newUser = new User;
                    $newUser->vendor_code = $vendor->CardCode;
                    $newUser->price_list_no = $vendor->ListNum;
                    $newUser->vendor_credit_limit = $vendor->CreditLine;
                    $newUser->name = $vendor->CardName;
                    $newUser->email = $vendor->Email;
                    $newUser->mobile = $vendor->Mobil;
                    $newUser->phone = $vendor->Telephone;
                    $newUser->GroupNum = $vendor->GroupNum;
                    $newUser->PymntGroup = $vendor->PymntGroup;
                    $newUser->balance = $vendor->Balance;
                    $newUser->salesperson = $salesperson->id;
                    $newUser->user_role = 3; 
                    $newUser->save();  
                    $icout++;
                }else{
                    $user->vendor_code = $vendor->CardCode;
                    $user->price_list_no = $vendor->ListNum;
                    $user->vendor_credit_limit = $vendor->CreditLine;
                    $user->name = $vendor->CardName;
                    $user->email = $vendor->Email;
                    $user->mobile = $vendor->Mobil;
                    $user->phone = $vendor->Telephone;
                    $user->GroupNum = $vendor->GroupNum;
                    $user->PymntGroup = $vendor->PymntGroup;
                    $user->salesperson = $salesperson->id;
                    $user->balance = $vendor->Balance;  
                    $user->save();  
                    $ucout++;
                }
            }else{
                $errors[] = array("BPCode" => "The line # ".$vendor->CardCode." and #Name ".$vendor->CardName);
                $skipRow++;
            }  
        }   
      return response()->json(['message'=>'skeep recored '.$skipRow.' successfully inserted '.$icout.' successfully Updated '.$ucout.' out of total '.$totalCount,'error'=>$errors],200); 
 
    }

    //Import product Data
    public function productImport()
    {
        
        $url = "http://192.168.10.20/api/ItemMaster/GetItemMaster";
        //$json =  @file_get_contents($url, false, $context);
        //$url =   asset('uploads/GetItemMaster.json');
       // $url =   'http://localhost/html/test/GetItemMaster1.json';

       /*  $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL,$url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYPEER, false );  
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
        $query = curl_exec($curl_handle);
        curl_close($curl_handle); */

        /*      $fquery   = substr($query, 1);
             $lquery   = substr($fquery, 0, -1);
       echo  $lquery ; */
          /*   $someArray = json_decode($query, true);
             dd($someArray);
            echo count($someArray);
            foreach($someArray as $dd){
            echo $dd['ItemCode'];
            echo '<br>';
            }
            exit();   */

      /*   $json =  file_get_contents($url);
        $obj = json_decode($json); */
      
        $xml_data = $this->getXMLData('http://192.168.10.20/api/ItemMaster/GetItemMaster');
        $xml = simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);   
        $productArr = json_decode($xml); 
       
        $icout = 0;
        $totalCount = count($productArr);
        $errors=  array();
        $skipRow = 0;
        $dd = array();
        $sizes=array("Pcs","38","39","46","40","41","42","43","44","45","10","Set","2XL","3XL","4XL","5XL","6XL","Pairs","Doz","L","M","S","XL","Mono","Kit","11","7XL","36","37","47","48","5","6","7","8","9","Roll","Bag","2","3","4","12","13","10-10.5","Box","8-8.5","Pkt","35","in/Out","Rabbit","9-9.5","Ctn","7-7.5");
        $colors=array("Clear","Dark","Blue","Green","Grey","Orange","Pink","Purple","Red","White","Yellow","Navy","Amber/yellow","Green/Navy","Black","Bear","Amber","Royal Blue","Silver","Orange/Navy","Yellow/Navy","Brown","Gold");
        
        foreach($productArr as $product){
            
            if(!empty($product->U_Itemgrp) && !empty($product->ItemCode) && !empty($product->U_Itemgrpname)  ){
                $attribute_id = NULL;
                $attr_variation_id = NULL;
                $product_id = NULL;
                $apiSizeName = trim($product->SizeName);
                if (in_array( $apiSizeName, $colors)){
                    $attribute_id = 1;
                }elseif(in_array( $apiSizeName, $sizes)){
                    $attribute_id = 2;
                }
                if(!empty( $attribute_id)){
                    $attrVarExist = Attributes_variations::where('attribute_id', '=',  $attribute_id)->where('variation_name', '=',   $apiSizeName)->first();

                    if($attrVarExist){
                        $attr_variation_id = $attrVarExist->id;
                    }else{
                        $avNew = new Attributes_variations;
                        $avNew->attribute_id = $attribute_id;
                        $avNew->variation_name = $apiSizeName;
                        $avNew->save();
                        $attr_variation_id = $avNew->id;
                    }
                }else{
                    $errors[] = array("gorupCode" => "The line # ".$product->U_Itemgrp." and # ".$product->ItemCode." have new new size name # ".$apiSizeName);
                }

                $productExist = Products::where('sku', '=',  $product->U_Itemgrp)->first();
                if( $productExist){
                    $productExist->product_name = $product->U_Itemgrpname;
                    $productExist->seo_title = $product->U_Itemgrpname;
                    $productExist->product_type = 'variable';
                    $productExist->save(); 
                    $product_id = $productExist->id;
             
                }else {
                
                    $slug =  $this->getUniqueSlug('Products', $product->U_Itemgrpname);
                    
                   /*  if(!empty($product->Brand)){
                        $brandID = $this->getBrandID($product->Brand);
                    }else{
                        $brandID = 0;
                    } */
                    $brandID = 0;
                    $newProduct = new Products;
                    $newProduct->sku = $product->U_Itemgrp;
                    $newProduct->product_name = $product->U_Itemgrpname;
                    $newProduct->slug = $slug;
                    $newProduct->userid = 9047; 
                    $newProduct->status = 1;
                    $newProduct->product_type = 'variable';
                    $newProduct->seo_title = $product->U_Itemgrpname;
                    $newProduct->save();  
                    $product_id = $newProduct->id;
                    

                    $newPD = new ProductDetail;
                    $newPD->product_id = $product_id; 
                    $newPD->VatGourpSa = $product->VatGourpSa;
                    $newPD->QryGroup1 = $product->QryGroup1;
                    $newPD->QryGroup2 = $product->QryGroup2;
                    $newPD->QryGroup3 = $product->QryGroup3;
                    $newPD->QryGroup4 = $product->QryGroup4;
                    $newPD->QryGroup5 = $product->QryGroup5;
                    $newPD->QryGroup6 = $product->QryGroup6;
                    $newPD->QryGroup7 = $product->QryGroup7;
                    $newPD->QryGroup8 = $product->QryGroup8;
                    $newPD->QryGroup9 = $product->QryGroup9;
                    $newPD->QryGroup10 = $product->QryGroup10;
                    $newPD->QryGroup11 = $product->QryGroup11;
                    $newPD->QryGroup12 = $product->QryGroup12;
                    $newPD->QryGroup13 = $product->QryGroup13;
                    $newPD->QryGroup14 = $product->QryGroup14;
                    $newPD->QryGroup15 = $product->QryGroup15;
                    $newPD->QryGroup16 = $product->QryGroup16;
                    $newPD->QryGroup17 = $product->QryGroup17;
                    $newPD->QryGroup18 = $product->QryGroup18;
                    $newPD->QryGroup19 = $product->QryGroup19;
                    $newPD->QryGroup20 = $product->QryGroup20;
                    $newPD->QryGroup21 = $product->QryGroup21;
                    $newPD->QryGroup22 = $product->QryGroup22;
                    $newPD->QryGroup23 = $product->QryGroup23;
                    $newPD->QryGroup24 = $product->QryGroup24;
                    $newPD->QryGroup25 = $product->QryGroup25;
                    $newPD->QryGroup26 = $product->QryGroup26;
                    $newPD->QryGroup27 = $product->QryGroup27;
                    $newPD->QryGroup28 = $product->QryGroup28;
                    $newPD->QryGroup29 = $product->QryGroup29;
                    $newPD->QryGroup30 = $product->QryGroup30;
                    $newPD->QryGroup31 = $product->QryGroup31;
                    $newPD->QryGroup32 = $product->QryGroup32;
                    $newPD->QryGroup33 = $product->QryGroup33;
                    $newPD->QryGroup34 = $product->QryGroup34;
                    $newPD->QryGroup35 = $product->QryGroup35;
                    $newPD->QryGroup36 = $product->QryGroup36;
                    $newPD->QryGroup37 = $product->QryGroup37;
                    $newPD->QryGroup38 = $product->QryGroup38;
                    $newPD->QryGroup39 = $product->QryGroup39;
                    $newPD->QryGroup40 = $product->QryGroup40;
                    $newPD->QryGroup41 = $product->QryGroup41;
                    $newPD->QryGroup42 = $product->QryGroup42;
                    $newPD->QryGroup43 = $product->QryGroup43;
                    $newPD->QryGroup44 = $product->QryGroup44;
                    $newPD->QryGroup45 = $product->QryGroup45;
                    $newPD->QryGroup46 = $product->QryGroup46;
                    $newPD->QryGroup47 = $product->QryGroup47;
                    $newPD->QryGroup48 = $product->QryGroup48;
                    $newPD->QryGroup49 = $product->QryGroup49;
                    $newPD->QryGroup50 = $product->QryGroup50;
                    $newPD->QryGroup51 = $product->QryGroup51;
                    $newPD->QryGroup52 = $product->QryGroup52;
                    $newPD->QryGroup53 = $product->QryGroup53;
                    $newPD->QryGroup54 = $product->QryGroup54;
                    $newPD->QryGroup55 = $product->QryGroup55;
                    $newPD->QryGroup56 = $product->QryGroup56;
                    $newPD->QryGroup57 = $product->QryGroup57;
                    $newPD->QryGroup58 = $product->QryGroup58;
                    $newPD->QryGroup59 = $product->QryGroup59;
                    $newPD->QryGroup60 = $product->QryGroup60;
                    $newPD->QryGroup61 = $product->QryGroup61;
                    $newPD->QryGroup62 = $product->QryGroup62;
                    $newPD->QryGroup63 = $product->QryGroup63;
                    $newPD->QryGroup64 = $product->QryGroup64;
                    $newPD->BuyUnitMsr = $product->BuyUnitMsr;
                    $newPD->SalUnitMsr = $product->SalUnitMsr;
                    $newPD->SuppCatNum = $product->SuppCatNum;
                    $newPD->FirmName = $product->FirmName;
                    $newPD->VatGroupPu = $product->VatGroupPu;
                    $newPD->U_OrgCountCod = $product->U_OrgCountCod;
                    $newPD->U_OrgCountNam = $product->U_OrgCountNam;
                //    $newPD->FirmName = $product->U_Category;
                    $newPD->U_SCartQty = $product->U_SCartQty;
                    $newPD->U_CBM = $product->U_CBM;
                    $newPD->U_CartQty = $product->U_CartQty;
                    $newPD->U_HsCode = $product->U_HsCode;
                    $newPD->U_HsName = $product->U_HsName; 
                    $newPD->save();
                }

                if(!empty($product_id) && !empty($attribute_id) && !empty($attr_variation_id)){
                    $product_Attr_id = array();
                    $paExist = ProductAttribute::where('product_id', '=',  $product_id)->where('attribute_id', '=',   $attribute_id)->where('attribute_variation_id', '=',   $attr_variation_id)->first();
                    if ($paExist === null) {
                        $pvNew = new ProductAttribute;
                        $pvNew->product_id =  $product_id;
                        $pvNew->attribute_id =  $attribute_id;
                        $pvNew->attribute_variation_id =  $attr_variation_id;
                        $pvNew->save();
                        $product_Attr_id[] = $pvNew->id;
                    }else{
                        $product_Attr_id[] = $paExist->id;
                    } 
                    $pvcExist = ProductVariantCombination::where('item_code', '=',  $product->ItemCode)->where('product_id', '=',   $product_id)->first();
                    if( $pvcExist){
                        $pvcExist->item_name = $product->ItemName;
                        $pvcExist->OnHand = $product->OnHand;
                        $pvcExist->IsCommited = $product->IsCommited;
                        $pvcExist->OnOrder = $product->OnOrder;
                        $pvcExist->U_GrossWt = $product->U_GrossWt;
                        $pvcExist->U_NetWt = $product->U_NetWt;
                        $pvcExist->U_Size = $product->U_Size;
                        $pvcExist->SizeName = $apiSizeName;
                        $pvcExist->product_variant_data = json_encode($product_Attr_id); 
                        $pvcExist->save();
                        $icout++;
                    }else{
                        $pvcNew = new ProductVariantCombination;
                        $pvcNew->product_id  = $product_id;
                        $pvcNew->item_code = $product->ItemCode;
                        $pvcNew->sku = $product->ItemCode;
                        $pvcNew->item_name = $product->ItemName;
                        $pvcNew->OnHand = $product->OnHand;
                        $pvcNew->IsCommited = $product->IsCommited;
                        $pvcNew->OnOrder = $product->OnOrder;
                        $pvcNew->U_GrossWt = $product->U_GrossWt;
                        $pvcNew->U_NetWt = $product->U_NetWt;
                        $pvcNew->U_Size = $product->U_Size;
                        $pvcNew->SizeName = $apiSizeName;
                        $pvcNew->product_variant_data = json_encode($product_Attr_id); 
                        $pvcNew->save();
                        $icout++;
                    }
                }else{
                    $errors[] = array("gorupCode" => "The line # ".$product->U_Itemgrp." and # ".$product->ItemCode."  variation not createed");
                    $skipRow++; 
                }
                 
            }else{
                $errors[] = array("gorupCode" => "The line # ".$product->U_Itemgrp." and # ".$product->ItemCode." don't have and goup title is empty");
                $skipRow++;
            }
            
            
        }  
        
        if(!empty($errors)){
            return response()->json(['message'=>'skeep recored '.$skipRow.' out of total '.$totalCount.'successfully inserted '.$icout,'error'=>$errors],200); 
        }else{
            return response()->json(['message'=>'recored  inserted','Total'=>$totalCount],200); 
        }
       
    }

    //Import product proice list for vendor
    public function productPriceImport()
    {
        $xml_data = $this->getXMLData('http://192.168.10.20/api/ItemMaster/GetPriceList');
        $xml = simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);   
        $productPriceListArr = json_decode($xml); 
        /* $ss = array();
        $yeeee = array(); */
        $count = 0;
        $insertcount = 0;
         foreach($productPriceListArr as $productPrice){

            /* if($productPrice->PriceList == 11){
                $productExist = Products::where('sku', '=',  $productPrice->U_Itemgrp)->first();
                if(!empty($productExist)){
                    $productExist->regular_price = ($productPrice->Price * 1.2); 
                    $productExist->save();
                    $insertcount++;
                }
                $count++;
            } */
        /*     if(empty($productPrice->U_Itemgrp)){
                 dd($productPrice);
             }
           $dddddd =  $productPrice->U_Itemgrp.'-'.$productPrice->PriceList;
            if (in_array($dddddd, $ss)){
                $yeeee[]=$dddddd;
            }else{
                $ss[]=$dddddd;
            }   */           
             $priceList = PriceList::where('item_no', '=',  $productPrice->U_Itemgrp)->where('price_list_no', '=',  $productPrice->PriceList)->first();
             if ($priceList === null) {
  
                 $newPL = new PriceList;
                $newPL->item_no = $productPrice->U_Itemgrp;
                $newPL->list_price = $productPrice->Price;
                $newPL->price_list_no = $productPrice->PriceList;
                $newPL->save();    
                /*   PriceList::create([
                    'item_no' => $productPrice->ItemCode,
                    'item_description' =>  $productPrice->ItemName,
                    'list_price' =>  $productPrice->Price,
                    'price_list_no' =>  $productPrice->PriceList,
                 ]);   */
           }       
        } 
     
        return response()->json(['message'=>'totla '. $count.'recored  inserted '. $insertcount],200);
    }
    //Import product proice list for vendor
    public function GetInvoiceDetails(Request $request )
    {   


        $FromDate = '';
        $ToDate = '';
        // if(isset($request->FromDate) && !empty($request->FromDate)){
        //     $FromDate = str_replace( array( '\'', '"',
        //     ',' , ';', '<', '>' ), '',$request->FromDate); ;
        // }
        // if(isset($request->ToDate) && !empty($request->ToDate)){
        //     $ToDate = str_replace( array( '\'', '"',
        //     ',' , ';', '<', '>' ), '',$request->ToDate); ;
        // }
        
        $customerCode = ($request->has('CustomerCode')) ? $request->CustomerCode : '';
        $xml_data = $this->getXMLData('http://192.168.10.20/api/Invoice/GetInvoiceDetails?CustomerCode='.$customerCode.'&FromDate='.$FromDate.'&ToDate'.$ToDate);
        $xml = simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);   
        $invoices = json_decode($xml); 
        
        return response()->json($invoices,200);

    }



    //For Get data from xml file
    public  function getXMLData($url)
    {
        $curl = curl_init();
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "Accept: application/xml",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp; 
    }

    //For Unique slug Start
    public  function getUniqueSlug($type,$name)
    {
        $slugCount = 0;
        $slug = $name;
        do {
            if ($slugCount == 0) {
                $currentSlug = slugify($slug);
            } else {
                $currentSlug = slugify($name . '-' . $slugCount);
            }
            if (Products::where('slug', $currentSlug)->first()) {
                $slugCount++;
            } else {
                $slug = $currentSlug;
                $slugCount = 0;
            }
        } while ($slugCount > 0);
      return $slug;  
    }

    //For get or create brand by name
    public function getBrandID($brandName){
        
        $brand = Brand::where('brand_name', '=',  $brandName)->first();
        if ($brand === null) {
            $newBrand = Brand::create([
                'brand_name' => $brandName
            ]);
            return $newBrand->id;
        }else{
            return $brand->id;
        }
    }


}
