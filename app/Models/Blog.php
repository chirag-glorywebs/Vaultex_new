<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Blog extends Model
{
    use HasFactory;

    public function blogCategory()
    { 
        return  $this->belongsTo(BlogCategory::class, 'category_id')->select(array('category_name', 'slug'));
        
    }
 }

