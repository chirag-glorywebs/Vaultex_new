<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\User;
use App\Models\User_addresses;
use App\Models\Order_statuses;
use App\Models\Order_products;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Manage_order_status;
use Orders;
use Session;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    /* get the all order*/
    public function get(Request $request)
    {
        $user = Auth::user();
        // dd($user);
        $page_title = 'Order';
        $page_description = 'All order list page';
        // $orders = DB::table('orders')
        //     ->join('users','users.id','=','orders.userid')
        // ->join('order_statuses','order_statuses.id','=','orders.orders_status')
        // ->select('orders.*','users.first_name','users.last_name','order_statuses.status')
        // ->where('orders.userid',$user)
        // ->get();
        //    dd($user);

        //     $orders = DB::table('orders')
        //     ->join('users','orders.userid', '=','users.id')
        //     ->join('order_statuses','orders.orders_status', '=', 'order_statuses.id')
        //    ->select('orders.*','order_statuses.status','users.name','users.first_name','users.last_name')
        //    ->where('orders.userid',$user_id)
        //    ->get();
        //    dd($orders);


        // dd($user);

        if ($request->ajax()) {
            // $orders = Order::join('order_statuses', 'order_statuses.id', '=', 'orders.status')
            //     ->join('users', 'users.id', '=', 'orders.userid')
            //     ->select('orders.*', 'order_statuses.status as statustext', 'users.first_name', 'users.last_name')
            //     ->get();

            // $orders = DB::table('orders')
            //         ->join('users','users.id','=','orders.userid')
            //     ->join('order_statuses','order_statuses.id','=','orders.orders_status')
            //     ->select('orders.*','users.first_name','users.last_name','order_statuses.status')
            //     ->where('orders.userid',$user_id)
            //     ->get();
            //     dd($orders);
            $orders = DB::table('orders')
                ->join('users', 'users.id', '=', 'orders.userid')
                ->join('order_statuses', 'order_statuses.id', '=', 'orders.orders_status')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'order_statuses.status')
                ->where('orders.userid', $user->id)
                ->get();
            //    dd($orders);

            return Datatables::of($orders)
                ->editColumn('first_last_name', function ($orderList) {
                    return $orderList->first_name . ' ' . $orderList->last_name;
                })
                ->editColumn('created_at', function ($orderList) {
                    $createdDateFormat = Carbon::parse($orderList->created_at);
                    return $createdDateFormat->format('m/d/Y');
                })
                ->addIndexColumn()
                ->addColumn('action', function ($orderList) {
                    return view('admin.orders.order_datatable', compact('orderList'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.orders.orders', compact('page_title', 'page_description'));
    }
    /* edit the order info and edit and save changes*/
    public function edit($id)
    {
        $page_title = 'Edit Order';
        $page_description = 'Edit order page';
        $allorders = Order::join('order_statuses', 'order_statuses.id', '=', 'orders.orders_status')
            ->join('user_addresses', 'user_addresses.userid', '=', 'orders.userid')
            ->select(
                'orders.*',
                'orders.id AS orderid',
                'order_statuses.*',
                'user_addresses.name',
                'user_addresses.email',
                'user_addresses.phone',
                'user_addresses.pincode',
                'user_addresses.address',
                'user_addresses.city',
                'user_addresses.state',
                'user_addresses.country',
                'user_addresses.landmark'
            )->where('orders.id', '=', $id)->get();
        
        $ordersstatus = Order_statuses::all();
        
        $ordersproduct = Order_products::join('products', 'products.id', '=', 'order_products.product_id')
            ->select('order_products.*', 'products.product_name', 'products.sku', 'products.main_image')
            ->where('order_id', '=', $id)
            ->get();

        $manage_orders = DB::table('Manage_order_status')->join('orders', 'orders.id', '=', 'Manage_order_status.orderid')
            ->select('order_status_date')
            ->where('orderid', $id)->first();
    
        $manage_ordersdata = $manage_orders->order_status_date;
        return view('admin.orders.add_orders', compact('page_title', 'manage_ordersdata', 'page_description', 'ordersstatus', 'allorders', 'ordersproduct'));
    }


    /* update order information*/
    // public function update(Request $req)
    // {

    //     $orders = Order::find($req->id);
    //     $orders->status = $req->status;
    //     $orders->save();


    //     // $req->date;
    //     Session::flash('message', 'Successfully updated!');
    //     return redirect('/admin/orders');
    // }
    public function update(Request $request)
    {
        # code...
        $orders  = Order::find($request->id);
        $orders->orders_status = $request->status;
        $orders->save();
        $Manage_order_status = new Manage_order_status;
        $Manage_order_status->orderid = $orders->id;
        $Manage_order_status->order_status_id = $request->status;
        $Manage_order_status->order_status_date = $request->date;
        $Manage_order_status->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/orders');
    }

    /* delete order*/
    public function delete($id)
    {
        $orders = Order::find($id);
        $orders->delete();
        Session::flash('message', 'Successfully delete!');
        return redirect('/admin/orders');
    }


/* get the all order status*/
    public function getStatus(Request $request)
    {
        $page_title = 'Order Status';
        $page_description = 'All order status list page';
        if ($request->ajax()) {
            $orderstatus = order_statuses::all();
            return Datatables::of($orderstatus)
                ->addIndexColumn()
                ->addColumn('action', function ($orderList) {
                    return view('admin.orders.order_status_datatable', compact('orderList'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.orders.order_status', compact('page_title', 'page_description'));
    }
    /* add new order status page*/
    public function addStatus()
    {
        $page_title = 'Add order status';
        $page_description = 'Add order status here';
        return view('admin.orders.add_order_status', compact('page_title', 'page_description'));
    }
    /* add new order status in db*/
    public function createStatus(Request $req)
    {
        $req->validate([
            'status' => 'required',
        ]);
        $orderstatus = new order_statuses;
        $orderstatus->status = $req->status;
        $orderstatus->save();
        Session::flash('message', 'Successfully added!');
        return redirect('/admin/orderstatus');
    }
    /* delete order status*/
    public function deleteStatus($id)
    {
        $orderstatus = order_statuses::find($id);
        $orderstatus->delete();
        Session::flash('message', 'Successfully delete!');
        return redirect('/admin/orderstatus');
    }
    /* edit order status information*/
    public function editStatus($id)
    {
        $page_title = 'Edit order status';
        $page_description = 'Edit order status info';
        $orderstatus = order_statuses::find($id);
        return view('admin.orders.add_order_status', compact('page_title', 'page_description', 'orderstatus'));
    }
    /* update order status information*/
    public function updateStatus(Request $req)
    {
        $req->validate([
            'status' => 'required',
        ]);
        $orderstatus = order_statuses::find($req->id);
        $orderstatus->status = $req->status;
        $orderstatus->save();
        Session::flash('message', 'Successfully updated!');
        return redirect('/admin/orderstatus');
    }
}
