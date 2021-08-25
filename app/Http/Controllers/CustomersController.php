<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Session;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Customers';
        $page_description = 'All customers list page';
        if ($request->ajax()) {
            $customers = User::where([['user_role', '=', '4']])->get();
            return Datatables::of($customers)
                ->addIndexColumn()
                ->editColumn('profileImage', function ($image) {
                    
                    if($image->profilepic){
                        $url = url($image->profilepic);
                    }else{
                        $url =  asset('uploads/product-placeholder.png');
                    }
                    return '<img alt="Profile Image" src="' . $url . '" width="auto" height="80"/>';
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

    /* edit user information*/
    public function edit($id)
    {
        $page_title = 'Edit Customer';
        $page_description = 'Edit Customer';
        $customers = User::find($id);
        $salesusers = User::select('id', 'first_name', 'last_name')->where([['user_role', '=', '2'], ['status', '=', '1']])->get();
        return view('admin.users.edit_customer', ['data' => $customers], compact('page_title', 'page_description', 'salesusers'));
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
        $customers = User::find($req->id);
        $customers->first_name = $req->firstname;
        $customers->last_name = $req->lastname;
        $customers->email = $req->email;
        $customers->phone = $req->phone;
        $customers->address = $req->address;
        $businessLogoImage = $req->old_business_logo;
        $profilePicImage = $req->old_profilepic;
        $customers->address = $req->address;
        if ($req->hasFile('profilepic')) {
            if ($customers->profilepic != null || $customers->profilepic != '') {
                $destinationPath = $customers->profilepic;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $profilePicFile = $req->file('profilepic');
            $profilePicImage = uplodImage($profilePicFile);
        }
        $customers->salesperson = $req->salesperson;

        $customers->enterprise_name = $req->enterprise_name;
        $customers->industry = $req->industry;
        $customers->business_exp = $req->business_exp;
        $customers->sales = $req->sales;
        $customers->turn_over = $req->turn_over;
        $customers->payment_mode = $req->payment_mode;
        $customers->downloadable = $req->downloadable;
        $customers->payment_interval = $req->payment_interval;
        if ($req->hasFile('business_logo')) {
            if ($customers->business_logo != null || $customers->business_logo != '') {
                $destinationPath = $customers->business_logo;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $businessLogoFile = $req->file('business_logo');
            $businessLogoImage = uplodImage($businessLogoFile);
        }
        $customers->status = $req->status;
        $customers->profilepic = $profilePicImage;
        $customers->business_logo = $businessLogoImage;
        $customers->vendor_code = $req->vendor_code;
        $customers->price_list_no = $req->price_list_no;
        $customers->vendor_credit_limit = $req->vendor_credit_limit;
        $customers->user_role = 4;
        $customers->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/customers');
    }

    /* get the single vendor users*/
    public function view($id)
    {
        $page_title = 'User';
        $page_description = 'user page';
        $customers = User::where([['id', '=', $id]])->first();
        $username = User::select('first_name')->where([['id', '=', $customers['salesperson']]])->first();
        return view('admin.users.user', compact('page_title', 'page_description', 'vendorusers', 'username'));
    }
    /* delete user*/
    public function delete($id)
    {
        $customers = User::find($id);
        if ($customers->profilepic != null || $customers->profilepic != '') {
            $destinationPath = $customers->profilepic;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        if ($customers->business_logo != null || $customers->business_logo != '') {
            $destinationPath = $customers->business_logo;
            $fileExists = file_exists($destinationPath);
            if ($fileExists) {
                unlink($destinationPath);
            }
        }
        $customers->delete();
        return redirect()->route('customers.index')->with('success', 'Customer Deleted successfully !!');
    }

    /* get the all customer users*/

}
