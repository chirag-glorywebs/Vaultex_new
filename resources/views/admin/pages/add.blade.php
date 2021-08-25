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
        <form class="form" method="post" action="{{ URL::to('/admin/pages')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">
                <div class="form-group">
                    <label>Page Name:</label>
                    <input type="text" class="form-control form-control-solid" name="page_name"
                           value="{{ old('page_name')}}" placeholder="Enter page name"/>
                </div>
                <div class="form-group">
                    <label>Page Description:</label>
                    <textarea id="kt-ckeditor" name="page_description">{{ old('page_description')}}</textarea>
                </div>
                <div class="form-group">
                    <label>Banner Image:</label>
                    <input type="file" class="form-control form-control-solid" name="banner"
                           alt="Banner Image Not Found" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Video:</label>
                    <input type="file" class="form-control form-control-solid" name="video" accept="video/*"/>
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
                <a class="btn btn-secondary" href="{{ route('pages.index') }}">Cancel</a>
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
