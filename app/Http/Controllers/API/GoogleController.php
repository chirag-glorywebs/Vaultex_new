<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\LinkedSocialAccount;
use App\Models\User;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends BaseController {

	public function redirctToGoogle() {
		return Socialite::driver('google')->redirect();
	}

	public function handleGoogleCallback() {
		try {

			$user = Socialite::driver('google')->user();

			$finduser = User::where('google_id', $user->id)->first();
			$user->getAvatar();
			dd($user->getAvatar());
			if ($finduser) {

				Auth::login($finduser);

				return redirect()->intended('dashboard');
			} else {
				$newUser = User::create([
					'name' => $user->name,
					'email' => $user->email,
					'google_id' => $user->id,
					'password' => encrypt('123456dummy'),
				]);

				Auth::login($newUser);

				return redirect()->intended('dashboard');
			}
		} catch (Exception $e) {
			dd($e->getMessage());
		}
	}

	/**
	 * googleLogin
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function googleLogin(Request $request) {
		$linkedSocialAccount = LinkedSocialAccount::where('provider_name', 'google')
			->where('provider_id', $request->google_id)
			->first();

		if ($linkedSocialAccount) {
			$sucess['token'] = $linkedSocialAccount->createToken('vaultex')->accessToken;
			$message = 'User login  successfully.';
		} else {
			$newUser = null;
			if ($email = $request->email) {
				$newUser = User::where('email', $email)->first();
			}
			if (!$newUser) {
				$hashed_random_password = Hash::make(Str::random(8));
				$newUser = User::create([
					'name' => $request->name,
					'email' => $request->email,
					'password' => $hashed_random_password,
					'user_role' => $request->user_role,
				]);
				//  return $newUser;
				$profilePic = $this->saveImageAvatar($request->picture);
				if ($profilePic) {
					$newUser->profilepic = $profilePic;
					$newUser->save();
				}
				$message = 'User register  successfully.';
			} else {
				$message = 'User login  successfully.';
			}
			$newUser->linkedSocialAccounts()->create([
				'provider_id' => 'google',
				'provider_name' => $request->google_id,
			]);

			$sucess['token'] = $newUser->createToken('vaultex')->accessToken;

			$sucess['token'] = $newUser->createToken('vaultex')->accessToken;
			$sucess['id'] = $newUser->id;
			$sucess['name'] = $newUser->name;
			$sucess['email'] = $newUser->email;
			$sucess['first_name'] = $newUser->first_name;
			$sucess['last_name'] = $newUser->last_name;
			$sucess['user_role'] = $newUser->user_role;

			$pic = $newUser->profilepic;

			if (empty($pic) && file_exists($pic)) {
				$sucess['profilepic'] = asset('uploads/product-placeholder.png');
			} else {
				$sucess['profilepic'] = asset($newUser->profilepic);
			}
		}

		return $this->sendResponse($sucess, $message);
	}

	/**
	 * saveImageAvatar
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function saveImageAvatar($image) {
		$path = date('Y') . '/' . date('m');
		$folderPath = 'uploads/' . $path . '/';
		$fileContents = file_get_contents($image);
		$imageName = $folderPath . 'profile-' . uniqid() . '.jpg';
		File::makeDirectory($folderPath, $mode = 0777, true, true);
		File::put($imageName, $fileContents);
		return $imageName;
	}
}
