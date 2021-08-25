<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGeneralSettings;
use App\Models\HomePage;
use App\Models\Settings;
use Illuminate\Http\Request;
use Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function homePage()
    {
        $page_title = 'Home Page';
        $page_description = 'Edit Home Page';
        $homeList = HomePage::all();
        return view('admin.home.home_update', compact('homeList','page_title','page_description'));
    }

    public function updateHomePage(Request $request)
    {
        $request->validate([
            'offer_top_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'offer_top_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'offer_top_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'offer_bottom_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'offer_bottom_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
        $homePageModel = HomePage::find($request->id);

        $offerTop_1 = $request->get('old_offer_top_1');
        if ($request->hasFile('offer_top_1')) {
            if ($homePageModel->offer_top_1 != null || $homePageModel->offer_top_1 != '') {
                $destinationPath = $homePageModel->offer_top_1;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $reqOfferTop_1 = $request->file('offer_top_1');
            $offerTop_1 = uplodImage($reqOfferTop_1);
        }
        $offerTop_2 = $request->get('old_offer_top_2');
        if ($request->hasFile('offer_top_2')) {
            if ($homePageModel->offer_top_2 != null || $homePageModel->offer_top_2 != '') {
                $destinationPath = $homePageModel->offer_top_2;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $reqOfferTop_1 = $request->file('offer_top_2');
            $offerTop_2 = uplodImage($reqOfferTop_1);
        }
        $offerTop_3 = $request->get('old_offer_top_3');
        if ($request->hasFile('offer_top_3')) {
            if ($homePageModel->offer_top_3 != null || $homePageModel->offer_top_3 != '') {
                $destinationPath = $homePageModel->offer_top_3;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $reqOfferTop_1 = $request->file('offer_top_3');
            $offerTop_3 = uplodImage($reqOfferTop_1);
        }

        $offerBottom_1 = $request->get('old_offer_bottom_1');
        if ($request->hasFile('offer_bottom_1')) {
            if ($homePageModel->offer_bottom_1 != null || $homePageModel->offer_bottom_1 != '') {
                $destinationPath = $homePageModel->offer_bottom_1;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $reqOfferTop_1 = $request->file('offer_bottom_1');
            $offerBottom_1 = uplodImage($reqOfferTop_1);
        }
        $offerBottom_2 = $request->get('old_offer_bottom_2');
        if ($request->hasFile('offer_bottom_2')) {
            if ($homePageModel->offer_bottom_2 != null || $homePageModel->offer_bottom_2 != '') {
                $destinationPath = $homePageModel->offer_bottom_2;
                $fileExists = file_exists($destinationPath);
                if ($fileExists) {
                    unlink($destinationPath);
                }
            }
            $reqOfferTop_1 = $request->file('offer_bottom_2');
            $offerBottom_2 = uplodImage($reqOfferTop_1);
        }
        $homePageModel->offer_top_1 = $offerTop_1;
        $homePageModel->offer_top_1_url = $request->offer_top_1_url;
        $homePageModel->offer_top_2 = $offerTop_2;
        $homePageModel->offer_top_2_url = $request->offer_top_2_url;
        $homePageModel->offer_top_3 = $offerTop_3;
        $homePageModel->offer_top_3_url = $request->offer_top_3_url;
        $homePageModel->offer_bottom_1 = $offerBottom_1;
        $homePageModel->offer_bottom_1_contents = $request->offer_bottom_1_contents;
        $homePageModel->offer_bottom_1_url = $request->offer_bottom_1_url;
        $homePageModel->offer_bottom_2 = $offerBottom_2;
        $homePageModel->offer_bottom_2_contents = $request->offer_bottom_2_contents;
        $homePageModel->offer_bottom_2_url = $request->offer_bottom_2_url;
        $homePageModel->save();

        Session::flash('message', 'Updated successfully!');
        return redirect()->route('home-page');
    }
}
