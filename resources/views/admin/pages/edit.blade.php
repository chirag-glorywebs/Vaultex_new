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
        <form class="form" method="post" action="{{ URL::to('admin/pages/update')  }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" class="form-control form-control-solid" name="id" value="{{$page_data['id']}}"/>
            <div class="card-body">
                <div class="form-group">
                    <label>Page Name:</label>
                    <input type="text" class="form-control form-control-solid" name="page_name"
                           placeholder="Enter page name" value="{{$page_data['page_name']}}"/>
                </div>
                <div class="form-group">
                    <label>Slug:</label>
                    <input type="text" class="form-control form-control-solid" name="slug" placeholder="Enter slug name"
                           value="{{ old('slug',$page_data['slug']) }}"/>
                </div>

                <div class="form-group">
                    <label>Page Description:</label>
                    <textarea id="kt-ckeditor" name="page_description">{{$page_data['page_description']}}</textarea>
                </div>

                <div class="form-group">
                    <label>Banner Image:</label>
                    @if(!empty($page_data->banner))
                        <img src="{{ URL::to($page_data->banner) }}" width="100"
                             height="100" alt="Banner Image Not Found"/>
                    @endif
                    <input type="file" class="form-control form-control-solid" name="banner" accept="image/*"/>
                    <input type="hidden" class="form-control form-control-solid" name="old_banner"
                           value="{{ old('banner',$page_data->banner ?? null) }}" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Video:</label>
                    @if(!empty($page_data->video))
                        <video width="100" height="100" controls>
                            <source
                                src="{{ URL::to($page_data->video) }}"
                                type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @endif
                    <input type="file" class="form-control form-control-solid" name="video" accept="video/*"/>
                    <input type="hidden" class="form-control form-control-solid" name="old_video"
                           value="{{ old('video',$page_data->video ?? null) }}"/>
                </div>

                <div class="form-group">
                    <label>SEO Title:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_title"
                           placeholder="Enter seo title" value="{{$page_data['seo_title']}}"/>
                </div>
                <div class="form-group">
                    <label>SEO Description:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_description"
                           placeholder="Enter seo description" value="{{$page_data['seo_description']}}"/>
                </div>

                <div class="form-group">
                    <label>SEO Keyword:</label>
                    <input type="text" class="form-control form-control-solid" name="seo_keyword"
                           placeholder="Enter seo keyword" value="{{$page_data['seo_keyword']}}"/>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                        <option value="1" {{ $page_data['status'] == 1 ? "selected" : "" }}>Active</option>
                        <option value="0" {{ $page_data['status'] == 0 ? "selected" : "" }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update</button>
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
