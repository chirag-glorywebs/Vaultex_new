<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Session; 
use App\Models\ProductVariantCombination;
 

class ProductVariantController extends Controller
{ 
     

    /* Insert*/ 
    public function create(Request $request)

    {
        $itemData = array();
        $rules = [   
            'product_id' => 'required|nullable',
            'noOfVariation'=>'required',
            'quantity'=>'required|integer',
            'sku'=>'required|unique:product_variant_combinations'
        ]; 
        for($i = 1; $i <= $request->noOfVariation; $i ++){
            $rules['variation_id'.$i] = 'required';
            $itemData[] =  (int) $request['variation_id'.$i];
        }
          

        $request->validate($rules);

        $data = [
            'product_id' =>  $request->product_id
        ];
        
        for($i = 1; $i <= $request->noOfVariation; $i ++){
            $data['variation_id'.$i] =  $request['variation_id'.$i];
        }
        
        $data['product_variant_data'] =  json_encode($itemData); 
        
        $findData = DB::table('product_variant_combinations')->where([['product_id', '=', $request->product_id],['product_variant_data', '=', $data['product_variant_data']]])->first(); 
        
        
        if(!empty($findData)){
            Session::flash('error', 'Product Variant already Created');
            return redirect()->back()->withInput(); 
        }
        $productData = DB::table('products')
                ->where('id',$request->product_id)->first(); 
        
        $itmeNameSufix ='';
        foreach($itemData as $id){
            $variationData = DB::table('attributes_variations')
                ->select('attribute_id','variation_name')
                ->where('id',$id)->first(); 
            if(!empty($variationData) && ($variationData->variation_name)){
                $itmeNameSufix .=$variationData->variation_name;
            }
        }    
        $data['item_name'] =   $productData->product_name.' - '.$itmeNameSufix;
         
        $newVariant = new ProductVariantCombination;
        $newVariant->product_id = $request->product_id;
        $newVariant->product_variant_data = $data['product_variant_data'];
        $newVariant->sku = $request->sku;
        /* $newVariant->variation_id1 = $data['variation_id1'];
        $newVariant->variation_id2 = $data['variation_id2'];  */
        $newVariant->item_name = $data['item_name'];
        $newVariant->OnHand = $request->quantity;
        $result = $newVariant->save();

        //$result =  DB::table('product_variant_combinations')->insert($data);  

        if($result){
            Session::flash('success', 'Product Variant Created');
            return redirect('/admin/products/edit/' . $request->product_id . '?tab=3A')->with('variantId', $newVariant->id);
        }else{
            return response()->json($request, 404);
        }
    } 
                              
    /* Update */
    public function update(Request $request)

    {
        $data  = array();
        $rules = [   
            'id' => 'required|nullable',
            'product_id'=>'required|nullable',
            'item_name'=>'required',
            'Quantity'=>'required',
            'item_code'=>'required',
            'sku'=>'required|unique:product_variant_combinations'. ',id,' . $request->id
            
        ]; 
        $request->validate($rules);
        $data['item_name'] =   $request->item_name;
        $data['item_code'] =   $request->item_code;
        $data['OnHand'] =   $request->Quantity;
        $data['sku'] =   $request->sku;
        $data['U_GrossWt'] =   $request->U_GrossWt;
        $data['U_NetWt'] =   $request->U_NetWt;
        $data['updated_at'] = now();
        
        $result =  DB::table('product_variant_combinations')
                ->where('id',$request->id)
                ->update($data);

        if($result){
            Session::flash('success', 'Product Variant Updated');
            return redirect('/admin/products/edit/' . $request->product_id . '?tab=3A')->with('variantId', $request->id);
        }else{
            return response()->json($request, 404);
        } 
    }

    /* Delete */
    public function destroy($id){
        $productVariantCombination = ProductVariantCombination::find( $id );
        $result = $productVariantCombination->forceDelete();
        if($result){  
            return response()->json($result, 200);
        }  
    }
    
}
