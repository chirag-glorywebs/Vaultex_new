<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_feature_videos extends Model
{
    use HasFactory;
    protected $table = "product_feature_videos";
    public $import
        = [
            'fields' => [
                'sku' => [
                    'displayName' => 'sku',
                ],
                'name' => [
                    'displayName' => 'name',
                ],
                'video' => [
                    'displayName' => 'video',
                ],
            ],
        ];
}
