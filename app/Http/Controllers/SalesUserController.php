<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Models\User;
use Session;
use Yajra\DataTables\Facades\DataTables;

class SalesUserController extends Controller
{
    /* get the all sales users*/
    public function get(Request $request)
    {
        $page_title = 'Sales Users';
        $page_description = 'All sales users list page';
        if ($request->ajax()) {
            $salesUsers = User::where([['user_role', '=', '2']])->get();
            return Datatables::of($salesUsers)
                ->addIndexColumn()
                ->editColumn('profileImage', function ($image) {
                    $imageUrl = asset($image->profilepic);
                    $placeHolderUrl = asset('uploads/product-placeholder.png');
                    if ($image->profilepic != null || $image->profilepic != '') {
                        $destinationPath = $image->profilepic;
                        $fileExists = file_exists($destinationPath);
                        if ($fileExists) {
                            return '<img src="' . $imageUrl . '" width="50" height="50"/>';
                        } else {
                            return '<img src="' . $placeHolderUrl . '" width="50" height="50"/>';
                        }
                    }
                    else {
                        return '<img src="' . $placeHolderUrl . '" width="50" height="50"/>';
                    }
                })
                ->editColumn('status', function ($salesUserList) {
                    $status = '';
                    if ($salesUserList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($salesUserList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($salesUserList) {
                    return view('admin.users.sales_users_datatable', compact('salesUserList'))->render();
                })
                ->rawColumns(['profileImage', 'status', 'action'])
                ->make(true);
        }
        return view('admin.users.sales_users', compact('page_title', 'page_description'));
    }

    /* add new sales users page*/
    public function add()
    {
        $page_title = 'Add Sales User';
        $page_description = 'Add sales users here';
        return view('admin.users.add_sales_user', compact('page_title', 'page_description'));
    }

    /* add new sales users in db*/
    public function create(Request $req)
    {
        $req->validate([
            'firstname' => 'required',
           /*  'lastname' => 'required', */
            'email' => 'required',
            'vendor_code' => 'unique:users',
            
           /*  'phone' => 'required', */
           /*  'address' => 'required',
            'profilepic' => 'required', */
        ]);
        $req->password = 'sales@123';
        $salesusers = new User;
        $salesusers->first_name = $req->firstname;
        $salesusers->last_name = $req->lastname;
        $salesusers->email = $req->email;
        $salesusers->phone = $req->phone;
        $salesusers->password = bcrypt($req->password);
        $salesusers->address = $req->address;
        $salesusers->vendor_code = $req->vendor_code;
        if ($req->hasFile('profilepic')) {
            $profileImage = $req->file('profilepic');
            $profileImageSave = uplodImage($profileImage);
        }
        if (isset($profileImageSave) && $profileImageSave != '') {
            $salesusers->profilepic = $profileImageSave;
        }
        $salesusers->status = $req->status;
        $salesusers->user_role = 2;
        $salesusers->save();
        Session::flash('message', 'Successfully added!');
        return redirect('/admin/salesuser');
    }

    /* delete user*/
    public function delete($id)
    {
        $salesusers = User::findOrFail($id);
        if ($salesusers->profilepic != null || $salesusers->profilepic != '') {
            $destinationPath = $salesusers->profilepic;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        $salesusers->delete();
        return redirect()->route('admin.salesuser')->with('success', 'Sales User Deleted successfully !!');
    }

    /* edit user information*/
    public function edit($id)
    {
        $page_title = 'Edit Sales Users';
        $page_description = 'Edit user';
        $salesusers = User::find($id);
        return view('admin.users.edit_sales_user', ['data' => $salesusers], compact('page_title', 'page_description'));

    }

    /* update user information*/
    public function update(Request $req)
    {
        $req->validate([
            'firstname' => 'required',
           /*  'lastname' => 'required', */
            'email' => 'required',
            'vendor_code' => 'required',
           /*  'phone' => 'required',
            'address' => 'required' */
        ]);
        $salesusers = User::find($req->id);
        $salesusers->first_name = $req->firstname;
        $salesusers->last_name = $req->lastname;
        $salesusers->email = $req->email;
        $salesusers->phone = $req->phone;
        $salesusers->address = $req->address;
        $salesusers->vendor_code = $req->vendor_code;
        $profilePicImage = $req->get('old_profilepic');
        if ($req->hasFile('profilepic')) {
            if ($salesusers->profilepic != null || $salesusers->profilepic != '') {
                $destinationPath = $salesusers->profilepic;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $profilePicFile = $req->file('profilepic');
            $profilePicImage = uplodImage($profilePicFile);
        }
        $salesusers->status = $req->status;
        $salesusers->user_role = 2;
        $salesusers->profilepic = $profilePicImage;
        $salesusers->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/salesuser');
    }
}
