<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Page;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Yajra\DataTables\Facades\DataTables;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_title = 'Page List';
        $page_description = 'Listing All The page...';
        if ($request->ajax()) {
            $pages = Page::all();
            return Datatables::of($pages)
                ->addIndexColumn()
                ->editColumn('status', function ($pageList) {
                    $status = '';
                    if ($pageList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($pageList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($pageList) {
                    return view('admin.pages.pages_datatable', compact('pageList'))->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view("admin.pages.index", compact('page_title', 'page_description'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Page';
        $page_description = 'Add Page...';
        return view('admin.pages.add', compact('page_title', 'page_description'));
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
            'page_name' => 'required',
            'page_description' => 'required'
            //'video' => 'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm',
        ]);
        if ($validator->fails()) {
            return redirect('admin/pages/add')->withErrors($validator)->withInput();
        } else {
            $page = new Page;
            $page->page_name = $request->page_name;

            //For Unique Slug Start
            $slug_count = 0;
            $slug = $pageyName = $request->page_name;
            do {
                if ($slug_count == 0) {
                    $currentSlug = slugify($slug);
                } else {
                    $currentSlug = slugify($pageyName . '-' . $slug_count);
                }
                if (Page::where('slug', $currentSlug)->first()) {
                    $slug_count++;
                } else {
                    $slug = $currentSlug;
                    $slug_count = 0;
                }
            } while ($slug_count > 0);
            $page->slug = $slug;
            //For Unique Slug End

            $page->page_description = $request->page_description;
            $page->banner = $request->banner;
            if ($request->hasFile('banner')) {
                $bannerImage = $request->file('banner');
                $bannerImageSave = uplodImage($bannerImage);
            }
            if (isset($bannerImageSave) && $bannerImageSave != '') {
                $page->banner = $bannerImageSave;
            }
            if ($request->hasFile('video')) {
                $video = $request->file('video');
                $videoSave = uplodImage($video);
            }
            if (isset($videoSave) && $videoSave != '') {
                $page->video = $videoSave;
            }
            $page->seo_title = $request->seo_title;
            $page->seo_description = $request->seo_description;
            $page->seo_keyword = $request->seo_keyword;
            $page->status = $request->status;
            $page->created_by = 1;
            $page->save();
            Session::flash('message', 'Successfully added!');
            return redirect('/admin/pages');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Edit Page';
        $page_description = 'Page info';
        $page_data = Page::find($id);
        if (!empty($page_data)) {
            return view('admin.pages.edit', compact('page_title', 'page_description', 'page_data'));
        } else {
            return redirect('/admin/pages');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        $id = $request->id;
        $request->validate([
            'page_name' => 'required',
            'page_description' => 'required',
            'slug' => [
                'required',
                Rule::unique('pages')->where(function ($query) use ($id) {
                    return $query->where('id', '!=', $id);
                })
            ],
            //'video' => 'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm',
        ]);
        $page = Page::find($request->id);
        $page->page_name = $request->page_name;
        $page->slug = slugify($request->slug);
        $page->page_description = $request->page_description;
        $bannerImage = $request->old_banner;
        $videoFile = $request->old_video;
        if ($request->hasFile('banner')) {
            if ($page->banner != null || $page->banner != '') {
                $destinationPath = $page->banner;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $bannerFile = $request->file('banner');
            $bannerImage = uplodImage($bannerFile);
        }
        if ($request->hasFile('video')) {
            if ($page->video != null || $page->video != '') {
                $destinationPath = $page->video;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $video = $request->file('video');
            $videoFile = uplodImage($video);
        }
        $page->seo_title = $request->seo_title;
        $page->seo_description = $request->seo_description;
        $page->seo_keyword = $request->seo_keyword;
        $page->banner = $bannerImage;
        $page->video = $videoFile;
        $page->status = $request->status;
        $page->created_by = 1;
        $page->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/pages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $pageDelete = Page::findOrFail($id);
        if ($pageDelete->banner != null || $pageDelete->banner != '') {
            $destinationPath = $pageDelete->banner;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        if ($pageDelete->video != null || $pageDelete->video != '') {
            $destinationPath = $pageDelete->video;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        $pageDelete->delete();
        return redirect()->route('pages.index')->with('success', 'Page Deleted successfully !!');
    }
}
