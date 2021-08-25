<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBasketAttribute extends Model
{
    use HasFactory;
    public $timestamps = FALSE;
    protected $table = "customers_basket_attributes";

}
