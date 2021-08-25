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

        <form class="form" method="post" action="{{ URL::to(isset($featurevideo) ? '/admin/products/featurevideo/update' : '/admin/products/featurevideo/create')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" class="form-control form-control-solid" name="proid" value="{{@$proid}}"/>
            <input type="hidden" class="form-control form-control-solid" name="id" value="{{@$featurevideo['id']}}"/>
            <div class="card-body">
              <div class="form-group">
                <label for="title">feature video name</label>
                <input type="text" id="featurevideoname" name="featurevideoname" class="form-control" value="{{@$featurevideo['name']}}">                              
              </div>
              <div class="form-group">
                  <label for="featurevideofiles">Select feature video</label>
                  @if(!empty($featurevideo['video']))
                  <video width="100" height="100" controls>
                    <source src="{{ URL::to('uploads/products/featurevideos/').'/'.$featurevideo['video'] }}" type="video/mp4">
                        Your browser does not support the video tag.
                  </video>
                  @endif
                  <input type="file" id="featurevideofiles" name="featurevideofiles" multiple accept="video/*" class="form-control1">
              </div>
              <div class="form-group">
                <label for="content">Display Order</label>
                <input type="text" id="display_order" name="display_order" class="form-control" value="{{@$featurevideo['display_order']}}"> 
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary mr-2">{{ isset($featurevideo) ? 'Update' : 'Add' }}</button>
              <button type="reset" class="btn btn-secondary">Cancel</button>
            </div>
        </form>

    </div>

@endsection
