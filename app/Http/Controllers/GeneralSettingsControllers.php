<?php

namespace App\Http\Controllers;

use App\GlobalConfiguration;
use App\Http\Requests\StoreGeneralSettings;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Illuminate\Support\Facades\Validator;


class GeneralSettingsControllers extends Controller
{
    public function index()
    {
        $page_title = 'General Settings';
        $page_description = 'Edit General Settings';
        $settings = Settings::where('id',21)
        ->orWhere('id','<=',14)
        ->get();
         $e_comm_setting = Settings::where('id', '>=', 15)
                ->where('id', '<=', 18)->get();
        
         $email_data = Settings::where('id','>=',19)
            ->where('id','<=',20)
            ->orwhere('id',25)
            ->get();
            // dd($email_data);
        $ImagesettingsList = Settings::where('id','>=',22)
            ->where('id','<=',24)
            ->get();
        
        return view('admin.settings.settings_update', compact('ImagesettingsList','settings','e_comm_setting','page_title', 'email_data','page_description'));
    }

    public function updateSettings(StoreGeneralSettings $request)
    {
        $request->validated();

        $settingsList = Settings::where('id',21)
        ->orWhere('id','<=',14)
        ->get();
    
        foreach ($settingsList as $key => $settings) {
            $settingsModel = Settings::where('name', '=', $settings->name)->first();
            if ($settingsModel->name == 'favicon') {
                $faviconSave = $request->get('old_favicon');
                if ($request->hasFile('favicon')) {
                    if ($settingsModel->value != null || $settingsModel->value != '') {
                        $destinationPath = $settingsModel->value;
                        $fileExists = file_exists($destinationPath);
                        if ($fileExists) {
                            unlink($destinationPath);
                        }
                    }
                    $favicon = $request->file($settingsModel->name);
                    $faviconSave = uplodImage($favicon);
                }
                $settingsModel->value = $faviconSave;
                $settingsModel->save();
            } elseif ($settingsModel->name == 'logo') {
                $logoSave = $request->get('old_logo');
                if ($request->hasFile('logo')) {
                    if ($settingsModel->value != null || $settingsModel->value != '') {
                        $destinationPath = $settingsModel->value;
                        $fileExists = file_exists($destinationPath);
                        if ($fileExists) {
                            unlink($destinationPath);
                        }
                    }
                    $logo = $request->file($settingsModel->name);
                    $logoSave = uplodImage($logo);
                }
                $settingsModel->value = $logoSave;
                $settingsModel->save();
            } elseif ($settingsModel->name == 'footer_image') {
                $footerImageSave = $request->get('old_footer_image');
                if ($request->hasFile('footer_image')) {
                    if ($settingsModel->value != null || $settingsModel->value != '') {
                        $destinationPath = $settingsModel->value;
                        $fileExists = file_exists($destinationPath);
                        if ($fileExists) {
                            unlink($destinationPath);
                        }
                    }
                    $footerImage = $request->file($settingsModel->name);
                    $footerImageSave = uplodImage($footerImage);
                }
                $settingsModel->value = $footerImageSave;
                $settingsModel->save();
            } else {
                $settingsToUpdate = array('value' => $request->get($settings->name));
                $settingsModel->fill($settingsToUpdate)->save();
            }
        }
        Session::flash('message', 'Settings updated successfully!');
        return redirect()->route('settings');
    }
    public function updateData(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'vat' => 'required',
            'post_per_page' => 'required',
            'currency'=>'required',
            'shipping_cost'=> 'required'
        ]);

        if ($validator->fails()) {
            return redirect('admin/settings')->withErrors($validator)->withInput();
        } else {
            $settings_data = Settings::where('id', '>=', 15)
                ->where('id', '<=', 18)->get();
                // dd($settings_data);
           foreach ($settings_data as $key => $settings) {
                $settingsModel = Settings::where('name', '=', $settings->name)->first();
                $settingsToUpdate = array('value' => $request->get($settings->name));
                $settingsModel->fill($settingsToUpdate)->save();
            }
            
            Session::flash('message', 'E-commerce Settings updated successfully!');
            return redirect()->route('settings');
        }
    }

    public function emailSettings(Request $request){

        $validator = Validator::make($request->all(), [
            'registration_email' => 'required',
            'bulkorder_email' => 'required',
            'placeorder_email' =>'required'          
        ]);
        if ($validator->fails()) {
            return redirect('admin/settings')->withErrors($validator)->withInput();
        } else {
          $email_data = Settings::where('id','>=',19)
            ->where('id','<=',20)
            ->orWhere('id',25)
            ->get();
            
            foreach ($email_data as $key => $email_settings) {
                $emailsettingsModel = Settings::where('name', '=', $email_settings->name)->first();
                $emailToUpdate = array('value' => $request->get($email_settings->name));
                $emailsettingsModel->fill($emailToUpdate)->save();
               }
            Session::flash('message','email Settings updated successful');
            return redirect()->route('settings');
            }
    }

    public function imageSettings(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'small_icon_img' => 'required',
            'medium_icon_img' => 'required',
            'large_icon_img' => 'required',
          
        ]);
        if ($validator->fails()) {
            return redirect('admin/settings')->withErrors($validator)->withInput();
        } else {
            $ImagesettingsList = Settings::where('id','>=',22)
            ->where('id','<=',24)
            ->get();
        foreach ($ImagesettingsList as $key => $image_settings) {
                $imagesettingsModel = Settings::where('name', '=', $image_settings->name)->first();
                $imageToUpdate = array('value' => $request->get($image_settings->name));
                $imagesettingsModel->fill($imageToUpdate)->save();
               }
   
        Session::flash('message', 'Image Settings updated successfully!');
        return redirect()->route('settings');}
            }
}
