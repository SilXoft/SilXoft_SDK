$(document).ready(function(){
    $('#bcbuttonslog').click(function(){
        var url = $(this).attr('data-rel');
        var $iframeDiv = $('<div/>').iframePopup({
				url:document.location.protocol + '//' + document.location.host + url,
			});
		 closeIframeFunction = function() {
            $iframeDiv.dialog('close');
            $iframeDiv.remove();
        }
    });
});


