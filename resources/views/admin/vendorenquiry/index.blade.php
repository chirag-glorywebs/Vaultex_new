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
                        <th>Vendor Code</th>
                        <th>Vendor Name</th>
                        <th>Vendor Email</th>
                        <th>Send Email</th>
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
                ajax: "{{ route('vendor-enquiry.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'vendor_code', name: 'vendor_code'},
                    {data: 'vendor_name', name: 'vendor_name'},
                    {data: 'email', name: 'email'},
                    {
                        data: 'id', name: 'id', render: function (data, type, row) {

                            return "<a href='javascript:;' class='send-email' data-target='" + row.email + "' data-target-code='" + row.vendor_code + "'>Send Email</a>"
                            //return "<a href='/users/" + row.email + "'>Send Email</a>"
                        }
                    },
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
            $(document).on("click", ".send-email", function () {
                var emailData = $(this).attr('data-target');
                var vendorCode = $(this).attr('data-target-code');
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: "POST",
                    //url: emailURL,
                    url: "{{ route('forget-password') }}",
                    data: {
                        _token: CSRF_TOKEN,
                        'email': emailData,
                        'vendor_code': vendorCode
                    },
                    success: function (data) {
                        
                        Swal.fire("Email Sent!", "The Email has been sent successfully!", "success");
                        
                    }
                });

            });
        });
    </script>
@endsection
