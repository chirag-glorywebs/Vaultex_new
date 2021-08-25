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

        <form class="form" method="post" action="{{ URL::to(isset($faqs) ? '/admin/products/faq/update' : '/admin/products/faq/create')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" class="form-control form-control-solid" name="proid" value="{{@$proid}}"/>
            <input type="hidden" class="form-control form-control-solid" name="id" value="{{@$faqs['id']}}"/>
            <div class="card-body">
              <div id="faqblocks">
                <div id="fblocks">
                  <p class="faqmaintitle">FAQ</p>
                  <div class="form-group">
                    <label for="title">FAQ Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{@$faqs['title']}}">
                  </div>
                  <div class="form-group">
                    <label for="description">FAQ Content</label>
                    <textarea id="description" name="description" class="form-control">{{@$faqs['description']}}</textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary mr-2">{{ isset($faqs) ? 'Update' : 'Add' }}</button>
            <a href="{{ URL::TO('/admin/products/edit/'.$proid.'?tab=7') }}" class="btn btn-secondary" type="reset">Cancel
            </div>
            
        </form>
 </div>

@endsection
