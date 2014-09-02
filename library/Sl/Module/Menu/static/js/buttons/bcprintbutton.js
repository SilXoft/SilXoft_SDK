$(document).ready(function(){
   $('body').on('click',".dropdown-menu a.bcprintbutton", function(){
        var href;
        if (($(this).attr('href')) !=undefined){
            href = $(this).attr('href');
            $("div.dropdown.pull-right.open").removeClass("open");
            window.open( href );
            return false;

        }
        
    });
});


