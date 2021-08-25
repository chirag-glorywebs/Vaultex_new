<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorEmail;

class VendorEmailController extends BaseController
{
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
        $user =  DB::table('users')->where('name','=', $request->name)->where('vendor_code', '=',$request->code)->first();
 
        if($user){
            return $this->sendResponse(true, 'The vendor has been found');
        }else{
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
        if($user){
            $userwithEmail =  DB::table('users')->where('email', $request->email)->first();
            if($userwithEmail){
                return $this->sendError(false, 'This email address is already used by other user. Please try again with different email address.',200);
            }else{
                $VEInfo =  DB::table('vendor_emails')->where('vendor_name', $request->name)->where('vendor_code', $request->code)->first();
                if(!empty($VEInfo)){
                    return $this->sendResponse(true, 'You already submited before. We will do proceed with your request as soon as possible Thank you for contacting us.');
                }else{
                $newVendorInfo = new VendorEmail;
                $newVendorInfo->vendor_name = $request->name;
                $newVendorInfo->vendor_code = $request->code;
                $newVendorInfo->email = $request->email;
                $newVendorInfo->save();
                return $this->sendResponse(true, 'Your request has been sucessfully submited one of our sale person will contact you soon.');
                }
             }
        }else{
            return $this->sendError(false, 'User does not exist. Please try again');
        }

    }
}
