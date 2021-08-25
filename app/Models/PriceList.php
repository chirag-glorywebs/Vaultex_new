<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory;
    protected $table = 'price_lists';
    protected $fillable = ['item_no','item_description','list_price','price_list_no'];
    //protected $fillable = ['sku','description','price','price_list_no'];
    public $timestamps = true;
}
