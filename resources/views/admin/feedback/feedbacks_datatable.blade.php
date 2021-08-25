<a href="javascript:;" data-url="feedbacks/destroy/{{$review->id}}"
   data-message="Are you sure you want to delete  {{ $review->title }} ?"
   data-success="The page has been deleted successfully."
   class="btn btn-icon btn-light btn-hover-primary btn-sm deleteitem" title="Delete"><i class="fa fa-remove"></i></a>

<script src="{{ asset('/js/ondeletepopup.js') }}"></script>
 
