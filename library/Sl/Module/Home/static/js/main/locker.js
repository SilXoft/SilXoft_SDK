var locker_url = '/home/locker/ajaxcheckresource' 
$(function(){
	
	var $btn =$('[locker_resource]:first');
	
	if ($btn.length && $btn.attr('locker_resource').length){
		
		var locker_resource = $btn.attr('locker_resource'); 
		var locker_request = function (){
			$.ajax({url:locker_url,
					type:'post',
					data:{resource:locker_resource},
					success: function(data){
						if (data.result){
							setTimeout(locker_request,20*1000);
						} else {
                            if(data.code == '1000') {
                                document.location.href = '/home';
                            } else {
                                alert(data.description);
                            }
						}
					}
					});
			
		}
		setTimeout(locker_request,20*1000);
		
		
	}
})
