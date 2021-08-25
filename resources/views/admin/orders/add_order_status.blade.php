{{-- Extends layout --}}
@extends('admin.layout.default')

{{-- Content --}}
@section('content')

    <div class="card card-custom productpage">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form class="form" method="post"
              action="{{ URL::to(isset($orderstatus) ? '/admin/orderstatus/update' : '/admin/orderstatus/create')}}"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" class="form-control form-control-solid" name="id" value="{{@$orderstatus['id']}}"/>
            <div class="card-body">
                <div class="form-group">
                    <label>Status:</label>
                    <input type="text" class="form-control form-control-solid" name="status" placeholder="Enter status"
                           value="{{@$orderstatus['status']}}"/>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">{{ isset($orderstatus) ? 'Update' : 'Add' }}</button>
                <a class="btn btn-secondary" href="{{ route('orderstatus.index') }}">Cancel</a>
            </div>
        </form>
    </div>

@endsection
