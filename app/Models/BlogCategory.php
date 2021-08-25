<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class BlogCategory extends Model
{
    use HasFactory;
    protected $table = 'blog_categories';
    protected $fillable = [
        'category_name',
        'category_description'
    ];
    public function getParentBlogCategoryName(){
        return $this->belongsTo(BlogCategory::class,'parent_id');
    }
}
