<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikedProduct extends Model
{
    use HasFactory;
    public $timestamps = FALSE;
    protected $table="liked_products";

    public function checkProductExist($pid,$uid)
    {
       return LikedProduct::select('liked_products_id')->where('liked_products_id',$pid)->where('liked_customers_id',$uid)->first();
    }
    public function demoprolist($id)
    {
       return LikedProduct::select('liked_products_id')->where('liked_products_id',$id)->get();
    }
  
}
