{{-- Extends layout --}}
@extends('admin.layout.default')

{{-- Content --}}
@section('content')
    @if (Session::has('message'))
        <div class="alert alert-success">{{ Session::get('message') }}</div>
    @endif
    <div class="card card-custom">
        <div class="card-body">
            <div class="datatable datatable-default datatable-bordered ">
            <table class="table table-bordered table-hover datatable-bordered datatable-head-custom datatable-table" id="datatable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Price List</th>
                    <th>Credit Limit</th>
                    <th>Status</th>
                    <th width="70">Action</th>
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
                ajax: "{{ route('vendoruser.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'vendor_code', name: 'vendor_code'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'price_list_no', name: 'price_list_no'},
                    {data: 'vendor_credit_limit', name: 'vendor_credit_limit'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection
