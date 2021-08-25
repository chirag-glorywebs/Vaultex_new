$(function($){
    var current = location.href;
    $('#kt_aside_menu li a').each(function(){
        var $this = $(this);
        //if($this.attr('href').indexOf(current) !== -1){
        if($this.attr('href') === current){
            $this.parent('li').addClass('menu-item-active');
            $this.parents('li').addClass('menu-item-open menu-item-here');
        }
    });
});
$(".productpage .nav-tabs li").click(function () {
    $(".nav-tabs li").removeClass("active");
    $(this).addClass("active");
});
/* generate random password*/
function randomPassword(length) {
    var chars = "abcdefghijklmnopqrstuvwxyz!@#$%^&*()-+<>ABCDEFGHIJKLMNOP1234567890";
    var pass = "";
    for (var x = 0; x < length; x++) {
        var i = Math.floor(Math.random() * chars.length);
        pass += chars.charAt(i);
    }
    return pass;
}
function generate() {
    adminfrm.password.value = randomPassword(adminfrm.length.value);
}
/* view password */
function showPassword() {
    var x = document.getElementById("myInput");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}

$("#show_hide_password .showpwd").on('click', function(event) {
    event.preventDefault();
    if($('#show_hide_password input').attr("type") == "text"){
        $('#show_hide_password input').attr('type', 'password');
        $('#show_hide_password i').addClass( "fa-eye-slash" );
        $('#show_hide_password i').removeClass( "fa-eye" );
    }else if($('#show_hide_password input').attr("type") == "password"){
        $('#show_hide_password input').attr('type', 'text');
        $('#show_hide_password i').removeClass( "fa-eye-slash" );
        $('#show_hide_password i').addClass( "fa-eye" );
    }
});

