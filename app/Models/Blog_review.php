<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog_review extends Model
{
    use HasFactory;
    protected $table= "blog_reviews";
    protected $fillable = ['name','email','comments','blog_id','user_id'];
}
