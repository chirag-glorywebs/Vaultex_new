<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\Page;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Session;

class SlidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_title = 'Sliders List';
        $page_description = 'Listing All The Sliders...';
        if ($request->ajax()) {
            $sliders = Slider::all();
            return DataTables::of($sliders)
                ->addIndexColumn()
                ->editColumn('status', function ($sliderList) {
                    $status = '';
                    if ($sliderList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($sliderList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                 ->editColumn('page_id', function ($sliders) {
                     return empty($sliders->getPage->page_name) ? '-' : $sliders->getPage->page_name;
                 })
                ->editColumn('image', function ($image) {
                    if ($image->image != '') {
                        $url = url($image->image);
                        return '<img alt="Image Not Found" src="' . $url . '" width="50" height="50"/>';
                    } else {
                        return "-";
                    }
                })
                ->addColumn('action', function ($sliderList) {
                    return view('admin.sliders.sliders_datatable', compact('sliderList'))->render();
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }
        return view("admin.sliders.index", compact('page_title', 'page_description'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add New Sliders';
        $page_description = 'Sliders info';
        $pages = Page::all();
        return view('admin.sliders.add', compact('page_title', 'page_description', 'pages'));
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'page_id' => 'required',
            /*'contents' => 'required',*/
        ]);
        if ($validator->fails()) {
            return redirect('admin/sliders/add')->withErrors($validator)->withInput();
        } else {
            $sliders = new Slider();
            $sliders->image = $request->image;
            if ($request->hasFile('image')) {
                $slidersImage = $request->file('image');
                $slidersImageSave = uplodImage($slidersImage);
            }
            if (isset($slidersImageSave) && $slidersImageSave != '') {
                $sliders->image = $slidersImageSave;
            }
            if (!empty($request->page_id)) {
                $sliders->page_id = $request->page_id;
            }

            $sliders->contents = $request->contents;
            $sliders->url = $request->url;
            $sliders->status = $request->status;
            $sliders->save();
            Session::flash('message', 'Successfully added!');
            return redirect('/admin/sliders');
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    function edit($id)
    {
        $page_title = 'Edit Sliders';
        $page_description = 'Sliders info';
        $slidersData = Slider::find($id);
        $pages = Page::select('id', 'page_name')->get();
        if (!empty($slidersData)) {
            return view('admin.sliders.edit', compact('page_title', 'page_description', 'slidersData', 'pages'));
        } else {
            return redirect('/admin/sliders');
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
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'page_id' => 'required',

        ]);
        $sliders = Slider::find($request->id);
        $sliderImage = $request->get('old_image');
        if ($request->hasFile('image')) {
            if ($sliders->image != null || $sliders->image != '') {
                $destinationPath = $sliders->image;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $sliders_file = $request->file('image');
            $sliderImage = uplodImage($sliders_file);
        }
        if (!empty($request->page_id)) {
            $sliders->page_id = $request->page_id;
        }
        $sliders->contents = $request->contents;
        $sliders->url = $request->url;
        $sliders->status = $request->status;
        $sliders->image = $sliderImage;
        $sliders->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/sliders');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    function destroy($id)
    {
        $slidersDelete = Slider::find($id);
        if ($slidersDelete->image != null || $slidersDelete->image != '') {
            $destinationPath = $slidersDelete->image;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        $slidersDelete->delete();
    }
}
