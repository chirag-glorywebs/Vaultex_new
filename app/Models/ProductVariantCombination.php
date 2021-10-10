<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariantCombination extends Model
{
    use HasFactory;
    // use SoftDeletes; 
    protected $table = "product_variant_combinations";

    // protected $dates = [ 'deleted_at' ];
}
