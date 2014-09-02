var ajaxgetprintform = 'ajaxgetprintform';
var ajaxgetprintform_url = '/customers/main/ajaxgetprintform';
var ajaxemailfile_url = '/customers/main/ajaxemailfile';
function getprintforminfo(id) {

     var id = $('#printformname').val();

    $.ajax({
        type: 'POST',
        cache: false,
        url: ajaxgetprintform_url,
        data: {id: id},
        success: function(data) {
            if (data.result) {
                $('#mailsubject').val(data.printform.subject);
                $('#mailbody').val(data.printform.body);
                
                
            } else {
                $.alert(data.description);
            }
        }
    });


}

$(document).ready(function() {
  
  
    $('body').on('change', '#printformname', function(e) {
        getprintforminfo();

    });
    
    $('body').on('click', '#sendprintform', function() {
        
        var emails = $('#useremail').val().split(/[;,]+/); // split element by , and ;
        valid = true;
         for (var i in emails) {
             value = emails[i].trim();
             valid = valid &&
                      value.match(/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/);
         }
        if (!valid){
            $.alert('Неверный почтовый адрес клиента.');
        } else{
        
        var objmodel = $('#modelidentifire').val();
        var objmodule = $('#moduleidentifire').val();
        var pfid = $('#printformname').val();
        var subject = $('#mailsubject').val();
        var body = $('#mailbody').val();
        var objid = $('#objectid').val();
        var useremail = $('#useremail').val();
        
         $('input, textarea, select').attr('disabled','disabled'); 
        
        $.ajax({
        type: 'POST',
        cache: false,
        url: ajaxemailfile_url,
        data: {pfid:pfid, objmodel:objmodel, objmodule:objmodule, subject:subject, body:body, objid:objid, useremail:useremail},
        success: function(data) {
            if (data.result) {
               $.alert(data.message, function(){
                   window.parent.closeIframeFunction();
               });
            } else {
                $.alert(data.description);
                $('input, textarea, select').removeAttr('disabled'); 
            }
        }
    });
        
  }    
        
    })
    
    $('body').on('click', '#cancelprintform', function() {
        
       window.parent.closeIframeFunction();
    });
    
});