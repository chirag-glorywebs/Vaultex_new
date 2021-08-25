<a href="javascript:;" data-url="vendor-enquiry/destroy/{{$vendorEnquiryList->id}}"
   data-message="Are you sure you want to delete  {{ $vendorEnquiryList->vendor_code }} ?"
   data-success="The page has been deleted successfully."
   class="btn btn-icon btn-light btn-hover-primary btn-sm deleteitem" title="Delete"><i class="fa fa-remove"></i></a>

<script src="{{ asset('/js/ondeletepopup.js') }}"></script>

<!--<script>
    $(document).ready(function () {
        if ($('.send-email').length) {

            $('.send-email').on('click', function () {
                var emailURL = $(this).attr('data-url');
                var email = $(this).attr('data-target');
                var venderCode = $(this).attr('data-code');
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: "POST",
                    //url: emailURL,
                    url: "{{--{{ URL::to('admin/forget-password') }}--}}" + '/' + email,
                    data: {
                        _token: CSRF_TOKEN,
                        'email': email,
                        'venderCode': venderCode
                    },
                    success: function (data) {
                    }
                });
            });
        }
        /*$(document).on("click", ".send-email", function () {
            var emailURL = $(this).attr('data-url');
            var email = $(this).attr('data-target');
            var venderCode = $(this).attr('data-code');
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: "POST",
                //url: emailURL,
                url: "{{--{{ URL::to('admin/forget-password') }}--}} " + '/' + email,
                data: {
                    _token: CSRF_TOKEN,
                    /!* 'email': email,
                     'venderCode': venderCode,*!/
                },
                success: function (data) {
                }
            });
        });*/
    });
</script>-->

<!--
https://codingdriver.com/custom-forgot-reset-password-functionality-in-laravel.html
-->

