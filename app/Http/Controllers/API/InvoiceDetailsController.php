<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\invoice_detail;

class InvoiceDetailsController extends Controller
{

    
public function getInvoice(Request $request)
{
    # code...
 
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://192.168.10.20/api/Invoice/GetInvoiceDetails?CustomerCode=S07001',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
      
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);



//    $data =  file_get_contents($response);
   $response = json_decode($response,TRUE);
    // print_r($response);exit();
    // foreach ($response as $val){
    //         print_r($val);

            // echo $p =array_keys($response);
            $data = explode(',',$response);
            var_dump($data);
            echo $data['1   '];
            // 
// }





    // return response()->json($response);
    // $invoice_detail = new invoice_detail; 
    // $data = json_decode($response,true);
            //  $info = array($data);
            // return $info;

                // return $info1 = json_decode($info,true);
            // if(isset($info['DocNum'])){
            //     return "yse";

            // }else{
            //     return "no";
            // }
    // print_r($data);
    // foreach($data as $k=>$v)
    // {
    //     print_r($k['DocNum']);
    // }

    // while ( list($k, $v) = each($data))
    // {
    //     echo $k."-->".$v."<br>";
    // }
    // $bar = print_r(each($data));

        // foreach($data as $value){
        //     print_r($value);
        // }
 
    //    $data =  each($data);
//    return $data[10];
//    $arraydata = json_decode($data,TRUE);


    // print_r($arraydata['DocNum']);


    // $x = new invoice_detail();
    // $arraydata = array();
    // echo $arraydata['DocNum'];
//    return $arraydata;
    // return $arraydata[4];
    // $data = $arraydata['DocNum'];
    // return $data;
    
    
// return count($arraydata);
// $d = print_r(array_values($arraydata));
// return $d;

// foreach($arraydata as $key => $value){
//    return  $value->DocNum;
//         print_r ($value);
// }


//    return $daat =  $arraydata->pluck("DocNum");
   

//    return $arraydata[5];


    // return  $arraydata->DocNum;
   
    // foreach($response as $k=>$v){

    //     $invoice_detail->DocNum = $request->$response->DocNum;
    //     $invoice_detail->CardCode = $request->CardCode;
    //     $invoice_detail->CardName = $request->CardName;
    //     $invoice_detail->EmpName = $request->EmpName;
    //     $invoice_detail->Telephone = $request->Telephone; 
    //     $invoice_detail->USR_Moile = $request->USR_Moile;
    //     $invoice_detail->USR_Email = $request->USR_Email;
    // }


}
}
