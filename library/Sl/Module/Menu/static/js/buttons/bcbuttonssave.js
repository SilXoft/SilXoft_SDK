$(document).ready(function(){
    var _setLoading = function(l) {
        if(l === false) {
            $('#bcbuttonssave').removeClass('icon-loading').addClass('icon-floppy-save');
        } else {
            $('#bcbuttonssave').removeClass('icon-floppy-save').addClass('icon-loading');
        }
    }
    
    $('#bcbuttonssave').click(function(){
        var $form = $('form:first');
        
        if(parseInt($form.attr('calculating')) > 0) {
            return;
        }
        
        if ($(this).attr('validate_action').length){
            var $save = $(this);
            if($save.attr('disabled') === 'disabled') return;
            $save.attr('disabled','disabled');
            
            $('.form-top-errors',$form).remove();
            _setLoading();
            $.ajax({
                type: 'POST',
                url: $(this).attr('validate_action'),
                data: $form.serialize(), 
                success: function(res) {
                    if(res.result){
                        $form.trigger('afterformvalidate.sl');
                        $form.submit();
                    } else {
                        showErrors(res.description,$form);
                        $('html,body').scrollTop(0);
                       // $form.trigger('validate.fail');
                        $form.trigger('formerror.sl');
                    }
                    _setLoading(false);
                    $save.removeAttr('disabled');
                },
                error: function(a,b,c){
                    $.alert('Validation error');
                    $save.removeAttr('disabled');
                    $form.trigger('formerror.sl');
                    _setLoading(false);
                     
                }
            });
        };
    });
    
    $(document).on('calculator_start', function(event, data) {
        if (data.wrapper != undefined) {
            _setLoading();
        }
    });

    $(document).on('calculator_finish', function(event, data) {
        if (data.wrapper != undefined) {
            _setLoading(false);
        }
    });
});


