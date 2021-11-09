<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ColumnFillable;
use Carbon\Carbon;
use Auth;

class ProductCategory extends Model
{
    use HasFactory;   
    use ColumnFillable;

    protected $table = 'product_categories';
    protected $primaryKey = 'id';    


    public static function assignProductCategories($productId, $categoryIdsArr){
        $deletedRows = ProductCategory::where('product_id', $productId)->delete();        
        $requestData = [];
        foreach ($categoryIdsArr as $key => $value) {
            $requestData[$key]['category_id'] = $value;
            $requestData[$key]['product_id'] = $productId;
            $requestData[$key]['status'] = 1;
            $requestData[$key]['created_by'] = Auth::user()->id;
            $requestData[$key]['updated_by'] = Auth::user()->id;
            $requestData[$key]['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $requestData[$key]['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');            
        }
        $productCategoryObj = ProductCategory::insert($requestData);
        return $productCategoryObj;
    }

}
