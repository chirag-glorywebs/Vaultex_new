<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_training_videos extends Model
{
    use HasFactory;

    protected $table = "product_training_videos";

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
