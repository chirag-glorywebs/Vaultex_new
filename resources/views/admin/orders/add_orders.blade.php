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
        <form class="form" method="post" action="{{ URL::to('/admin/orders/update')}}" enctype="multipart/form-data">
            {{csrf_field()}}
            <?php
            if (!empty($allorders)) {
                $orders = @$allorders[0];
            }
            ?>
       
            <input type="hidden" class="form-control form-control-solid" name="id" value="{{@$orders['orderid']}}"/>
            <div class="card-body">
                <div class="my-5">
                    <div class="row">
                        <div class="col-xl-12"><h3 class="text-dark font-weight-bold mb-10">User Information:</h3>
                            <hr>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-4"><b>Name</b></label>
                                <div class="col-8">{{@$orders['customer_name']}}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4"><b>Email</b></label>
                                <div class="col-8">{{@$orders['customer_email']}}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4"><b>Phone</b></label>
                                <div class="col-8">{{@$orders['customers_phone']}}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4"><b>Pincode</b></label>
                                <div class="col-8">{{@$orders['customers_postcode']}}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4"><b>Address</b></label>
                                <div class="col-8">{{@$orders['customer_street_address']}}</div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-4"><b>City</b></label>
                                <div class="col-8">{{@$orders['customers_city']}}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4"><b>State</b></label>
                                <div class="col-8">{{@$orders['customers_state']}}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4"><b>Country</b></label>
                                <div class="col-8">{{@$orders['customers_country']}}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4"><b>Landmark</b></label>
                                <div class="col-8">{{@$orders['customers_landmark']}}</div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <br>
                            <h3 class="text-dark font-weight-bold mb-10">Product Info:</h3>
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product Image</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Quntity</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $total = 0;?>
                                @foreach($ordersproduct as $key => $data)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            @if($data->main_image && file_exists($data->main_image))
                                                <img src="{{ URL::to($data->main_image) }}" width="100" height="100"/>
                                            @else  
                                             <img src="{{ asset('uploads/product-placeholder.png')}}"
                                                width="100" height="100"/>
                                                @endif
                                            </td>
                                        <td>{{ $data->product_name }}</td>
                                        <td>{{ $data->final_price }}</td>
                                        <td>{{ $data->product_quantity }}</td>
                                        <td>{{ $data->final_price * $data->product_quantity }}</td>
                                    </tr>
                                    <?php $total += $data->final_price * $data->product_quantity;?>
                                @endforeach
                                </tbody>
                            </table>
                           <div class="row">
                           {{--     <div class="col-md-6">
                                    <label>Status: </label>
                                    <select name="status" class="form-control">
                                        @foreach($ordersstatus as $status)
                                        <option
                                                value="{{ old('status', $status->status) }}" {{ $status->id == @$orders['status'] ? "selected" : "" }}>{{$status->status}}</option>
                                        @endforeach
                                    </select>
                                </div>--}} 
                             {{--   <div class="col-md-6">
                                    <label>Order Status: </label>

<select name="status" class="form-control">
@foreach($ordersstatus as $key => $status)   
<option value="{{ old('status', $status->status) }}" {{ $status->id == @$orders['status'] ? "selected" : "" }}>
{{$status->status}}</option>
@endforeach                                    </select>
                                </div>
                                <div class="col-md-6 text-right">
                                    Total: <strong><?php //echo $total;?></strong>
                                </div>
                                <div class="col-md-6">
                                    <label>Order Date: </label>
                                    <input type="date" name="date" id="date" class="form-control"
                                     style="width: 100%; display: inline;"
                                      onchange="invoicedue(event);" required value={{$manage_ordersdata}}> 
                                </div>
                            </div>
                        </div>
                    </div>--}}
                    <div class="col md-6">
                    <label >Order Status</label>
                 {{--  <select  name = "status"  class="form-control">
                                @foreach($ordersstatus as $status)
                                @endforeach
                                <option value="2" @if((old('status', $orders->status ?? null ) == '2' ))
                                selected="selected" @endif>Completed                                </option>
                                <option value="3" @if((old('status', $orders->status ?? null ) == '3' ))
                                selected="selected" @endif>Cancle
                                </option>
                                <option value="4" @if((old('status', $orders->status ?? null ) == '4' ))
                                selected="selected" @endif>Return
                                </option>
                                <option value="6" @if((old('status', $orders->status ?? null ) == '6' ))
                                selected="selected" @endif>Processed
                                </option>
                                <option value="7" @if((old('status', $orders->status ?? null ) == '7' ))
                                selected="selected" @endif>Packed
                                </option>
                                <option value="8" @if((old('status', $orders->status ?? null ) == '8' ))
                                selected="selected" @endif>Shipped
                                </option>
                                <option value="9" @if((old('status', $orders->status ?? null ) == '9' ))
                                selected="selected" @endif>Accepted
                                </option>
                 </select>--}}
                 <select name="status" class="form-control">
                 @foreach($ordersstatus as $status)
                         <option
                             value="{{$status->id}}" {{ $status->status == @$orders['status'] ? "selected" : "" }}>{{$status->status}}</option>
                             @endforeach
                 </select>
                    </div>
                 <div class="col-md-6 text-right">
                                    Total: <strong><?php echo $total;?></strong>
                                </div>
                                <div class="col-md-6">
                                    <label>Order Date: </label>
                                    <input type="date" name="date" id="date" class="form-control"
                                     style="width: 100%; display: inline;"
                                      onchange="invoicedue(event);" required value={{$manage_ordersdata}}> 
                                </div>

                    </div>
                </div>




                <div class="card-footer">
                    <button type="submit" class="btn btn-primary mr-2">{{ isset($orders) ? 'Update' : 'Add' }}</button>
                    <a class="btn btn-secondary" href="{{ route('order.index') }}">Cancel</a>
                </div>
        </form>
    </div>

@endsection
