<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    public function getParentCategoryName(){
       return $this->belongsTo(Categories::class,'parent_category');
    }

    /* This relationship will only returns one lavel of child item */
    public function categories()
    {
        return $this->hasMany(Categories::class,'parent_category')->select('id','category_name','parent_category','logo','category_description','slug');
    }
    /* This method where we implement recursive relationship */
   /*  public function childCategoires()
    {
        return $this->hasMany(Categories::class,'parent_category')->with('categories')->select('id','category_name','parent_category','logo','category_description','slug');
    } */

   /*  public function parent()
    {
        return $this->belongsTo(Categories::class,'parent_category')->select('id','category_name','parent_category','logo','category_description','slug');
    } */

    public function children()
    {
        return  $this->hasMany(Categories::class,'parent_category')->select('id','category_name','parent_category','logo','category_description','slug')->orderby('category_name','ASC');
        
    }
    // recursive, loads all descendants
    public function childCategoires()
    {
     return $this->children()->with('childCategoires');
    }
}
