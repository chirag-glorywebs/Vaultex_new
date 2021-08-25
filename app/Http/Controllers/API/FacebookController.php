<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\API\BaseController;
use Exception;
use App\Models\User;
use App\Models\LinkedSocialAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use File; 

class FacebookController extends BaseController
{   
   

    /**
     * facebookLogin  
     *
     * @return \Illuminate\Http\Response
     */
    public function facebookLogin(Request $request)
    {    
        $linkedSocialAccount = LinkedSocialAccount::where('provider_name', 'facebook')
            ->where('provider_id', $request->facebook_id)
            ->first();
        if ($linkedSocialAccount) {
            $sucess['token'] =  $linkedSocialAccount->createToken('vaultex')->accessToken;
            $message = 'User login  successfully.';
        } else {
            $newUser = null;
            if ($email = $request->email) {
                $newUser = User::where('email', $email)->first();
                //    return $newUser;
            }
            if (!$newUser) {
                $hashed_random_password = bcrypt(str::random(8));
                $newUser = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $hashed_random_password
                ]);
                $profilePic = $this->saveImageAvatar($request->picture);
                if($profilePic){
                    $newUser->profilepic = $profilePic;
                    $newUser->save();
                }
               
                $message = 'User register  successfully.';
            } else {
                $message = 'User login  successfully.';
            }
            $newUser->linkedSocialAccounts()->create([
                'provider_id' => 'facebook',
                'provider_name' => $request->facebook_id,
            ]);
            $sucess['token'] =  $newUser->createToken('vaultex')->accessToken;
            $sucess['id'] = $newUser->id;
            $sucess['name'] = $newUser->name;
            $sucess['email'] = $newUser->email;
            $sucess['first_name'] = $newUser->first_name;
            $sucess['last_name'] = $newUser->last_name;
            $pic =  $newUser->profilepic;

            if ($pic == null) {
                $sucess['profilepic'] = asset('uploads/product-placeholder.png');
            } else {
                $sucess['profilepic'] = asset($newUser->profilepic);
            }
        }
        return $this->sendResponse($sucess,  $message);
    }

     /**
     * saveImageAvatar  
     *
     * @return \Illuminate\Http\Response
     */
    public function saveImageAvatar($image)
    {
        $path = date('Y') . '/' . date('m');
      //  $path = date('Y') ;
        $folderPath = 'uploads/' . $path.'/';
        $fileContents = file_get_contents($image);
        $imageName =  $folderPath .'profile-'. uniqid() . '.jpg';
        File::makeDirectory($folderPath, $mode = 0777, true, true);
        File::put($imageName, $fileContents);
        return  $imageName;
    }
}
