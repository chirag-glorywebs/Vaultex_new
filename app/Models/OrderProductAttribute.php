<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductAttribute extends Model
{
    use HasFactory;
    public $timestamps = FALSE;

    public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }
}
