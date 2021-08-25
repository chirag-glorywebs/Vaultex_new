<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use App\Helpers\APIHelpers;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Validation\Rule;
use Session;
use App\Models\Products;
use Illuminate\Http\File;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_title = 'Blog List';
        $page_description = 'Listing All The blogs...';
        $categories = BlogCategory::all();
        if ($request->ajax()) {
            $blogs = Blog::all();
            return DataTables::of($blogs)
                ->addIndexColumn()
                ->editColumn('status', function ($blogList) {
                    $status = '';
                    if ($blogList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($blogList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->editColumn('category', function ($blog) {
                    return empty($blog->blogCategory->category_name) ? '-' : $blog->blogCategory->category_name;
                })
                ->editColumn('blog_image', function ($image) {
                    $imageUrl = asset($image->blog_image);
                    $placeHolderUrl = asset('uploads/product-placeholder.png');
                    if ($image->blog_image != null || $image->blog_image != '') {
                        $destinationPath = $image->blog_image;
                        $fileExists = file_exists($destinationPath);
                        if ($fileExists) {
                            return '<img src="' . $imageUrl . '" width="50" height="50"/>';
                        } else {
                            return '<img src="' . $placeHolderUrl . '" width="50" height="50"/>';
                        }
                    } else {
                        return '<img src="' . $placeHolderUrl . '" width="50" height="50"/>';
                    }
                })
                ->addColumn('action', function ($blogList) {
                    return view('admin.blogs.blogs_datatable', compact('blogList'))->render();
                })
                ->rawColumns(['category', 'blog_image', 'status', 'action'])
                ->make(true);
        }
        return view("admin.blogs.index", compact('page_title', 'page_description', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add New Blog';
        $page_description = 'Blog info';
        $categories = BlogCategory::all();
        return view('admin.blogs.add', compact('page_title', 'page_description', 'categories'));
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
            'blog_name' => 'required',
            'blog_description' => 'required',
            'blog_image' => 'required',
            'blog_date' => 'required',

        ]);
        if ($validator->fails()) {
            return redirect('admin/blogs/add')->withErrors($validator)->withInput();
        } else {
            $blog = new Blog;
            $blog_date = date('Y-m-d', strtotime($request->blog_date));
            $blog->blog_name = $request->blog_name;
            $blog->blog_image = $request->blog_image;
            if ($request->hasFile('blog_image')) {
                $blogImage = $request->file('blog_image');
                $blogImageSave = uplodImage($blogImage);
            }
            if (isset($blogImageSave) && $blogImageSave != '') {
                $blog->blog_image = $blogImageSave;
            }
            if (!empty($request->category_id)) {
                $blog->category_id = $request->category_id;
            }
            if ($request->hasFile('banner')) {
                $bannerImage = $request->file('banner');
                $bannerImageSave = uplodImage($bannerImage);
            }
            if (isset($bannerImageSave) && $bannerImageSave != '') {
                $blog->banner = $bannerImageSave;
            }
            if (!empty($blog_date)) {
                $blog->blog_date = $blog_date;
            }
            //For Unique slug Start
            $slugCount = 0;
            $slug = $blogName = $request->blog_name;
            do {
                if ($slugCount == 0) {
                    $currentSlug = slugify($slug);
                } else {
                    $currentSlug = slugify($blogName . '-' . $slugCount);
                }
                if (Blog::where('slug', $currentSlug)->first()) {
                    $slugCount++;
                } else {
                    $slug = $currentSlug;
                    $slugCount = 0;
                }
            } while ($slugCount > 0);
            $blog->slug = $slug;
            //For Unique slug End
            $blog->blog_description = $request->blog_description;
            $blog->seo_title = $request->seo_title;
            $blog->seo_description = $request->seo_description;
            $blog->seo_keyword = $request->seo_keyword;
            $blog->status = $request->status;
            $blog->created_by = 1;
            $blog->save();
            Session::flash('message', 'Successfully added!');
            return redirect('/admin/blogs');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    function edit($id)
    {
        $page_title = 'Edit Blog';
        $page_description = 'Blog info';
        $blog_data = Blog::find($id);
        $categories = BlogCategory::select('id', 'category_name')->get();
        $blog_date = date('m/d/Y', strtotime($blog_data->blog_date));
        if (!empty($blog_data)) {
            return view('admin.blogs.edit', compact('page_title', 'page_description', 'blog_data', 'categories', 'blog_date'));
        } else {
            return redirect('/admin/blogs');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    function update(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'blog_name' => 'required',
            'blog_description' => 'required',
            'slug' => [
                'required',
                Rule::unique('blogs')->where(function ($query) use ($id) {
                    return $query->where('id', '!=', $id);
                })
            ],
        ]);
        $blog = Blog::find($request->id);
        $blog_date = date('Y-m-d', strtotime($request->blog_date));
        $blog->blog_name = $request->blog_name;
        $blog->slug  = slugify($request->slug);
        $blogImage = $request->get('old_blog_image');
        $bannerImage = $request->get('old_banner');
        /*Update Blog Image*/
        if ($request->hasFile('blog_image')) {
            if ($blog->blog_image != null || $blog->blog_image != '') {
                $destinationPath = $blog->blog_image;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $blog_file = $request->file('blog_image');
            $blogImage = uplodImage($blog_file);
        }
        /*Update Banner Image*/
        if ($request->hasFile('banner')) {
            if ($blog->banner != null || $blog->banner != '') {
                $destinationPath = $blog->banner;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $blog_file = $request->file('banner');
            $bannerImage = uplodImage($blog_file);
        }
        if (!empty($request->category_id)) {
            $blog->category_id = $request->category_id;
        }
        if (!empty($blog_date)) {
            $blog->blog_date = $blog_date;
        }
        $blog->seo_title = $request->seo_title;
        $blog->seo_description = $request->seo_description;
        $blog->seo_keyword = $request->seo_keyword;
        $blog->status = $request->status;
        $blog->created_by = 1;
        $blog->blog_image = $blogImage;
        $blog->banner = $bannerImage;
        $blog->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/blogs');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function destroy($id)
    {
        $blogDelete = Blog::find($id);
        if ($blogDelete->blog_image != null || $blogDelete->blog_image != '') {
            $destinationPath = $blogDelete->blog_image;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        if ($blogDelete->banner != null || $blogDelete->banner != '') {
            $destinationPath = $blogDelete->banner;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        $blogDelete->delete();
    }
}
