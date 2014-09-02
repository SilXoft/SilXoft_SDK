$(document).ready(function(){
    $('.field-modulerelation_customerphones-phone').change(function(){
        var reg = /[^-+ ()0-9]+/g;
        $(this).val($(this).val().replace(reg, ''));
    });
    
    $('.field-modulerelation_customeremails-mail').change(function(){
        var reg = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
        console.log('new:');
        if(reg.test($(this).val())) {
            $(this).parents('.item.control-group:first').removeClass('error');
            $('#Save').attr('disabled', null);
        } else {
            $(this).parents('.item.control-group:first').addClass('error');
            $('#Save').attr('disabled', 'disabled');
        }
        
        console.log($(this).parents('.item.control-group:first').siblings('.new_item'));
        $(this).parents('.item.control-group:first').siblings('.new_item').removeClass('error');
    });
});


