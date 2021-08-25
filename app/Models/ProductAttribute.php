<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;
    public $timestamps = FALSE;

    public function attributes()
    {
        return  $this->belongsToMany(Attributes::class, 'attribute_id')->select(array('attribute_name', 'id'));
    }
}
