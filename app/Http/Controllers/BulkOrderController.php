<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBulkOrder;
use App\Models\BulkOrder;
use App\Models\BulkOrderItem;
use App\Models\Products;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class BulkOrderController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $page_title = 'Bulk Orders';
        $page_description = 'Bulk Orders list page';
        if ($request->ajax()) {
            /*$bulkOrderData = BulkOrder::leftJoin('bulk_order_items', 'bulk_order_items.order_id', '=', 'bulk_orders.id')
                ->select('bulk_order_items.id', 'bulk_order_items.product_or_category_details', 'bulk_order_items.quantity', 'bulk_order_items.order_id', 'bulk_order_items.status', 'bulk_order_items.brand', 'bulk_orders.id', 'bulk_orders.name', 'bulk_orders.email', 'bulk_orders.phone', 'bulk_orders.description')
                ->get();*/
            $bulkOrderData = BulkOrder::all();
            return DataTables::of($bulkOrderData)
                ->addIndexColumn()
                ->editColumn('status', function ($bulkOrderList) {
                    $status = '';
                    if ($bulkOrderList->status == 'Pending') {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Pending</span>';
                    } elseif ($bulkOrderList->status == 'Completed') {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Completed</span>';
                    } elseif ($bulkOrderList->status == 'Cancel') {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Cancel</span>';
                    } elseif ($bulkOrderList->status == 'Return') {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Return</span>';
                    }
                    return $status;
                })
                ->editColumn('created_at', function ($orderList) {
                    $createdDateFormat = Carbon::parse($orderList->created_at);
                    return $createdDateFormat->format('m/d/Y');
                })
                ->addColumn('action', function ($bulkOrderList) {
                    return view('admin.bulkorders.bulk_orders_datatable', compact('bulkOrderList'))->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.bulkorders.index', compact('page_title', 'page_description'));
    }

    /**
     * @param StoreBulkOrder $request
     */
    public function storeOrdersBulkData(StoreBulkOrder $request)
    {
        $user = Auth::user();
        $request->validated();
        $addorderBulkData = new BulkOrder();
        $addorderBulkData->name = $request->get('name');
        $addorderBulkData->email = $request->get('email');
        $addorderBulkData->phone = $request->get('phone');
        $addorderBulkData->description = $request->get('description');
        $addorderBulkData->user_id = $user->id;
        $addorderBulkData->gstin = $request->get('gstin');
        $addorderBulkData->save();
        $bulkOrderLastId = $addorderBulkData->id;
        $addBuldOrderItems = new BulkOrderItem();
        if (count($request->product_or_category_details) > 0) {
            foreach ($request->product_or_category_details as $items => $itemValue) {
                $addBuldOrderItems->product_or_category_details = $request->get('product_or_category_details')[$items];
                $addBuldOrderItems->quantity = $request->get('quantity')[$items];
                $addBuldOrderItems->brand = $request->get('brand')[$items];
                $addBuldOrderItems->order_id = $bulkOrderLastId;
                $addBuldOrderItems->status = $request->get('status');
                $addBuldOrderItems->save();
            }
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $page_title = 'Edit Bulk Order';
        $page_description = 'Edit bulk order page';
        $bulkOrderDataShow = BulkOrder::findOrFail($id);
        $bulkOrderItemDataShow = BulkOrderItem::findOrFail($id);
        return view('admin.bulkorders.edit_bulk_order', compact('bulkOrderDataShow', 'bulkOrderItemDataShow', 'page_title', 'page_description'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $page_title = 'Show Bulk Order';
        $page_description = 'Show bulk order page';
        $bulkOrderDataShow = BulkOrder::findOrFail($id);
        $bulkOrderItemDataShow = BulkOrderItem::where('order_id', $id)->get();
        return view('admin.bulkorders.show_bulk_orders', compact('bulkOrderDataShow', 'bulkOrderItemDataShow','page_title','page_description'));
    }

    /**
     * @param StoreBulkOrder $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBulkOrder(StoreBulkOrder $request)
    {
        //Update Bulk Order Table
        if ($request->has('business_or_customer')) {
            $accept = 1;
        } else {
            $accept = 0;
        }
        DB::table('bulk_orders')
            ->where('id', $request->order_id)->update([
                'business_or_customer' => $accept,
                'gstin' => $request->get('gstin'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'description' => $request->input('description'),
            ]);
        //Update Bulk Order Items Table
        $proCatDetails = $request->get('product_or_category_details');
        $quantity = $request->get('quantity');
        $brand = $request->get('brand');
        $status = $request->get('status');
        $orderId = $request->get('order_id');
        $countItems = count($proCatDetails);
        for ($i = 0; $i < $countItems; $i++) {
            DB::table('bulk_order_items')
                ->where('order_id', $orderId)
                ->update([
                    'product_or_category_details' => $proCatDetails[$i],
                    'quantity' => $quantity[$i],
                    'brand' => $brand[$i],
                    'status' => $status,
                ]);
        }
        return redirect()->route('bulk-order');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete($id)
    {
        $bulkOrderDelete = BulkOrder::find($id);
        $bulkOrderDelete->delete();
        Session::flash('message', 'Successfully delete!');
        return redirect()->route('bulk-order');;
    }
}
