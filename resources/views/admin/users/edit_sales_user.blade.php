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
        <form class="form" method="post" action="{{ URL::to('admin/salesuser/update')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" class="form-control form-control-solid" name="id" value="{{$data['id']}}"/>
            <div class="card-body">
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" class="form-control form-control-solid" name="firstname"
                           placeholder="Enter first name" value="{{$data['first_name']}}"/>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" class="form-control form-control-solid" name="lastname"
                           placeholder="Enter last name" value="{{$data['last_name']}}"/>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" class="form-control form-control-solid" name="email" placeholder="Enter email"
                           value="{{$data['email']}}"/>
                </div>
                <div class="form-group">
                    <label>Saler Code:</label>
                    <input type="text" class="form-control form-control-solid" name="vendor_code" placeholder="Enter code"
                           value="{{$data['vendor_code']}}"/>
                </div>
                <div class="form-group">
                    <label>Phone Number:</label>
                    <input type="text" class="form-control form-control-solid" name="phone" placeholder="Enter phone"
                           value="{{$data['phone']}}"/>
                </div>
                <div class="form-group">
                    <label>Address:</label>
                    <textarea class="form-control form-control-solid" name="address"
                              placeholder="Enter address">{{$data['address']}}</textarea>
                </div>
                <div class="form-group">
                    <label>Profile Picture:</label>
                    @if($data->profilepic)
                        <img src="{{ URL::to($data->profilepic) }}" alt="Profile Picture" width="50" height="50"/>
                    @endif 
                    <input type="file" class="form-control form-control-solid" name="profilepic" accept="image/*"/>
                    <input type="hidden" class="form-control form-control-solid" name="old_profilepic" value="{{ old('profilepic',$data->profilepic ?? null) }}" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                        <option value="1" {{ $data['status'] == 1 ? "selected" : "" }}>Active</option>
                        <option value="0" {{ $data['status'] == 0 ? "selected" : "" }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update</button>
                <a class="btn btn-secondary" href="{{ route('admin.salesuser') }}">Cancel</a>
            </div>
        </form>

    </div>

@endsection
