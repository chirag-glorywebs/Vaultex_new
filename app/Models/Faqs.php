<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faqs extends Model
{
    use HasFactory;

    protected $table = "faqs";
    protected $fillable = ['title', 'description', 'proid'];

    public function faqDetails($id)
    {
        return Faqs::select('id', 'title', 'description')->where('proid', $id)->get();
    }

    public $import
        = [
            'fields' => [
                'sku' => [
                    'displayName' => 'sku',
                ],
                'title' => [
                    'displayName' => 'title',
                ],
                'description' => [
                    'displayName' => 'description',
                ],
            ],
        ];

}
