<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator; 
use Illuminate\Http\Request;
use App\Models\ContactUs;
use App\Http\Controllers\API\BaseController;

class ContactusController extends BaseController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'name' => 'required|string',
            'contact' => 'min:11|numeric',
             'message'=>'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }else{
            $contactus = new ContactUs;
            $contactus->name = $request->name;
            $contactus->email = $request->email;
            $contactus->company_name = $request->company_name;
            $contactus->contact = $request->contact;
            $contactus->message = $request->message; 
            $restult = $contactus->save();
            return $this->sendResponse($restult, 'Form Submitted Successfull');
        }
    
    }
}
