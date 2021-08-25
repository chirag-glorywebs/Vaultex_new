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
                <div class="my-5">
                    <div class="row">
                        <div class="col-xl-12">
                            <a style="float:right;" class="btn btn-secondary" href="{{ route('bulk-order') }}">Back</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <h3 class="text-dark font-weight-bold mb-10">Bulk Order Info:</h3>

                            <hr>
                        </div>
                        @if(isset($bulkOrderDataShow))
                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4"><b>GSTIN : </b></label>
                                    <div class="col-8">{{$bulkOrderDataShow['gstin']}}</div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4"><b>Name : </b></label>
                                    <div class="col-8">{{$bulkOrderDataShow['name']}}</div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4"><b>Email : </b></label>
                                    <div class="col-8">{{$bulkOrderDataShow['email']}}</div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4"><b>Phone : </b></label>
                                    <div class="col-8">{{$bulkOrderDataShow['phone']}}</div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4"><b>Description : </b></label>
                                    <div class="col-8">{{$bulkOrderDataShow['description']}}</div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4"><b>Order Date : </b></label>
                                    <div class="col-8">{{ date('m/d/Y', strtotime($bulkOrderDataShow['created_at'])) }}</div>
                                </div>
                            </div>
                        @endif

                        <div class="col-xl-12">
                            <br>
                            <h3 class="text-dark font-weight-bold mb-10">Bulk Order Item Info:</h3>
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Order No</th>
                                    <th>Product/Category Details</th>
                                    <th>Quantity</th>
                                    <th>Brand</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($bulkOrderItemDataShow))
                                    @foreach($bulkOrderItemDataShow as $key => $bulkOrderItemShowValue)
                                        <tr>
                                            <td>{{$bulkOrderItemShowValue['order_id']}}</td>
                                            <td>{{$bulkOrderItemShowValue['product_or_category_details']}}</td>
                                            <td>{{$bulkOrderItemShowValue['quantity']}}</td>
                                            <td>{{$bulkOrderItemShowValue['brand']}}</td>
                                            <td>{{$bulkOrderItemShowValue['status']}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                </div>

        </form>
    </div>

@endsection
