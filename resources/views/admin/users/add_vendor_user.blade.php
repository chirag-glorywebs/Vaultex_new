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
        <form class="form" method="post" action="{{ URL::to('/admin/vendoruser/create')}}"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Personal Information</h3>
                </div>
            </div>
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
                    <label>Phone Number:</label>
                    <input type="text" class="form-control form-control-solid" name="phone" placeholder="Enter phone"
                           value="{{ old('phone') }}"/>
                </div>
                
                <div class="form-group">
                    <label>Mobile:</label>
                    <input type="text" class="form-control form-control-solid" name="mobile" placeholder="Enter mobile"
                           value="{{$data['mobile']}}"/>
                </div>

                <div class="form-group">
                    <label>Address:</label>
                    <textarea class="form-control form-control-solid" name="address"
                              placeholder="Enter address">{{ old('address') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Assign Sales-Person:</label>
                    <select class="form-control" name="salesperson">
                        @foreach($salesusers as $user)
                            <option value="{{$user->id}}"
                                    @if(old('salesperson') == $user->id) selected @endif>{{$user->first_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Profile Picture:</label>
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
                           placeholder="Enter enterprise name" value="{{ old('enterprise_name') }}"/>
                </div>
                <div class="form-group">
                    <label>Industry:</label>
                    <input type="text" class="form-control form-control-solid" name="industry"
                           placeholder="Enter industry" value="{{ old('industry') }}"/>
                </div>
                <div class="form-group">
                    <label>Business Experience(in yrs):</label>
                    <input type="text" class="form-control form-control-solid" name="business_exp"
                           placeholder="Enter business experience" value="{{ old('business_exp') }}"/>
                </div>
                <div class="form-group">
                    <label>Sales (per/month):</label>
                    <input type="text" class="form-control form-control-solid" name="sales" placeholder="Enter sales"
                           value="{{ old('sales') }}"/>
                </div>
                <div class="form-group">
                    <label>Turn Over (Yearly):</label>
                    <input type="text" class="form-control form-control-solid" name="turn_over"
                           placeholder="Enter turn over" value="{{ old('turn_over') }}"/>
                </div>
                <div class="form-group">
                    <label>Mode of Payment:</label>
                    <select class="form-control" name="payment_mode">
                        <option value="cash" @if(old('payment_mode') == 'cash') selected @endif>Cash</option>
                        <option value="check" @if(old('payment_mode') == 'check') selected @endif>Check</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Is Downloadable:</label>
                    <select class="form-control" name="downloadable">
                        <option value="yes" @if(old('downloadable') == 'yes') selected @endif>Yes</option>
                        <option value="no" @if(old('downloadable') == 'no') selected @endif>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Interval:</label>
                    <select class="form-control" name="payment_interval">
                        <option value="monthly" @if(old('payment_interval') == 'monthly') selected @endif>Monthly
                        </option>
                        <option value="yearly" @if(old('payment_interval') == 'yearly') selected @endif>Yearly</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Business Logo:</label>
                    <input type="file" class="form-control form-control-solid" name="business_logo" accept="image/png"/>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                        <option value="1" @if(old('status') == '1') selected @endif>Active</option>
                        <option value="0" @if(old('status') == '0') selected @endif>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Vendor Code:</label>
                    <input type="text" class="form-control form-control-solid" name="vendor_code"
                           placeholder="Enter Vendor Code" value="{{ old('vendor_code') }}"/>
                </div>
                <div class="form-group">
                    <label>Price List No.:</label>
                    <input type="text" class="form-control form-control-solid" name="price_list_no"
                           placeholder="Enter Price List No" value="{{ old('price_list_no') }}"/>
                </div>
                <div class="form-group">
                    <label>Vendor Credit Limit:</label>
                    <input type="text" class="form-control form-control-solid" name="vendor_credit_limit"
                           placeholder="Enter turn over" value="{{ old('vendor_credit_limit') }}"/>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Add</button>
                <a class="btn btn-secondary" href="{{ route('vendoruser.index') }}">Cancel</a>
            </div>
        </form>
    </div>

@endsection
