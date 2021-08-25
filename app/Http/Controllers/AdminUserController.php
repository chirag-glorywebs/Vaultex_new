<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Models\Blog;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\DB;
use Session;
use Yajra\DataTables\Facades\DataTables;

class AdminUserController extends Controller
{
    public function index()
    {
        $page_title = 'Login';
        $page_description = 'login page';
        return view('admin.login', compact('page_title', 'page_description'));
    }
    /*  public function adminLogin(Request $req)
        {
            $this->validate($req, [
                'email' => 'required',
                'password' => 'required'
            ]);
            $user_data = [
                'email' => $req->get('email'),
                'password' => $req->get('password')
            ];
            if (Auth::attempt($user_data)) {
                return redirect('/admin/dashboard');
            } else {
                return back()->with('error', 'Wrong Login Details');
            }

        }*/

        public function adminLogin(Request $request){
        # code...
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if(Auth::attempt(array('email'=>$request->email,'password'=>$request->password))){
             $user_info = DB::table('users')->select('user_role','status')->where('email',$request->email)->first();
           $user_role =  $user_info->user_role;
           $status = $user_info->status;
           
            if($user_role == 1 || $user_role == 2){
                if($status == 1){
                        return redirect('admin/dashboard');
                }else{
                    Auth::logout();
                  return redirect('login')->with('error', 'Inactive User can not access this account!');
                }
            }else{
                 Auth::logout();
                  return redirect('login')->with('error', 'You can not access this account!');
             }
        }else{
            return redirect()->back()->with('error', 'Email-Address And Password Are Wrong!');
        }
     }
    function adminLogout()
    {
        Auth::logout();
        return redirect('login');
        /*return redirect('admin');*/
    }

    public function dashboard()
    {
        $page_title = 'Dashboard';
        $page_description = 'Some description for the page';

        return view('admin.dashboard.dashboard', compact('page_title', 'page_description'));
    }

    /* get the all admin users*/
    public function get(Request $request)
    {
        $page_title = 'Admin Users';
        $page_description = 'All admin users list page';
        if ($request->ajax()) {
            $adminusers = User::where([['user_role', '=', '1']])->get();
            return Datatables::of($adminusers)
                ->addIndexColumn()
                ->editColumn('status', function ($userlist) {
                    $status = '';
                    if ($userlist->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($userlist->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($userlist) {
                    return view('admin.users.user_datatable', compact('userlist'))->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.users.admin_users', compact('page_title', 'page_description'));
    }
    /* add new admin users page*/
    public function add()
    {
        $page_title = 'Add Admin User';
        $page_description = 'Add admin users here';
        return view('admin.users.add_admin_user', compact('page_title', 'page_description'));
    }

    /* add new admin users in db*/
    public function create(Request $req)
    {
        $req->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);
        $adminusers = new User;
        $adminusers->first_name = $req->firstname;
        $adminusers->last_name = $req->lastname;
        $adminusers->email = $req->email;
        $adminusers->password = bcrypt($req->password);
        $adminusers->status = $req->status;
        $adminusers->user_role = 1;
        $adminusers->save();
        Session::flash('message', 'Successfully added!');
        return redirect('/admin/adminuser');
    }

    /* delete user*/
    public function destroy($id)
    {
        $adminusers = User::find($id);
        $adminusers->delete();
        Session::flash('message', 'Successfully delete!');
        return redirect('/admin/adminuser');
    }

    /* edit user information*/
    public function edit($id)
    {
        $page_title = 'Edit Admin Users';
        $page_description = 'Edit user';
        $adminusers = User::find($id);
        return view('admin.users.edit_admin_user', ['data' => $adminusers], compact('page_title', 'page_description'));

    }

    /* update user information*/
    public function update(Request $req)
    {
        $req->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);
        $adminusers = User::find($req->id);
        $adminusers->first_name = $req->firstname;
        $adminusers->last_name = $req->lastname;
        $adminusers->email = $req->email;
        if ($adminusers->password != $req->password) {
            $adminusers->password = bcrypt($req->password);
        }
        $adminusers->status = $req->status;
        $adminusers->user_role = 1;
        $adminusers->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/adminuser');
    }
}