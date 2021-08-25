{{-- Extends layout --}}
@extends('admin.layout.default')

{{-- Content --}}
@section('content')
    @if (Session::has('message'))
        <div class="alert alert-success">{{ Session::get('message') }}</div>
    @endif
    <div class="card card-custom">
        <div class="card-body">
            <div class="datatable datatable-default datatable-bordered datatable-loaded">
            <table class="table table-bordered table-hover " id="datatable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Product Name</th>
                    <th>Image</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            </div>
        </div>
    </div>
@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>

    <style>

div.dataTables_wrapper div.dataTables_processing {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    margin-left: 0;
    margin-top: 0;
    text-align: center;
    color:#fff;
    padding: 1em 0;
    height: 100%;
    background: rgba(0,0,0,0.8);
    font-size: 25px;
 
}
    </style>
@endsection

{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}
    <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
    {{-- page scripts --}}
    <!-- <script src="{{ asset('js/pages/crud/datatables/basic/basic.js') }}" type="text/javascript"></script> -->
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        //Data table initialization
        $(document).ready(function () {
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                //order: [[0, 'desc']],
                ajax: "{{ route('products.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'product_name', name: 'product_name'},
                    {data: 'mainImage', name: 'mainImage'},
                    {data: 'sku', name: 'sku'},
                    {data: 'category_name', name: 'category_name'},
                    {data: 'brand_name', name: 'brand_name'},
                    {data: 'regular_price', name: 'regular_price'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection
