$(document).ready(function(){
    /**
     * Взаимодействие с диалогом добавления условий в фильтры
     * 
     * @param {string} type Тип. init используется для инициализации. Остальное для управления
     * @param {type} data Необходимые данные
     * @param {type} doneCallback 
     * @param {type} cancelCallback
     * @returns {undefined}
     */
    fnSlConditionDialog = function(type, data, doneCb, cancelCb) {
        var old = {
            done: function() { 
                
            },
            cancel: function() {
                
            }
        };
        var doneCallback = doneCb || old.done;
        var cancelCallback = cancelCb || old.cancel;
        
        var $dialog = $('#condition_dialog');
        if(type === 'init') {
            $dialog.modal({
                show: false,
                keyboard: false,
                backdrop: 'static'
            });
            
            $($dialog).on('change', '[role="compare"]', function(){
                var selector = $(this).val();
                var type = $(this).data('type') || 'text';
                var selector_ext = selector+'-'+type;
                if($('[data-type="comp"][data-name="'+selector_ext+'"]').length) {
                    selector = selector_ext;
                }
                if($('[data-type="comp"][data-name="'+selector+'"]').length <= 0) {
                    cancelCallback();
                    return;
                }
                $dialog .find('[role="value"]')
                        .empty()
                        .append($('[data-type="comp"][data-name="'+selector+'"]')
                                    .first()
                                    .clone(false));
                $dialog.find('[role="value"] .date').datepicker(datepicker_options);
            });
            
            $dialog.on('change', '[role="value"]', function(){
                $(this).removeClass('error');
            });
        } else {
            $dialog.find('.comparison-cacnel').unbind('click');
            $('.comparison-cancel', $dialog).click(function(){
                $dialog.modal('hide');
                try {
                    cancelCallback();
                } catch(e) {
                    console.log(e.message);
                }
            });
            $dialog.off('click', '.comparison-ok');
            switch(type) {
                case 'add':
                    // Переданные данные - объект перемещения jstree
                    var field = data.o;
                    var field_data = $(field).data();
                    $dialog.find('[role="field"]').text($.jstree._reference(field).get_text(field));
                    $dialog.find('[role="field"]').data('name', $(field).data('name'));

                    var comps = $('[data-type="type"][data-value="'+field_data.type+'"]').text().split(';');
                    if((comps.length === 1) && (comps[0] === "")) {
                        cancelCallback();
                        return;
                    }
                    $dialog.find('[role="compare"] option').remove();
                    $(comps).map(function(ind, el){
                        if($('[data-type="comp"][data-name="'+el+'"]').length > 0) {
                            var t_data = $('[data-type="comp"][data-name="'+el+'"]').first().data();
                            $dialog.find('[role="compare"]').data('type', field_data.type);
                            $dialog.find('[role="compare"]').append('<option value="'+t_data.name+'">'+t_data.value+'</option>');
                        }
                    });
                    $dialog.find('[role="compare"]').change();
                    $dialog.find('[role="field_name"]').text($(field).find('a:first').text());

                    $dialog.on('click', '.comparison-ok', function(){
                        if($('[role="value"] input', $dialog).val().length <= 0) {
                            $('[role="value"]', $dialog).addClass('error');
                            return;
                        }
                        $dialog.modal('hide');
                        try {
                            var t_data = []; 
                            $dialog.find('[role="value"]').find('input, select').each(function(){
                                t_data[t_data.length] = $(this).val();
                            });
                            $(data.oc).data('name', $('[role="field"]', $dialog).data('name'));
                            $(data.oc).data('value', t_data.join(';'));
                            $(data.oc).data('type', $dialog.find('[role="compare"] :selected').val());
                            $(data.oc).data('field_type', field_data.type);
                            var t_text = fnSlBuildCondition(
                                    $dialog.find('[role="field"]').text(),
                                    $dialog.find('[role="compare"] :selected').val().toLowerCase(),
                                    t_data);
                            data.ot.rename_node(data.oc, t_text);
                            doneCallback();
                        } catch(e) {
                            console.log(e.message);
                        }
                    });
                    $dialog.modal('show');
                    break;
                case 'edit':
                    // Передвнные данные - данные о поле
                    try {
                        var field = $.jstree._reference(data)._get_node(data);
                        var field_data = field.data();
                        // Ищем название
                        var comp_name = field_data.label || $('#fields-tree [data-name="'+field_data.name+'"] a').text() || field_data.name;
                        $dialog.find('[role="field"]').text(comp_name);
                        // Формируем опции сравнения
                        var comps = $('[data-type="type"][data-value="'+field_data.field_type+'"]').text().split(';');
                        if((comps.length === 1) && (comps[0] === "")) {
                            cancelCallback();
                            return;
                        }
                        $dialog.find('[role="compare"] option').remove();
                        $(comps).map(function(ind, el){
                            if($('[data-type="comp"][data-name="'+el+'"]').length > 0) {
                                var t_data = $('[data-type="comp"][data-name="'+el+'"]').first().data();
                                $dialog.find('[role="compare"]').data('type', field_data.field_type);
                                $dialog.find('[role="compare"]').append('<option value="'+t_data.name+'">'+t_data.value+'</option>');
                            }
                        });
                        $dialog.find('[role="compare"]').change();
                        
                        var $value_field = $('.filter_condition .cond_value:first', field);
                        var value = '';
                        if($value_field.attr('data-id')) {
                            value = $value_field.attr('data-id');
                        } else {
                            value = $value_field.text();
                        }
                        
                        if(/,/.test(value)) {
                            value = $.map(value.split(','), function(el){
                                return (el+'').trim();
                            });
                            $('[role="value"] input, [role="value"] select', $dialog).each(function(ind, el){
                                $(el).val(value[ind]);
                            });
                        } else {
                            $('[role="value"] input, [role="value"] select', $dialog).val(value);
                        }
                        
                        $dialog.on('click', '.comparison-ok', function(){
                            $('[role="value"] input, [role="value"] input', $dialog).each(function(){
                                if($(this).val().length <= 0) {
                                    $('[role="value"]', $dialog).addClass('error');
                                    return;
                                }
                            });
                            
                            $dialog.modal('hide');
                            try {
                                var t_data = []; 
                                $dialog.find('[role="value"]').find('input, select').each(function(){
                                    t_data[t_data.length] = $(this).val();
                                });
                                
                                $(field).data('value', t_data.join(';'));
                                $(field).data('type', $dialog.find('[role="compare"] :selected').val());
                                
                                var t_text = fnSlBuildCondition(
                                        $dialog.find('[role="field"]').text(),
                                        $dialog.find('[role="compare"] :selected').val().toLowerCase(),
                                        t_data);
                                $.jstree._reference(field).rename_node(field, t_text);
                                doneCallback();
                            } catch(e) {
                                console.log(e.message);
                            }
                        });
                        $dialog.modal('show');
                    } catch(e) {
                        console.log(e.message);
                    }
                    break;
            }
        }
    };
    
    fnSlConditionDialog('init');
});