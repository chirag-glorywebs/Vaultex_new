{{-- Extends layout --}}
@extends('admin.layout.default')
{{-- Content --}}
@section('content')
<div  class="pageLoader" id="pageLoader"></div>

@if (Session::has('message'))
<div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
    
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="col-md-12">
            <div class="row">
           
            <div class="col-md-6">
                <div class="card card-custom">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">Products Import
                        </h3>
                    </div>
                </div>
            
        <div class="card-body">
            
            {{-- <div class="mb-4">
            <a href="{{URL::to('/')}}/api/export-products" target="_blank">
                <i class="fa fa-download"></i> Export Data
            </a>
            </div> --}}

            <form class="form" method="post" action="{{ route('products-import') }}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                    <label>Products Import</label>
                    <input type="file" class="form-control form-control-solid" name="products_import"
                           accept="text/csv"/>
                    <!--<input type="file" class="form-control form-control-solid" name="price_list_file" accept="text/csv"/>-->
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>                    
                </div>                
            </form>            
        </div>

        
    </div>
            </div>
         
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">Product FAQ Import
                        </h3>
                    </div>
                </div>
        <form class="form" method="post" action="{{ route('faq-import') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">
                <div class="form-group">
                    <label>Product FAQ Import</label>
                    <input type="file" class="form-control form-control-solid" name="faq_file"
                           accept="text/csv"/>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                </div>
            </div>
            
        </form>
        </div>
    </div>
    </div>
    </div>
    <div class="example example-compact mt-2 mb-10"></div>
    <div class="col-md-12">
        <div class="row">
       
        <div class="col-md-6">
            <div class="card card-custom">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Product Training Video Import
                    </h3>
                </div>
            </div>
        <form class="form" method="post" action="{{ route('training-video-import') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">
                <div class="form-group">
                    <label>Product Training Video Import</label>
                    <input type="file" class="form-control form-control-solid" name="training_video_file"
                           accept="text/csv"/>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                </div>
            </div>
            
        </form>
    </div>
    </div>
    <div class="col-md-6">
        <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">Product Feature Video Import
                </h3>
            </div>
        </div>

        <form class="form" method="post" action="{{ route('feature-video-import') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">
                <div class="form-group">
                    <label>Product Feature Video Import</label>
                    <input type="file" class="form-control form-control-solid" name="feature_video_file"
                           accept="text/csv"/>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                </div>
            </div>
           
        </form>
    </div>
</div>
</div>
</div>

@endsection

{{-- Scripts Section --}}
@section('scripts')
    <!--begin::Page Vendors(used by this page)-->
    <script src="{{ asset('plugins/custom/ckeditor/ckeditor-classic.bundle.js?v=7.1.7') }}"></script>
    <!--end::Page Vendors-->

    <script type="text/javascript">
        $(window).on('beforeunload', function(){
            $('#pageLoader').show();
        });
        $(function() {
            $('#pageLoader').hide();
        });
    </script>
@endsection

<style>
    .pageLoader{
        background: url('../uploads/loader.gif') no-repeat center center;
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        z-index: 9999999;
        background-color: #ffffff8c;
    }
</style>