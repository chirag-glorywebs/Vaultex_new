<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VendorEmailController extends BaseController
{
    /**
     * Send OTP to Vendor Mobile Number api
     *
     * @return \Illuminate\Http\Response
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|min:3',
            'mobile' => 'required|string|min:3',
        ]);

        $otp = rand(100000, 999999);

        $user = User::query()
            ->where('mobile', $request->mobile)
            ->where('vendor_code', $request->code)
            ->first();
        if (!$user) {
            $user = new User();
            $user->mobile = $request->mobile;
            $user->vendor_code = $request->code;
        }
        $user->otp = $otp;
        $user->save();

        $response = Http::get('https://smpplive.com/api/send_sms/single_sms', [
            'to' => "91" . $request->mobile,
            'username' => 'inventmedia',
            'password' => 'In@R5304',
            'from' => 'SKYOTP',
            'content' => "$otp from Vaultex Project"
        ]);

        if ($response->successful()) {
            return $this->sendResponse(true, "OTP sent successfully");
        }
        return $this->sendError(true, $response->failed());
    }

    /**
     * Verify OTP for Vendor Mobile Number api
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|min:3',
            'mobile' => 'required|string|min:3',
            'otp' => 'required|string|digit_between:6,6',
        ]);

        $user = User::query()
            ->where('mobile', $request->mobile)
            ->where('vendor_code', $request->code)
            ->where('otp', $request->otp)
            ->first();

        if ($user) {
            return $this->sendResponse(true, "OTP verify successfully");
        }
        return $this->sendError(false, 'Invalid OTP. Please try again');
    }

    /**
     * Send Forget Password Email api
     *
     * @return \Illuminate\Http\Response
     */
    public function sendForgetPasswordEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|min:3',
            'mobile' => 'required|string|min:3',
            'otp' => 'required|string|min:3',
            'email' => 'required|string|min:3',
        ]);

        $user = User::query()
            ->where('mobile', $request->mobile)
            ->where('vendor_code', $request->code)
            ->where('otp', $request->otp)
            ->first();

        if (!$user) {
            return $this->sendError(false, 'Record not found in database');
        }
        $user->email = $request->email;
        $user->save();

        $token = Str::random(60);
        PasswordReset::create(
            ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );

        Mail::send('API.email.email-verify', [
            'email' => $request->email,
            'token' => $token,
            'reset_url' => route('password.update', ['token' => $token, 'email' => $request->email]),
        ], function ($message) use ($request) {
            $message->subject('Reset Password Request');
            $message->to($request->email);
        });

        return $this->sendResponse(true, "Mail send successfully");
    }

    /**
     * Store Review api
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyVendor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'code' => 'required|string|min:3',
        ]);
        $user =  DB::table('users')->where('name', '=', $request->name)->where('vendor_code', '=', $request->code)->first();

        if ($user) {
            return $this->sendResponse(true, 'The vendor has been found');
        } else {
            return $this->sendError(false, 'User does not exist. Please try again');
        }
    }
    /**
     * Store Review api
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'code' => 'required|string|min:3',
            'email' => 'required|string|email|unique:vendor_emails',
        ]);

        $user =  DB::table('users')->where('name', $request->name)->where('vendor_code', $request->code)->first();
        if ($user) {
            // $userwithEmail =  DB::table('users')->where('email', $request->email)->first();
            // if($userwithEmail){
            //     return $this->sendError(false, 'This email address is already used by other user. Please try again with different email address.',200);
            // }else{
            $VEInfo =  DB::table('vendor_emails')->where('vendor_name', $request->name)->where('vendor_code', $request->code)->first();
            if (!empty($VEInfo)) {
                return $this->sendResponse(true, 'You already submited before. We will do proceed with your request as soon as possible Thank you for contacting us.');
            } else {
                $newVendorInfo = new VendorEmail;
                $newVendorInfo->vendor_name = $request->name;
                $newVendorInfo->vendor_code = $request->code;
                $newVendorInfo->email = $request->email;
                $newVendorInfo->save();
                return $this->sendResponse(true, 'Your request has been sucessfully submited one of our sale person will contact you soon.');
            }
            //  }
        } else {
            return $this->sendError(false, 'User does not exist. Please try again');
        }
    }
}
