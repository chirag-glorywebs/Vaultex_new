<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Mail\ForgotPasswordRequestMail;
use App\Mail\PasswordResetSuccessMail;
use Illuminate\Support\Facades\Mail;


class PasswordResetController extends BaseController
{
    /**
     * Generate string
     *
     * @return \Illuminate\Http\Response
     */

    public function generate_otp( $length = 4) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);
        $user = User::where('email', $request->email)->first();

        if (!$user){
            return $this->sendError('We can not find a user with that e-mail address.');
        }
        $otp = $this->generate_otp(4);

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'otp' => $otp,
                'token' => Str::random(60),

            ]
        );

        if ($user)

        Mail::to( $user['email'])->send(new ForgotPasswordRequestMail($passwordReset->token,$passwordReset->otp));
            return $this->sendResponse(true,'The verification code has been sent to your registered email address!');
    }
    public function find(Request $request)
    {
        $otp = $request->otp;
        $passwordReset = PasswordReset::select('token')->where('otp', $otp)
            ->first();
        if (!$passwordReset){
           return $this->sendError('This password reset OTP is invalid.');
        }
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return $this->sendError('This password reset OTP has been expired.');
        }
        return $this->sendResponse(['temp_access_token'=>$passwordReset->token],'This password reset OTP is valid');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'confirm_password' => 'required|same:password',
            'token' => 'required|string',
            'otp' => 'required'
        ]);

        $passwordReset = PasswordReset::where('token',$request->token)
        ->orWhere('token',$request->token)
        ->orWhere('otp',$request->otp)
        ->first();
        if (!$passwordReset){
            return $this->sendError('This token has been expired.');
        }
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user){
            return $this->sendError('We can not find a user with that e-mail address.');
        }

        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        Mail::to( $user['email'])->send(new PasswordResetSuccessMail($passwordReset));


        $userinfo['id'] = $user->id;
        $userinfo['first_name'] = $user->first_name;
        $userinfo['email'] = $user->email;
        $userinfo['last_name'] = $user->last_name;
        $userinfo['mobile'] = $user->mobile;
        $userinfo['phone'] = $user->phone;
        $userinfo['address'] = $user->address;
        $userinfo['user_role'] = $user->user_role;
        $userinfo['profilepic'] = asset($user->profilepic);
        if (!empty($user->profilepic) && file_exists($user->profilepic)) {
            $userinfo['profilepic'] = asset($user->profilepic);
        } else {
            $userinfo['profilepic'] = asset('uploads/product-placeholder.png');
        }
        $userTokens = $user->tokens;
        foreach ($userTokens as $token) {
            $token->revoke();
        }
        $userinfo['token'] =  $user->createToken('vaultex')->accessToken;
        return $this->sendResponse($userinfo, 'Password Reset Successful.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'confirm_password' => 'required|same:password',
            'token' => 'required|string',
        ]);
        $passwordReset1 = PasswordReset::select('token')->where('token', $request->token) ->first();
        if(!empty($passwordReset1)){
            if (Carbon::parse($passwordReset1->updated_at)->addMinutes(720)->isPast()) {
                $passwordReset1->delete();
                return $this->sendError('This password reset token has been expired.');
            }
            $passwordReset = PasswordReset::where('token', $request->token)->first();
            if (empty($passwordReset)) {
                return $this->sendError('This token has been expired.');
            }
            $user = User::where('email', $passwordReset->email)->first();
            if (!$user) {
                return $this->sendError('We can not find a user with that e-mail address.');
            }

            $user->password = bcrypt($request->password);
            $user->save();
            $passwordReset->delete();
            Mail::to($user['email'])->send(new PasswordResetSuccessMail($passwordReset));

            $userinfo['id'] = $user->id;
            $userinfo['first_name'] = $user->first_name;
            $userinfo['email'] = $user->email;
            $userinfo['last_name'] = $user->last_name;
            $userinfo['mobile'] = $user->mobile;
            $userinfo['phone'] = $user->phone;
            $userinfo['address'] = $user->address;
            $userinfo['user_role'] = $user->user_role;
            $userinfo['profilepic'] = asset($user->profilepic);
            if (!empty($user->profilepic) && file_exists($user->profilepic)) {
                $userinfo['profilepic'] = asset($user->profilepic);
            } else {
                $userinfo['profilepic'] = asset('uploads/product-placeholder.png');
            }
            $userTokens = $user->tokens;
            foreach ($userTokens as $token) {
                $token->revoke();
            }
            $userinfo['token'] = $user->createToken('vaultex')->accessToken;
            return $this->sendResponse($userinfo, 'Your password reset Successfully. Got to login for access website.');
        }else{
            return $this->sendError('This token has been expired.');
        }
    }
    public function resetPasswordCheckToken(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'token' => 'required|string',
        ]);
        $passwordReset1 = PasswordReset::select('token','email')->where('token', $request->token)->where('email', $request->email)->first();
        if(!empty($passwordReset1)){
            return $this->sendResponse($passwordReset1, 'The token is valid.');
        }else{
            return $this->sendError('This token has been expired.');
        }


    }

    public function changePassword(Request $request)
    {
        # code...
        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {

            return $this->sendError(
                'Your current password does not matches with the password you provided. Please try again.',
                ['error' => 'Unauthorised'],
                401
            );
        }
        if (strcmp($request->get('current_password'), $request->get('new_password')) == 0) {
            // Current password and new password are same
            return $this->sendResponse('New Password cannot be same as your current password. Please choose a different password.', ['error' => 'Unauthorised'], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_new_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        //Change Password
        $user = Auth::user();

        $user->password = bcrypt($request->get('new_password'));
        $user->save();

        $userdata = array($user);

        $userinfo[0]['id'] = $userdata[0]['id'];
        $userinfo[0]['first_name'] = $userdata[0]['first_name'];
        $userinfo[0]['email'] = $userdata[0]['email'];
        $userinfo[0]['last_name'] = $userdata[0]['last_name'];
        $userinfo[0]['mobile'] = $userdata[0]['mobile'];
        $userinfo[0]['phone'] = $userdata[0]['phone'];
        $userinfo[0]['address'] = $userdata[0]['address'];
        $userinfo[0]['user_role'] = $userdata[0]['user_role'];
        $userinfo[0]['profilepic'] = asset($userdata[0]['profilepic']);
        if (!empty($user->profilepic) && file_exists($user->profilepic)) {
            $userinfo[0]['profilepic'] = asset($user->profilepic);
        } else {
            $userinfo[0]['profilepic'] = asset('uploads/product-placeholder.png');
        }

        $userTokens = $user->tokens;
        foreach ($userTokens as $token) {
            $token->revoke();
        }

        $userinfo[0]['token'] =  $user->createToken('vaultex')->accessToken;
        $data = $userinfo;
        return $this->sendResponse($data, 'Password changed successfully .');
    }
}
