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
        <form class="form" name="adminfrm" method="post" action="{{ URL::to('admin/adminuser/create')}}">
            {{csrf_field()}}
            <div class="card-body">
              <div class="form-group">
                   <label>First Name:</label>
                   <input type="text" class="form-control form-control-solid" name="firstname" placeholder="Enter first name" value="{{ old('firstname') }}" />
              </div>
              <div class="form-group">
                   <label>Last Name:</label>
                   <input type="text" class="form-control form-control-solid" name="lastname"placeholder="Enter last name" value="{{ old('lastname') }}"/>
              </div>
              <div class="form-group">
                   <label>Email:</label>
                   <input type="email" class="form-control form-control-solid" name="email" placeholder="Enter email" value="{{ old('email') }}"/>
              </div>
              <div class="form-group">
                   <label>Password:</label>
                   <div class="input-group" id="show_hide_password">
                     <input type="password" class="form-control form-control-solid" name="password" placeholder="Enter password" value="{{ old('password') }}"/>
                     <div class="input-group-addon">
                        <div class="showpwd"><i class="fa fa-eye-slash" aria-hidden="true"></i></div>
                     </div>
                   </div>
                   <input type="button" class="button btn btn-primary mt-2 passwordgenrate" value="Generate" onClick="generate();" >
                   <input type="hidden" name="length" value="10">
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
              <a href="{{ route('admin.adminuser') }}" class="btn btn-secondary" data-dismiss="modal">Cancel</a>
            </div>
        </form>

    </div>

@endsection
