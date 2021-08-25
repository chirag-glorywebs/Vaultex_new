<!--<a href="bulk-order/edit/{{$bulkOrderList->id}}" class="btn btn-icon btn-light btn-hover-primary btn-sm"><i
        class="fa fa-pencil"></i></a>-->
<a href="bulk-order/show/{{$bulkOrderList->id}}" class="btn btn-icon btn-light btn-hover-primary btn-sm"><i
        class="fa fa-eye"></i></a>
<a href="javascript:;" data-url="bulk-order/delete/{{$bulkOrderList->id}}" data-message="Are you sure you want to delete  {{ $bulkOrderList->name }} ?"
   data-success="The page has been deleted successfully."
   class="btn btn-icon btn-light btn-hover-primary btn-sm deleteitem" title="Delete"><i class="fa fa-remove"></i></a>

<script src="{{ asset('/js/ondeletepopup.js') }}"></script>
