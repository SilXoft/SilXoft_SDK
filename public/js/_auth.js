(function(d){
    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement('script'); js.id = id; js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    ref.parentNode.insertBefore(js, ref);
}(document));

window.fbAsyncInit = function() {
    FB.init({
        appId      : '288248954621364', // App ID
        status     : true, // check login status
        cookie     : true, // enable cookies to allow the server to access the session
        xfbml      : true  // parse XFBML
    });
};

jQuery.fn.extend({ 
    disableSelection : function() { 
            this.each(function() { 
                    this.onselectstart = function() { return false; }; 
                    this.unselectable = "on"; 
                    jQuery(this).css({
                      '-moz-user-select': 'none',
                      '-khtml-user-select': 'none',
                      '-webkit-user-select': 'none',
                      '-o-user-select': 'none',
                      'user-select': 'none'
                    });
 
            }); 
    },
    enableSelection : function() { 
            this.each(function() { 
                    this.onselectstart = function() {}; 
                    this.unselectable = "off";
                    jQuery(this).css({
                      '-moz-user-select': 'auto',
                      '-khtml-user-select': 'auto',
                      '-webkit-user-select': 'auto',
                      '-o-user-select': 'auto',
                      'user-select': 'auto'
                    });
            }); 
    } 
});

$(document).ready(function(){
    var $nav = $('#nav');

    $nav.delegate('li','click.wl', function(event){
            var _this = $(this),
                    _parent = _this.parent(),
                    a = _parent.find('a');
            _parent.find('ul').slideUp('fast');
            a.removeClass('active');
            _this.find('ul:hidden').slideDown('fast');
            _this.find('a').eq(0).addClass('active');
            event.stopPropagation();
    });
    
    $('.handle').disableSelection();
    
    $('.fb.login').click(function(){
        FB.login(function(resp){
            if (resp.authResponse) {
                FB.api('/me', function(resp) {
                    var birth = resp.birthday.split('/'); // 12/31/2012
                    var data = {
                        name: resp.name,
                        f_name: resp.first_name,
                        l_name: resp.last_name,
                        soc: 'fb',
                        soc_id: resp.id,
                        email: resp.email
                    };
                    register(data);
                });
            }
        }, {scope: 'email'});
    });
    
    $('.ga.login').click(function(){
        var url = 'https://accounts.google.com/o/oauth2/auth?';
        url += 'scope=https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email&';
        url += 'redirect_uri=http://kea.silencatech.com/auth/google&';
        url += 'response_type=code&';
        url += 'client_id=605468493829.apps.googleusercontent.com';
        document.location.href = url;
    });
});

function register(data) {
    data.withLogin = true;
    $.ajax({
        type: 'POST',
        cache: false,
        url: '/auth/ajaxlogin',
        data: data,
        success: function(resp){
            if(resp.result) {
                document.location.href = '/';
            } else {
                console.log(resp.description);
            }
        }
    });
}
