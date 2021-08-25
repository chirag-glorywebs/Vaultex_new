<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkOrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_or_category_details','quantity','brand'
    ];
    public $timestamps = true;
}
