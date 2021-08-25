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
        <form class="form" method="post" action="{{ URL::to('admin/sliders/update')  }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" class="form-control form-control-solid" name="id" value="{{$slidersData['id']}}"/>
            <div class="card-body">
                <div class="form-group">
                    <label>Image:</label>
                    @if($slidersData->image)
                        <input type="hidden" name="old_image" value="{{ old('image',
                    $slidersData->image ?? null) }}">
                        <img height="50" width="50" src="{{ URL::to($slidersData->image) }}"
                             alt="Image Not Found">
                    @endif
                    <input type="file" class="form-control form-control-solid" name="image" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Contents:</label>
                    <textarea id="kt-ckeditor" name="contents">{{ old('contents',$slidersData['contents'])}}</textarea>
                </div>
                <div class="form-group">
                    <label>Url:</label>
                    <input type="text" class="form-control form-control-solid" name="url"
                           placeholder="Enter Url" value="{{ old('url',$slidersData['url']) }}"/>
                </div>

                <div class="form-group">
                    <label>Pages:</label>
                    <select class="form-control select2" id="kt_select2" name="page_id">
                        <option value="">Select</option>
                        @foreach($pages as $page)
                            <option
                                value="{{$page->id}}" {{ $page->id == $slidersData['page_id'] ? "selected" : "" }} > {{$page->page_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                        <option value="1" {{ $slidersData['status'] == 1 ? "selected" : "" }}>Active</option>
                        <option value="0" {{ $slidersData['status'] == 0 ? "selected" : "" }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update</button>
                <a class="btn btn-secondary" href="{{ route('sliders.index') }}">Cancel</a>
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
