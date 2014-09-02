(function( $ ){
   $.fn.iframePopup = function(data) {
   	  	console.log('start iframe');
   	  	//get data
   	  	var url = data.hasOwnProperty('url')?data.url:'';
   	  	if ($(this).is(':not(div)')){
   	  		console.log('there is not div in iframePopup');
   	  		return false;
   	  	}
   	  	if (!url.length){
   	  		console.log('there is no url in iframePopup request');
   	  		return false;
   	  	}
   	  	
   	  	var request_data = data.hasOwnProperty('data')?data.data:{};
   	  	var width = data.hasOwnProperty('width')?data.width:1050;
   	  	var height = data.hasOwnProperty('height')?data.height:600;
   	  	var title = data.hasOwnProperty('title')?data.title:'';
   	    if (!request_data.hasOwnProperty('is_iframe')) request_data['is_iframe']=1;
   	    
   	    $(this).css({
	            width: width-10,
	            padding: 0
	    });
	
	    var $iframe = $('<iframe />').attr({
	            width: width-10,
	            height: height-50
	        }).css({
	            border: 0
	        }).attr('src', url + (Object.keys(request_data).length?('?' + $.param(request_data)):'')).appendTo($(this));
	
	        $('body').append($(this));
	
	        
	
	        $(this).dialog({
	            title: title,
	            autoOpen: false,
	            width: width,
	            height: height,
	            modal: true,
	            resizable: false
	        });
			
	       var $popup = $(this).dialog('open');
			
               return $popup;
			
   	  
      
   }; 
})( jQuery );
