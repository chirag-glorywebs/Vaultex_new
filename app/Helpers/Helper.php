<?php

namespace App\Helpers;

use App\Mail\AdminPlaceOrderMail;
use App\Mail\PlaceOrderMail;
use App\Models\Order;
use App\Models\Order_products;
use App\Models\OrderProductAttribute;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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
            $postData->merchantAttributes->redirectUrl = "http://sbmmarketplace.com/thank-you?order_id=" . $data['order_id'];
            $postData->merchantAttributes->skipConfirmationPage = true;
            $postData->merchantAttributes->cancelUrl = "http://sbmmarketplace.com/thank-you?order_id=" . $data['order_id'];
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

    public static function sendMailOnOrder($order_id, $user_id)
    {
        $cart_data = DB::table('orders')
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('price_lists', 'products.sku', '=', 'price_lists.item_no')
            ->select(
                'orders.date_purchased',
                'products.sale_price',
                'products.regular_price',
                'order_products.sub_total',
                'order_products.product_id',
                'order_products.product_name',
                'order_products.product_quantity',
                'order_products.product_price',
                'products.main_image',
                DB::raw('COALESCE(price_lists.list_price, products.regular_price) as uprice'),
            )
            ->where('orders.id', $order_id)
            ->where('orders.userid', $user_id)
            ->get();

        foreach ($cart_data as $mainimg) {
            if (!empty($mainimg->main_image) && file_exists($mainimg->main_image)) {
                $mainimg->main_image = asset($mainimg->main_image);
            } else {
                $mainimg->main_image = asset('uploads/product-placeholder.jpg');
            }
        }

        $order = DB::table('orders')
            ->select('id', 'customer_street_address', 'customer_landmark', 'customers_city', 'customers_country', 'date_purchased', 'coupon_amount', 'shipping_cost', 'total_tax', 'order_price', 'payment_method')
            ->where('orders.id', $order_id)
            ->first();

        $estdate = date('Y-m-d', strtotime(' +6 day'));
        $order->estimate_delivery_date  = $estdate;

        $currancy = Settings::select('value')
            ->where('id', 17)
            ->first();

        $user_info = User::where('id', $user_id)->get();
        $email_send  = User::where('id', $user_id)->select('email')->first();

        Mail::to($email_send)->send(new PlaceOrderMail($user_info, $cart_data, $order, $currancy));
        $adminEmail = Settings::select('value')->where('id', 25)->get();
        $place_order =  $adminEmail[0]['value'];
        $admin = explode(',', $place_order);

        Mail::to($admin)->send(new AdminPlaceOrderMail($user_info, $cart_data, $order, $currancy));

        // Self::salesOrderApi($order_id);

        return 'success';
    }

    public static function salesOrderApi($order_id)
    {
        $order = Order::query()
            ->find($order_id);
        // dd($order);

        $user = User::query()
            ->find($order->userid);
        // dd($user);

        $data = [
            "CardCode" => $user->vendor_code,
            "DocDate" => $order->date_purchased->format('Y-m-d'),
            "DocDueDate" => $order->date_purchased->format('Y-m-d'),
            "TaxDate" => $order->date_purchased->format('Y-m-d'),
            "NumAtCard" => "",
            "DiscountPercent" => "",
            "Comments" => "",
        ];
        $documentLines = [];

        $orderProductAttributes = OrderProductAttribute::query()
            ->where('order_id', $order->id)
            ->get();

        foreach ($orderProductAttributes as $attr) {
            $documentLines[] = [
                "ItemCode" => $attr->item_code,
                "Quantity" => $attr->quantity,
                "TaxCode" => "5%",
                "UnitPrice" => $attr->product_price,
                "WarehouseCode" => "",
                "DiscountPercent" => ""
            ];
        }

        $data['documentLines'] = $documentLines;
        // dd($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://192.168.22.4:50000/b1s/v1/Login");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/vnd.ni-payment.v2+json",
            "Accept: application/vnd.ni-payment.v2+json"
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "CompanyDB" => "TEST_MOBILE",
            "Password" => "123456",
            "UserName" => "manager"
        ]));
        $output = curl_exec($ch);
        $output = json_decode($output);
        curl_close($ch);

        $sessionId = $output->SessionId;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://192.168.22.4:50000/b1s/v1/Orders");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/vnd.ni-payment.v2+json",
            "Accept: application/vnd.ni-payment.v2+json",
            "Cookie: B1SESSION=" . $sessionId . "; ROUTEID=.node0"
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $output = curl_exec($ch);
        $output = json_decode($output);
        curl_close($ch);

        return 'success';
    }
}
