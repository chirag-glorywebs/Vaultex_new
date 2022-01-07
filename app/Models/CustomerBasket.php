<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBasket extends Model
{
    use HasFactory;
    protected $table = "customers_basket";

    public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
