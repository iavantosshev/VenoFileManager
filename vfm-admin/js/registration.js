   var delay = (function(){
      var timer = 0;
      return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
      };
    })();

function checkUserAvailable(thisinput){
    
    var gliph;

    thisinput.parent().next('.form-control-feedback').removeClass('glyphicon-minus').removeClass('glyphicon-remove').removeClass('glyphicon-ok').addClass('glyphicon-refresh').addClass('fa-spin');
    thisinput.closest('.has-feedback').removeClass('has-error').removeClass('has-success');

    delay(function(){
        var username = $("#user_name").val();
        if (username.length >= 3) {
            $.ajax({
              method: "POST",
              url: "vfm-admin/ajax/usr-check.php",
              data: { user_name: username }
            })
            .done(function( msg ) {
                // console.log( "Data Saved: " + msg );
                // $("#user-result").html( msg );
                if (msg == 'success') {
                    gliph = 'glyphicon-ok';
                } else {
                    gliph = 'glyphicon-remove';
                }
                thisinput.closest('.has-feedback').addClass('has-'+msg);
                thisinput.parent().next('.form-control-feedback').removeClass('glyphicon-refresh').removeClass('fa-spin').addClass(gliph);
            });
        } else {
            thisinput.closest('.has-feedback').addClass('has-error');
            thisinput.parent().next('.form-control-feedback').removeClass('glyphicon-refresh').removeClass('fa-spin').addClass('glyphicon-remove');
        }
    }, 1000 );
}

$(document).on('keyup', '#user_name', function(){
    checkUserAvailable($(this));
});

$(document).on('keyup keypress', '#regform', function(e){
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) { 
        e.preventDefault();
        return false;
    }
});

$(document).on('submit', '#regform', function (event) {
        
    $regform = $(this);
    $regform.data('submitted', false);
    event.preventDefault();
    $('#regresponse').html('');

    var pwd1 = $('#user_pass').val();
    var pwd2 = $('#user_pass_check').val();

    if ($('#agree').length && !$('#agree').prop('checked')){
        var transaccept = $('#trans_accept_terms').val();
        $('#regresponse').html('<div class="alert alert-warning" role="alert">'+transaccept+'</div>');
        return false;
    }

    if (pwd1 !== pwd2) {
        var transerror = $('#trans_pwd_match').val();
        $('#user_pass_check').focus();
        $('#regresponse').html('<div class="alert alert-warning" role="alert">'+transerror+'</div>');
        return false;
    }

    $('.mailpreload').fadeIn('fast', function(){

        if ($regform.data('submitted') == false) {
            $regform.data('submitted', true);
            var now = $.now();
            var serial = $("#regform").serialize();
            $.ajax({
                cache: false,
                method: "POST",
                url: "vfm-admin/ajax/usr-reg.php?t=" + now,
                data: serial
            })
            .done(function( msg ) {
                $('#regresponse').html(msg);
                $('#captcha').attr('src', 'vfm-admin/captcha.php?' + now);
                $('.mailpreload').fadeOut('slow', function(){
                    $regform.data('submitted', false);
                });
                
            }).fail(function() {
                $('#regresponse').html('<div class="alert alert-danger" role="alert">error connecting user-reg.php</div>');
                $('#captcha').attr('src', 'vfm-admin/captcha.php?' + now);
                $('.mailpreload').fadeOut('slow', function(){
                    $regform.data('submitted', false);
                });
            });
        }
    });
});
