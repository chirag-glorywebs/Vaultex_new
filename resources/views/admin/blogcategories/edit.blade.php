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
        <form class="form" method="post" action="{{ URL::to('admin/blogcategories/update')  }}"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" class="form-control form-control-solid" name="id" value="{{$category_data['id']}}"/>
            <div class="card-body">
                <div class="form-group">
                    <label>Category Name:</label>
                    <input type="text" class="form-control form-control-solid" name="category_name"
                           placeholder="Enter category name" value="{{$category_data['category_name']}}"/>
                </div>
                <div class="form-group">
                    <label>Slug:</label>
                    <input type="text" class="form-control form-control-solid" name="slug" placeholder="Enter slug name"
                           value="{{ old('slug',$category_data['slug']) }}"/>
                </div>
                <div class="form-group">
                    <label>Parent Category:</label>
                    <select class="form-control select2" id="kt_select2" name="parent_id">
                        <option value="">Select</option>
                        @foreach($parentcategory as $cat)
                            <option
                                value="{{$cat->id}}" {{ $cat->id == $category_data['parent_id'] ? "selected" : "" }}>{{$cat->category_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Category Description:</label>
                    <textarea id="kt-ckeditor"
                              name="category_description">{{$category_data['category_description']}}</textarea>
                </div>
                <div class="form-group">
                    <label>Category Logo:</label>
                    @if($category_data->category_image)
                    <img src="{{ URL::to($category_data->category_image) }}" width="50"
                         height="50"/>
                    @endif 
                    <input type="file" class="form-control form-control-solid" name="category_image" accept="image/*"/>
                    <input type="hidden" class="form-control form-control-solid" name="old_category_image"
                           value="{{ old('category_image',$category_data->category_image ?? null) }}" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Banner Image:</label>
                    @if($category_data->banner)
                    <img src="{{ URL::to($category_data->banner) }}" width="50"
                         height="50"/>
                      @endif
                    <input type="file" class="form-control form-control-solid" name="banner" accept="image/*"/>
                    <input type="hidden" class="form-control form-control-solid" name="old_banner"
                           value="{{ old('banner',$category_data->banner ?? null) }}" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Display Order:</label>
                    <input type="text" class="form-control form-control-solid" name="display_order"
                           placeholder="Enter display order" value="{{$category_data['display_order']}}"/>
                </div>
                <div class="form-group">
                    <label>SEO Title:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_title"
                           placeholder="Enter seo title" value="{{$category_data['seo_title']}}"/>
                </div>
                <div class="form-group">
                    <label>SEO Description:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_description"
                           placeholder="Enter seo description" value="{{$category_data['seo_description']}}"/>
                </div>

                <div class="form-group">
                    <label>SEO Keyword:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_keyword"
                           placeholder="Enter seo keyword" value="{{$category_data['seo_keyword']}}"/>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                        <option value="1" {{ $category_data['status'] == 1 ? "selected" : "" }}>Active</option>
                        <option value="0" {{ $category_data['status'] == 0 ? "selected" : "" }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update</button>
                <a class="btn btn-secondary" href="{{ route('blogcategories.index') }}">Cancel</a>
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
        $('#kt_select2').select2({
            placeholder: "Select a category"
        });
    </script>
    <!--end::Page Scripts-->
@endsection
