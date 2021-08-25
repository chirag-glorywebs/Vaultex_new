{{-- Extends layout --}}
@extends('admin.layout.default')

{{-- Content --}}
@section('content')
    @if (Session::has('message'))
        <div class="alert alert-success">{{ Session::get('message') }}</div>
    @endif
    <div class="card card-custom">
        <div class="card-body">
            <div class="datatable datatable-default datatable-bordered">
                <table class="data-table table table-bordered table-hover datatable-bordered datatable-head-custom datatable-table"
                       id="datatable">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        @endsection
        {{-- Styles Section --}}
        @section('styles')
            <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
                  type="text/css"/>
        @endsection
        {{-- Scripts Section --}}
        @section('scripts')
            {{-- vendors --}}
            <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
            {{-- page scripts --}}
            <script src="{{ asset('js/pages/crud/datatables/basic/basic.js') }}" type="text/javascript"></script>
            <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
            {{-- Yajra Datatables Script --}}
            <script type="text/javascript">
                //Data table initialization
                $(document).ready(function () {
                    var table = $('#datatable').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.adminuser') }}",
                        columns: [
                            {data: 'id', name: 'id'},
                            {data: 'first_name', name: 'first_name'},
                            {data: 'last_name', name: 'last_name'},
                            {data: 'email', name: 'email'},
                            {data: 'status', name: 'status'},
                            {data: 'action', name: 'action', orderable: false, searchable: false},
                        ]
                    });
                });
            </script>
@endsection
