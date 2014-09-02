var calculator_entry_point = '/main/ajaxcalculator';
var calculator_warnings_key_regex = /form_warnings$/;
var calculator_warnings_description_key = 'description';
var calculator_warnings_fields_key = 'fields';
var calculator_set_val;
var relation_names_regex = /-names$/;
var calculator_selectors;
var div_item_selector = 'div.item';

calculator_set_val = function($obj, value) {

	if ($obj.attr('type') == 'checkbox') {
		if (value) {
			if (!$obj.is(':checked')) {
				$obj.attr('checked', 'checked');
				return true;
			}

		} else {
			if ($obj.is(':checked')) {
				$obj.removeAttr('checked');
				return true;
			}
		}
	} else {
		if ($obj.val() != value) {
			$obj.val(value);
            $obj.animate({
                borderColor : '#3cb521',
                '-webkit-box-shadow' : 'inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6)',
                '-moz-box-shadow' : 'inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6)',
                'box-shadow' : 'inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6)'
            }, {
                duration : 5000,
                queue : false,
                easing : 'easeOutElastic',
                complete : function() {
                    $(this).css({
                        borderColor : '#cccccc',
                        '-webkit-box-shadow' : 'none',
                        '-moz-box-shadow' : 'none',
                        'box-shadow' : 'none'
                    });
                }
            });
			return true;
		}

	}
	return false;
}
var calculator = {

	calculators_fields : {},
	unwarning_fields : {},
	calculator_selectors : {},
	calculator_wrappers : {},
	addCalculators : function(calculator_selectors) {
		
		this.calculator_selectors = calculator_selectors;
		this.registerCalcWrappers(calculator_selectors);
		
		for (form_class in calculator_selectors) {
			if (calculator_selectors[form_class].hasOwnProperty('calculators_fields')) {
				var obj_this = this;
				$.each(calculator_selectors[form_class].calculators_fields, function(key, value) {
					obj_this.calculators_fields[key] = value;
				});
				//this.calculators_fields = this.calculators_fields.concat( calculator_selectors[form_class].calculators_fields);

			}
			if (calculator_selectors[form_class].hasOwnProperty('unwarning_fields')) {
				var obj_this = this;

				$.each(calculator_selectors[form_class].unwarning_fields, function(key, value) {
					obj_this.unwarning_fields[key] = value;
				});
				//this.calculators_fields = this.calculators_fields.concat( calculator_selectors[form_class].calculators_fields);

			}

			var options = calculator_selectors[form_class];

			if (options.hasOwnProperty('fields')) {
				selector = 'form.' + form_class 
				$form = $(selector);
				this_object = this;
				
				$form.each(function() {
					this_object.addFormCalc($(this), options.fields, 'form.' + form_class + ':first');
				});

				selector = 'div.' + form_class +'_subform'; 
				$form = $(selector);
				this_object = this;
				
				$form.each(function() {
					this_object.addFormCalc($(this), options.fields, 'div.' + form_class + '_subform:first');
				});

			}

			if (options.hasOwnProperty('modulerelations')) {

				for (relation_class in options.modulerelations) {
					selector = 'div.form_list.' + relation_class + ' '+div_item_selector; 
					$form = $(selector);
					
					this_object = this;
					
					$form.each(function() {
						this_object.addFormCalc($(this), options.modulerelations[relation_class], div_item_selector+':first', relation_class);
					});

					//'div.form_list.'+relation_class+' div.item:first'

				}

			}

		}
		
	},
	delayedCalcs : {},
	
	unregisterDelayedCalc : function(wrapper){
		$(document).trigger('calculator_delay_delete', {

						wrapper : this.delayedCalcs[wrapper].form
					});
		delete this.delayedCalcs[wrapper];			
	},
	registerDelayedCalcs : function(wrapper, calc, $form, selectors, relation, changed){
		
		if (!this.delayedCalcs.hasOwnProperty(wrapper)){
			this.delayedCalcs[wrapper] = {form:$form, calcs:{}, selectors: {}, relation:relation, changed: {}};
			$(document).trigger('calculator_delay', {
						wrapper : $form
					});
			
		} 
		
		this.delayedCalcs[wrapper].calcs[calc] = calc;
		this.delayedCalcs[wrapper].changed[changed] = changed;
		
		for (i in selectors) this.delayedCalcs[wrapper].selectors[selectors[i]]=selectors[i];
		
		
		
	},
	
	registerCalcWrappers : function (calculator_selectors){
		var selector;
		for (form_class in calculator_selectors) {
		
			var options = calculator_selectors[form_class];

			if (options.hasOwnProperty('fields')) {
				selector = 'form.' + form_class; 
				
				if ( options.hasOwnProperty('comformity') && options.comformity.hasOwnProperty(form_class)){
					for (i in options.comformity[form_class]){
						this.registerCalcWrapper(selector,options.comformity[form_class][i]);
					}
				} 
			}

			if (options.hasOwnProperty('modulerelations')) {

				for (relation_class in options.modulerelations) {
					selector = 'div.form_list.' + relation_class; 
					if ( options.hasOwnProperty('comformity') && options.comformity.hasOwnProperty(relation_class)){
						for (i in options.comformity[relation_class]){
							this.registerCalcWrapper(selector,options.comformity[relation_class][i]);
						}
					} 
				}

			}

		}
		
		
	},
	
	registerCalcWrapper : function (wrapper, calc){
		
		this.calculator_wrappers[calc] = wrapper;
		
	},
	addNewRelationCalcs : function($wrapper) {

		calculator_selectors = this.calculator_selectors;

		for (form_class in calculator_selectors) {

			var options = calculator_selectors[form_class];

			if (options.hasOwnProperty('modulerelations')) {

				for (relation_class in options.modulerelations) {

					$form = $('div.form_list.' + relation_class + ' '+div_item_selector);

					this_object = this;
					//	console.log($wrapper);
					$form.each(function() {

						if ($wrapper[0] == $(this)[0]) {

							this_object.addFormCalc($(this), options.modulerelations[relation_class], div_item_selector+':first', relation_class);

						}
					});

				}

			}

		}
	},
	
	
	
	addFormCalc : function($wrapper, calculator_selectors, field_selector, relation) {
		var obj_this = this;
		

		$.each(calculator_selectors, function(classname, calculators) {
			
			$wrapper.delegate('.' + classname, 'change', function(event, extra) {
				
				
				var calcupdate = (extra instanceof Object && extra.hasOwnProperty('calcupdate'));
				
				
				var selectors = [];
				var unwarn_selectors = [];

				$.each(calculators, function(i, calc_name) {
					if (obj_this.unwarning_fields[calc_name] != undefined && obj_this.unwarning_fields[calc_name].length) {
						unwarn_selectors = unwarn_selectors.concat(obj_this.unwarning_fields[calc_name]);

					}
				});

				var $form_wrapper = $(this).parents(field_selector);
				var $field = $(this);
				if (unwarn_selectors.length) {
					for (i in unwarn_selectors) {
						$('.' + unwarn_selectors[i], $form_wrapper).parents('.control-group:first').removeClass('warning');
					}
				}
				
				var now_calcs = [];
				var changed = $(this).attr('id');
				$.each(calculators, function(i, calc_name) {
					
					if (obj_this.calculators_fields.hasOwnProperty(calc_name) && obj_this.calculators_fields[calc_name] != undefined)
						{	
							if (	!calcupdate &&
									((relation == undefined && $field.parents(div_item_selector).length == 0) 
									|| obj_this.calculator_wrappers[calc_name] == 'div.form_list.' + relation) ){

								selectors = selectors.concat(obj_this.calculators_fields[calc_name]);
								now_calcs[now_calcs.length] = calc_name;	
								
								
							} else if (calcupdate){
								var selector = 'calcupdate';
								obj_this.registerDelayedCalcs(selector , calc_name, $wrapper, obj_this.calculators_fields[calc_name] , relation, changed);
							}  else {
								
								var selector = 'div.form_list.'+$field.parents('div.form_list:first').attr('class').trim().split(' ').join('.');
								obj_this.registerDelayedCalcs(selector , calc_name, $wrapper, obj_this.calculators_fields[calc_name] , relation, changed);
								
							}
							
							
						}
				});
				
				
				obj_this.doCalcs($form_wrapper,now_calcs, selectors, relation, changed);
				//console.log($form_wrapper.find('.'+selectors.join(':not(.deleted), .')+':not(.deleted)'));

				
			});

		});
	}, 
	
	doCalcs : function ($form_wrapper, calculators, selectors, relation, changed){
		
		if (calculators.length && selectors != undefined && selectors.length > 0) {
			
					var $parent_form = $form_wrapper.is('form')?$form_wrapper:$form_wrapper.parents('form:first');
					// Find disabled inputs, and remove the "disabled" attribute
					var disabled = $form_wrapper.find(':disabled').removeAttr('disabled');
				
					$form_wrapper.find('.' + selectors.join(':not(.deleted), .') + ':not(.deleted)').filter('input[type="checkbox"]:not(:checked)').attr('checked', 'checked').val(0);

					var formArr = $form_wrapper.find('.' + selectors.join(':not(.deleted), .') + ':not(.deleted)').serializeArray();

					$form_wrapper.find('.' + selectors.join(':not(.deleted), .') + ':not(.deleted)').filter('input[type="checkbox"][value=0]:checked').removeAttr('checked').val(1);
					
					// re-disabled the set of inputs that you previously enabled
					
					disabled.attr('disabled', 'disabled');

					$.each(formArr, function(i, field) {
						formArr[i].value = $.trim(field.value);
					});
					
					changed = (changed instanceof Array)? changed:[changed];
					
					formArr[formArr.length] = {
						name : 'model_changed_fields',
						value : changed
					};

					formArr[formArr.length] = {
						name : 'model_calculators',
						value : calculators
					}

					if (relation != undefined)
						formArr[formArr.length] = {
							name : 'relation_array_name',
							value : relation
						};

					var data = $.param(formArr);

					$(document).trigger('calculator_start', {
						data : formArr,
						wrapper : $parent_form
					});

				

					$.ajax({
						url : calculator_entry_point,
						data : data,
						type : 'post',
						dataType : 'json', 
						success : function(data) {
							$(document).trigger('calculator_finish', {data:data, wrapper:$parent_form});

							if (data.hasOwnProperty('result') && data.result instanceof Object && Object.keys(data.result).length) {

								var edited = [];
								var $object;

								for (class_name in data.result) {

									if (class_name.match(calculator_warnings_key_regex)) {
										var warnings = data.result[class_name];

										if (warnings.length) {

											for (warning_key in warnings) {
												if (warnings[warning_key][calculator_warnings_fields_key] != undefined && warnings[warning_key][calculator_warnings_fields_key].length) {
													var warning_fields = warnings[warning_key][calculator_warnings_fields_key];

													for (field_num in warning_fields) {
														$object = $form_wrapper.find('.' + warning_fields[field_num] + ':not(.deleted)').parents('.control-group:first').addClass('warning');

													}
												}
											}
										}
									} else if (data.result[class_name] instanceof Object) {

										for (obj_id in data.result[class_name]) {
											$object = $form_wrapper.find('.' + class_name + ':not(.deleted)[id*="-' + obj_id + '-"]');
											if ($object.length && calculator_set_val($object, data.result[class_name][obj_id])) {

												if ($.inArray(class_name, selectors) == -1)
													edited[edited.length] = $object;

												if (class_name.match(relation_names_regex)) {

													var relation_ids_class = class_name.replace(relation_names_regex, '');
													
													$object = $form_wrapper.find('.' + relation_ids_class + ':not(.deleted)[id*="-' + obj_id + '-"]');
													
													if ($object.length && $object.attr('data-name') != undefined) {
														$object.attr('data-name', data.result[class_name][obj_id]);
													}
												}
											}

										}

									} else {

										$object = $form_wrapper.find('.' + class_name + ':not(.deleted)');

										if ($object.length && calculator_set_val($object, data.result[class_name])) {

											if ($.inArray(class_name, selectors) == -1)
												edited[edited.length] = $object;

											//if is a relations names, update relations data-name attribute
											if (class_name.match(relation_names_regex)) {
												var renation_ids_class = class_name.replace(relation_names_regex, '');
												$object = $form_wrapper.find('.' + renation_ids_class + ':not(.deleted)');
												if ($object.length && $object.attr('data-name') != undefined) {
													$object.attr('data-name', data.result[class_name]);
												}
											}

										}

									}

								}

								for (i in edited) {
									//edited[i].change();
									//console.log(edited[i]);
									edited[i].trigger('change', {calcupdate:true});
								}
								
								var wrapper = 'calcupdate';
								if (calculator.delayedCalcs[wrapper] != undefined){
									calculator.doCalcs(calculator.delayedCalcs[wrapper].form, 
													   Object.keys(calculator.delayedCalcs[wrapper].calcs),
													   Object.keys(calculator.delayedCalcs[wrapper].selectors), 
													   calculator.delayedCalcs[wrapper].relation,
													   Object.keys(calculator.delayedCalcs[wrapper].changed)
													   );
									
									
									calculator.unregisterDelayedCalc(wrapper);
									
								}
								

							} else if (data.hasOwnProperty('error')) {
								console.log('calc error');
								console.log(data.error);
							}
						},
						
					}).error(function(a, b, c) {
							$(document).trigger('calculator_finish', {data:data, wrapper:$parent_form});
							console.log('calc error');
							console.log(a);
							console.log(b);
							console.log(c);
						});
				}
	}
	
}
$(function(){
	if (!(calculator_selectors == undefined)){
		calculator.addCalculators(calculator_selectors);
		$('body').on('click', '*', function(event){
			if (this == event.target)
			for (wrapper in calculator.delayedCalcs){
				
				if ($(this).parents(wrapper).length == 0){
				
					calculator.doCalcs(calculator.delayedCalcs[wrapper].form, 
									   Object.keys(calculator.delayedCalcs[wrapper].calcs),
									   Object.keys(calculator.delayedCalcs[wrapper].selectors), 
									   calculator.delayedCalcs[wrapper].relation,
									   Object.keys(calculator.delayedCalcs[wrapper].changed)
									   );
					
					
					calculator.unregisterDelayedCalc(wrapper);
					
				}
			}
		});
	}	
});
