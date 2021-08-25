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
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Title</th>
                        <th>Review descr</th>
                        <th>UserName</th>
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
                ajax: "{{ route('feedbacks.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {
                    data: 'product_name', name: 'product_name', render: function (data, type, row) {
                            return "<a href='products/edit/"+row.proid+"'>" + row.product_name + "</a>";
                        }
                    },
                    {data: 'title', name: 'title'},
                    {data: 'comment', name: 'comment'},
                    
                    {
                    data: 'name', name: 'name', render: function (data, type, row) {
                            if(row.name != ''){
                                return "<a href='vendoruser/edit/"+row.userid+"'>" + row.name + "</a>";
                            }else{
                                return "<a href='customers/edit/"+row.userid+"'>" + row.first_name + "</a>";
                            }
                            
                        }
                    },
 
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        
        });
    </script>
@endsection
