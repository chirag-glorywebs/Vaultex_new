<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Validation\Rule;
use Session;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    /* get the all brands*/
    public function get(Request $request)
    {
        $page_title = 'Brand';
        $page_description = 'All brand list page';
        if ($request->ajax()) {
            $brand = Brand::all();
            return Datatables::of($brand)
                ->addIndexColumn()
                ->editColumn('brandLogo', function ($image) {
                        if($image->brand_logo){
                            $url = url($image->brand_logo);
                        }else{
                            $url =  asset('uploads/product-placeholder.png');
                        }
                        return '<img alt="Brand Logo" src="' . $url . '" width="auto" height="80"/>';
                    
                })
                ->editColumn('status', function ($branList) {
                    $status = '';
                    if ($branList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($branList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($branList) {
                    return view('admin.brand.brand_datatale', compact('branList'))->render();
                })
                ->rawColumns(['brandLogo', 'status', 'action'])
                ->make(true);
        }
        return view('admin.brand.brand', compact('page_title', 'page_description'));

    }

    /* add new brand page*/
    public function add()
    {
        $page_title = 'Add Brand';
        $page_description = 'Add brand here';
        return view('admin.brand.add_brand', compact('page_title', 'page_description'));
    }

    /* add new brand in db*/
    public function create(Request $req)
    {
        $req->validate([
            'brand_name' => 'required',
            'brand_description' => 'required',
            'brand_logo' => 'required',
        ]);
        $brand = new Brand;
        $brand->brand_name = $req->brand_name;

        //For Unique Slug Insert Start
        $slug_count = 0;
        $slug = $brandName = $req->brand_name;
        do {
            if ($slug_count == 0) {
                $currentSlug = slugify($slug);
            } else {
                $currentSlug = slugify($brandName . '-' . $slug_count);
            }
            if (Brand::where('slug', $currentSlug)->first()) {
                $slug_count++;
            } else {
                $slug = $currentSlug;
                $slug_count = 0;
            }
        } while ($slug_count > 0);
        $brand->slug = $slug;
        //For Unique Slug Insert End

        $brand->brand_description = $req->brand_description;
        if ($req->hasFile('brand_logo')) {
            $brandImage = $req->file('brand_logo');
            $brandImageSave = uplodImage($brandImage);
        }
        if (isset($brandImageSave) && $brandImageSave != '') {
            $brand->brand_logo = $brandImageSave;
        }
        $brand->display_order = $req->display_order;
        $brand->seo_name = $req->seo_name;
        $brand->seo_description = $req->seo_description;
        $brand->seo_title = $req->seo_title;
        $brand->seo_keyword = $req->seo_keyword;
        $brand->status = $req->status;
        $brand->save();
        Session::flash('message', 'Successfully added!');
        return redirect('/admin/brand');
    }

    /* delete brand*/
    public function delete($id)
    {
        $brand = Brand::find($id);
        if ($brand->brand_logo != null || $brand->brand_logo != '') {
            $destinationPath = $brand->brand_logo;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        $brand->delete();
        return redirect()->route('brand.index')->with('success', 'Brand Deleted successfully !!');
    }

    /* edit brand information*/
    public function edit($id)
    {
        $page_title = 'Edit brand';
        $page_description = 'Edit brand info';
        $brand = Brand::find($id);
        return view('admin.brand.edit_brand', compact('page_title', 'page_description', 'brand'));
    }

    /* update brand information*/
    public function update(Request $req)
    {
        $id = $req->id;
        $req->validate([
            'brand_name' => 'required',
            'brand_description' => 'required',
            'slug' => [
                'required',
                Rule::unique('brands')->where(function ($query) use ($id) {
                    return $query->where('id', '!=', $id);
                })
            ],
        ]);
        $brand = Brand::find($req->id);
        $brand->brand_name = $req->brand_name;
        $brand->slug = slugify($req->slug);
        $brand->brand_description = $req->brand_description;
        $brandImage = $req->get('old_brand_logo');
        if ($req->hasFile('brand_logo')) {
            if ($brand->brand_logo != null || $brand->brand_logo != '') {
                $destinationPath = $brand->brand_logo;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $profilePicFile = $req->file('brand_logo');
            $brandImage = uplodImage($profilePicFile);
        }
        $brand->display_order = $req->display_order;
        $brand->seo_name = $req->seo_name;
        $brand->seo_description = $req->seo_description;
        $brand->seo_title = $req->seo_title;
        $brand->seo_keyword = $req->seo_keyword;
        $brand->status = $req->status;
        $brand->brand_logo = $brandImage;
        $brand->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/brand');
    }
}
