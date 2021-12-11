<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Session;
use Helper;

use Yajra\DataTables\Facades\DataTables;


class VendorUserController extends Controller
{
    /* get the all vendor users*/
    public function get(Request $request)
    {
        $page_title = 'Vendor Users';
        $page_description = 'All vendor users list page';
        if ($request->ajax()) {
            $vendorUsers = User::where([['user_role', '=', Helper::getRollId('VENDOR')]]);
            return Datatables::of($vendorUsers)
                ->addIndexColumn()
                ->editColumn('profileImage', function ($image) {
                    if ($image->profilepic != '') {
                        $url = url($image->profilepic);
                        return '<img alt="Profile Image Not Found" src="' . $url . '" width="50" height="50"/>';
                    } else {
                        return "-";
                    }
                })
                ->editColumn('status', function ($vendorUserList) {
                    $status = '';
                    if ($vendorUserList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($vendorUserList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($vendorUserList) {
                    return view('admin.users.vendor_users_datatable', compact('vendorUserList'))->render();
                })
                ->rawColumns(['profileImage', 'status', 'action'])
                ->make(true);
        }
        return view('admin.users.vendor_users', compact('page_title', 'page_description'));
    }

    /* add new vendor users page*/
    public function add()
    {
        $page_title = 'Add Vendor User';
        $page_description = 'Add vendor users here';
        $salesusers = User::select('id', 'first_name', 'last_name')->where([['user_role', '=', '2'], ['status', '=', '1']])->get();
        return view('admin.users.add_vendor_user', compact('page_title', 'page_description', 'salesusers'));
    }

    /* add new vendor users in db*/
    public function create(Request $req)
    {
        $req->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'profilepic' => 'required',
            'salesperson' => 'required',
            'enterprise_name' => 'required',
            'industry' => 'required',
            'business_exp' => 'required',
            'sales' => 'required',
            'turn_over' => 'required',
            'payment_mode' => 'required',
            'business_logo' => 'required'
        ]);
        $vendorusers = new User;
        $vendorusers->first_name = $req->firstname;
        $vendorusers->last_name = $req->lastname;
        $vendorusers->email = $req->email;
        $vendorusers->phone = $req->phone;
        $vendorusers->mobile = $req->mobile;
        $vendorusers->address = $req->address;
        if ($req->hasFile('profilepic')) {
            $profileImage = $req->file('profilepic');
            $profileImageSave = uplodImage($profileImage);
        }
        if (isset($profileImageSave) && $profileImageSave != '') {
            $vendorusers->profilepic = $profileImageSave;
        }
        $vendorusers->salesperson = $req->salesperson;

        $vendorusers->enterprise_name = $req->enterprise_name;
        $vendorusers->industry = $req->industry;
        $vendorusers->business_exp = $req->business_exp;
        $vendorusers->sales = $req->sales;
        $vendorusers->turn_over = $req->turn_over;
        $vendorusers->payment_mode = $req->payment_mode;
        $vendorusers->downloadable = $req->downloadable;
        $vendorusers->payment_interval = $req->payment_interval;
        if ($req->hasFile('business_logo')) {
            $businessLogo = $req->file('business_logo');
            $businessLogoSave = uplodImage($businessLogo);
        }
        if (isset($businessLogoSave) && $businessLogoSave != '') {
            $vendorusers->business_logo = $businessLogoSave;
        }
        $vendorusers->status = $req->status;
        $vendorusers->vendor_code = $req->vendor_code;
        $vendorusers->price_list_no = $req->price_list_no;
        $vendorusers->vendor_credit_limit = $req->vendor_credit_limit;
        $vendorusers->user_role = 3;
        $vendorusers->save();
        Session::flash('message', 'Successfully added!');
        return redirect('/admin/vendoruser');
    }

    /* delete user*/
    public function delete($id)
    {
        $vendorusers = User::find($id);
        if ($vendorusers->profilepic != null || $vendorusers->profilepic != '') {
            $destinationPath = $vendorusers->profilepic;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        if ($vendorusers->business_logo != null || $vendorusers->business_logo != '') {
            $destinationPath = $vendorusers->business_logo;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        $vendorusers->delete();
        return redirect()->route('vendoruser.index')->with('success', 'Vendor User Deleted successfully !!');
    }

    /* edit user information*/
    public function edit($id)
    {
        $page_title = 'Edit Vendor Users';
        $page_description = 'Edit user';
        $vendorusers = User::find($id);
        $salesusers = User::select('id', 'first_name', 'last_name')->where([['user_role', '=', '2'], ['status', '=', '1']])->get();
        return view('admin.users.edit_vendor_user', ['data' => $vendorusers], compact('page_title', 'page_description', 'salesusers'));
    }

    /* update user information*/
    public function update(Request $req)
    {
        $req->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'salesperson' => 'required',
            'enterprise_name' => 'required',
            'industry' => 'required',
            'business_exp' => 'required',
            'sales' => 'required',
            'turn_over' => 'required',
            'payment_mode' => 'required',
        ]);
        $vendorusers = User::find($req->id);
        $vendorusers->first_name = $req->firstname;
        $vendorusers->last_name = $req->lastname;
        $vendorusers->email = $req->email;
        $vendorusers->phone = $req->phone;
        $vendorusers->mobile = $req->mobile;
        $vendorusers->address = $req->address;
        $businessLogoImage = $req->old_business_logo;
        $profilePicImage = $req->old_profilepic;
        $vendorusers->address = $req->address;
        if ($req->hasFile('profilepic')) {
            if ($vendorusers->profilepic != null || $vendorusers->profilepic != '') {
                $destinationPath = $vendorusers->profilepic;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $profilePicFile = $req->file('profilepic');
            $profilePicImage = uplodImage($profilePicFile);
        }
        $vendorusers->salesperson = $req->salesperson;

        $vendorusers->enterprise_name = $req->enterprise_name;
        $vendorusers->industry = $req->industry;
        $vendorusers->business_exp = $req->business_exp;
        $vendorusers->sales = $req->sales;
        $vendorusers->turn_over = $req->turn_over;
        $vendorusers->payment_mode = $req->payment_mode;
        $vendorusers->downloadable = $req->downloadable;
        $vendorusers->payment_interval = $req->payment_interval;
        if ($req->hasFile('business_logo')) {
            if ($vendorusers->business_logo != null || $vendorusers->business_logo != '') {
                $destinationPath = $vendorusers->business_logo;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $businessLogoFile = $req->file('business_logo');
            $businessLogoImage = uplodImage($businessLogoFile);
        }
        $vendorusers->status = $req->status;
        $vendorusers->profilepic = $profilePicImage;
        $vendorusers->business_logo = $businessLogoImage;
        $vendorusers->vendor_code = $req->vendor_code;
        $vendorusers->price_list_no = $req->price_list_no;
        $vendorusers->vendor_credit_limit = $req->vendor_credit_limit;
        $vendorusers->user_role = 3;
        $vendorusers->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/vendoruser');
    }

    /* get the single vendor users*/
    public function view($id)
    {
        $page_title = 'User';
        $page_description = 'user page';
        $vendorusers = User::where([['id', '=', $id]])->first();
        $username = User::select('first_name')->where([['id', '=', $vendorusers['salesperson']]])->first();
        return view('admin.users.user', compact('page_title', 'page_description', 'vendorusers', 'username'));
    }

    /* get the all customer users*/
    public function customers(Request $request)
    {
        $page_title = 'Customers';
        $page_description = 'All customers list page';
        if ($request->ajax()) {
            $customers = User::where([['user_role', '=', '4']])->get();
            return Datatables::of($customers)
                ->addIndexColumn()
                ->editColumn('profileImage', function ($image) {
                    if ($image->profilepic != '') {
                        $url = url("uploads/profile/" . $image->profilepic);
                        return '<img alt="Profile Image Not Found" src="' . $url . '" width="50" height="50"/>';
                    } else {
                        return "-";
                    }
                })
                ->editColumn('status', function ($customerList) {
                    $status = '';
                    if ($customerList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($customerList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($customerList) {
                    return view('admin.users.customer_datatable', compact('customerList'))->render();
                })
                ->rawColumns(['profileImage', 'status', 'action'])
                ->make(true);
        }
        return view('admin.users.customers', compact('page_title', 'page_description'));
    }
}
