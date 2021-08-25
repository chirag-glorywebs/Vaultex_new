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
        <form class="form" method="post" action="{{ URL::to('/admin/salesuser/create')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" class="form-control form-control-solid" name="firstname"
                           placeholder="Enter first name" value="{{ old('firstname') }}"/>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" class="form-control form-control-solid" name="lastname"
                           placeholder="Enter last name" value="{{ old('lastname') }}"/>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" class="form-control form-control-solid" name="email" placeholder="Enter email"
                           value="{{ old('email') }}"/>
                </div>
                <div class="form-group">
                    <label>Saler Code:</label>
                    <input type="text" class="form-control form-control-solid" name="vendor_code" placeholder="Enter code"
                           value="{{ old('vendor_code') }}"/>
                </div>
                <div class="form-group">
                    <label>Phone Number:</label>
                    <input type="text" class="form-control form-control-solid" name="phone" placeholder="Enter phone"
                           value="{{ old('phone') }}"/>
                </div>
                <div class="form-group">
                    <label>Address:</label>
                    <textarea class="form-control form-control-solid" name="address"
                              placeholder="Enter address">{{ old('address') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Profile Picture:</label>
                    <input type="file" class="form-control form-control-solid" name="profilepic" accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                        <option value="1" @if(old('status') == '1') selected @endif>Active</option>
                        <option value="0" @if(old('status') == '0') selected @endif>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Add</button>
                <a class="btn btn-secondary" href="{{ route('admin.salesuser') }}">Cancel</a>
            </div>
        </form>
    </div>

@endsection
