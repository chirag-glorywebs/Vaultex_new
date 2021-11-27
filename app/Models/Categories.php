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
        return  $this->hasMany(Categories::class,'parent_category')->select('id','category_name','parent_category','logo','category_description','slug')->orderby('display_order','ASC');
        
    }
    // recursive, loads all descendants
    public function childCategoires()
    {
     return $this->children()->with('childCategoires')->WHERE('status',1);
    }


    // One level parent
    public function parent() {
        return $this->belongsTo(Categories::class,'parent_category')->select(['id', 'category_name', 'slug', 'parent_category']);
    }

    // Recursive parents
    public function parents() {
        return $this->belongsTo(Categories::class, 'parent_category')->select(['id', 'category_name', 'slug', 'parent_category'])
          			->with('parent');
    }

    // Recursive catParents
    public function catParents() {
        return $this->belongsTo(Categories::class, 'parent_category')->select(['id', 'category_name', 'slug', 'parent_category'])
          			->with('catParents');
    }

}
