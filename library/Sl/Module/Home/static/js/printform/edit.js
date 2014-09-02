var printform_request_url = '/home/printform/ajaxprintformhelp';

$(function(){
    //console.log(printform_help_action);
    forprintformhelp();
    $('#name').change(function(){
        forprintformhelp();
    })
    
    $('body').on('click',".printform_helper div.inline", function(){
         var template; 
         
         template = $(this).find(".name").attr('title');
         
         $( "#data" ).insertAtCaret(template);
         CKEDITOR.instances.data.insertText( template );
     });
});
 
var
        forprintformhelp = function() {

    var name = $('#name').val();

    if (printform_help_action == true) {
 
        $.ajax({
            type: 'POST',
            cache: false,
            url: printform_request_url,
            data: {
                name: name,
            },
            success: function(data) {
                $('#printform_helper').remove();
                if (data.content) {
                    if ($("#printform_helper").length) {
                        $("#printform_helper").replaceWith(data.content);
                    } else {
                       $(data.content).insertAfter("#data");
                    }

                }
            }


        });
    }
}
    
    
$(document).ready(function(){
    var $popup = $('<div />').css({ display: 'none' }).appendTo($('body'));
    
    $popup.dialog({
        autoOpen: false,
        modal: true,
        title: 'Priview',
        buttons: {
            'Ok': {
                text: 'Ok',
                click: function(){
                    $popup.dialog('close');
                }
            }
        }
    });
    
    var $preview = $('<span />').text('Preview').addClass('btn').css({
        display: 'none',
        position: 'absolute',
        marginLeft: '-81px'
    }).appendTo($('#data').parent());
    
    $preview.on('click', function(){
        var val = $('#data').val() || $('#data').text() || $('#data').html();
        $popup.html(val);
        $popup.dialog('open');
    });
    
    $('#type').change(function(){
        var $this = $(this);
        var out_type = '';
        $preview.css('display', 'none');
        switch($this.val()) {
            case 'xls':
                out_type = 'application/vnd.ms-excel';
                break;
            case 'pdf':
                out_type = 'application/pdf';
                break;
            case 'txt':
                $preview.show();
                break;
        }
        out_type = $this.val();
        if(out_type.length) {
            $('input[type="file"]').attr('accept', out_type);
        }
    });//.change();
    
    
     
});


    
