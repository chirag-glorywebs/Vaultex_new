<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['product_id','QryGroup1','QryGroup2','QryGroup3','QryGroup4','QryGroup5','SizeCd','SizeName','U_SCartQty','CBM','OnHand','U_Itemgrp','U_Itemgrpname','U_OrgCountCod','FirmCode','FirmName'];
}
