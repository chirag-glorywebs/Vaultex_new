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
                    <th>Category Name</th>
                    <th>Parent Category</th>
                    <th>Category Logo</th>
                    <th>Display Order</th>
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
                ajax: "{{ route('category.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'category_name', name: 'category_name'},
                    {data: 'parentCategoryName', name: 'parentCategoryName'},
                    {data: 'logoImage', name: 'logoImage'},
                    {data: 'display_order', name: 'display_order'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection
