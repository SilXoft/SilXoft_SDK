$(document).ready(function(){
    $('ul.breadcrumb').on('click', '.emailbutton', function(e){
        
        closeIframeFunction = function() {
            $iframeDiv.dialog('close');
            $iframeDiv.remove();
        }
        
        var $a = $(this);
        url = $a.attr('rel');   
        //alert($a.attr('rel'));
        var request_obj = {};
 
        request_obj['is_iframe'] = 1;

        //var url = '/customers/main/ajaxgetemailpf';

        var $iframeDiv = $('<div />').css({
            width: 500,
            padding: 0,
        });
        var $iframe = $('<iframe />').attr({
            width: 490,
            height: 450
        }).css({
            border: 0
        }).attr('src', document.location.protocol + '//' + document.location.host + url + '?' + $.param(request_obj)).appendTo($iframeDiv);
        
        $('body').append($iframeDiv);
        
       //  var title = $(this).attr('data-title') || $(this).attr('title') || '';
	
	        $iframeDiv.dialog({
	            title: 'Форма отправки письма',
	            autoOpen: false,
	            width: 500,
	            height: 490,
	            modal: true,
	            resizable: false
	        });
	        $iframeDiv.dialog('open');
               
      
      
    });
    
    
});