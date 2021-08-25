<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Session;
use Yajra\DataTables\Facades\DataTables;


class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_title = 'Coupon Code';
        $page_description = 'All Coupons list page';
        if ($request->ajax()) {
            $coupons = Coupon::all();
            return Datatables::of($coupons)
                ->addIndexColumn()
                ->editColumn('status', function ($couponsList) {
                    $status = '';
                    if ($couponsList->status == 1) {
                        $status = '<span class="label font-weight-bold label-lg label-light-success label-inline">Active</span>';
                    } elseif ($couponsList->status == 0) {
                        $status = '<span class="label font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
                    }
                    return $status;
                })->editColumn('discount_type', function ($couponsList) {
                    return str_replace('_', ' ', $couponsList->discount_type);
                })
                ->editColumn('expiry_date', function ($couponsList) {
                    $expiryDate = Carbon::parse($couponsList->expiry_date);
                    return $expiryDate->format('m/d/Y');
                })
                ->editColumn('amount', function ($couponsList) {
                    if ($couponsList->discount_type == 'fixed_product' or $couponsList->discount_type == 'fixed_cart') {
                        $amount = $couponsList->amount;
                    } else {
                        $amount = $couponsList->amount . '%';
                    }
                    return $amount;
                })->addColumn('action', function ($couponsList) {
                    return view('admin.coupons.coupon_datatable', compact('couponsList'))->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view("admin.coupons.index", compact('page_title', 'page_description'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $page_title = 'Coupon Code';
        $page_title = 'Add category';
        $page_description = 'Add category here';
        return view('admin.coupons.add', compact('page_title', 'page_description'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:coupons',
            'discount_type' => 'required',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'start_date' => 'required',
            'expiry_date' => 'required',
            'minimum_amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'usage_limit' => 'required|integer',
            'usage_limit_per_user' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return redirect('admin/coupons/add')->withErrors($validator)->withInput();
        } else {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $expiry_date = date('Y-m-d', strtotime($request->expiry_date));

            $coupon = new Coupon;

            $coupon->code = $request->code;
            $coupon->description = $request->description;
            $coupon->discount_type = $request->discount_type;
            $coupon->amount = $request->amount;
            $coupon->start_date = $start_date;
            $coupon->expiry_date = $expiry_date;
            $coupon->minimum_amount = $request->minimum_amount;
            $coupon->usage_limit = $request->usage_limit;
            $coupon->usage_limit_per_user = $request->usage_limit_per_user;
            $coupon->created_by = 1;
            $coupon->status = $request->status;
            $coupon->save();
            Session::flash('message', 'Successfully added!');
            return redirect('/admin/coupons');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Coupon $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Coupon $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Edit product';
        $page_description = 'Product info';
        $coupon_data = Coupon::find($id);
        $start_date = date('m/d/Y', strtotime($coupon_data->start_date));
        $expiry_date = date('m/d/Y', strtotime($coupon_data->expiry_date));

        return view('admin.coupons.edit', compact('page_title', 'page_description', 'coupon_data', 'start_date', 'expiry_date'));
        // redirect('/admin/coupons/edit/{{$coupon->id}}');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Coupon $coupon
     * @return \Illuminate\Http\RedirectResponse
     */

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'discount_type' => 'required',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'start_date' => 'required',
            'expiry_date' => 'required',
            'minimum_amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'usage_limit' => 'required|integer',
            'usage_limit_per_user' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return redirect('admin/coupons/edit/' . $request->id)->withErrors($validator)->withInput();
        } else {
            //check coupon already exist
            $code = $request->code;
            // $couponInfo = $this->Coupon->getcode($code);
            $couponInfo = Coupon::where('code', '=', $code)->get();

            if (count($couponInfo) > 1) {
                return redirect()->back()->withErrors('The code has already been taken.')->withInput();
            } else {
                $start_date = date('Y-m-d', strtotime($request->start_date));
                $expiry_date = date('Y-m-d', strtotime($request->expiry_date));
                $coupon = Coupon::find($request->id);
                $coupon->code = $request->code;
                $coupon->description = $request->description;
                $coupon->discount_type = $request->discount_type;
                $coupon->amount = $request->amount;
                $coupon->start_date = $start_date;
                $coupon->expiry_date = $expiry_date;
                $coupon->minimum_amount = $request->minimum_amount;
                $coupon->usage_limit = $request->usage_limit;
                $coupon->usage_limit_per_user = $request->usage_limit_per_user;
                $coupon->created_by = 1;
                $coupon->status = $request->status;
                $coupon->save();
                Session::flash('message', 'Successfully updated!');
                return redirect('admin/coupons');
            }
        }

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Coupon $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $coupon = Coupon::find($id);
            $coupon->delete();

        } catch (QueryException $e) {
            return response()->json($e);
        }
        return response()->json(true);
    }
}
