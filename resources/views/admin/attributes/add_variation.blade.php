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

        <form class="form" method="post"
              action="{{ URL::to(isset($variations) ? '/admin/attributes/'.$attid.'/variations/update' : '/admin/attributes/'.$attid.'/variations/create')}}"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" class="form-control form-control-solid" name="attid" value="{{@$attid}}"/>
            <input type="hidden" class="form-control form-control-solid" name="id" value="{{@$variations['id']}}"/>
            <div class="card-body">
                <div class="form-group">
                    <label>Variation Name:</label>
                    <input type="text" class="form-control form-control-solid" name="variation_name"
                           placeholder="Enter variation name" value="{{@$variations['variation_name']}}"/>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                    <!--<option value="1" {{--{{ @$variations['status'] == 1 ? "selected" : "" }}--}}>Active</option>
                        <option value="0" {{--{{ @$variations['status'] == 0 ? "selected" : "" }}--}}>Inactive</option>-->
                        <option value="1" @if((old('status', $variations->status ?? null ) == '1' ))
                        selected="selected" @endif>Active
                        </option>
                        <option value="0" @if((old('status', $variations->status ?? null ) == '0' ))
                        selected="selected" @endif>Inactive
                        </option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">{{ isset($variations) ? 'Update' : 'Add' }}</button>
                <a class="btn btn-secondary" href="{{ url('/admin/attributes/' . $attid . '/variations') }}">Cancel</a>
            </div>
        </form>

    </div>

@endsection
