$(function(){
	$('#module_name, #name').change(function(){
		var values=[$('#module_name').val()];
		var model_name = $('#name').val().split('\\');
		values[values.length] = model_name[model_name.length-1];
		$('#table_name').val(values.join('_'));
	});
	
});
