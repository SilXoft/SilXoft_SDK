$(function(){
	$('#module_name, #model_name, #target_model_name').change(function(){
		var values=[$('#module_name').val()];
		var model_name = $('#model_name').val().split('\\');
		var target_model_name = $('#target_model_name').val().split('\\');
		values[values.length] = model_name[model_name.length-1];
		values[values.length] = target_model_name[target_model_name.length-1]; 
		$('#table_name').val(values.join('_'));
	});
	$('#model_name, #target_model_name').change(function(){
		var values=[];
		var model_name = $('#model_name').val().split('\\');
		var target_model_name = $('#target_model_name').val().split('\\');
		values[values.length] = model_name[model_name.length-1];
		values[values.length] = target_model_name[target_model_name.length-1]; 
		$('#modulerelation_name').val(values.join(''));
	});
});
