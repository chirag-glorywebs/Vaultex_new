/* delete record*/
$(document).ready(function () {
    if ($('.deleteitem').length) {
        $('.deleteitem').on('click', function () {
            var whichtr = $(this).closest("tr");
            var delurl = $(this).data('url');

            var delTitle = "Are you sure?";
            var delText = "You won't be able to revert this!";
            var delSuccess = "Your data has been deleted.";
            if ($(this).data('message')) {
                delTitle = $(this).data('message');
            }
            if ($(this).data('success')) {
                delSuccess = $(this).data('success');
            }
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            Swal.fire({
                title: delTitle,
                text: delText,
                icon: "warning",

                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "post",
                        url: delurl,
                        data: {
                            _token: CSRF_TOKEN,
                            _method: 'DELETE',
                        },
                        success: function (data) {
                            console.log(data);
                            //table.draw();
                            Swal.fire({
                                title: "Deleted!",
                                text: delSuccess,
                                timer: 800,
                                icon: "success",
                                showConfirmButton: false,
                            });
                            whichtr.remove();
                        },
                        error: function (data) {
                            console.log('Error:', data);
                            Swal.fire(
                                "Error",
                                "Oops. Something went wrong. Please try again later.",
                                "error"
                            )
                        }

                    });

                    // window.location.href = delurl;
                } else if (result.dismiss === "cancel") {
                    Swal.fire(
                        "Cancelled",
                        "Your data is safe :)",
                        "error"
                    );
                }
            });
        });
    }
});

