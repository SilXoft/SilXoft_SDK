var groupbtn_selector = '.groupbtn';
var deselectcheckbox;
var objlimit = 20;
var selected_els_count_btn = '.selected_els_count_btn';
var get_id_from_selected_data = function(el){
		//TODO: Deprecate after filters resolve
        if ($(el).attr('id')){
        	var id_arr = $(el).attr('id').split('-');
        	return id_arr[1];	
        } 
        //For filters action
        else {
        	return $(el).data('id')+''; 
        }
}
var get_data_alias_from_selected_data = function(el){
		//TODO: Deprecate after filters resolve

        	return $(el).attr('alias')+''; 
       
}

var countgroupids = function(wrapper) {
    var ids = [];
    $(selected_data_selector, wrapper).each(function() {
        ids[ids.length] = get_id_from_selected_data(this);
    });
	
    $(selected_cnt_bage_selector, wrapper).html(ids.length);
    if ($(selected_els_count_btn, wrapper).length){
        $(selected_els_count_btn, wrapper).attr('title', $(selected_els_count_btn, wrapper).data('label')+': '+ids.length);
    }
};

$(function() {

    $('body').on('DOMSubtreeModified', list_selected_models_wrapper, function() {
        countgroupids($(this).parents(table_controls_wrapper + ':first'));
    });

    // Функция подписки на событие выбора
    function grActsCountProcess() {
        var ctrl = $(this).data('controller');
        if(ctrl) {
            // Словили таблицу с контроллером - больше нам не нужно слушать это событие
            $('table.table.datatable').off('tabledraw.sl', grActsCountProcess);
            ctrl.getSelectedWrapper().on('selected-changed.sl', function(){
                //$(selected_els_count_btn, ctrl.getWrapper()).blinkShadow();
                $(selected_els_count_btn, ctrl.getWrapper()).blink({ duration: 500, items: { opacity: '0.5' } });
            });
        }
    }
    $('table.table.datatable').on('tabledraw.sl', grActsCountProcess);

    $('body').on('click', '.datatable .select_all', function() {

        var $t_wrapper = $(this).parents(table_controls_wrapper + ':first');
        var ids = [];
        var $inputs = $('.datatable tbody tr input[type="checkbox"]:not(:checked)', $t_wrapper);
        $inputs.attr('checked', 'checked').each(function() {
            var id = $(this).parents('tr:first').attr('id').split('-');
            id = id[2];
            if (id > 0) {
                ids[ids.length] = id;

            }
        });
        if (ids.length) {
            var $wrapper = $(list_selected_models_wrapper + ':first', $t_wrapper);
            getSelectedStrings(ids, $wrapper);
        }
        
        
        /*else {
         var $inputs = $('.datatable tbody tr input[type="checkbox"]:checked');
         $inputs.removeAttr('checked').change();
         }*/
    });

    $('body').on('click', '.datatable .select_allpage', function() {

        var $t_wrapper = $(this).parents(table_controls_wrapper + ':first').find(div_selected_models_wrapper + ':first');
       
        var ids =[];

        deselectcheckbox($t_wrapper);
        
        $.post(selected_items_entry_point, function(data) {
            if (data.rezult = true) {

                $.each(data.aaData, function(index, value) {

                    if (value[0] > 0) {
                        ids[ids.length] = value[0];

                    }

                });
        if (ids.length) {
            var $wrapper = $(list_selected_models_wrapper + ':first', $t_wrapper);
            getSelectedStrings(ids, $wrapper);
        }
        $('.datatable tbody tr input[type="checkbox"]:not(:checked)').attr('checked', 'checked');        
                    
        if(typeof selected_items== 'function') {
              selected_items(ids);
        }     
        
            }
        });

        /*
         var $t_wrapper = $(this).parents(table_controls_wrapper + ':first');
         var ids = [];
         var $inputs = $('.datatable tbody tr input[type="checkbox"]:not(:checked)', $t_wrapper);
         $inputs.attr('checked', 'checked').each(function() {
         var id = $(this).parents('tr:first').attr('id').split('-');
         id = id[2];
         if (id > 0) {
         ids[ids.length] = id;
         
         }
         });
         if (ids.length) {
         var $wrapper = $(list_selected_models_wrapper + ':first', $t_wrapper);
         getSelectedStrings(ids, $wrapper);
         }
         */
        /*else {
         var $inputs = $('.datatable tbody tr input[type="checkbox"]:checked');
         $inputs.removeAttr('checked').change();
         }*/
    });

    deselectcheckbox = function($t_wrapper, exept_id) {
		
		
		var $selects = $(selected_data_selector, $t_wrapper);
        if ($selects.length) {
            var ids = [];
            $selects.each(function() {
                ids[ids.length]= get_id_from_selected_data(this);
            });
            if (exept_id) {
            	
                var res_arr = $(ids).not(exept_id).get();
        
            } else {
                res_arr = ids;
            }
            //TODO: deprecate after deprecate nlist
            removeSelectedmodel(res_arr, $t_wrapper);
            var ctrl = $('table.table.datatable').data('controller');
        
            if (ctrl)
            	ctrl.removeSelectedModel(res_arr); 
            
        }

    };

    $('body').on('click', groupbtn_selector+' .deselect_all', function() {

        var $t_wrapper = $(this).parents(table_controls_wrapper + ':first').find(div_selected_models_wrapper + ':first');

        deselectcheckbox($t_wrapper);
        /*
         var $inputs = $('.datatable tbody tr input[type="checkbox"]:checked',$t_wrapper);
         $inputs.removeAttr('checked').change();
         */
    });
    
       groupprintsend = function ($selects, $table_wrapper, rel){
           if ($selects.length) {
            var ids = [];
            $selects.each(function() {
                var id_arr = $(this).attr('id').split('-');
                ids[ids.length] = id_arr[1];

            });
            var $table = $table_wrapper.find('table.dataTable:first').dataTable();
               
            if (ids.length && rel) {
				var params = $.param({id:ids});
                var url = rel+'?'+params;
                window.open(document.location.protocol + '//' + document.location.host+url);
            }
        } 
           
       };
    
	$('body').on('click', groupbtn_selector + ' .btn.print_action', function() {
        var $table_wrapper = $(this).parents(table_controls_wrapper + ':first');
        var $t_wrapper = $(this).parents(table_controls_wrapper + ':first').find(div_selected_models_wrapper + ':first');
        var $selects = $(selected_data_selector, $table_wrapper.find(div_selected_models_wrapper + ':first'));
        $form = $(this).parents(table_controls_wrapper + ':first');
        var rel = $(this).attr('rel');
        
        if ($selects.length > 20){
           $.confirm('Подтвердите, пожалуйста ...', 'Запрос содержит более '+objlimit+' объектов и может долго обрабатываться.', null, function(){
             groupprintsend ($selects, $table_wrapper, rel);
        }, null, function(){
                return false;
            });
        } else {
        groupprintsend ($selects, $table_wrapper, rel);
        }
    });
    
    $('body').on('click', groupbtn_selector + ' .action', function() {
        var $table_wrapper = $(this).parents(table_controls_wrapper + ':first');
        var autoclear = $(this).is('.autoclear');
        var $t_wrapper = $(this).parents(table_controls_wrapper + ':first').find(div_selected_models_wrapper + ':first');
        var $selects = $(selected_data_selector, $table_wrapper.find(div_selected_models_wrapper + ':first'));
        $form = $(this).parents(table_controls_wrapper + ':first');

        if ($selects.length) {
            var ids = [];
            var data_ids = [];
            $selects.each(function() {
              ids[ids.length]= get_id_from_selected_data(this);              
              data_ids [data_ids.length] = [get_data_alias_from_selected_data(this), get_id_from_selected_data(this)];
            });            
            var $table = $table_wrapper.find('table.dataTable:first').dataTable();

            if ($(this).attr('rel')) {
				
                var url = $(this).attr('rel');
                var text = $(this).attr('title')?$(this).attr('title'):$(this).text();
                $.confirm(text + '...', 'Вы уверены?', undefined, function() {
                    hideErrors($form);
                    $.ajax({
                        cache: false,
                        type: 'POST',
                        data: {id: ids, data_ids:data_ids},
                        url: url,
                        success: function(data) {
                            if (data.result) {
                                //$.alert('Запись успешно удалена!');
                                //table.fnDeleteRow($this.parents('tr:first'));
                                if (autoclear) {
                                    deselectcheckbox($t_wrapper);
                                    
                                }
                            } else {
                                if (data.errIds && data.errIds.length) {
                                    deselectcheckbox($t_wrapper, data.errIds);
                                }
                                if (data.description) {
                                    console.log('a');
                                    showErrors(data.description, $form);
                                    $('html,body').scrollTop(0);
                                }
                            }
                        }
                    }).done(function() {
                        $table.fnDraw();
                    });
                }, undefined);
            }


        }
    });
    $('body').on('click', groupbtn_selector+' .hide_selected', function() {
        var $t_wrapper = $(this).parents(table_controls_wrapper + ':first');
        var $at_wrapper = $(this).parents(table_controls_wrapper + ':first').find(div_selected_models_wrapper + ':first');
        var ids = [];
		


        $('tr:has(td > input[type=checkbox]:checked)', $t_wrapper).each(function() {
            var id = $(this).attr('data-id');
            ids[ids.length] = id;
            $(this).remove();
        });
		//TODO: deprecate after deprecate nlist		
        removeSelectedmodel(ids, $at_wrapper);
        
        
        if (ids.length)
        
            $('body').trigger('dtdraw', $t_wrapper);
    });



});
