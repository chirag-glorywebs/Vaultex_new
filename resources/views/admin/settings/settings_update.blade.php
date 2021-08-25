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
    <ul class="nav nav-tabs">
        <li><a data-toggle="tab" href="#tab1">General Settings</a></li>
        <li><a data-toggle="tab" href="#tab2">E-commerce-setting</a></li>
        <li><a data-toggle="tab" href="#tab3">Email Settings</a></li>
        <li><a data-toggle="tab" href="#tab4">Image Settings</a></li>
    </ul>
    <div class="tab-content">
        <!--------------------------------------------------------------------
            ----------- Set the all basic fields of products (tab1)--------------
            ---------------------------------------------------------------------->
        <div id="tab1" class="tab-pane fade in active show">
            <form class="form" method="post" action="{{ route('settings-update')  }}" enctype="multipart/form-data">
                {{csrf_field()}}
                @foreach($settings as $setting)@endforeach
                @if(!empty($setting))
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-10 col-md-10 row">
                            <label>Favicon:</label>

                            @if($settings[0]->name)

                            <input type="hidden" name="old_favicon" value="{{ old($settings[0]->name,$settings[0]->value ?? null) }}">
                            <img src="{{ URL::to($settings[0]['value']) }}" width="16px" height="16px" style="margin-top:10px;">
                            @endif

                            <input type="file" class="form-control form-control-solid" name="{{ $settings[0]->name }}" accept="image/*" />
                            <span class="help-block">Upload a "Favicon" Icon</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-10 col-md-10 row">
                            <label>Logo:</label>
                            @if($settings[1]->name)
                            {{--$settings->faviconimg_path--}}
                            <input type="hidden" name="old_logo" value="{{ old($settings[1]->name,$settings[1]->value ?? null) }}">
                            <img src="{{ URL::to($settings[1]['value']) }}">
                            @endif

                            <input type="file" class="form-control form-control-solid" name="{{ $settings[1]->name }}" accept="image/*" />
                            <span class="help-block">Upload a "Logo"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Website Name:</label>
                        <div class="col-sm-10 col-md-8">
                            {{--@foreach($settings as $key => $settingsValue)--}}
                            <input type="text" class="form-control form-control-solid" name="{{ $settings[2]->name }}" placeholder="Enter Website Name" value="{{ old($settings[2]->name,$settings[2]->value ?? null) }}" />
                            <span class="help-block">Enter a "Website Name"</span>
                            {{--@endforeach--}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Phone Number:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="{{ $settings[3]->name }}" placeholder="Enter Phone Number" value="{{ old($settings[3]->name,$settings[3]->value ?? null) }}" />
                            <span class="help-block">Enter a "Phone Number"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Email:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="{{ $settings[4]->name }}" placeholder="Enter Email Id" value="{{ old($settings[4]->name,$settings[4]->value ?? null) }}" />
                            <span class="help-block">Enter a "Email Id"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Company Address:</label>
                        <div class="col-sm-10 col-md-8">
                            <textarea id="kt-ckeditor" name="{{ $settings[5]->name }}">{{ old($settings[5]->name,$settings[5]->value ?? null) }}</textarea>
                            <span class="help-block">Enter a "Company Address"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Facebook Link:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="{{ $settings[6]->name }}" placeholder="Enter Facebook Link" value="{{ old($settings[6]->name,$settings[6]->value ?? null) }}" />
                            <span class="help-block">Enter a "Facebook Link"</span>

                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Linkedin Link:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="{{ $settings[7]->name }}" placeholder="Enter Linkedin Link" value="{{ old($settings[7]->name,$settings[7]->value ?? null) }}" />
                            <span class="help-block">Enter a "Linkedin Link"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>WhatsApp Link:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="{{ $settings[8]->name }}" placeholder="Enter WhatsApp Link" value="{{ old($settings[8]->name,$settings[8]->value ?? null) }}" />
                            <span class="help-block">Enter a "WhatsApp Link"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Twitter Link:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="{{ $settings[9]->name }}" placeholder="Enter Twitter Link" value="{{ old($settings[9]->name,$settings[9]->value ?? null) }}" />
                            <span class="help-block">Enter a "Twitter Link"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Youtube Link:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="{{ $settings[10]->name }}" placeholder="Enter Youtube Link" value="{{ old($settings[10]->name,$settings[10]->value ?? null) }}" />
                            <span class="help-block">Enter a "Youtube Link"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Header Tagline:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="{{ $settings[11]->name }}" placeholder="Enter Header Tagline" value="{{ old($settings[11]->name,$settings[11]->value ?? null) }}" />
                            <span class="help-block">Enter a "Header Tagline"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>Footer Copyright Text:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="{{ $settings[12]->name }}" placeholder="Enter Footer Copyright Text" value="{{ old($settings[12]->name,$settings[12]->value ?? null) }}" />
                            <span class="help-block">Enter a "Footer Copyright Text"</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-10 col-md-10 row">
                            <label>Footer Image:</label>
                            @if($settings[13]->name)
                            {{--$settings->faviconimg_path--}}
                            <input type="hidden" name="old_footer_image" value="{{ old($settings[13]->name,$settings[13]->value ?? null) }}">
                            <img src="{{ URL::to($settings[13]['value']) }}">
                            @endif

                            <input type="file" class="form-control form-control-solid" name="{{ $settings[13]->name }}" accept="image/*" />
                            <span class="help-block">Upload a "Footer Image"</span>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                        <label>FrontEnd URL:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="{{ $settings[14]->name }}" placeholder="Enter Frontend Link" value="{{ old($settings[14]->name,$settings[14]->value ?? null) }}" />
                            <span class="help-block">Enter a "Frontend URL"</span>
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

        <!--------------------------------------------------------------------
            ------------------- Set the gallery fields (Tab2)----------------------
            ---------------------------------------------------------------------->
        <div id="tab2" class="tab-pane fade">
            {{--<div id="tab2" class="tab-pane fade in active show">--}}
           {{-- <form action="{{URL::to('admin/settings/e-commerce-setting')}}" method="post"> --}} 
            <form action="{{route('e-commerce-setting')}}" method="post">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="vat" class="form-label">VAT:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="vat" placeholder="Enter VAT" value="{{ old($e_comm_setting[0]->name,$e_comm_setting[0]->value ?? null) }}" />
                            <span class="help-block">Enter a "VAT"</span>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="post_per_page" class="form-label">Post Per Page:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="post_per_page" placeholder="Enter Post Per page" value="{{ old($e_comm_setting[1]->name,$e_comm_setting[1]->value ?? null) }}" />
                            <span class="help-block">Enter a "Post Per page"</span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="currency" class="form-label">Currency:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="currency" placeholder="Enter currency" value="{{ old($e_comm_setting[2]->name,$e_comm_setting[2]->value ?? null) }}" />
                            <span class="help-block">Enter a "currency"</span>
                        </div>
                    </div>


                    <div class="form-group row" class="group">
                        <label for="shipping_cost">Shipping Cost:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="shipping_cost" placeholder="Enter Shipping Cost" value="{{ old($e_comm_setting[3]->name,$e_comm_setting[3]->value ?? null) }}" />
                            <span class="help-block">Enter a "Shipping Cost"</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <a class="btn btn-secondary" href="{{ route('dashboard') }}">Back</a>
                    </div>
                </div>
            </form>
         </div>

<!--------------------------------------------------------------------
            ----------- Set the all basic fields of products (tab3)------------------
            ---------------------------------------------------------------------->
        <div id="tab3" class="tab-pane fade">
            {{--<form method="post" action="{{URL::to('admin/settings/email-setting')}}">--}}
                <form method="post" action="{{route('email-setting')}}">
                    @csrf
                
                   <div class="card-body">
                    <div class="form-group row">
                        <label for="RegEmail" class="form-label">Registration Email:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid"  name="registration_email"    placeholder="Enter Email Address" value="{{ old($email_data[0]->name,$email_data[0]->value ?? null) }}" />
                            <span class="help-block">Enter a Email Address</span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="bulkOrderEmail" class="form-label">BulkOrder Email:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="bulkorder_email" placeholder="Enter Email Address" value="{{ old($email_data[1]->name,$email_data[1]->value ?? null) }}" />
                            <span class="help-block">Enter a "Email Addrress"</span>
                        </div>
                    </div>
                       <div class="card-footer">
                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <a class="btn btn-secondary" href="{{ route('dashboard') }}">Back</a>
                    </div>
                </div>
            </form>
        </div>

<!--------------------------------------------------------------------
            ----------- Set the all basic fields of products (tab4)------------------
            ---------------------------------------------------------------------->

        <div id="tab4" class="tab-pane fade"> 
            <form action="{{route('image-setting')}}" method="post">
                @csrf
    
                 <div class="card-body">

                    <div class="form-group row" class="group">
                        <label >Small image size:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="small_icon_img" placeholder="Enter a Small image size" value="{{ old($ImagesettingsList[0]->name,$ImagesettingsList[0]->value ?? null) }}" />
                            <span class="help-block">Enter a "Small image size"</span>
                        </div>
                    </div>

                     <div class="form-group row" class="group">
                        <label >Medium image size:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="medium_icon_img" placeholder="Enter a Medium image size" value="{{ old($ImagesettingsList[1]->name,$ImagesettingsList[1]->value ?? null) }}" />
                            <span class="help-block">Enter a "Medium image size"</span>
                        </div>
                    </div>


                     <div class="form-group row" class="group">
                        <label>Large image size:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" class="form-control form-control-solid" name="large_icon_img" placeholder="Enter a Large image size" value="{{ old($ImagesettingsList[2]->name,$ImagesettingsList[2]->value ?? null) }}" />
                            <span class="help-block">Enter a "Large image size"</span>
                        </div>
                    </div>
                                           
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <a class="btn btn-secondary" href="{{ route('dashboard') }}">Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>


    </div>

    
    

@endsection
{{-- Styles Section --}}
@section('styles')
<link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
<style>


label{
       display:inline-block;
        margin-bottom: 0.7rem;
    }

    .col-sm-10.row>label {
    padding-bottom: 10px;
    padding-right: 10px;
    font-weight: bold !important;
    min-width: auto !important;
    }
    
    a,      
    button {
        outline: 0 !important;
    }


    .nav-tabs li a {
        color: #4a4b52;
        padding: 15px 18px;

    }
    .nav-tabs li a.active:hover {
        background-color:#eaeaea;
        padding: 15px 18px;
        
    }


    .nav-tabs li {
        background: #eaeaea;
        font-size: 12px;
        display: inherit;
    }

    .nav-tabs li.active {
        background: #c5c6ce;
    }
    .nav-tabs a.active{
        background-color: #c5c6ce;
    }
 
    /* .nav-tabs li :hover{
        background-color: red;
    } */
    .nav-tabs li.active a {
        color: #000;
    }

    label {
        padding-top: 7px;
        font-weight: bold !important;
        min-width: 150px !important;
    }

    .col-sm-10.row {
        display: block;
    }

    .col-sm-10.row>label {
        padding-bottom: 10px;
        padding-right: 10px;
        font-weight: bold !important;
        min-width: auto !important;
    }


    .col-sm-10.row>img {
        margin-bottom: 10px;
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
@endsection
{{-- Scripts Section --}}
@section('scripts')
@if(request()->tab)
<script type="text/javascript">
    var tabno = "{{request()->tab}}";
    var tabvalue = "#tab" + "{{request()->tab}}";
    $(".tab-content .tab-pane").removeClass("in active show");
    $(tabvalue).addClass("in active show");
    $(".nav-tabs li").removeClass("active");
    $(".nav-tabs").children('li').eq(tabno - 1).addClass("active");
</script>
@endif


<!--begin::Page Vendors(used by this page)-->
<script src="{{ asset('plugins/custom/ckeditor/ckeditor-classic.bundle.js?v=7.1.7') }}"></script>
<!--end::Page Vendors-->
<!--begin::Page Scripts(used by this page)-->
<script type="text/javascript">
    var KTCkeditor = function() {
        // Private functions
        var demos = function() {
            ClassicEditor
                .create(document.querySelector('#kt-ckeditor'))
                .then(editor => {
                    //console.log(editor);
                })
                .catch(error => {
                    //console.error(error);
                });
        }
        return {
            // public functions
            init: function() {
                demos();
            }
        };
    }();

    var KTBootstrapDatepicker = function() {

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
        var demos = function() {
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
            init: function() {
                demos();
            }
        };
    }();


    // Initialization
    jQuery(document).ready(function() {
        KTCkeditor.init();
        KTBootstrapDatepicker.init();
    });
    $('#kt_select2').select2({
        placeholder: "Select a category"
    });

    //Cancel button Refresh
    $(document).ready(function() {
        $(".cancel").click(function() {
            window.reload();
        });
    });
</script>

<!--end::Page Scripts-->
@endsection