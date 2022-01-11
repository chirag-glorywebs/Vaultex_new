<?php

namespace App\Helpers;

use App\Models\Settings;
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
            $api = config('ngenius.api') . "identity/auth/access-token";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "accept: application/vnd.ni-identity.v1+json",
                "authorization: Basic " . config('ngenius.key'),
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
            // $currencySetting = Settings::query()
            //     ->find(17);
            // if ($currencySetting) {
            //     $currencyCode = $currencySetting->value;
            // } else {
            $currencyCode = "AED";
            // }

            if (!isset($data['order_id'])) {
                $data['order_id'] = 28;
            }

            $postData = new stdClass();
            $postData->action = "PURCHASE";
            $postData->amount = new StdClass();
            $postData->amount->currencyCode = $currencyCode;
            $postData->amount->value = (float) $data['amount'] * 100;                   // 400;
            $postData->emailAddress = $data['email'];                                   // 'hardikkhorasiya09@gmail.com';
            $postData->merchantAttributes = new StdClass();
            // $postData->merchantAttributes->redirectUrl = url('/');
            $postData->merchantAttributes->redirectUrl = "http://sbmmarketplace.com/thank-you?order_id=". $data['order_id'];
            $postData->merchantAttributes->skipConfirmationPage = true;
            $postData->merchantAttributes->cancelUrl = "http://sbmmarketplace.com/thank-you?order_id=". $data['order_id'];
            $postData->merchantAttributes->cancelText = "Continue Shopping";
            $postData->merchantOrderReference = $data['order_id'] ?? 'test-order';
            $postData->billingAddress = new stdClass();
            $postData->billingAddress->firstName = $data['first_name'] ?? "Test";                 // "Test";
            $postData->billingAddress->lastName = $data["last_name"] ?? "Customer";                   // "Customer";
            $postData->billingAddress->address1 = $data["address1"] ?? "Address1";
            $postData->billingAddress->city = $data["city"] ?? 'City';
            $postData->billingAddress->countryCode = $data["countryCode"] ?? "USA";

            $outlet = config('ngenius.outlet');

            $json = json_encode($postData);
            // return $postData;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, config('ngenius.api') . "transactions/outlets/" . $outlet . "/orders");
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
            // $ref = "3d0bf4f9-63d9-4457-a8ce-9ad4abc3ad9e";
            // $ref = "51bc0e11-97d4-46ad-94f7-e7e1ca25a888";

            // return $postData;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, config('ngenius.api') . "transactions/outlets/" . $outlet . "/orders/" . $ref);
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
