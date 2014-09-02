var context_click_function;

$(function() {      
   
 var  $contextMenu = $("#contextMenu");
 
if($contextMenu){
$("body").on("contextmenu", "table.dataTable tbody tr", function(e) {
   $contextMenu.css({
      display: "block",
      left: e.pageX,
      top: e.pageY
   });
   return false;
});
//var $contextMenu = $("#contextMenu");
var $rowClicked;
var $cellClicked;

$("body").on("contextmenu", "table.dataTable tbody tr td", function (e) {
    $rowClicked = $(this).parents('tr:first')
    $cellClicked = $(this);
    $contextMenu.css({
        display: "block",
        left: e.pageX,
        top: e.pageY
    });
    return false;
});
$contextMenu.on("mouseover", "a, span", function () {
    var $data = $rowClicked.data(); 
    var url = $(this).attr('data-baseurl');
    var id = $(this).attr('field-id');
    var page_current_alias = $('body').attr('data-alias');  
    
    url = url.replace('{{$id}}', $data.id );    
    url = url.replace(page_current_alias.replace('.','/'), $data.alias.replace('.', '/'));    
    url = url+'/'+id+'/'+$data.id;
   $(this).attr('data-url', url); 
   $(this).attr('href', url);    
   
    
});

$contextMenu.on("click", "a", function () { 

    $contextMenu.hide();
  //  window.location.href = url;
 
});

$contextMenu.on('click', "a", function(e){
	e.preventDefault();
	var a = this;
	if ($(this).is('.confirm')){
		
		var text = $(this).text();
		$.confirm('Подтвердите действие, пожалуйста:', text+'?', undefined, function() {
        
				context_click_function(a, $cellClicked);
        });
		
		
		
	} else {
		context_click_function(a, $cellClicked);
	}
	
	
	
});

$contextMenu.on("click", "a.ajax-action", function () {
		return false;
});


$(document).click(function () {
    $contextMenu.hide();
});

}
});


context_click_function = function (a, cellClicked){
	if ($(a).is('.ajax-action')){
		
		var table = cellClicked.parents('table.datatable');
		var text = $(a).text();
		var url = $(a).attr('href');
		if (table.length && url){
		
			$.get(url,{},function(data){
				if (!data.result){
					
					showErrors(data.description || data.errors || {text :'Some error throw action'},table.parents(table_controls_wrapper));
				}
				table.dataTable().fnDraw(); 
			}, 'json').error(function(data, data_1, data_2){
				console.log(data);
				console.log(data_1);
				console.log(data_2);
				});	
			
		} 
	} else if ($(a).is('.target-blank')) {
		
		window.open($(a).attr('href'));
		
	} else {
		
		document.location.href = $(a).attr('href');
		
	}	
}