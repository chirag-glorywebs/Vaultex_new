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
        <form class="form" method="post" action="{{ URL::to('admin/vendoruser/update')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" class="form-control form-control-solid" name="id" value="{{$data['id']}}"/>
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Personal Information</h3>
                </div>
            </div>
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
                    <label>Phone Number:</label>
                    <input type="text" class="form-control form-control-solid" name="phone" placeholder="Enter phone"
                           value="{{$data['phone']}}"/>
                </div>

                <div class="form-group">
                    <label>Mobile:</label>
                    <input type="text" class="form-control form-control-solid" name="mobile" placeholder="Enter mobile"
                           value="{{$data['mobile']}}"/>
                </div>

                <div class="form-group">
                    <label>Address:</label>
                    <textarea class="form-control form-control-solid" name="address"
                              placeholder="Enter address">{{$data['address']}}</textarea>
                </div>
                <div class="form-group">
                    <label>Assign Sales-Person:</label>
                    <select class="form-control" name="salesperson">
                        @foreach($salesusers as $user)
                            <option
                                value="{{$user->id}}" {{ $user->id == $data['salesperson'] ? "selected" : "" }}>{{$user->first_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Profile Picture:</label>
                        @if($data->profilepic)
                        <img src="{{ URL::to($data->profilepic) }}" alt="Profile Picture" width="50" height="50"/>
                        @endif 
                     <input type="hidden" class="form-control form-control-solid" name="old_profilepic" value="{{ old('profilepic',$data->profilepic ?? null) }}" accept="image/*"/>
                   
                    <input type="file" class="form-control form-control-solid" name="profilepic" accept="image/*"/>
                </div>
            </div>
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Business Information</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Enterprise Name:</label>
                    <input type="text" class="form-control form-control-solid" name="enterprise_name"
                           placeholder="Enter enterprise name" value="{{$data['enterprise_name']}}"/>
                </div>
                <div class="form-group">
                    <label>Industry:</label>
                    <input type="text" class="form-control form-control-solid" name="industry"
                           placeholder="Enter industry" value="{{$data['industry']}}"/>
                </div>
                <div class="form-group">
                    <label>Business Experience(in yrs):</label>
                    <input type="text" class="form-control form-control-solid" name="business_exp"
                           placeholder="Enter business experience" value="{{$data['business_exp']}}"/>
                </div>
                <div class="form-group">
                    <label>Sales (per/month):</label>
                    <input type="text" class="form-control form-control-solid" name="sales" placeholder="Enter sales"
                           value="{{$data['sales']}}"/>
                </div>
                <div class="form-group">
                    <label>Turn Over (Yearly):</label>
                    <input type="text" class="form-control form-control-solid" name="turn_over"
                           placeholder="Enter turn over" value="{{$data['turn_over']}}"/>
                </div>
                <div class="form-group">
                    <label>Mode of Payment:</label>
                    <select class="form-control" name="payment_mode">
                        <option value="cash" {{ $data['payment_mode'] == 'cash' ? "selected" : "" }}>Cash</option>
                        <option value="check" {{ $data['payment_mode'] == 'check' ? "selected" : "" }}>Check</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Is Downloadable:</label>
                    <select class="form-control" name="downloadable">
                        <option value="yes" {{ $data['downloadable'] == 'yes' ? "selected" : "" }}>Yes</option>
                        <option value="no" {{ $data['downloadable'] == 'no' ? "selected" : "" }}>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Interval:</label>
                    <select class="form-control" name="payment_interval">
                        <option value="monthly" {{ $data['payment_interval'] == 'monthly' ? "selected" : "" }}>Monthly
                        </option>
                        <option value="yearly" {{ $data['payment_interval'] == 'yearly' ? "selected" : "" }}>Yearly
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Business Logo:</label>
                    @if($data->profilepic)
                        <img src="{{ URL::to($data->business_logo) }}" alt="Business Logo Not Found" width="50" height="50"/>
                    @endif
                    <input type="hidden" class="form-control form-control-solid" name="old_business_logo" value="{{ old('business_logo',$data->business_logo ?? null) }}" accept="image/png"/>
                    <input type="file" class="form-control form-control-solid" name="business_logo" accept="image/png"
                           value="{{$data['email']}}"/>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                        <option value="1" {{ $data['status'] == 1 ? "selected" : "" }}>Active</option>
                        <option value="0" {{ $data['status'] == 0 ? "selected" : "" }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Vendor Code:</label>
                    <input type="text" class="form-control form-control-solid" name="vendor_code"
                           placeholder="Enter Vendor Code" value="{{ $data['vendor_code'] }}"/>
                </div>
                <div class="form-group">
                    <label>Price List No.:</label>
                    <input type="text" class="form-control form-control-solid" name="price_list_no"
                           placeholder="Enter Price List No" value="{{ $data['price_list_no'] }}"/>
                </div>
                <div class="form-group">
                    <label>Vendor Credit Limit:</label>
                    <input type="text" class="form-control form-control-solid" name="vendor_credit_limit"
                           placeholder="Enter turn over" value="{{ $data['vendor_credit_limit'] }}"/>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update</button>
                <a class="btn btn-secondary" href="{{ route('vendoruser.index') }}">Cancel</a>
            </div>
        </form>
    </div>

@endsection
