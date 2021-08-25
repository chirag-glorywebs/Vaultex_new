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
        <form class="form" method="post" action="{{ URL::to('admin/category/update')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" class="form-control form-control-solid" name="id" value="{{$category['id']}}"/>
            <div class="card-body">
                <div class="form-group">
                    <label>Category Name:</label>
                    <input type="text" class="form-control form-control-solid" name="category_name"
                           placeholder="Enter category name" value="{{$category['category_name']}}"/>
                </div>
                <div class="form-group">
                    <label>Slug:</label>
                    <input type="text" class="form-control form-control-solid" name="slug" placeholder="Enter slug name"
                           value="{{ $category['slug'] }}"/>
                </div>
                <div class="form-group">
                    <label>Parent Category:</label>
                    <select class="form-control select2" id="kt_select2" name="parent_category">
                        <option value="">Select</option>
                        @php
                        $edit_cat =  ( isset($category['parent_category'])) ? $category['parent_category'] : "";
                        foreach ($parentcategory as $cat) {
                                echo '<option value="'.$cat->id.'" '.(($cat->id == $edit_cat) ? 'selected="selected"': "" ).'>'.$cat->category_name.'</option>';
                                if(isset($cat->childCategoires) && !empty($cat->childCategoires)){
                                    foreach ($cat->childCategoires as $aa) {
                                        echo '<option value="'.$aa->id.'" '.(($aa->id == $edit_cat) ? 'selected="selected"': "" ).'> - '.$aa->category_name.'</option>';
                                        if(isset($aa->childCategoires) && !empty($aa->childCategoires)){
                                            foreach ($aa->childCategoires as $bb) {
                                                echo '<option value="'.$bb->id.'" '.(($bb->id == $edit_cat) ? 'selected="selected"': "" ).'> - - '.$bb->category_name.'</option>';
                                                if(isset($bb->childCategoires) && !empty($bb->childCategoires)){
                                                    foreach ($bb->childCategoires as $cc) {
                                                        echo '<option value="'.$cc->id.'" '.(($cc->id == $edit_cat) ? 'selected="selected"': "" ).'> - - - '.$cc->category_name.'</option>';
                                                        if(isset($cc->childCategoires) && !empty($cc->childCategoires)){
                                                            foreach ($cc->childCategoires as $dd) {
                                                                echo '<option value="'.$dd->id.'" '.(($dd->id == $edit_cat) ? 'selected="selected"': "" ).'> - - - - '.$dd->category_name.'</option>';
                                                        
                                                            } 
                                                        }
                                                    } 
                                                }
                                            } 
                                        }
                                     } 
                                }
                            
                            }  
                    @endphp 
                    </select>
                </div>
                <div class="form-group">
                    <label>Category Description:</label>
                    <textarea id="kt-ckeditor"
                              name="category_description">{{$category['category_description']}}</textarea>
                </div>
                <div class="form-group">
                    <label>Category Logo:</label>
                    @if($category->logo)
                    <img src="{{ URL::to($category->logo) }}" alt="Category Logo Not Found" width="50" height="50"/>
                    @endif
                    <input type="file" class="form-control form-control-solid" name="logo" accept="image/*"/>
                    <input type="hidden" class="form-control form-control-solid" name="old_logo"
                           value="{{ old('logo',$category->logo) }}" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Banner Image:</label>
                    @if($category->banner)
                    <img src="{{ URL::to($category->banner) }}" alt="Banner Image Not Found" width="50" height="50"/>
                    @endif
                    <input type="file" class="form-control form-control-solid" name="banner" accept="image/*"/>
                    <input type="hidden" class="form-control form-control-solid" name="old_banner"
                           value="{{ old('banner',$category->banner) }}" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Display Order:</label>
                    <input type="text" class="form-control form-control-solid" name="display_order"
                           placeholder="Enter display order" value="{{$category['display_order']}}"/>
                </div>
                <div class="form-group">
                    <label>SEO Name:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_name"
                           placeholder="Enter seo name" value="{{$category['seo_name']}}"/>
                </div>
                <div class="form-group">
                    <label>SEO Description:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_description"
                           placeholder="Enter seo description" value="{{$category['seo_description']}}"/>
                </div>
                <div class="form-group">
                    <label>SEO Title:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_title"
                           placeholder="Enter seo title" value="{{$category['seo_title']}}"/>
                </div>
                <div class="form-group">
                    <label>SEO Keyword:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_keyword"
                           placeholder="Enter seo keyword" value="{{$category['seo_keyword']}}"/>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                        <option value="1" {{ $category['status'] == 1 ? "selected" : "" }}>Active</option>
                        <option value="0" {{ $category['status'] == 0 ? "selected" : "" }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update</button>
                <a class="btn btn-secondary" href="{{ route('category.index') }}">Cancel</a>
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
