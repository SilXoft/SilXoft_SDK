$(document).ready(function(){
    var _setLoading = function(l) {
        if(l === false) {
            $('#bcbuttonssaveandreturn').removeClass('icon-loading').addClass('icon-floppy-disk');
        } else {
            $('#bcbuttonssaveandreturn').removeClass('icon-floppy-disk').addClass('icon-loading');
        }
    }
   
    
    $('#bcbuttonssaveandreturn').click(function(){
        var $this = $(this);
        if($('#bcbuttonssave').attr('disabled') === 'disabled') return;
        $('form:first').one('afterformvalidate.sl', function(){
            var $form = $(this); 
            $form.append('<input type="hidden" data-type="saveandreturn" name="form_after_save_url" value="'+$this.attr('data-rel')+'">');
            _setLoading(false);
        });
        $('form:first').one('formerror.sl', function(){
            var $form = $(this); 
            $form.find('[data-type="saveandreturn"]').remove();
            _setLoading(false);
           
        });
        _setLoading();
        $('#bcbuttonssave').trigger('click');
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


