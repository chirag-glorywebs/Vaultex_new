<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Models\User;
use App\Mail\UserRegisterMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Models\User_addresses;
use Illuminate\Support\Facades\Auth;
use App\Mail\AdminRegisterMail;
use App\Models\CustomerBasket;
use App\Models\Settings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class UserController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'user_role' => 'required|numeric',
            'address' => 'required',
            'phone_number' => 'required',
            'city' => 'required',
            'country' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user->address = $input['address'];
        $user->phone = $input['phone_number'];
        $user->first_name = $request->name;
        $user->user_role = $input['user_role'];
        $user->vendor_credit_limit = 0;
        if (!empty($input['business_card']) && ($input['user_role'] == 3)) {
            $user->business_card =  $this->createImage($input['business_card']);
        }
        $user->save();

        if ($user->user_role == 3) {
            $business_user['token'] =  $user->createToken('vaultex')->accessToken;
            $business_user['id'] = $user->id;
            $business_user['name'] = $user->name;
            $business_user['user_role'] = $user->user_role;

            if (!empty($user->profilepic) && file_exists($user->profilepic)) {
                $business_user['profilepic'] = asset($user->profilepic);
            } else {
                $business_user['profilepic'] = asset('uploads/product-placeholder.png');
            }
            $business_user['email'] = $user->email;
            if (!empty($user->business_logo) && file_exists($user->business_logo)) {
                $business_user['business_logo'] = asset($user->business_logo);
            } else {
                $business_user['business_logo'] = asset('uploads/product-placeholder.png');
            }
            $business_user['business_card'] = asset($user->business_card);
            $business_user['vendor_code'] = $user->vendor_code;
            $business_user['vendor_credit_limit'] = $user->vendor_credit_limit;


            //Insert Data into user_address table busness user details
            $userAddress = new User_addresses;
            $userAddress->userid =  $business_user['id'];
            $userAddress->email = $business_user['email'];
            $userAddress->name = $business_user['name'];
            $userAddress->phone = $request->phone_number;
            $userAddress->address = $request->address;
            $userAddress->city = $request->city;
            $userAddress->landmark = $request->landmark;
            $userAddress->country = $request->country;
            $userAddress->country_code = $request->country_code;
            $userAddress->state = $request->state;
            $userAddress->pincode = $request->pincode;
            $userAddress->save();
            $business_user['country_code'] = $userAddress->country_code;
            $data = $business_user;
            $details = [

                'name'=> $data['name'],
                'title'=>'Vaultex',
                'body'=>'Congratulations , Your Account has been successfully created.'
            ];
            if($user->user_role == 3){
                Mail::to($data['email'])->send(new UserRegisterMail($details));
                }
                $email_data = Settings::where('id','>=',19)
                    ->get();
                $reg_email =  $email_data[0]['value'];
                $data_co = explode(',',$reg_email);
                $Admin = [
                    'title'=>'vaultex',
                    'name' => $data['name'],
                    'body'=>'user register successfully'
                ];
        
                Mail::to($data_co)->send(new AdminRegisterMail($Admin)); 
           
            return $this->sendResponse($data, 'Business User Register Successfully');
        }
        $success['id'] = $user->id;
        $success['name'] =  $user->name;
        $success['user_role'] = $user->user_role;
        $success['token'] =  $user->createToken('vaultex')->accessToken;
        $success['email'] = $user->email;
        $success['first_name'] = $user->first_name;
        $success['last_name'] = $user->last_name;
        $success['vendor_credit_limit'] = $user->vendor_credit_limit;


        if (!empty($user->profilepic) && file_exists($user->profilepic)) {
            $success['profilepic'] = asset($user->profilepic);
        } else {
            $success['profilepic'] = asset('uploads/product-placeholder.png');
        }
        // insert data User_addresses table
        $userAddress = new User_addresses;
        $user_id = $user->id;
        $userAddress->userid =  $user_id;
        $userAddress->name = $input['name'];
        $userAddress->email = $input['email'];
        $userAddress->phone = $request->phone_number;
        $userAddress->city = $request->city;
        $userAddress->state = $request->state;
        $userAddress->pincode = $request->pincode;
        $userAddress->country = $request->country;
        $userAddress->country_code = $request->country_code;
        $userAddress->address = $request->address;
        $userAddress->landmark = $request->landmark;
        $userAddress->save();
        $success['country_code'] = $userAddress->country_code;
        $details = ['title'=>'mail from vaultex',
                        'name'=>$success['name'],
           'body'=>'Congratulations , Your Account has been successfully created.'
        ];
        Mail::to($success['email'])->send(new UserRegisterMail($details));
        
        $email_data = Settings::where('id','>=',19)
            ->get();
        $reg_email =  $email_data[0]['value'];
        $data_co = explode(',',$reg_email);
        $Admin = [
            'title'=>'vaultex',
            'name' => $success['name'],
            'body'=>'user register successfully'
        ];

        Mail::to($data_co)->send(new AdminRegisterMail($Admin));
        return $this->sendResponse($success, 'User register  successfully.');
    }

    /**
     * createImage  
     *
     * @return \Illuminate\Http\Response
     */
    public function createImage($image)
    {

        /* $path = date('Y') . '/' . date('m');
        $folderPath = 'uploads/' . $path . '/';
        $image = $image;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName =  $folderPath . uniqid() . '.png';

        file_put_contents($imageName, base64_decode($image));

        return  $imageName; */
        $path = date('Y') . '/' . date('m');
        //  $path = date('Y') ;
        $folderPath = 'uploads/' . $path . '/';
        $fileContents = file_get_contents($image);
        $imageName =  $folderPath . 'profile-' . uniqid() . '.jpg';
        File::makeDirectory($folderPath, $mode = 0777, true, true);
        File::put($imageName, $fileContents);
        return  $imageName;
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        $user = User::where('email', $request->email)
        ->orWhere('vendor_code',$request->email)
        ->first();
    
        if ($user) {
            if (Auth::attempt(['password' => request('password'),'email'=> request('email')])  || Auth::attempt(['password' => request('password'),'vendor_code'=> request('email')])) {
                $user = Auth::user();

                if ($user->status == 0) {
                    return $this->sendError('Your has account has been deactivated. please contact to administrator.');
                }
                if ($user->user_role == 3) {
                    $business_user['token'] =  $user->createToken('vaultex')->accessToken;
                    $business_user['id'] = $user->id;
                    $business_user['name'] = $user->name;
                    $business_user['profilepic'] = asset($user->profilepic);
                    $business_user['email'] = $user->email;
                    $business_user['business_logo'] = asset($user->business_logo);
                    $business_user['user_role'] = $user->user_role;
                    $business_user['vendor_code'] = $user->vendor_code;
                    $business_user['vendor_credit_limit'] = $user->vendor_credit_limit;
                    $data = $business_user;
                    return $this->sendResponse($data, 'Business User Login Successfully');
                }
                $success['token'] =  $user->createToken('vaultex')->accessToken;
                $success['id'] = $user->id;
                $success['email'] = $user->email;
                $success['name'] = $user->name;
                $success['first_name'] = $user->first_name;
                $success['last_name'] = $user->last_name;
                $success['user_role'] = $user->user_role;
                if (!empty($user->profilepic) && file_exists($user->profilepic)) {
                    $success['profilepic'] = asset($user->profilepic);
                } else {
                    $success['profilepic'] = asset('uploads/product-placeholder.png');
                }

                return $this->sendResponse($success, 'User login  successfully.');
            } else {
                return $this->sendError('Password mismatch. Please try again.', ['error' => 'Unauthorised'], 401);
            }
        } else {
            return $this->sendError('User does not exist. Please try again.', ['error' => 'Unauthorised'], 401);
        }
    }
    /**
     * Logout api
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        if (Auth::check()) {
            $success = Auth::user()->token()->revoke();
            return $this->sendResponse($success, 'User logout successfully.');
        }
    }
    /**
     * Channge User Default Address.
     *
     * @return \Illuminate\Http\Response
     */
    public function setDefaultAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $data = array();

        $user_id = $request->user()->id;
        $user_address  = User_addresses::where('id', $request->address_id)->where('userid', $user_id)->first();
        if(!empty($user_address)){
            $user_data  = User::where('id', $user_id)->first();
            $user_data->address_id = $request->address_id;
            $user_data->save();
        }else{
            return $this->sendError('You have selected invalid address please try again.');
        }
        $user_address->save();
       /*  $data['id'] = $request->address_id; */
        $data =   $request->user();
        if(!empty($data->business_card)){
            $data->business_card = asset($data->business_card);
        }
        return $this->sendResponse($data, 'Default address has been changed successfully');

    }
    /**
     * Update User Profile api
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user_id = $request->user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'email' => 'required|string|email|unique:users,email,' . $user_id . ',id',
            'address' => 'required',
            'phone_number' => 'required',
            'city' => 'required',
            'country' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $data = array();

        $user_data  = User::where('id', $user_id)->first();
        $user_address  = User_addresses::where('userid', $user_id)->first();
        // return $user_address;
        $user_data->name = $request->name;
        $user_data->email = $request->email;
        $user_data->phone = $request->phone_number;
        // if(!empty($user_address)){
        //     $user_address = new User_addresses;
        //     $user_address->name = $request->name;
        //     $user_address->email = $request->email;
        //     $user_address->phone = $request->phone;
        // }
        $user_address->name = $request->name;
        $user_address->email = $request->email;
        $user_address->phone = $request->phone_number;
        $user_address->userid =  $user_data->id;
        $user_address->address = $request->address;
        $user_address->city = $request->city;
        $user_address->country_code = $request->country_code;
        if (isset($request->state) && !empty($request->state)) {
            $user_address->state = $request->state;
            $data['state'] = $user_address->state;
        }

        $user_address->country = $request->country;
        $user_address->landmark = $request->landmark;
        // return $user_address;

        $data['id'] = $user_data->id;
        $data['name'] = $user_data->name;
        $data['email'] = $user_data->email;
        $data['address'] = $user_address->address;
        $data['city'] = $user_address->city;
        $data['country_code'] = $user_address->country_code;
        $data['country'] = $user_address->country;
        $data['landmark'] = $user_address->landmark;

        if ($user_data->user_role == 3 && !empty($request->business_card)) {
            $user_data->business_card =  $this->createImage($request->business_card);

            if (!empty($user_data->business_card) && file_exists($user_data->business_card)) {
                $data['business_card'] = asset($user_data->business_card);
            } else {
                $data['business_card'] = asset('uploads/profile/users.jpg');
            }
        }

        if (isset($request->profilepic) && !empty($request->profilepic)) {
            $user_data->profilepic =  $this->createImage($request->profilepic);
        }
        if (!empty($user_data->profilepic) && file_exists($user_data->profilepic)) {
            $data['profilepic'] =  asset($user_data->profilepic);
        } else {
            $data['profilepic'] = asset('uploads/profile/users.jpg');
        }
        //    return $user_address;
        $user_data->save();
        $user_address->save();
        return $this->sendResponse($data, 'User profile updated successfully');
    }

    /**
     * Show  User Profile api
     *
     * @return \Illuminate\Http\Response
     */
    public function showProfile(Request $request)
    {

        $user_id = $request->user()->id;
        $user =  User::select('user_role')->where('id', $user_id)->first();


        if ($user->user_role == 3) {
            $data =  User::join('user_addresses', 'users.id', '=', 'user_addresses.userid')
                ->where('users.id', $user_id)
                ->where('user_addresses.userid', $user_id)
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'user_addresses.address',
                    'users.profilepic',
                    'users.business_card',
                    'user_addresses.city',
                    'user_addresses.country',
                    'user_addresses.country_code',
                    'user_addresses.landmark',
                    'users.vendor_credit_limit'
                )->first();
            if (!empty($data)) {
                if (!empty($data->business_card) && file_exists($data->business_card)) {
                    $data->business_card = asset($data->business_card);
                } else {
                    $data->profilepic = asset('uploads/profile/users.jpg');
                }
                if (!empty($data->profilepic) && file_exists($data->profilepic)) {
                    $data->profilepic = asset($data->profilepic);
                } else {
                    $data->profilepic = asset('uploads/profile/users.jpg');
                }
                return  $this->sendResponse($data, 'Business User Profile.');
            } else {
                return $this->sendError('User profile Not Found.');
            }
        } else { 
            $data =  User::join('user_addresses', 'users.id', '=', 'user_addresses.userid')
                ->where('users.id', $user_id)
                ->where('user_addresses.userid', $user_id)
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'user_addresses.address',
                    'users.profilepic',
                    'user_addresses.city',
                    'user_addresses.country',
                    'user_addresses.country_code',
                    'user_addresses.landmark'
                )->first();
            if (!empty($data)) {

                if (!empty($data->profilepic) && file_exists($data->profilepic)) {
                    $data->profilepic = asset($data->profilepic);
                } else {
                    $data->profilepic = asset('uploads/profile/users.jpg');
                }
                return  $this->sendResponse($data, 'User Profile.');
            } else {
                return $this->sendError('user Profile Not Found.');
            }
        }
    }


    /**
     * Add User Address api
     *
     * @return \Illuminate\Http\Response
     */

    public function add(Request $request)
    {
        # code...
        $user_id = $request->user()->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'email' => 'required|string|email',
            'address' => 'required',
            'phone_number' => 'required|min:11|numeric',
            'pincode' => 'required|min:6|numeric',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]); 

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {

            $User_addresses = new User_addresses;
            $User_addresses->userid = $user_id;
            $User_addresses->name = $request->name;
            $User_addresses->email = $request->email;
            $User_addresses->phone = $request->phone_number;
            $User_addresses->pincode = $request->pincode;
            $User_addresses->address = $request->address;
            $User_addresses->city = $request->city;
            $User_addresses->state = $request->state;
            $User_addresses->country = $request->country;
            $User_addresses->country_code = $request->country_code;
            $User_addresses->landmark = $request->landmark;
            $User_addresses->save();
            $User_addresses->id = $User_addresses->id;
            return $this->sendResponse($User_addresses, 'Your address  is successfully added.');
        }
    }

    /**
     * update User Address api
     *
     * @return \Illuminate\Http\Response
     */
    public function updateAdrdess(Request $request)
    {
        # code...
        $user_id = $request->user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'email' => 'required|string|email',
            'address' => 'required',
            'address_id' => 'required',
            'phone_number' => 'required',
            'pincode' => 'required|min:6|numeric',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {

            $details = User_addresses::where('userid', $user_id)->where('id', $request->address_id)->first();
            // return $details;
            if (isset($details)) {
                $details->userid = $user_id;
                $details->name = $request->name;
                $details->email = $request->email;
                $details->phone = $request->phone_number;
                $details->pincode = $request->pincode;
                $details->address = $request->address;
                $details->city = $request->city;
                $details->state = $request->state;
                $details->country = $request->country;
                $details->country_code = $request->country_code;
                $details->landmark = $request->landmark;
                // return $details;
                $details->save();
                return $this->sendResponse($details, 'Your address  is successfully updated.');
            } else {
                return $this->sendError('Address Not Found.');
            }
        }
    }

    /**
     * Delete User Address api
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAddress(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'address_id' => 'required'
        ]);
        $address_id =  $request->address_id;
        $user_id = $request->user()->id;

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {

            $userAddress = User_addresses::where('userid', $user_id)->where('id', $address_id)->first();
            //  return $userAddress['id'];
            if ($userAddress) {
                $data = User_addresses::where('id', $userAddress->id)->delete();
                return $this->sendResponse($data ,'Address Deleted Successfully.');
            } else {
                return $this->sendError("Address not found.");
            }
        }
    }
    /**
     * View User Address api
     *
     * @return \Illuminate\Http\Response
     */

    public function showAddress(Request $request)
    {
        # code...
        $user_id = $request->user()->id;
        $user_data = User_addresses::where('userid', $user_id)->get();
        if (!$user_data->isEmpty()) {
            return $this->sendResponse($user_data, 'user addresses List.');
        } else {
            return $this->sendError('Address not found.');
        }
    }


    public function user(Request $request)
    {
        $data =   $request->user();
        if ($data) {
            if(!empty($data->business_card) && file_exists($data->business_card)){
                $data->business_card = asset($data->business_card);
            }else{
                $data->business_card = asset('uploads/profile/users.jpg');
            }
            return  $this->sendResponse($data, 'User Details.');
        } else {
            return $this->sendError('Data not found');
        }
    }
}
