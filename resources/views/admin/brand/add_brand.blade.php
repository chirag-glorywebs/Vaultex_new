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
        <form class="form" method="post" action="{{ URL::to('/admin/brand/create')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">
                <div class="form-group">
                    <label>Brand Name:</label>
                    <input type="text" class="form-control form-control-solid" name="brand_name"
                           placeholder="Enter brand name" value="{{ old('brand_name') }}"/>
                </div>
                <div class="form-group">
                    <label>Brand Description:</label>
                    <textarea id="kt-ckeditor" name="brand_description">{{ old('brand_description') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Brand Logo:</label>
                    <input type="file" class="form-control form-control-solid" name="brand_logo" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Display Order:</label>
                    <input type="text" class="form-control form-control-solid" name="display_order"
                           placeholder="Enter display order" value="{{ old('display_order') }}"/>
                </div>
                <div class="form-group">
                    <label>SEO Name:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_name"
                           placeholder="Enter seo name" value="{{ old('seo_name') }}"/>
                </div>
                <div class="form-group">
                    <label>SEO Description:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_description"
                           placeholder="Enter seo description" value="{{ old('seo_description') }}"/>
                </div>
                <div class="form-group">
                    <label>SEO Title:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_title"
                           placeholder="Enter seo title" value="{{ old('seo_title') }}"/>
                </div>
                <div class="form-group">
                    <label>SEO Keyword:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_keyword"
                           placeholder="Enter seo keyword" value="{{ old('seo_keyword') }}"/>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                        <option value="1" @if(old('status') == '1') selected @endif>Active</option>
                        <option value="0" @if(old('status') == '0') selected @endif>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Add</button>
                <a class="btn btn-secondary" href="{{ route('brand.index') }}">Cancel</a>
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

        // Initialization
        jQuery(document).ready(function () {
            KTCkeditor.init();
        });
    </script>
    <!--end::Page Scripts-->
@endsection
