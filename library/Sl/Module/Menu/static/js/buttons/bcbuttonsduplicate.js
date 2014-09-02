$(document).ready(function(){
    $('#bcbuttonsduplicate').click(function(){
        if (is_iframe != undefined && is_iframe) 
		{
			window.parent.closeIframeFunction();
		} else {
			var url = $(this).attr('data-rel');
			url = url!=undefined?url:'/';
			window.location.href=url;
		}
    });
});


