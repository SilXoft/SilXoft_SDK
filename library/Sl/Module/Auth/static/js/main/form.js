/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$( document ).ready(function() {
   $('.btn-success').parent().parent().css('float','left');
});

var client_email;
//$.alert('rabotayu') 
$('body').on('click', '.new-password', (function() {
                       $('.auth-form').toggle();
                       $('.change-password').toggle();
                   })
                   );
    
$('body').on('click', '.close-newpassword', (function() {
                       $(".auth-form").toggle();
                       $('.change-password').toggle();
                   })
                   );  
/*$('body').on('click', '.send-password', (function() {
                       
                       
                       $(".new-password").toggle();
                       $('.change-password').toggle();
                   })
                   );  
    */
    
    /*
                             $.ajax({
					type : 'POST',
					cache : false,
					
					data : {
						user_email : $('#' + modulerelation_input_id).val(),
						request_type : 'customer_ballance_div'
					},

					success : function(data) {
						if (data.result) {
							$customer_ballance_div.html(data.content);
						} else {
							alert(data.description)
						}

					}
				});
})*/