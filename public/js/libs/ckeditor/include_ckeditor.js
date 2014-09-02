$(document).ready(function() {
    
    var ckeditor = $('.ckeditor_form');
    if(ckeditor){
    ckeditor.each( function(key,val){
        var ckeditor_id = $(val).attr('id');
        CKEDITOR.replace( ckeditor_id, {
             filebrowserBrowseUrl: '/ajaxfilemanager/ajaxfilemanager.php',    
            language: 'ru'
        });  
    });    
    }   
});
    
