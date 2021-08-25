{{-- Extends layout --}}
@extends('admin.layout.default')

{{-- Content --}}
@section('content')
    @if (Session::has('message'))
        <div class="alert alert-success">{{ Session::get('message') }}</div>
    @endif
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
        <form class="form" method="post" action="{{ route('home-page-update')  }}" enctype="multipart/form-data">
            {{csrf_field()}}
            @if(!empty($homeList[0]))
                <div class="card-body">
                    <div class="form-group row">
                        <label>Offer Top 1:</label>
                        <input type="hidden" name="id"
                               value="{{ $homeList[0]->id }}">
                        @if($homeList[0]->offer_top_1)
                            <input type="hidden" name="old_offer_top_1"
                                   value="{{ old($homeList[0]->offer_top_1,$homeList[0]->offer_top_1 ?? null) }}">
                            <img src="{{ URL::to($homeList[0]['offer_top_1']) }}" width="70" height="70">
                        @endif
                        <div class="col-sm-10 col-md-7">
                            <input type="file" class="form-control form-control-solid" name="offer_top_1"
                                   accept="image/*"/>
                            <span class="help-block">Upload a "Offer Top 1" Image</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Offer Top 1 Url:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="offer_top_1_url"
                                   placeholder="Enter Offer Top 1 Url"
                                   value="{{ old($homeList[0]->offer_top_1_url,$homeList[0]->offer_top_1_url ?? null) }}"/>
                            <span class="help-block">Enter a "Offer Top 1 Url"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Offer Top 2:</label>
                        @if($homeList[0]->offer_top_2)
                            <input type="hidden" name="old_offer_top_2"
                                   value="{{ old($homeList[0]->offer_top_2,$homeList[0]->offer_top_2 ?? null) }}">
                            <img src="{{ URL::to($homeList[0]['offer_top_2']) }}" width="70" height="70">
                        @endif
                        <div class="col-sm-10 col-md-7">
                            <input type="file" class="form-control form-control-solid" name="offer_top_2"
                                   accept="image/*"/>
                            <span class="help-block">Upload a "Offer Top 2" Image</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Offer Top 2 Url:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="offer_top_2_url"
                                   placeholder="Enter Offer Top 2 Url"
                                   value="{{ old($homeList[0]->offer_top_2_url,$homeList[0]->offer_top_2_url ?? null) }}"/>
                            <span class="help-block">Enter a "Offer Top 2 Url"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Offer Top 3:</label>
                        @if($homeList[0]->offer_top_3)
                            <input type="hidden" name="old_offer_top_3"
                                   value="{{ old($homeList[0]->offer_top_3,$homeList[0]->offer_top_3 ?? null) }}">
                            <img src="{{ URL::to($homeList[0]['offer_top_3']) }}" width="70" height="70">
                        @endif
                        <div class="col-sm-10 col-md-7">
                            <input type="file" class="form-control form-control-solid" name="offer_top_3"
                                   accept="image/*"/>
                            <span class="help-block">Upload a "Offer Top 3" Image</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Offer Top 3 Url:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="offer_top_3_url"
                                   placeholder="Enter Offer Top 3 Url"
                                   value="{{ old($homeList[0]->offer_top_3_url,$homeList[0]->offer_top_3_url ?? null) }}"/>
                            <span class="help-block">Enter a "Offer Top 3 Url"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Offer Bottom 1:</label>
                        @if($homeList[0]->offer_bottom_1)
                            <input type="hidden" name="old_offer_bottom_1"
                                   value="{{ old($homeList[0]->offer_bottom_1,$homeList[0]->offer_bottom_1 ?? null) }}">
                            <img src="{{ URL::to($homeList[0]['offer_bottom_1']) }}" width="70" height="70">
                        @endif
                        <div class="col-sm-10 col-md-7">
                            <input type="file" class="form-control form-control-solid" name="offer_bottom_1"
                                   accept="image/*"/>
                            <span class="help-block">Upload a "Offer Bottom 1" Image</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Offer Bottom 1 Contents:</label>
                        <div class="col-sm-10 col-md-8">
           <textarea id="kt-ckeditor"
                     name="offer_bottom_1_contents">{{$homeList[0]['offer_bottom_1_contents']}}</textarea>
                            <span class="help-block">Enter a "Offer Bottom 1 Contents"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Offer Bottom 1 Url:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="offer_bottom_1_url"
                                   placeholder="Enter Offer Bottom 1 Url"
                                   value="{{ old($homeList[0]->offer_bottom_1_url,$homeList[0]->offer_bottom_1_url ?? null) }}"/>
                            <span class="help-block">Enter a "Offer Bottom 1 Url"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Offer Bottom 2:</label>
                        @if($homeList[0]->offer_bottom_2)
                            <input type="hidden" name="old_offer_bottom_2"
                                   value="{{ old($homeList[0]->offer_bottom_2,$homeList[0]->offer_bottom_2 ?? null) }}">
                            <img src="{{ URL::to($homeList[0]['offer_bottom_2']) }}" width="70" height="70">
                        @endif
                        <div class="col-sm-10 col-md-7">
                            <input type="file" class="form-control form-control-solid" name="offer_bottom_2"
                                   accept="image/*"/>
                            <span class="help-block">Upload a "Offer Bottom 2" Image</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Offer Bottom 2 Contents:</label>
                        <div class="col-sm-10 col-md-8">
               <textarea id="kt-ckeditor1"
                         name="offer_bottom_2_contents">{{$homeList[0]['offer_bottom_2_contents']}}</textarea>
                            <span class="help-block">Enter a "Offer Bottom 2 Contents"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Offer Bottom 2 Url:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="offer_bottom_2_url"
                                   placeholder="Enter Offer Bottom 2 Url"
                                   value="{{ old($homeList[0]->offer_bottom_2_url,$homeList[0]->offer_bottom_2_url ?? null) }}"/>
                            <span class="help-block">Enter a "Offer Bottom 2 Url"</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary mr-2">Update</button>
                    <a class="btn btn-secondary" href="{{ route('dashboard') }}">Back</a>
                </div>
            @else
                <h4 style="margin-top: 30px;color: #808080d1">No records found</h4>
                <div class="card-footer">
                    <a class="btn btn-secondary" href="{{ route('dashboard') }}">Back</a>
                </div>
            @endif

        </form>
    </div>

@endsection
{{-- Scripts Section --}}
<style>
    label {
        padding-top: 7px;
        font-weight: bold !important;
        min-width: 150px !important;
    }

    .form {
        margin-left: 150px;
    }

    .help-block {
        font-weight: normal;
        font-size: 11px;
        margin-bottom: 0;
        margin-top: 0;
    }
</style>
@section('scripts')
    <!--begin::Page Vendors(used by this page)-->
    <script src="{{ asset('plugins/custom/ckeditor/ckeditor-classic.bundle.js?v=7.1.7') }}"></script>
    <!--end::Page Vendors-->
    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">
        var KTCkeditor = function () {
            // Private functions
            var demos = function () {
                ClassicEditor
                    .create(document.querySelector('#kt-ckeditor'))
                    .then(editor => {
                        //console.log(editor);
                    })
                    .catch(error => {
                        //console.error(error);
                    });
            }
            var demos1 = function () {
                ClassicEditor
                    .create(document.querySelector('#kt-ckeditor1'))
                    .then(editor => {
                        //console.log(editor);
                    })
                    .catch(error => {
                        //console.error(error);
                    });
            }
            return {
                // public functions
                init: function () {
                    demos();
                    demos1();
                }
            };
        }();

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

            };

            return {
                // public functions
                init: function () {
                    demos();
                }
            };
        }();


        // Initialization
        jQuery(document).ready(function () {
            KTCkeditor.init();
            KTBootstrapDatepicker.init();
        });
        $('#kt_select2').select2({
            placeholder: "Select a category"
        });

        //Cancel button Refresh
        $(document).ready(function () {
            $(".cancel").click(function () {
                window.reload();
            });
        });
    </script>
    <!--end::Page Scripts-->
@endsection
