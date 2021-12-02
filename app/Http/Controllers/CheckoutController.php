<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $getNGeniusAccessToken = Helper::getNGeniusAccessToken();
        if ($getNGeniusAccessToken['success']) {
            $access_token = $getNGeniusAccessToken['output']->access_token;
            $createOrder = Helper::createOrder($access_token, $request->all());
            // dd($createOrder);
            if ($createOrder['success']) {
                if (isset($createOrder['output']->code)) {
                    return response()->json(['success' => false, 'message' => 'Something went wrong!!', "data" => $createOrder['output']]);
                } elseif (isset($createOrder['output']->_links)) {
                    // return redirect(['success' => true, "link" => $createOrder['output']->_links->payment->href]);
                    return response()->json(['success' => true, "link" => $createOrder['output']->_links->payment->href]);
                }
            }
        }
        return response()->json(['success' => false, 'message' => 'Something went wrong!!']);
    }

    public function success(Request $request)
    {
        dd($request->all());
    }

    public function ref()
    {
        $getNGeniusAccessToken = Helper::getNGeniusAccessToken();
        if ($getNGeniusAccessToken['success']) {
            $access_token = $getNGeniusAccessToken['output']->access_token;
            $createOrder = Helper::orderRef($access_token);
            dd($createOrder);
            if ($createOrder['success']) {
                if (isset($createOrder['output']->code)) {
                    return response()->json($createOrder['output']);
                } elseif (isset($createOrder['output']->_links)) {
                    return redirect($createOrder['output']->_links->payment->href);
                }
            }
        }
        return response()->json(['success' => true, 'message' => 'Something went wrong!!']);
    }
}
