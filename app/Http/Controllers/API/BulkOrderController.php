<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Models\BulkOrder;
use App\Models\BulkOrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Mail\BulkOrderMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Models\Settings;

class BulkOrderController extends BaseController
{
    /**
     * Create Bulk order api
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
       
        # code...
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'items' => 'required|array|min:1',
            'email' => 'required|email',
            'phone' => 'required',
            'description' => 'required|string'

        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {
                $user_id = $request->user()->id;
                $bulkorder = new  BulkOrder;
                $bulkorder->name = $request->name;
                $bulkorder->user_id = $user_id;
                $bulkorder->email = $request->email;
                if(isset($request->gstin)){
                    $bulkorder->gstin = $request->gstin;
                }
            
                $bulkorder->phone = $request->phone;
                $bulkorder->description = $request->description;
              /*   if (isset($request->business_or_customer) == 1) {
                    $bulkorder->business_or_customer = $request->business_or_customer;
                } */
                $bulkorder->save();
                $bulkOrderLastId = $bulkorder->id;
                if (count($request->items) > 0) {

                    foreach ($request->items as $data) {
                        $status = "Pending";
                        $BulkOrderItem = new BulkOrderItem;
                        $BulkOrderItem->product_or_category_details =  $data['product_or_category_details'];
                        $BulkOrderItem->quantity = $data['quantity'];
                    /*  $BulkOrderItem->brand = $data['brand']; */
                        $BulkOrderItem->order_id = $bulkOrderLastId;
                        $BulkOrderItem->status = $status;
                        $BulkOrderItem->save();
                        $data =  $BulkOrderItem;
                    }
                }
            
               /*  $email_send = $bulkorder->email; 
                Mail::to($email_send)->send(new BulkOrderMail($data,$bulkorder));
                $email_data = Settings::where('id','>=',20)
                ->get();
                $bulk_order =  $email_data[0]['value'];
                
                $data_co = explode(',',$bulk_order);

                Mail::to($data_co)->send(new BulkOrderMail($data,$bulkorder)); */
                return $this->sendResponse($data, 'Bulk order items added successfully.');
          
        }
    }

    /**
     * VIEW Bulk order api
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        # code...
        $BulkOrder = new BulkOrder;
        $user = Auth::user()->id;
        
    $query = $BulkOrder::select('id AS order_id','created_at as placed_on')->where('user_id',$user)->orderBy('id',"DESC")->get();
        foreach ($query as $data) {
         $items =  DB::table('bulk_order_items')->select('bulk_order_items.*')->where('order_id',$data['order_id'])
           ->orderBy('order_id',"DESC") 
         ->get();
        $data->items = $items;
        }
    if (!$query->isEmpty()) {
     return $this->sendResponse($query,'Bulk order items list');
        } else {
            return $this->sendError('Bulk order details not found.');
        }
    }


    // /**
    //  * view Bulk details order api
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    
    public function showBulkDetails($id)
    {
        # code...
        $BulkOrder = new BulkOrder;
        $user = Auth::user()->id;

        $query = $BulkOrder::select('id AS order_id','created_at as placed_on')->where('id',$id)->first();
        if($query){
           $item = DB::table('bulk_order_items')->join('bulk_orders','bulk_orders.id','=','bulk_order_items.order_id')
            ->where('user_id',$user)
            ->where('bulk_order_items.order_id',$id)
            ->select(['bulk_order_items.*'])
            ->get();
            $query->items   = $item;
        }

        if($query){
            return $this->sendResponse($query,'Bulk order Details');
        }else{
            return $this->sendError('Bulk order details not found');
        }
    }

    /**
     * Create Bulk order with bid price
     *
     * @return \Illuminate\Http\Response
     */
    public function createByProductPage(Request $request)
    {  
        # code...
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'sku' => 'required',
            'bid_price' => 'required',
            'quantity' => 'required|integer',
            'email' => 'required|email',
            'phone' => 'required',
            'description' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {
                $user_id = $request->user()->id;
                $bulkorder = new  BulkOrder;
                $bulkorder->name = $request->name;
                $bulkorder->user_id = $user_id;
                $bulkorder->email = $request->email;
                if(isset($request->gstin)){
                    $bulkorder->gstin = $request->gstin;
                }
            
                $bulkorder->phone = $request->phone;
                $bulkorder->description = $request->description;
              
                $bulkorder->save();
                $bulkOrderLastId = $bulkorder->id;

                $status = "Pending";
                $BulkOrderItem = new BulkOrderItem;
                $BulkOrderItem->product_or_category_details =  $request->sku;
                $BulkOrderItem->quantity = $request->quantity;
                $BulkOrderItem->bid_price = $request->bid_price;
                $BulkOrderItem->order_id = $bulkOrderLastId;
                $BulkOrderItem->status = $status;

                $BulkOrderItem->save();
                $data =  $BulkOrderItem;

               /*  if (count($request->items) > 0) {

                    foreach ($request->items as $data) {
                        $status = "Pending";
                        $BulkOrderItem = new BulkOrderItem;
                        $BulkOrderItem->product_or_category_details =  $data['product_or_category_details'];
                        $BulkOrderItem->quantity = $data['quantity'];
                      // $BulkOrderItem->brand = $data['brand'];  
                        $BulkOrderItem->order_id = $bulkOrderLastId;
                        $BulkOrderItem->status = $status;
                        $BulkOrderItem->save();
                        $data =  $BulkOrderItem;
                    }
                } */
            
               /*  $email_send = $bulkorder->email; 
                Mail::to($email_send)->send(new BulkOrderMail($data,$bulkorder));
                $email_data = Settings::where('id','>=',20)
                ->get();
                $bulk_order =  $email_data[0]['value'];
                
                $data_co = explode(',',$bulk_order);

                Mail::to($data_co)->send(new BulkOrderMail($data,$bulkorder)); */
                return $this->sendResponse($data, 'Successfully sumbited your bid request.');
          
        }
    }
}