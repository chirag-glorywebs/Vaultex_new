<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Models\Categories;
use Illuminate\Validation\Rule;
use Session;
use Yajra\DataTables\Facades\DataTables;

class CategoriesController extends Controller
{
    /* get the all category*/
    public function get(Request $request)
    {
        $page_title = 'Categories';
        $page_description = 'All category list page';
        if ($request->ajax()) {
            $category = Categories::all();
            return DataTables::of($category)
                ->addIndexColumn()
                ->editColumn('status', function ($categoryList) {
                    $status = '';
                    if ($categoryList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($categoryList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->editColumn('parentCategoryName', function ($parentCategory) {
                    return empty ($parentCategory->getParentCategoryName->category_name) ? '-' : $parentCategory->getParentCategoryName->category_name;
                })
                ->editColumn('logoImage', function ($image) {
                 
 
                        if($image->logo){
                            $url = url($image->logo);
                        }else{
                            $url =  asset('uploads/product-placeholder.png');
                        }
                        return '<img alt="Category Image" src="' . $url . '" width="auto" height="80"/>';
                     
                })
                ->addColumn('action', function ($categoryList) {
                    return view('admin.category.category_datatable', compact('categoryList'))->render();
                })
                ->rawColumns(['parentCategoryName', 'logoImage', 'status', 'action'])
                ->make(true);
        }
        return view('admin.category.category', compact('page_title', 'page_description'));
    }

    /* add new category page*/
    public function add()
    {
        $page_title = 'Add category';
        $page_description = 'Add category here';
     /*    $category = Categories::all(); */

        $category = Categories::with('childCategoires')->whereNull('parent_category')->select('id','category_name','parent_category')->where('status',1)->orderby('category_name','ASC')->get(); 
       return view('admin.category.add_category', compact('page_title', 'page_description', 'category'));
    }

    /* add new category in db*/
    public function create(Request $req)
    {
        $req->validate([
            'category_name' => 'required',
            'category_description' => 'required',
            'logo' => 'required'
        ]);
        $category = new Categories;
        $category->category_name = $req->category_name;
        //For Unique Slug Start
        $slug_count = 0;
        $slug = $categoryName = $req->category_name;
        do {
            if ($slug_count == 0) {
                $currentSlug = slugify($slug);
            } else {
                $currentSlug = slugify($categoryName . '-' . $slug_count);
            }
            if (Categories::where('slug', $currentSlug)->first()) {
                $slug_count++;
            } else {
                $slug = $currentSlug;
                $slug_count = 0;
            }
        } while ($slug_count > 0);
        $category->slug = $slug;
        //For Unique Slug End
        $category->parent_category = $req->parent_category;
        $category->category_description = $req->category_description;
        if ($req->hasFile('logo')) {
            $logoImage = $req->file('logo');
            $logoImageSave = uplodImage($logoImage);
        }
        if (isset($logoImageSave) && $logoImageSave != '') {
            $category->logo = $logoImageSave;
        }
        if ($req->hasFile('banner')) {
            $logoImage = $req->file('banner');
            $logoImageSave = uplodImage($logoImage);
        }
        if (isset($logoImageSave) && $logoImageSave != '') {
            $category->banner = $logoImageSave;
        }
        $category->display_order = $req->display_order;
        $category->seo_name = $req->seo_name;
        $category->seo_description = $req->seo_description;
        $category->seo_title = $req->seo_title;
        $category->seo_keyword = $req->seo_keyword;
        $category->status = $req->status;
        $category->save();
        Session::flash('message', 'Successfully added!');
        return redirect('/admin/category');
    }

    /* delete category*/
    public function delete($id)
    {
        $category = Categories::find($id);
        if ($category->logo != null || $category->logo != '') {
            $destinationPath = $category->logo;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        if ($category->banner != null || $category->banner != '') {
            $destinationPath = $category->banner;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        $category->delete();
        return redirect()->route('category.index')->with('success', 'Category Deleted successfully !!');
    }

    /* edit category information*/
    public function edit($id)
    {
        $page_title = 'Edit category';
        $page_description = 'Edit category info';
        $category = Categories::find($id);
       /*  $parentcategory = Categories::select('id', 'category_name')->get(); */
        $parentcategory = Categories::with('childCategoires')->whereNull('parent_category')->select('id','category_name','parent_category')->where('status',1)->orderby('category_name','ASC')->get(); 
        return view('admin.category.edit_category', compact('page_title', 'page_description', 'category', 'parentcategory'));
    }

    /* update category information*/
    public function update(Request $req)
    {
        $id = $req->id;
        $req->validate([
            'category_name' => 'required',
            'category_description' => 'required',
            'slug' => [
                'required',
                Rule::unique('categories')->where(function ($query) use ($id) {
                    return $query->where('id', '!=', $id);
                })
            ],
        ]);
        $category = Categories::find($req->id);
        $category->category_name = $req->category_name;
        $category->slug = slugify($req->slug);
        $category->parent_category = $req->parent_category;
        $category->category_description = $req->category_description;
        $logoImage = $req->get('old_logo');
        if ($req->hasFile('logo')) {
            if ($category->logo != null || $category->logo != '') {
                $destinationPath = $category->logo;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $logoFile = $req->file('logo');
            $logoImage = uplodImage($logoFile);
        }
        $bannerImage = $req->get('old_banner');
        if ($req->hasFile('banner')) {
            if ($category->banner != null || $category->banner != '') {
                $destinationPath = $category->banner;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $bannerFile = $req->file('banner');
            $bannerImage = uplodImage($bannerFile);
        }
        $category->display_order = $req->display_order;
        $category->seo_name = $req->seo_name;
        $category->seo_description = $req->seo_description;
        $category->seo_title = $req->seo_title;
        $category->seo_keyword = $req->seo_keyword;
        $category->status = $req->status;
        $category->logo = $logoImage;
        $category->banner = $bannerImage;
        $category->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/category');
    }
}
