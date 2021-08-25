<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\User;
use App\Models\VendorEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Illuminate\Support\Str;

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
        /* Mail::send('API.email.email-verify', ['token' => $token], function($message) use($email){
             $message->to($email);
             $message->subject('Reset Password Notification');
         });*/
        Mail::send('API.email.email-verify', [
            'email' => $request->email,
            'token' => $token,
            'reset_url' => route('password.update', ['token' => $token, 'email' => $request->email]),
        ], function ($message) use ($request) {
            $message->subject('Reset Password Request');
            $message->to($request->email);
        });
    }
}
