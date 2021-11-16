<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\User;
use App\Models\VendorEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use DB;
use Mail;
use Helper;

class VendorEnquiryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_title = 'Retrieve login access enquiry page name';
        $page_description = 'Listing of all retrieve login access enquiry page name';
        if ($request->ajax()) {
            $vendorEnquiry = VendorEmail::all();

            $vendorEnquiry = VendorEmail::select('vendor_emails.*');
            $user = Auth::user();
            if($user->user_role==Helper::getRollId('SALES')){
                $vendorEnquiry = $vendorEnquiry
                ->join('users', 'users.vendor_code', '=', 'vendor_emails.vendor_code')
                ->where('users.salesperson', $user->id);
            }            
            $vendorEnquiry = $vendorEnquiry->get();

            return Datatables::of($vendorEnquiry)
                ->addIndexColumn()
                ->addColumn('action', function ($vendorEnquiryList) {
                    return view('admin.vendorenquiry.vendor_email_datatable', compact('vendorEnquiryList'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("admin.vendorenquiry.index", compact('page_title', 'page_description'));
    }

    public function destroy($id)
    {
        $pageDelete = VendorEmail::findOrFail($id);
        $pageDelete->delete();
        return redirect()->route('vendor-enquiry.index')->with('success', 'Deleted successfully !!');
    }

    public function postEmail(Request $request)
    {
        $token = Str::random(60);
        $emailExists = User::select('email')->where('vendor_code', $request->vendor_code)->first();
        if (empty($emailExists->email)) {
            DB::table('users')->where('vendor_code', $request->vendor_code)->update(array('email' => $request->email));
        }
      
        DB::table('password_resets')->insert(
            ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
       
        Mail::send('API.email.email-verify', [
            'email' => $request->email,
            'token' => $token,
            'reset_url' => route('password.update', ['token' => $token, 'email' => $request->email]),
        ], function ($message) use ($request) {
            //Vendor access Request
            $message->subject('Reset Password Request');
            $message->to($request->email);
        });
    }
}
