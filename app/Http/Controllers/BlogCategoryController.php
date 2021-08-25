<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Yajra\DataTables\Facades\DataTables;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_title = 'Blog Categories';
        $page_description = 'Listing All The blogs...';
        if ($request->ajax()) {
            $blogCategoryList = BlogCategory::all();
            return DataTables::of($blogCategoryList)
                ->addIndexColumn()
                ->editColumn('status', function ($blogCategoryList) {
                    $status = '';
                    if ($blogCategoryList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($blogCategoryList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->editColumn('parentBlogCategoryName', function ($parentBlogCategory) {
                    return empty ($parentBlogCategory->getParentBlogCategoryName->category_name) ? '-' : $parentBlogCategory->getParentBlogCategoryName->category_name;
                })
                ->editColumn('logoImage', function ($image) {
                    
                        if($image->category_image){
                            $url = url($image->category_image);
                        }else{
                            $url =  asset('uploads/product-placeholder.png');
                        }
                        return '<img src="' . $url . '" width="50" height="50"/>';
                    
                })
                ->addColumn('action', function ($blogCategoryList) {
                    return view('admin.blogcategories.blog_category_datatable', compact('blogCategoryList'))->render();
                })
                ->rawColumns(['parentBlogCategoryName', 'logoImage', 'status', 'action'])
                ->make(true);
        }
        return view("admin.blogcategories.index", compact('page_title', 'page_description'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Blog Category';
        $page_description = 'Add Blog Category...';
        $category = BlogCategory::all();
        return view('admin.blogcategories.add', compact('page_title', 'page_description', 'category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'category_description' => 'required',
            'category_image' => 'required',
            'display_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return redirect('admin/blogcategories/add')->withErrors($validator)->withInput();
        } else {
            $category = new BlogCategory;
            $category->category_name = $request->category_name;
            if (!empty($request->parent_id)) {
                $category->parent_id = $request->parent_id;
            }
            $category->category_description = $request->category_description;
            if ($request->hasFile('category_image')) {
                $categoryImage = $request->file('category_image');
                $categoryImageSave = uplodImage($categoryImage);
            }
            if (isset($categoryImageSave) && $categoryImageSave != '') {
                $category->category_image = $categoryImageSave;
            }
            if ($request->hasFile('banner')) {
                $bannerImage = $request->file('banner');
                $bannerImageSave = uplodImage($bannerImage);
            }
            if (isset($bannerImageSave) && $bannerImageSave != '') {
                $category->banner = $bannerImageSave;
            }
            if (!empty($request->display_order)) {
                $category->display_order = $request->display_order;
            }
            //For Unique Slug Start
            $slugCount = 0;
            $slug = $categoryName = $request->category_name;
            do {
                if ($slugCount == 0) {
                    $currentSlug = slugify($slug);
                } else {
                    $currentSlug = slugify($categoryName . '-' . $slugCount);
                }
                if (BlogCategory::where('slug', $currentSlug)->first()) {
                    $slugCount++;
                } else {
                    $slug = $currentSlug;
                    $slugCount = 0;
                }
            } while ($slugCount > 0);
            $category->slug = $slug;
            //For Unique Slug End
            $category->seo_title = $request->seo_title;
            $category->seo_description = $request->seo_description;
            $category->seo_keyword = $request->seo_keyword;
            $category->status = $request->status;
            $category->created_by = 1;
            $category->save();
            Session::flash('message', 'Successfully added!');
            return redirect('/admin/blogcategories');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Edit Blog Category';
        $page_description = 'Blog Category info';
        $category_data = BlogCategory::find($id);
        $parentcategory = BlogCategory::select('id', 'category_name')->where('id', '!=', $id)->get();
        if (!empty($category_data)) {
            return view('admin.blogcategories.edit', compact('page_title', 'page_description', 'category_data', 'parentcategory'));
        } else {
            return redirect('/admin/blogcategories');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'category_name' => 'required',
            'category_description' => 'required',
            'display_order' => 'nullable|integer',
            'slug' => [
                'required',
                Rule::unique('blog_categories')->where(function ($query) use ($id) {
                    return $query->where('id', '!=', $id);
                })
            ],
        ]);
        $category = BlogCategory::find($request->id);
        $bannerImage = $request->get('old_banner');
        if ($request->hasFile('banner')) {
            if ($category->banner != null || $category->banner != '') {
                $destinationPath = $category->banner;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $bannerFile = $request->file('banner');
            $bannerImage = uplodImage($bannerFile);
        }
        $categoryImage = $request->get('old_category_image');
        if ($request->hasFile('category_image')) {
            if ($category->category_image != null || $category->category_image != '') {
                $destinationPath = $category->category_image;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $profilePicFile = $request->file('category_image');
            $categoryImage = uplodImage($profilePicFile);
        }
        if (!empty($request->parent_id)) {
            $category->parent_id = $request->parent_id;
        }
        if (!empty($request->display_order)) {
            $category->display_order = $request->display_order;
        }
        $category->slug = slugify($request->slug);
        $category->seo_title = $request->seo_title;
        $category->seo_description = $request->seo_description;
        $category->seo_keyword = $request->seo_keyword;
        $category->status = $request->status;
        $category->created_by = 1;
        $category->banner = $bannerImage;
        $category->category_image = $categoryImage;
        $category->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/blogcategories');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $blogCatDelete = BlogCategory::findOrFail($id);
        if ($blogCatDelete->banner != null || $blogCatDelete->banner != '') {
            $destinationPath = $blogCatDelete->banner;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        if ($blogCatDelete->category_image != null || $blogCatDelete->category_image != '') {
            $destinationPath = $blogCatDelete->category_image;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        $blogCatDelete->delete();
        return redirect()->route('blogcategories.index')->with('success', 'Blogs Deleted successfully !!');
    }
}
