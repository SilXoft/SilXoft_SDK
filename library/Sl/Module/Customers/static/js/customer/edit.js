$(document).ready(function(){
    $('.field-modulerelation_customerphones-phone').change(function(){
        var reg = /[^-+ ()0-9]+/g;
        $(this).val($(this).val().replace(reg, ''));
    });
/*    
    	$('body').on('click', '.listform .formsize', function(){
		if ($(this).is('.icon-resize-small')){
			$(this).removeClass('icon-resize-small');
			$(this).addClass('icon-resize-full');
                        $(this).parents('.wellform:first').removeClass('wellform_all');
			
		} else {
			$(this).removeClass('icon-resize-full');
			$(this).addClass('icon-resize-small');
                        $(this).parents('.wellform:first').addClass('wellform_all');
			//$('#packageitemflag').css({maxHeight:1500});
		}
		
	});
  */  
    
});


