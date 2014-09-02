var     debug,                                      // Флаг дебага
        fnSlConditionDialog,                        // Функция управления диалогом добавления сравнения
        filters_tree_selector = '#filters-tree',    // Селектор дерева фильтров
        fnSlBuildData = function(){};               // Построение информации о фильтре
        
       
var mDataRender;

$(document).ready(function(){
    // Определение дебага
    debug = ($('#debug').length > 0);
    // Событие построения условия сравнения
    $('body').on('build_condition_name.sl', function(e, data){
        switch(data.type) {
            case 'isnull':
                if(!data.value || (data.value === 'false')) {
                    data.type = 'nisnull';
                }
                data.value = '';
                break;
            case 'nisnull':
                data.value = '';
                break;
        }
    });
    
    $('#advanced').on('click', '.change_filter_btn:not(.active)', function(){
        var $this = $(this);
        $this.parents(':first').find('.change_filter_btn').removeClass('active');
        $this.addClass('active');
        
        var cur_data = fnSlBuildData(filters_tree_selector);
        
        fnSlLoadFilter(this, '', function(){
            $(filters_tree_selector).attr('data-hash', '---');
        }, function(data){
            // Ничего делать не нужно
            $.alert(data.message);
        });
    });
    
    // Определение нужно ли отправить запрос по фильтру
    // @TODO: Переделать бы. А то как-то костыльно ....
    setInterval(function(){
        if($('#advanced').length && $('#advanced').is(':visible')) {
            var selector = filters_tree_selector;
            if($(selector).length) {
                var hash = $(selector).attr('data-hash');
                var data = fnSlBuildData($(selector));
                if(!data) {
                    return;
                }
                data = hex_md5(JSON.stringify(data));
                if(hash) {
                    if(hash !== data) {
                        $(selector).attr('data-hash', data);
                        try {
                            $(selector).trigger('filters-changed.sl');
                        } catch(e) {
                            console.log(e.message);
                        }
                    }
                } else {
                    // Инициализация
                    try {
                        $(selector).attr('data-hash', data);
                    } catch(e) {
                        console.log(e.message);
                    }
                }
            }
        }
    }, 1000);
    // Указываем селекторы для построения управляющих кнопок таблицы
    $('table:first').on('sf_init.sl', function(e, d){
        // Указываем где что искать
        $.extend(d.oSSettings, {
            $LengthSelector: $(this).siblings('.custom_length'),
            $FiltersSelector: $(this).siblings('.table_filters'),
            $FieldsetsSelector: $(this).siblings('.table_fieldsets')
        });
        // На всех изменениях фильтров - перерисовываем таблицу
        $(filters_tree_selector).on('filters-changed.sl', function(){
            d.oSSettings.oInstance.fnDraw(true);
        });
    });
        
    // Инициализация таблицы
    $('table:first').dataTable({
        sDom: "R<<'left'S>>t<'row'<'span6'i><'span5'p>>",
        sPaginationType: 'bootstrap',
        bServerSide: true,
        sAjaxSource: '/itftc/application/ajaxfilters',
        aoColumns: aoColumnDefinitions,
        fnServerParams: function(aoData, return_result) {
            $(document).trigger('sf_fill_server_data.sf', {
                aoData: aoData
            });
            if(return_result === true) {
                return aoData;
            }
        },
         "fnDrawCallback": function(){
	        $('body').trigger('dtdraw', this);
	        
	    },
	    aaSorting: [[0, 'desc']], 
        bStateSave: true,
        oColReorder: {
            iFixedColumns: 1
        },
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
        	
        	for (key in aData){
        		if (aData[key].hasOwnProperty('attributesTR')){
        			for (a in aData[key].attributesTR){
        				var attr = [];
        				var a_name = a.toString().trim();
        				attr[0] = $(nRow).attr(a_name);
        				attr[1] = (aData[key].attributesTR[a] instanceof Array)?aData[key].attributesTR[a]:aData[key].attributesTR[a];
        				
        				$(nRow).attr(a_name,
        						attr.join(' ').trim());
        				
						        								
        			}
        		}
        	}
        }
  
        
    });
    // Инициализация плагина layout-а
    //TODO: відповитися від #....
    $('#advanced-layout').layout({
        'north': {
            resizable: false,
            closable: false,
            size: 50
        },
        'east': {
            closable: false,
            size: 300
        }
    });
    $('#advanced').hide();
    // Если настраиваем фильтр, то меняем кол-во записей на странице
    $(document).on('sf_fill_server_data.sf', function(e, data){
        if($('#advanced').is(':visible')) {
            data.aoData.push({
                name: 'iDisplayLength',
                value: 3
            });
            var d = fnSlBuildData(filters_tree_selector, 'current_filter');
            if(d) {
                for(var i in d) {
                    data.aoData.push({
                        name: i,
                        value: d[i]
                    });
                }
            }
        }
    });
    // Сохранение фильтра
    $('.btn_filter_save').on('click', function(){
        var send_data;
        try {
            send_data = fnSlBuildData(filters_tree_selector);
        } catch(e) {
            console.log(e);
        }
        send_data['test'] = '1';
        $.ajax({
            type: 'POST',
            cache: false,
            url: '/home/main/ajaxdescribefilter',
            data: send_data,
            success: function(data) {
                console.log(data);
            }
        });
    });
});
// аналог array_search из PHP
function array_search(needle, haystack, strict) {
    var strict = !!strict;
    for(var key in haystack){
        if( (strict && haystack[key] === needle) || (!strict && haystack[key] == needle) ){
            return key;
        }
    }
    return false;
}

$(function(){
	$('.datatable').on('dblclick','tr[data-id]',function() {
			
	        var $this = $(this);
	        var id = $this.attr('data-id');
	        var editable = $this.attr('data-editable');
	        var controller = $this.attr('data-controller').split('.').join('/');
	        console.log('/'+ controller + '/edit/id/' + id + (is_iframe ? '/is_iframe/1' : ''));
	        //return;
	        if (controller != undefined) {
	            if (editable == '1') {
	                window.location.href = '/'+ controller + '/edit/id/' + id + (is_iframe ? '/is_iframe/1' : '');
	            } else if (editable == '0') {
	                window.location.href = '/'+ controller + '/detailed/id/' + id + (is_iframe ? '/is_iframe/1' : '');
	            }
	        }
	    });
 });   