<?php

namespace App\Helpers;

use stdClass;

class Helper
{
    public static function getRollId(string $roleTitle = '')
    {
        if ($roleTitle) {
            $rolesByUser = [
                'ADMIN' => 1,
                'SALES' => 2,
                'VENDOR' => 3,
                'CUSTOMER' => 4
            ];
            return $rolesByUser[$roleTitle];
        }
        return false;
    }

    public static function getNGeniusAccessToken()
    {
        try {
            $apikey = "MDBkZGEzYmYtNzkzZi00MTk1LWFmNmQtM2FiNjRkYmQzOWY1OmY0MzEzZmQ3LWQ2ZTItNGI3NC04ZWY1LTU3ODNlOWMwNDRlMw==";     // enter your API key here
            $api = "https://api-gateway.sandbox.ngenius-payments.com/identity/auth/access-token";

            // $http = Http::withOptions([
            //     'CURLOPT_SSL_VERIFYPEER' => false,
            // ])->withHeaders([
            //     "accept" => "application/vnd.ni-identity.v1+json",
            //     "authorization" => "Basic " . $apikey,
            //     "content-type" => "application/vnd.ni-identity.v1+json"
            // ])->post($api);

            // dd($http->json());
            // if ($http->successful()) {
            // } else {
            //     dd($http->failed());
            // }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "accept: application/vnd.ni-identity.v1+json",
                "authorization: Basic " . $apikey,
                "content-type: application/vnd.ni-identity.v1+json"
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  "{\"realmName\":\"ni\"}");
            $output = curl_exec($ch);
            $output = json_decode($output);
            curl_close($ch);
            return [
                'success' => true,
                'output' => $output
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'output' => "Something went wrong!!"
            ];
        }
    }

    public static function createOrder($token, $data = [])
    {
        try {
            $postData = new stdClass();
            $postData->action = "PURCHASE";
            $postData->amount = new StdClass();
            $postData->amount->currencyCode = "AED";
            $postData->amount->value = (float) $data['amount'] * 100;                   // 400;
            $postData->emailAddress = $data['email'];                                   // 'hardikkhorasiya09@gmail.com';
            $postData->merchantAttributes = new StdClass();
            // $postData->merchantAttributes->redirectUrl = url('/');
            $postData->merchantAttributes->redirectUrl = "http://sbmmarketplace.com/";
            $postData->merchantAttributes->skipConfirmationPage = false;
            $postData->merchantAttributes->cancelUrl = "http://sbmmarketplace.com/";
            $postData->merchantAttributes->cancelText = "Continue Shopping";
            $postData->merchantOrderReference = "myorder-0001";
            $postData->billingAddress = new stdClass();
            $postData->billingAddress->firstName = $data['first_name'];                 // "Test";
            $postData->billingAddress->lastName = $data["last_name"];                   // "Customer";

            $outlet = "61016811-5bae-40e6-acf2-3d85079cbd23";

            $json = json_encode($postData);
            // return $postData;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://api-gateway.sandbox.ngenius-payments.com/transactions/outlets/" . $outlet . "/orders");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer " . $token,
                "Content-Type: application/vnd.ni-payment.v2+json",
                "Accept: application/vnd.ni-payment.v2+json"
            ));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

            $output = curl_exec($ch);
            $output = json_decode($output);
            curl_close($ch);
            return [
                'success' => true,
                'output' => $output
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'output' => "Something went wrong!!"
            ];
        }
    }

    public static function orderRef($token, $ref = "")
    {
        try {
            $outlet = "61016811-5bae-40e6-acf2-3d85079cbd23";
            $ref = "3d0bf4f9-63d9-4457-a8ce-9ad4abc3ad9e";

            // return $postData;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://api-gateway.sandbox.ngenius-payments.com/transactions/outlets/" . $outlet . "/orders/" . $ref);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer " . $token,
                "Content-Type: application/vnd.ni-payment.v2+json",
                "Accept: application/vnd.ni-payment.v2+json"
            ));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $output = curl_exec($ch);
            $output = json_decode($output);
            curl_close($ch);
            return [
                'success' => true,
                'output' => $output
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'output' => "Something went wrong!!"
            ];
        }
    }
}
