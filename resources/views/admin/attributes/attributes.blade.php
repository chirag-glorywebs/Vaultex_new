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
                    <th>Attribute Name</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Add Variation</th>
                    <th>Variation List</th>
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
                ajax: "{{ route('attributes.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'attribute_name', name: 'attribute_name'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                    {data: 'add_variation', name: 'add_variation', orderable: false, searchable: false},
                    {data: 'list_variation', name: 'list_variation', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection
