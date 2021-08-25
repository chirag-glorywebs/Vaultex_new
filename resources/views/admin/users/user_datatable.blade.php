
<a href="adminuser/edit/{{$userlist->id}}" class="btn btn-icon btn-light btn-hover-primary btn-sm"><i
        class="fa fa-pencil"></i></a>
<a href="javascript:;" data-url="adminuser/destroy/{{$userlist->id}}" data-message="Are you sure you want to delete {{ $userlist->first_name }} ?"
   data-success="The page has been deleted successfully."
   class="btn btn-icon btn-light btn-hover-primary btn-sm deleteitem" title="Delete"><i class="fa fa-remove"></i></a>

<script src="{{ asset('/js/ondeletepopup.js') }}"></script>
