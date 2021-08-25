<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Models\Attributes;
use App\Models\Attributes_variations;
use Session;
use Yajra\DataTables\Facades\DataTables;

class AttributesController extends Controller
{
    /* get the all attributes*/
    public function get(Request $request)
    {
        $page_title = 'Attributes';
        $page_description = 'All attributes list page';
        if ($request->ajax()) {
            $attributes = Attributes::all();
            return Datatables::of($attributes)
                ->addIndexColumn()
                ->editColumn('status', function ($attributeList) {
                    $status = '';
                    if ($attributeList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($attributeList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($attributeList) {
                    return view('admin.attributes.attribute_datatable', compact('attributeList'))->render();
                })->addColumn('add_variation', function ($attributeList) {
                    return view('admin.attributes.add_variation_datatable', compact('attributeList'))->render();
                })->addColumn('list_variation', function ($attributeList) {
                    return view('admin.attributes.variation_list_datatable', compact('attributeList'))->render();
                })
                ->rawColumns(['status', 'action', 'add_variation', 'list_variation'])
                ->make(true);
        }
        return view('admin.attributes.attributes', compact('page_title', 'page_description'));
    }

    /* add new attributes page*/
    public function add()
    {
        $page_title = 'Add attribute';
        $page_description = 'Add attribute here';
        return view('admin.attributes.add_attributes', compact('page_title', 'page_description'));
    }

    /* add new attributes in db*/
    public function create(Request $req)
    {
        $req->validate([
            'attribute_name' => 'required'
        ]);
        $attributes = new Attributes;
        $attributes->attribute_name = $req->attribute_name;
        $attributes->status = $req->status;
        $attributes->save();
        Session::flash('message', 'Successfully added!');
        return redirect('/admin/attributes');
    }

    /* delete attributes*/
    public function delete($id)
    {
        $attributes = Attributes::find($id);
        $attributes->delete();
        Session::flash('message', 'Successfully delete!');
        return redirect('/admin/attributes');
    }

    /* edit attributes information*/
    public function edit($id)
    {
        $page_title = 'Edit attribute';
        $page_description = 'Edit attribute info';
        $attributes = Attributes::find($id);
        return view('admin.attributes.add_attributes', compact('page_title', 'page_description', 'attributes'));
    }

    /* update attributes information*/
    public function update(Request $req)
    {
        $req->validate([
            'attribute_name' => 'required'
        ]);
        $attributes = Attributes::find($req->id);
        $attributes->attribute_name = $req->attribute_name;
        $attributes->status = $req->status;
        $attributes->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/attributes');
    }

    /* get the all variations*/
    public function getVariation(Request $request, $attid)
    {
        $attid = $attid;
        $attributes = Attributes::find($attid);
        $page_title = $attributes->attribute_name . ' variation';
        $page_description = 'All ' . $attributes->attribute_name . ' variations list page';
        if ($request->ajax()) {
            $variations = Attributes_variations::where([['attribute_id', '=', $attid]])->get();
            return Datatables::of($variations)
                ->addIndexColumn()
                ->editColumn('status', function ($variationsList) {
                    $status = '';
                    if ($variationsList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($variationsList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($variationsList) {
                    return view('admin.attributes.variations_datatable', compact('variationsList'))->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.attributes.variations', compact('page_title', 'page_description', 'attid'));
    }

    /* add new variations page*/
    public function addVariation($attid)
    {
        $attributes = Attributes::find($attid);
        $page_title = 'Add ' . $attributes->attribute_name . ' variations';
        $page_description = 'Add ' . $attributes->attribute_name . ' variations here';
        $attid = $attid;
        return view('admin.attributes.add_variation', compact('page_title', 'page_description', 'attid'));
    }

    /* add new variations in db*/
    public function createVariation(Request $req)
    {
        $req->validate([
            'variation_name' => 'required'
        ]);
        $variations = new Attributes_variations;
        $variations->attribute_id = $req->attid;
        $variations->variation_name = $req->variation_name;
        $variations->status = $req->status;
        $variations->save();
        Session::flash('message', 'Successfully added!');
        return redirect('/admin/attributes/' . $req->attid . '/variations');
    }

    /* delete variations*/
    public function deleteVariation($attid, $id)
    {
        $variations = Attributes_variations::find($id);
        $variations->delete();
        Session::flash('message', 'Successfully delete!');
        return redirect('/admin/attributes/' . $attid . '/variations');
    }

    /* edit variations information*/
    public function editVariation($attid, $id)
    {
        $attributes = Attributes::find($attid);
        $page_title = 'Edit ' . $attributes->attribute_name . ' variation';
        $page_description = 'Edit ' . $attributes->attribute_name . ' variations info';
        $variations = Attributes_variations::find($id);
        $attid = $attid;
        return view('admin.attributes.add_variation', compact('page_title', 'page_description', 'variations', 'attid'));
    }

    /* update variation information*/
    public function updateVariation(Request $req)
    {
        $req->validate([
            'variation_name' => 'required'
        ]);
        $variations = Attributes_variations::find($req->id);
        $variations->attribute_id = $req->attid;
        $variations->variation_name = $req->variation_name;
        $variations->status = $req->status;
        $variations->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/attributes/' . $req->attid . '/variations');
    }
}
