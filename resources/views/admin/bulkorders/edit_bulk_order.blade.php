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
        <form class="form" name="bulkorderform" method="post" action="{{ URL::to('admin/bulk-order/update')  }}">
            {{csrf_field()}}
                <div class="card-body">
                    <!-- Multiple Order Items Add Start -->
                    <div class="form-group">
                        <label>Product/Category Details*:</label>
                        <input type="text" class="form-control form-control-solid" name="product_or_category_details[]"
                               placeholder="Enter Product/Category Details"
                               value="{{ $data->product_or_category_details }}"/>
                    </div>
                    <div class="form-group">
                        <label>Qty*:</label>
                        <input type="text" class="form-control form-control-solid" name="quantity[]"
                               placeholder="Enter Qty" value="{{ $data->quantity }}"/>
                    </div>
                    <div class="form-group">
                        <label>Brand*</label>
                        <input type="text" class="form-control form-control-solid" name="brand[]"
                               placeholder="Enter Brand"
                               value="{{ $data->brand }}"/>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" class="" name="business_or_customer" id="business_or_customer" value="1" {{ $data->business_or_customer == 1 ? 'checked' : 0 }}>
                            I am a business customer
                        </label>
                    </div>
                    <!-- Multiple Order Items Add End -->
                    <div class="form-group">
                        <label>GSTIN*:</label>
                        <input type="text" class="form-control form-control-solid" name="gstin"
                               placeholder="Enter GSTIN" value="{{ $data->gstin }}"/>
                    </div>
                    <div class="form-group">
                        <label>Name*:</label>
                        <div class="input-group" id="show_hide_password">
                            <input type="text" class="form-control form-control-solid" name="name"
                                   placeholder="Enter Name" value="{{ $data->name }}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status:</label>
                        <select class="form-control" name="status">
                            <option value="Pending" {{ $data['status'] == 'Pending' ? "selected" : "" }}>Pending</option>
                            <option value="Completed" {{ $data['status'] == 'Completed' ? "selected" : "" }}>Completed</option>
                            <option value="Cancel" {{ $data['status'] == 'Cancel' ? "selected" : "" }}>Cancel</option>
                            <option value="Return" {{ $data['status'] == 'Return' ? "selected" : "" }}>Return</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Email*:</label>
                        <div class="input-group" id="show_hide_password">
                            <input type="text" class="form-control form-control-solid" name="email"
                                   placeholder="Enter Email" value="{{ $data->email }}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Phone*:</label>
                        <div class="input-group" id="show_hide_password">
                            <input type="text" class="form-control form-control-solid" name="phone"
                                   placeholder="Enter Email" value="{{ $data->phone }}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description*:</label>
                        <div class="input-group" id="show_hide_password">
                        <textarea class="form-control" name="description"
                                  id="description">{{ $data->description }}</textarea>
                        </div>
                    </div>
                </div>
            <div class="card-footer">
                <input type="hidden" name="id" id="id" value="{{ $data->id }}">
                <input type="hidden" name="order_id" id="order_id" value="{{ $data->order_id }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-primary mr-2">Update</button>
                <a href="{{ route('bulk-order') }}" class="btn btn-secondary" data-dismiss="modal">Cancel</a>
            </div>
        </form>
    </div>

@endsection
