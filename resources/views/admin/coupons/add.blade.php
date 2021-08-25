{{-- Extends layout --}}
@extends('admin.layout.default')

{{-- Content --}}
@section('content')

    <div class="card card-custom">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form class="form" method="post" action="{{ URL::to('/admin/coupons')}}">
            {{csrf_field()}}
            <div class="card-body">
                <div class="form-group">
                    <label>Coupon Code:</label>
                    <input type="text" class="form-control form-control-solid" name="code" value="{{ old('code') }}"
                           placeholder="Enter coupon code"/>
                </div>
                <div class="form-group">
                    <label>Coupon Description:</label>
                    <textarea class="form-control" name="description">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Discount Type:</label>
                    <select name="discount_type" class="form-control form-control-solid">
                        <option value="fixed_cart" selected>Cart Discount</option>
                        <option value="percent">Cart % Discount</option>
                        <option value="fixed_product">Product Discount</option>
                        <option value="percent_product">Product % Discount</option>
                    </select>
                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                    Determines the type of discount that will be applied. Options: percent, fixed cart and fixed product. Default is fixed cart.</span>
                </div>
                <div class="form-group">
                    <label>Coupon Amount</label>
                    <input type="text" class="form-control form-control-solid" name="amount" value="{{ old('amount') }}"
                           placeholder="Enter seo name"/>
                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;"> The amount of discount.</span>
                </div>
                <div class="form-group">
                    <label>Coupon Start Date</label>
                    <div class="input-group">
                        <input type="text" id="kt_datepicker_1" class="form-control form-control-solid"
                               value="{{ old('start_date') }}" name="start_date" placeholder="Select a Start Date"/>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Coupon Expiry Date</label>

                    <div class="input-group">
                        <input type="text" class="form-control" name="expiry_date" value="{{ old('expiry_date') }}"
                               placeholder="Select a Expiry Date" id="kt_datepicker_2">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Minimum Spend:</label>
                    <input type="text" class="form-control form-control-solid" name="minimum_amount"
                           value="{{ old('minimum_amount') }}" placeholder="Enter minimum spend"/>
                </div>
                <div class="form-group">
                    <label>Usage Limit Per Coupon </label>
                    <input type="text" class="form-control form-control-solid" name="usage_limit"
                           value="{{ old('usage_limit') }}" placeholder="Enter usage Limit Per Coupon"/>
                </div>
                <div class="form-group">
                    <label>Usage Limit Per User </label>
                    <input type="text" class="form-control form-control-solid" name="usage_limit_per_user"
                           value="{{ old('usage_limit_per_user') }}" placeholder="Enter usage Limit Per User "/>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                        <option value="1">Active</option>
                        <option value="0 ">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Add</button>
                <a class="btn btn-secondary" href="{{ route('coupons.index') }}">Cancel</a>
            </div>
        </form>

    </div>

@endsection
{{-- Scripts Section --}}
@section('scripts')
    <!--begin::Page Vendors(used by this page)-->
    <script>
        var KTBootstrapDatepicker = function () {

            var arrows;
            if (KTUtil.isRTL()) {
                arrows = {
                    leftArrow: '<i class="la la-angle-right"></i>',
                    rightArrow: '<i class="la la-angle-left"></i>'
                }
            } else {
                arrows = {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            }
            var demos = function () {
                // input group layout

                $('#kt_datepicker_1').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: arrows,
                    autoclose: true

                });
                $('#kt_datepicker_2').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: arrows,
                    autoclose: true
                });
            };

            return {
                // public functions
                init: function () {
                    demos();
                }
            };
        }();

        jQuery(document).ready(function () {
            KTBootstrapDatepicker.init();
        });
    </script>
@endsection
