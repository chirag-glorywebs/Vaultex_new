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
                    {{-- <th>
                        <input type="checkbox" name="select_all" value="1" id="product-select-all">
                    </th> --}}
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
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

.toolbar-delete {
    float: right;
    padding: 0 10px;
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
                // select: {
                //     style: 'multi',
                //     selector: 'td:first-child'
                // },
                // dom: 'l<"toolbar-delete">frtip',
                // initComplete: function(){
                //     $("div.toolbar-delete")
                //         .html('<button class="btn btn-sm btn-danger">Delete Selected Products</button>');
                // },
                processing: true,
                serverSide: true,
                responsive: true,
                order: [[1, 'asc']],
                ajax: "{{ route('products.index') }}",
                columns: [ 
                    // {
                    //     data: "id", 
                    //     name: "selectedIds",
                    //     orderable: false,
                    //     searchable: false,
                    //     render: function (data, type, full, meta){
                    //         return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
                    //     }               
                    // },                      
                    {
                        sTitle: "No",
                        data: "id", 
                        name: "id",
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            var str = (data) ? data : '';
                            return str;
                        }                
                    },               
                    {
                        sTitle: "Product Name",
                        data: "product_name", 
                        name: "product_name",
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row, meta) {
                            var str = (data) ? data : '';
                            return str;
                        }                
                    },
                    {
                        sTitle: "Image",
                        data: "mainImage", 
                        name: "mainImage",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            var str = (data) ? data : '';                           
                            return str;
                        }                
                    },
                    {
                        sTitle: "SKU",
                        data: "sku", 
                        name: "sku",
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row, meta) {
                            var str = (data) ? data : '';
                            return str;
                        }                
                    },
                    {
                        sTitle: "Category",
                        data: "product_categories", 
                        name: "product_categories",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) { 
                            var str = '';
                            var categoryArr = []; 
                            if(data.length>0){
                                data.forEach(element => {
                                    if(element.category_name){
                                        // var categoryHtml = '';
                                        // categoryHtml += '<div class="badge-default m-2">';
                                        //     categoryHtml += element.category_name; 
                                        // categoryHtml+='</div>';
                                        // categoryArr += categoryHtml; //push(categoryHtml);                                  
                                        categoryArr.push(element.category_name);
                                    }
                                });
                                str = categoryArr.toString();  
                            }
                            return str;
                        }                
                    },
                    {
                        sTitle: "Brand",
                        data: "brand_name", 
                        name: "brands.brand_name",
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row, meta) {
                            var str = (data) ? data : '';
                            return str;
                        }                
                    },
                    {
                        sTitle: "Price",
                        data: "regular_price", 
                        name: "regular_price",
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row, meta) {
                            var str = (data) ? data : '';
                            return str;
                        }                
                    },                    
                    {
                        sTitle: "Status",
                        data: "status", 
                        name: "status",
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            var str = (data) ? data : '';
                            return str;
                        }                
                    },
                    {
                        sTitle: "Action",
                        data: "action", 
                        name: "action",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            var str = (data) ? data : '';
                            return str;
                        }                
                    }
                    // {data: 'id', name: 'id'},
                    // {data: 'product_name', name: 'product_name'},
                    // {data: 'mainImage', name: 'mainImage'},  
                    // {data: 'sku', name: 'sku'},
                    // {data: 'category_name', name: 'category_name'},
                    // {data: 'brand_name', name: 'brand_name'},
                    // {data: 'regular_price', name: 'regular_price'},
                    // {data: 'status', name: 'status'},
                    // {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            // Handle Delete Selected Checkbox Event
            $('.toolbar-delete').on('click', function(e){
                
                var ids = [];
                $.each($("input[type='checkbox']:checked"), function(){
                    if($(this).val()!='on'){
                        ids.push($(this).val());
                    }
                });
                if(ids.length>0){
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                dataType: "json",
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    "ids": ids
                                },
                                url     : '/admin/products/delete-multi-products',
                                type    : 'post',
                                beforeSend: function () {
                                },
                                success: function (resultData) {
                                    table.ajax.reload( null, false );
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: 'Product deleted successfully.',
                                        timer: 800,
                                        icon: "success",
                                        showConfirmButton: false,
                                    });
                                },
                                error: function (xhr, err) { 
                                    Swal.fire(
                                        "Error",
                                        "Oops. Something went wrong. Please try again later.",
                                        "error"
                                    )
                                }
                            });
                        }
                    });                    
                }
                
            }); 
            
            // Handle click on "Select all" control
            $('#product-select-all').on('click', function(){
                // Get all rows with search applied
                var rows = table.rows({ 'search': 'applied' }).nodes();
                // Check/uncheck checkboxes for all rows in the table
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });

            // Handle click on checkbox to set state of "Select all" control
            $('#datatable tbody').on('change', 'input[type="checkbox"]', function(){
                // If checkbox is not checked
                if(!this.checked){
                    var el = $('#product-select-all').get(0);
                    // If "Select all" control is checked and has 'indeterminate' property
                    if(el && el.checked && ('indeterminate' in el)){
                        // Set visual state of "Select all" control
                        // as 'indeterminate'
                        el.indeterminate = true;
                    }
                }
            });

        });
    </script>
    
@endsection
