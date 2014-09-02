$.fn.dataTableExt.aoFeatures.push({
    fnInit: function(oSettings) {
        var params = [];
        // Напихали все, что смогли ....
        var oSSettings = {
            oInstance: oSettings.oInstance,
            oSettings: oSettings
        };
        try {
            $(oSettings.oInstance, document).trigger('sf_init', {
                nTableEl: oSettings.nTable,
                oSSettings: oSSettings
            });
        } catch(e) {
            console.log('Error on trigger "sf_init.sf"');
            console.log(e);
        }

        var $div = $('<div />').addClass('dt-controls');
        var clone;
        // Переопределенный выбор кол-ва
        
        if(oSSettings.hasOwnProperty('$LengthSelector') && oSSettings.$LengthSelector.length) {
            $(oSettings.oInstance).trigger('feature_add.sl', {
                sFeature: 'length',
                oExtras: {
                    oObject: $(oSSettings.$LengthSelector)
                },
                oSSettings: oSSettings
            });
            oSSettings.$LengthSelector.find('a').click(function(e){
                e.preventDefault();
                $(this).parents('ul:first').find('a').removeClass('current');
                $(this).addClass('current');
                oSettings._iDisplayLength = $(this).attr('data-value');
                oSettings.oInstance.fnDraw(true);
            });
            oSSettings.$LengthSelector.appendTo($div);
        }
        
        // Выбор фильтра/настройка
        if(oSSettings.hasOwnProperty('$FiltersSelector') && oSSettings.$FiltersSelector.length) {
            
            $(oSettings.oInstance).trigger('feature_add.sl', {
                sFeature: 'filters',
                oExtras: {
                    oObject: oSSettings.$FiltersSelector
                },
                oSSettings: oSSettings
            });
            
            oSSettings.$FiltersSelector.on('click', 'a:not(.advanced)', function(e){
                e.preventDefault();
                $(this).parents('ul:first').find('a').removeClass('current');
                $(this).addClass('current');
                params.push({
                    name: 'filter',
                    value: $(this).attr('data-value')
                });
                oSettings.oInstance.fnDraw(true);
            });
            
           oSSettings.$FiltersSelector.on('click', '.advanced', function(){
                if($('#advanced').is(':visible')) {
                    $('#advanced .btn').removeClass('active');
                    $('#advanced').hide();
                    $('#table_filters a.current').click();
                } else {
                    var active_filter = oSSettings.$FiltersSelector.find('a.current').attr('data-value');
                    $('#advanced').find('.change_filter_btn[data-value="'+active_filter+'"]').addClass('active');
                    fnSlLoadFilter($('#advanced').find('[data-value="'+active_filter+'"]'), 'filters-tree', function(){
                        $('#advanced').show();
                        oSettings.oInstance.fnDraw(true);
                    }, function(data){
                        // Ничего делать не нужно
                        $.alert(data.message);
                    });
                }
            });
            oSSettings.$FiltersSelector.appendTo($div);
        }
        // Выбор колонок
        if(oSSettings.hasOwnProperty('$FieldsetsSelector')&& oSSettings.$FieldsetsSelector.length) {
            $(oSettings.oInstance).trigger('feature_add.sl', {
                sFeature: 'fieldsets',
                oExtras: {
                    oObject: oSSettings.$FieldsetsSelector
                },
                oSSettings: oSSettings
            });
            oSSettings.$FieldsetsSelector.on('click', 'a', function(e){
                e.preventDefault();
                $(this).parents('ul:first').find('a').removeClass('current');
                $(this).addClass('current');
                setCookie('current-logistic_package-fieldset', $(this).attr('data-value'));
                document.location.href = cleanBaseUrl();
            });
            oSSettings.$FieldsetsSelector.appendTo($div);
        }
        $(document).bind('sf_fill_server_data.sf', function(e, d){
            for(var i in params) {
                d.aoData.push(params[i]);
            }
            if(typeof field_roles == 'object') {
                for(var i in field_roles) {
                    d.aoData.push({
                        name: 'field_roles['+i+']',
                        value: field_roles[i]
                    });
                }
            }
        });

        if($('#advanced').length) {
            $('#advanced').appendTo($div);
        }
        
        return $div.get(0);
    },
    cFeature: 'S',
    sFeature: 'Silenca Features :)'
});

function fnSlLoadFilter(field, wrapper, doneCallback, errorCallback) {
    var $field = $(field);
    $(document).trigger('filter_load.sl', {
        wrapper: $(wrapper),
        field: $field
    });
    $.ajax({
        type: 'POST',
        cache: false,
        url: '/home/main/ajaxdescribefilter',
        data: {
            model: model_alias,
            filter: $field.data('value')
        },
        success: function(data){ 
            if(data.result) {
                // Переименовать root дерева
                var $tree = $.jstree._reference(filters_tree_selector);
                try {
                    $tree.rename_node($('#root'), data.filter.name.toUpperCase());
                    $tree.delete_node($('li:not(#root)', $tree.get_container_ul()));
                    fnSlBuildFilter($tree, data.filter.filter, $('#root'));
                } catch(e) {
                    console.log(e.message);
                } 
                doneCallback(data.filter);
            } else {
                console.log(data.description);
                errorCallback({
                    message: data.description
                });
            }
        },
        error: function(e) {
            console.log(e);
            errorCallback({
                message: data.description
            });
        }
    });
}

function fnSlBuildFilter(wrapper, data, p) {
    var parent = p || null;
    var $tree = wrapper;
    if(data.type === 'multi') {
        var comp = 'and';
        if(data.comp === '1') {
            comp = 'or';
        }
        $tree.create(parent, 'last', {
            attr: {
                'data-type': comp
            },
            data: (comp === 'and')?'И':'ИЛИ'
        }, function(o_created){
            $tree.set_type('term', o_created);
            for(var i in data.comps) {
                fnSlBuildFilter($tree, data.comps[i], o_created);
            }
        }, true);
    } else {
        // Добавляем в дерево
        $tree.create(parent, 'last', {
            attr: {
                'data-type': data.type
            },
            data: '-'
        }, function(o_created){
            $tree.set_type('condition', o_created);
            $(o_created).data({
                name: data.field,
                type: data.type,
                field_type: data.field_type,
                label: data.label,
                value: data.value
            });
            $tree.rename_node(o_created, fnSlBuildCondition(data.label, data.type, data.value));
        }, true);
    }
}

function fnSlBuildCondition(f, t, v) {
    var _oEventDdata = {
        fieldname: f,
        type: t,
        value: v
    };
    // Может кто-то захочет сделать еще красивее
    $('body').trigger('build_condition_name.sl', _oEventDdata);

    var     fieldname = _oEventDdata.fieldname,
            type = _oEventDdata.type,
            value = _oEventDdata.value;

    if($('[data-type="comp"][data-name="'+type+'"]').length) {
        type = $('[data-type="comp"][data-name="'+type+'"]:first').data('value').toLowerCase();
    }

    if(typeof value === Array) {
        value = value.join(', ');
    }

    var $span = $('<span class="filter_condition" />');

    $('<span class="cond_field" />').text(fieldname+' ').appendTo($span);
    $('<span class="cond_type" />').text(type+' ').appendTo($span);
    $('<span class="cond_value" />').text(value+' ').appendTo($span);
    return $span.get(0);
}
$(function(){
	$('body').on('dtdraw', function(event, table) {
		
        var $calculate_fields = $('thead > tr.titles th[data-calc]', table);
        if ($calculate_fields.length) {
            var $tr = $('<tr />').attr('data-type', 'calcrow');
            $('thead > tr.titles > th', table).each(function(index) {
                var fieldName = $(this).attr('field-name');
                var $th = $('<th>').attr('field-name', fieldName);

                if ($(this).data('calc')) {
                    var calculate = $(this).data('calc');

                    var sum = 0;

                    var length = 0;
                    var i = index + 1;
                    
                    $('td:nth-child(' + i + ')', table).each(function() {
                        if ($(this).text().length) {
                            sum += parseFloat($(this).text());
                            length++;
                        }

                    });
                    switch (calculate) {
                        case 'sum':
                            $th.html(' &Sigma; ' + Math.round(sum * 100) / 100);
                            break;
                        case 'average' :
                            $th.html('&Xi; ' + (length ? Math.round(sum / length * 100) / 100 : '0'));
                            break;
                    }
                }

                $th.appendTo($tr);
            });
            $('tr[data-type="calcrow"]', table).remove();
            $tr.appendTo($(table).find('tbody'));
        }
    });
});
