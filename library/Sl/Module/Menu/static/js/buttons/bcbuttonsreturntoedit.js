$(document).ready(function(){
    $('#bcbuttonsreturntoedit').click(function(){
        var url = $(this).attr('data-rel');

        if (is_iframe){
            window.location.href=url+'/is_iframe/1';
        } else {
            window.location.href=url; 
        }
    });
});


