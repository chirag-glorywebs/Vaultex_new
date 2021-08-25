<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;
    public function getPage()
    {
        return $this->belongsTo(Page::class, 'page_id')->select(array('page_name'));
    }
}
