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
        <form class="form" method="post" action="{{ URL::to('/admin/blogs')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">
                <div class="form-group">
                    <label>Blog Name:</label>
                    <input type="text" class="form-control form-control-solid" name="blog_name"
                           value="{{ old('blog_name')}}" placeholder="Enter blog name"/>
                </div>
                <div class="form-group">
                    <label>Blog Category:</label>
                    <select class="form-control select2" id="kt_select2" name="category_id">
                        <option value="">Select</option>
                        @foreach($categories as $cat)
                            <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Blog Description:</label>
                    <textarea id="kt-ckeditor" name="blog_description">{{ old('blog_description')}}</textarea>
                </div>
                <div class="form-group">
                    <label>Blog Image:</label>
                    <input type="file" class="form-control form-control-solid" name="blog_image" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Banner Image:</label>
                    <input type="file" class="form-control form-control-solid" name="banner" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Published Date</label>
                    <div class="input-group">
                        <input type="text" id="kt_datepicker_1" class="form-control form-control-solid"
                               value="{{ old('blog_date') }}" name="blog_date" placeholder="Select a Date" readonly/>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>SEO Name:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_name"
                           value="{{ old('seo_name')}}" placeholder="Enter seo name"/>
                </div>
                <div class="form-group">
                    <label>SEO Description:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_description"
                           value="{{ old('seo_description')}}" placeholder="Enter seo description"/>
                </div>
                <div class="form-group">
                    <label>SEO Title:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_title"
                           value="{{ old('seo_title')}}" placeholder="Enter seo title"/>
                </div>
                <div class="form-group">
                    <label>SEO Keyword:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_keyword"
                           value="{{ old('seo_keyword')}}" placeholder="Enter seo keyword"/>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Add</button>
                <a class="btn btn-secondary" href="{{ route('blogs.index') }}">Cancel</a>
            </div>
        </form>

    </div>

@endsection
{{-- Scripts Section --}}
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
                        console.log(editor);
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }

            return {
                // public functions
                init: function () {
                    demos();
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
    </script>
    <!--end::Page Scripts-->
@endsection
