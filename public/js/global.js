var closeIframeFunction; // for close iframe
var table_request_data;
var table_selected_data;
var table_filter_data;
var getSelectedStrings;
var addSelectedmodel;
var enterpressed;
var assignAutocomplete;
var removeSelectedmodel;
var getFilters;
var entry_point;
var $popupDiv;
var current_relation_type;
var form_after_save_url_field = 'form_after_save_url';
var datepicker_selector = 'input.fieldtype-date';
var datetimepicker_selector = 'input.fieldtype-datetime';
var current_date_selector = 'input.current-date';
var current_datetime_selector = 'input.current-datetime';
var tommorow_date_selector = 'input.tommorow-datetime';
var datepicker_options = {
    dateFormat: 'yy-mm-dd',
    changeYear: true,
    yearRange: '-65:+1',
    beforeShow: function() {
        setTimeout(function() {
            $('.ui-datepicker').css('z-index', 3000);
        }, 0);
    }
};
var datetimepicker_options = {	
        timeFormat: 'HH:mm:ss',
        dateFormat: 'yy-mm-dd',	
	stepMinute: 15,
        beforeShow: function() {
        setTimeout(function() {
            $('.ui-datepicker').css('z-index', 3000);
        }, 0);
    }        
};

var selected_data_selector = '.selected_data';
var int_positive_selector = 'input.intPositive'; 

var float_positive_selector = 'input.floatPositive';
var int_selector = 'input.int';
var float_selector = 'input.float';
var float_fixed_digits_regex = /^fixedDigits-[\d]+$/;
var can_empty_selector = 'input.canEmpty';
var dtCustomConfig = {};
var updateHiddenColumns = function(){};

var alertErrors;
var showErrors;

var iOS = (navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false);

var tod = new Date();
var tomorrow = new Date(tod.getTime() + (24 * 60 * 60 * 1000));

var today = new Date();

var today_string = [today.getFullYear(),(today.getMonth()<9?'0':'')+ (today.getMonth()+1),(today.getDate()<10?'0':'')+today.getDate()].join('-');
var tommorow_string = [tomorrow.getFullYear(),(tomorrow.getMonth()<9?'0':'')+ (tomorrow.getMonth()+1),(tomorrow.getDate()<10?'0':'')+tomorrow.getDate()].join('-');
var todaytime_string = [(today.getHours()<9?'0':'')+ (today.getHours()),(today.getMinutes()<9?'0':'')+ (today.getMinutes()),'00'].join(':');
var list_selected_models_wrapper = '.selected_models';
var div_selected_models_wrapper = '.selected_models_wrapper';    
var table_controls_wrapper = '.table-controls-wrapper';
var selected_cnt_bage_selector = '.selected_els_count';
var selected_els_count_btn_selector = '.selected_els_count_btn'; 


/*
 "sScrollX": "100%",
 "sScrollXInner": "110%",
 "bScrollCollapse": true
 * */
var dtConfig = {
    'bAutoWidth': false,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": '/',
    "aaSorting": [[0, "desc"]],
    "sPaginationType": "full_numbers",
    "sDom": 'ltipr',
    "sServerMethod": "POST",
    "oLanguage": {
        /*    "oPaginate": {
         "sFirst"    : "<<",
         "sLast"     : ">>",
         "sPrevious" : "<",
         "sNext"     : ">" 
         }, */
        "sEmptyTable": "Нет данных",
        "sInfo": "Всего _TOTAL_ (показаны _START_ - _END_)",
        "sInfoEmpty": "Нет данных",
        "sInfoFiltered": " - отфильтровано (всего: _MAX_ записей)",
        "sSearch": "Поиск: "
    },
    "fnRowCallback": function(nRow, aData, iDisplayIndex) {
        $(nRow).attr('data-id', aData[0]);
        $(nRow).attr('id', 'data-id-' + aData[0]);
        $(nRow).attr('data-editable', aData[1]);
        $(nRow).attr('data-controller', aData[2]);
        return nRow;
    }
};




var setSelectionCallBack = function(ids, names, els) {
    var $initiator = getPopupInitiator();
    var id = $initiator.attr('id'), gr_id;
    if ($initiator.is('li[id^=node]')) {
        var reload_id;
        if (node_action !== undefined && node_action == 'create') {
            reload_id = $initiator.attr('id');
        } else {
            $parent_node = $initiator.parents('li[id^=node]:first');
            reload_id = $parent_node.attr('id');
        }

        $.jstree._reference("#" + reload_id).refresh("#" + reload_id);
        return false;
    } else if ($initiator.attr('data-isfile') == '1') {
        gr_id = id.split('_').slice(0, 2).join('_');
        $('#' + gr_id).val(ids.join(';'));
        $('#' + gr_id + '_list li').remove();
        $('#' + gr_id + '_list').parent('blockquote').hide();
        $.each(names, function(index, name) {
            $('<a href="/file/edit/id/' + ids[index] + '">').text(name).appendTo($('#' + gr_id + '_list')).wrap('<li />');
            $('#' + gr_id + '_list').parent('blockquote').show();
        });

    } else if (/^[0-9]2$/.test($initiator.attr('data-type'))) {
        if (/_create$/.test($initiator.attr('id'))) {
            $('#' + gr_id + '_list').parent('blockquote').hide();
            // Тут els это URL-ы
            gr_id = id.split('_').slice(0, 2).join('_');
            $('#' + gr_id).val(ids.join(';'));
            $.each(els, function(index, el) {
                $('#' + gr_id + '_list').parent('blockquote').show();
                var href = el.href;
                var name = el.name;
                $('<a href="' + href + '">').text(name).appendTo($('#' + gr_id + '_list')).wrap('<li />');
            });
        } else {
            $('#' + gr_id + '_list').parent('blockquote').hide();
            gr_id = id.split('_').slice(0, 2).join('_');
            $('#' + gr_id).val(ids.join(';'));
            $('#' + gr_id + '_list li').remove();
            $.each(els, function(index, el) {
                $('#' + gr_id + '_list').parent('blockquote').show();
                var href = $(el).attr('url');
                var name = $(el).text();
                $('<a href="' + href + '">').text(name).attr('data-id', ids[index]).appendTo($('#' + gr_id + '_list')).wrap('<li />');
            });
        }
    }

    return true;
};

var setSelectionCallBackInTable = function(ids, names, els, returnfields) {
    var $initiator = getPopupInitiator();
    var id = $initiator.attr('id'), gr_id;
    if ($initiator.is('li[id^=node]')) {
        var reload_id;
        if (node_action !== undefined && node_action == 'create') {
            reload_id = $initiator.attr('id');
        } else {
            $parent_node = $initiator.parents('li[id^=node]:first');
            reload_id = $parent_node.attr('id');
        }

        $.jstree._reference("#" + reload_id).refresh("#" + reload_id);
        return false;
    } else if ($initiator.attr('data-isfile') == '1') {
        gr_id = id.split('_').slice(0, 2).join('_');
        $('#' + gr_id).val(ids.join(';'));
        $('#' + gr_id + '_list li').remove();
        $('#' + gr_id + '_list').parent('blockquote').hide();
        $.each(names, function(index, name) {
            $('<a href="/file/edit/id/' + ids[index] + '">').text(name).appendTo($('#' + gr_id + '_list')).wrap('<li />');
            $('#' + gr_id + '_list').parent('blockquote').show();
        });

    } else if (/^[0-9]2$/.test($initiator.attr('data-type'))) {

        //console.log($initiator.attr('id'));
        if (/_create$/.test($initiator.attr('id'))) {
            $('#' + gr_id + '_list').parent('blockquote').hide();
            // Тут els это URL-ы
            gr_id = id.split('_').slice(0, 2).join('_');
            $('#' + gr_id).val(ids.join(';'));
            var return_fields = $initiator.attr('data-returnfields');
            $.each(els, function(index, el) {
                $('#' + gr_id + '_list').parent('blockquote').show();
                var href = el.href;
                var name = el.name;
                var extra_fields=el.extra;
                if(typeof extra_fields !='object')                {
                    var extra_fields=jQuery.parseJSON(el.extra);
                }
                $tr = $('<tr>').appendTo($('#' + gr_id + '_list'));
                $('<a href="' + href + '">').text(name).attr('data-id', ids[index]).appendTo($('#' + gr_id + '_list tbody tr:last')).wrap($('<td />').attr('data-url', href).attr('data-value', name).addClass('tostring'));
                $.each(return_fields.split(','), function(index_field, el_field) {
                    
                    if (extra_fields[el_field]) {
                        $('<span>').text(extra_fields[el_field]).appendTo($('#' + gr_id + '_list tbody tr:last')).wrap(
                                $('<td />').addClass(el_field.replace('.', '-')).attr('data-value', extra_fields[el_field])
                                );
                    }
                    else {
                        $('<td />').addClass(el_field.replace('.', '-')).appendTo($('#' + gr_id + '_list tbody tr:last'));
                        $.ajax({
                            url: '/' + el.module_name + '/' + el.model_name + '/ajaxdetailed',
                            data: {"data-extended": 'fields:' + el_field, ids: extra_fields.id},
                            async: false,
                            type: 'POST',
                        }).done(function(data) {

                            if (data.result) {
                                $('td.' + el_field.replace('.', '-') + ':last').html($('<span>')
                                        .attr('data-url', data.extra[extra_fields.id]['url'])
                                        .attr('data-value', data.extra[extra_fields.id][el_field])
                                        .html(data.extra[extra_fields.id][el_field]));
                            }
                        });
                    }
                });
            });
            if (typeof custom_refresh_relation == 'function') {
                custom_refresh_relation();
            }
        } else {

            $('#' + gr_id + '_list').parent('blockquote').hide();
            gr_id = id.split('_').slice(0, 2).join('_');
            $('#' + gr_id).val(ids.join(';'));
            $('#' + gr_id + '_list tbody tr').remove();
          var return_fields = $initiator.attr('data-returnfields');
            $.each(els, function(index, el) {
                $('#' + gr_id + '_list').parent('.blockquote').show();
                var href = $(el).attr('url');
                var name = $(el).text();
                $tr = $('<tr>').appendTo($('#' + gr_id + '_list tbody'));
                $('<a href="' + href + '">').text(name).attr('data-id', ids[index]).appendTo($('#' + gr_id + '_list tbody tr:last')).wrap('<td />');

                $.each(return_fields.split(','), function(index_field, el_field) {

                    $('<span>').text($(el).attr(el_field)).appendTo($('#' + gr_id + '_list tbody tr:last')).wrap('<td />');
                });
            });
        }

    }
    return true;
};

var popup_initiator = null;
function setPopupInitiator(initiator) {
    popup_initiator = initiator;
}
function getPopupInitiator() {
    return popup_initiator;
}

function touchHandler(event) {
    var touches = event.changedTouches,
            first = touches[0],
            type = "";

    switch(event.type)
    {
       case "touchstart": type = "mousedown"; break;
       case "touchmove":  type = "mousemove"; break;        
       case "touchend":   type = "mouseup"; break;
       default: return;
    }


    var simulatedEvent = document.createEvent("MouseEvent");
    simulatedEvent.initMouseEvent(type, true, true, window, 1,
            first.screenX, first.screenY,
            first.clientX, first.clientY, false,
            false, false, false, 0/*left*/, null);

    first.target.dispatchEvent(simulatedEvent);
    event.preventDefault();
}

function init() {
    document.addEventListener("touchstart", touchHandler, true);
    document.addEventListener("touchmove", touchHandler, true);
    document.addEventListener("touchend", touchHandler, true);
    document.addEventListener("touchcancel", touchHandler, true);
}

$(document).ready(function() {

    $(document).on('calculator_start, calculator_delay', function(event, data) {
        if (data.wrapper != undefined) {
            data.wrapper.attr('calculating', parseInt(data.wrapper.attr('calculating')) + 1);
        }
    });

    $(document).on('calculator_finish, calculator_delay_delete', function(event, data) {
    	if (data.wrapper != undefined) {
            data.wrapper.attr('calculating', parseInt(data.wrapper.attr('calculating') - 1));
        }
    });

// remove options from readonly select
    
   $('select[readonly]').each(function(){
   		$('option[value!='+$(this).val()+']',this).remove();
   });

// remove readonly checkbox click
   $('body').on('click', 'input[type=checkbox][readonly]', function(){
   		return false;
   });
      
$(document).keydown(function(event){
    var elid = $(document.activeElement);
    if (event.keyCode === 8 && !(elid.is("input") || elid.is("textarea")) ){
      return false;
  };
    
});

$(document).keypress(function(event){

	var keycode = (event.keyCode ? event.keyCode : event.which);
	if(keycode == '13'){
                //enterpressed = true;	
	}
 
});

    $('body').on('submit','form',function(){
        if (enterpressed == true){
            enterpressed = false;
            return false;
        }
    });

    	$('body').on('click', 'form .listform .formsize', function(){
		if ($(this).is('.icon-resize-small')){
			$(this).removeClass('icon-resize-small');
			$(this).addClass('icon-resize-full');
                        $(this).parents('.wellform:first').removeClass('wellform_all');
			
		} else {
			$(this).removeClass('icon-resize-full');
			$(this).addClass('icon-resize-small');
                        $(this).parents('.wellform:first').addClass('wellform_all');
			//$('#packageitemflag').css({maxHeight:1500});
		}
		
	});
    
    updateHiddenColumns = function() {
        $('#hide_columns_div input.items').each(function() {
            var hidden = getCookie($(this).attr('id'));
            if (($(this).attr('data-hidden') == '1') && (hidden != '0')) {
                hidden = '1';
            }
            hidden = (hidden == '1');
            $(this).prop('checked', hidden);
            if (table) {
                table.dataTable().fnSetColumnVis($(this).attr('data-ind'), !hidden, false);
               
                
            }
            /*
             
             if(hidden == '1') {
             $(this).prop('checked', true);
             if(table) {
             table.dataTable().fnSetColumnVis($(this).attr('data-ind'), false, false);
             }
             } else {
             $(this).prop('checked', false);
             if(table) {
             table.dataTable().fnSetColumnVis($(this).attr('data-ind'), true, false);
             }
             }*/
        }); 
    }
    var $hideColsDiv = $('#hide_columns_div');
    $hideColsDiv.dialog({
        autoOpen: false,
        modal: true,
        width: '700px'
                /*buttons: {
                 ok: {
                 text: 'Ok',
                 click: function() {
                 //$hideColsDiv.find('input.items').each(function(){
                 //    setCookie($(this).attr('id'), $(this).prop('checked')?'1':'0');
                 //});
                 //updateHiddenColumns();
                 //$hideColsDiv.dialog('close');
                 }
                 },
                 cancel: {
                 text: 'Cancel',
                 click: function() {
                 updateHiddenColumns();
                 $hideColsDiv.dialog('close');
                 }
                 }
                 }*/
    });

    $hideColsDiv.on('change', '.items', function() {
        var ind = $(this).attr('data-ind');
        var vis = $(this).prop('checked') ? false : true;

        table.dataTable().fnSetColumnVis(ind, vis, false);
        setCookie($(this).attr('id'), $(this).prop('checked') ? '1' : '0');
        $('body').trigger('dtdraw',table);
    });


    $('.hide_columns.btn').click(function() {
        var $this = $(this);
        $hideColsDiv.dialog('option', 'title', $this.attr('title'));
        $hideColsDiv.dialog('open');
    });

    $('.fileinput-button').each(function() {
        var $this = $(this);
        var $fileInput = $this.find('input[type="file"]:first');
        var id = $fileInput.attr('id');
        var gr_id = id.split('_').slice(0, 2).join('_');

        $this.fileupload({
            dataType: 'json',
            url: $(this).attr('data-rel'),
            done: function(e, data) {
                if (data.result.result) {
                    var ids = $('#' + gr_id).val().split(';');
                    //$('#'+gr_id+'_list li').remove();
                    $('#' + gr_id + '_list').parent('blockquote').hide();
                    $.each(data.result.files, function(index, file) {
                        ids[ids.length] = file.id;
                        $('<a href="/file/edit/id/' + file.id + '">').text(file.name).appendTo($('#' + gr_id + '_list')).wrap('<li />');
                        $('#' + gr_id + '_list').parent('blockquote').show();
                    });
                    $('#' + gr_id).val(ids.join(';'));
                } else {
                    alert(data.result.description);
                }
            },
            add: function(e, data) {
                data.paramName = 'location';
                data.submit();
            }
        });

    });
    // Обробники полів:

    // Дата
    $(datepicker_selector).datepicker(datepicker_options);
    $(current_date_selector + '[value=""]').val(today_string);

    $(datetimepicker_selector).datetimepicker(datetimepicker_options);
    $(current_datetime_selector + '[value=""]').val(today_string+' '+todaytime_string);
    $(tommorow_date_selector + '[value=""]').val(tommorow_string+' '+todaytime_string);
    
    $('div.date > span.add-on').live('click', function() {
        var $input = $(this).prev('input.hasDatepicker');

		if ($input.length) {$input.focus();} 

    });

    //пошук зв'язків

    assignAutocomplete('form:first');

    $('form').attr('calculating', '0');

    $('body').on('click', 'form button.save_btn', function() {
        if ($(this).parent('form').attr('calculating') != undefined && $(this).parent('form').attr('calculating') !== '0') {
            return;
        }
        $form = $(this).parents('form:first');

        if ($(this).attr('validate_action').length) {
            var $save = $(this);
            $save.attr('disabled', 'disabled');
            $('.form-top-errors', $form).remove();

            $.ajax({
                type: 'post',
                url: $(this).attr('validate_action'),
                data: $form.serialize(),
                success: function(res) {
                    if (res.result) {
                        $form.trigger('afterformvalidate.sl');
                        $form.submit();
                    } else {
                        $form.trigger('formerror.sl');
                        showErrors(res.description, $form);
                        $('html,body').scrollTop(0);
                    }
                    $save.removeAttr('disabled');
                },
                error: function(a, b, c) {
                    $form.trigger('formerror.sl');
                    $.alert('validation error');
                    $save.removeAttr('disabled');
                }
            });
        } else {
            $form.submit();
        }

        // 
    });



    // Integer positive
    $('body').on('change', int_positive_selector, function() {
        var current_val = parseInt($(this).val());
        if ($(this).is(can_empty_selector) && $(this).val().trim() == '') {
            $(this).val('')
        } else {
            var val = parseInt($(this).val());
            $(this).val((val > 0 ? val : 0));
        }
        if (current_val != parseInt($(this).val())) {/*$(this).change();*/
        }
    });

    // Float positive
    $('body').on('change', float_positive_selector, function() {
        var current_val = parseFloat($(this).val());
        if ($(this).is(can_empty_selector) && $(this).val().trim() == '') {
            $(this).val('')
        } else {
            var val = parseFloat($(this).val().replace(',', '.'));
            var fixed_digits = 2;
            var classes = $(this).attr('class').split(' ');
            if (classes.length)
                $(classes).each(function() {
                    if (this.match(float_fixed_digits_regex)) {
                        digits = this.split('-');
                        fixed_digits = digits[1];
                    }
                });
            $(this).val((val > 0 ? val : 0).toFixed(fixed_digits))
        }
        if (false && current_val != parseFloat($(this).val())) {
            $(this).change();
        }
    });

    //float coma replacing
    $('body').on('keyup', [float_positive_selector, float_selector].join(', '), function() {
        var current_val = $(this).val().replace(',', '.').replace(/[^\-\d\.]/, '').split('.');

        if (current_val.length > 1) {
            current_val = [current_val[0], current_val[1]].join('.');
        } else {
            current_val = current_val[0];
        }
        $(this).val(current_val);
    });


    // Integer 
    $('body').on('change', int_selector, function() {
        var current_val = parseInt($(this).val());
        if ($(this).is(can_empty_selector) && $(this).val().trim() == '') {
            $(this).val('')
        } else {
            var val = parseInt($(this).val());
            $(this).val(val);
        }
        if (false && current_val != parseInt($(this).val())) {
            $(this).change();
        }
    });

    // Float 
    $('body').on('change', float_selector, function() {
        var current_val = parseFloat($(this).val());
        if ($(this).is(can_empty_selector) && $(this).val().trim() == '') {
            $(this).val('')
        } else {
            var val = parseFloat($(this).val().replace(',', '.'));
            var fixed_digits = 2;
            var classes = $(this).attr('class').split(' ');
            if (classes.length)
                $(classes).each(function() {
                    if (this.match(float_fixed_digits_regex)) {
                        digits = this.split('-');
                        fixed_digits = digits[1];
                    }
                });
            $(this).val(val.toFixed(fixed_digits))
        }
        if (false && current_val != parseFloat($(this).val())) {
            $(this).change();
        }
    });





    $('body').on('focus', [int_positive_selector, float_positive_selector, int_selector, float_selector].join(', '), function() {
        var value = $(this).val();

        if (!$(this).attr('readonly') && !$(this).attr('disabled') && parseFloat(value) == 0) {
            $(this).attr('data-prev_value', value);
            $(this).val('');
        }

    });

    $('body').on('blur', [int_positive_selector, float_positive_selector, int_selector, float_selector].join(', '), function() {
        var value = $(this).val().trim();
        var prev_value = $(this).attr('data-prev_value');
        if (prev_value != undefined) {
            $(this).removeAttr('data-prev_value');
            if (value == '') {
                $(this).val(prev_value);
            }
        }


    });



    var $nav = $('#nav');
    $popupDiv = $('<div></div>').attr({
        id: 'popup_div',
        'class': 'popup'
    }).css({
        display: 'none'
    });
    var $ids_field;
    var $ids_names;
    var $ids_btn;

    $('body').append($popupDiv);

    $popupDiv.dialog({
        title: '',
        autoOpen: false,
        width: 900,
        height: 600,
        modal: true
    });

    getFilters = function($wrapper, $this) {
        var filter_fields = [];
        var i = 0;
        while ($this.attr('data-filter' + i) != undefined) {

            var filter = $this.attr('data-filter' + (i++)).split('-');
            var filter_value = [];

            filter_value[0] = filter[0]; // type
            filter_value[1] = filter[1]; // like
            filter_value[2] = filter[3]; // val:xxxx
            is_strong = filter[4]; // strong

            var values = [];
            var class_name;

            if (is_strong)
                values[values.length] = 0;

            if (filter_value[2] != undefined && (/^val:/.test(filter_value[2]))) {
                values[values.length] = filter_value[2].replace(/^val:(.+)$/g, '$1');

            } else {

                class_name = filter.splice(2, filter.length - 3).join('-');

                $('.' + class_name, $wrapper).not($this).each(function() {

                    if ($(this).val().length) {
                        values[values.length] = $(this).val();
                    }
                });
            }
            if (values.length)
                filter_fields[filter_fields.length] = [filter_value[0], filter_value[1], values.join(',')].join('-');
        }
        return filter_fields;
    }

    if ($.inArray(cur_action, ['edit', 'create']) != -1) {
        var cur_height = parseInt($('.breadcrumb').css('top')) - 3;
        $(window).scroll(function(e) {
            if ($(window).scrollTop() >= cur_height) {
                $('.breadcrumb').css('top', '5px');
            } else {
                $('.breadcrumb').css('top', (cur_height - $(window).scrollTop()) + 'px');
            }
        });
    }





    $('.click_create').live('click', function() {
        if ($(this).attr('data-url').length)
            window.location.href = $(this).attr('data-url');
    });

    $('.go-edit[data-url]').live('click', function() {
        if (is_iframe) {
            if ($(this).attr('data-url').length)
                window.open(href = $(this).attr('data-url'));
        } else {
            window.location.href = $(this).attr('data-url');
        }
    });

    $('.return_to_edit').click(function() {
        var url = $(this).attr('data-rel');

        if (is_iframe) {
            window.location.href = url + '/is_iframe/1';
        } else {
            window.location.href = url;
        }
    });

    $('.print_btn').click(function() {
        var url = $(this).attr('data-rel');

        window.open(url);

    });



    $('form').on('click', 'button.cancel_form, button.duplicate_form', function() {
        if (is_iframe != undefined && is_iframe)
        {
            window.parent.closeIframeFunction();
        } else {
            var url = $(this).attr('data-rel');
            url = url != undefined ? url : '/';
            window.location.href = url;
        }
    });



    $(document).on('click', 'button.delete_btn', function() {
        var $this = $(this);
        var url = $this.attr('data-rel');

        $this.attr('data-inprogress', '1');
        $.confirm('Удаление записи', 'Вы уверены, что хотите удалить запись?', undefined, function() {
            url = url != undefined ? url : '/';
            window.location.href = url;

        });
    });

	$('body').on('click','.clear_modulerelation',function(){
		 var ids_field_name = $(this).attr('id').replace(/_clear$/, '');
		 $('#' + ids_field_name).val('').attr('data-name','').change();
         $('#' + ids_field_name + '_names').val('');
	});
	
    $('.set_modulerelation').live('click', function() {
        var $this = $(this);
        var url = $(this).attr('data-rel');
        var type = $(this).attr('data-type');
        var handling = $(this).attr('data-handling');
        var returnfields = $(this).attr('data-returnfields');
        var ids_field_name = $(this).attr('id').replace(/_btn$/, '');
        var is_file = $this.attr('data-isfile') == '1';
        if (is_file) {
            ids_field_name = $this.attr('id').split('_').splice(0, 2).join('_');
        }
        $ids_field = $('#' + ids_field_name);
        $ids_btn = $('#' + ids_field_name + '_btn');
        $ids_names = $('#' + ids_field_name + '_names');
        if (is_file) {
            $ids_names = [];
        }
        var selected_ids = $ids_field.val().split(';');
        //
        var request_fields = $(this).attr('data-request_fields') != undefined && $(this).attr('data-request_fields').length ? $(this).attr('data-request_fields').split(' ') : [];

        //створення фільтрів

        var $form_wrapper = $(this).parents('form:first');
        var filter_fields = getFilters($form_wrapper, $(this));

        var loadData = function() {
            $.post(url, {
                selected: selected_ids,
                request_fields: request_fields,
                type: type,
                filter_fields: filter_fields,
                handling: handling,
                returnfields: returnfields,
            }, function(data) {
                if($(data).is('[data-alias]')) {
                    $popupDiv.html(data);

                    var ctrl = new listviewController($popupDiv.find('[data-alias]:first'), {
                        useFolders: false,
                        fnRedrawTable: function() {
                            loadData();
                        },
                        setupButton: true,
                        fnSetupCallback: function() {
                            var ids = ctrl.getSelected();
                            var names = [], els = [];
                            _.each($('[data-id]', ctrl.getSelectedWrapper()), function(el){
                                els[els.length] = $(el);
                                $(el).children().remove();
                                names[names.length] = $(el).text().trim();
                            });
                           console.log('loadData'); 
                            if(returnfields) {
                                if(setSelectionCallBackInTable(ids, names, els, ctrl.getReturnFields(true))) {
                                    if($ids_btn.attr('data-type') % 10 == 1) {
                                        ids = [ids[0]];
                                        names = [names[0]];
                                        els = [els[0]];
                                    }
                                    $ids_field.val(ids.join(';'));
                                    $ids_field.change();
                                    if($ids_names.length) {
                                        if($ids_names.attr('type') == 'text') {
                                            $ids_names.val(names.join('; '));
                                        } else {
                                            $ids_names.html(names.join('; '));
                                        }
                                    }
                                }

                            } else {
                                if(setSelectionCallBack(ids, names, els)) {
                                    if($ids_btn.attr('data-type') % 10 == 1) {
                                        ids = [ids[0]];
                                        names = [names[0]];
                                        els = [els[0]];
                                    }
                                    $ids_field.val(ids.join(';'));
                                    $ids_field.change();
                                    if($ids_names.length) {
                                        if($ids_names.attr('type') == 'text') {
                                            $ids_names.val(names.join('; '));
                                        } else {
                                            $ids_names.html(names.join('; '));
                                        }
                                    }
                                }
                            }
                            $popupDiv.dialog('close');
                        }
                    });

                    setPopupInitiator($this);
                    $popupDiv.dialog('open');

                    $popupDiv.trigger('popup_open.sl', {d: $popupDiv, ob:$this});
                    return;
                } else {
                    $popupDiv.html(data);
                    if (dtCustomConfig) {
                        $.extend(dtConfig, dtCustomConfig);
                    }
                    var $table = $popupDiv.find('table.datatable').dataTable(dtConfig);
                    if ($table) {
                        use_prev_data = false;
                        $table.fnDraw();
                    }

                    $('thead .dt_search_input', $table).keyup(function(e) {
                        var $this = $(this);
                        $table.fnFilter($this.val(), $this.attr('data-ind'));

                    });

                    setPopupInitiator($this);
                    $popupDiv.dialog('open');

                    $popupDiv.trigger('popup_open.sl', {d: $popupDiv, ob:$this});
                }
            });
        };
        loadData();
    });
    
    $('#popup_div').on('popup_open.sl', function(e, a){
       
        if(typeof popup_open== 'function') {
              popup_open(a);
        }       
        
    });

    $('thead .dt_search_input').live('click', function(e) {
        return false;
    });

    $('#popup_div .set_selections').live('click', function() {
        var ids = [];
        var names = [];
        var els = [];
		$(selected_data_selector, $popupDiv).each(function() {

            var id = $(this).attr('id').split('-');
            ids[ids.length] = id[1];
            $(this).children().remove();
            
            names[names.length] = $(this).text();
            els[els.length] = $(this);
        });
console.log('#popup_div .set_selections'); 
        if (returnfields)
        {
            if (setSelectionCallBackInTable(ids, names, els)) {
                if ($ids_btn.attr('data-type') % 10 == 1) {
                    ids = [ids[0]];
                    names = [names[0]];
                    els = [els[0]];

                }
                $ids_field.val(ids.join(';'));
                $ids_field.change();
                if ($ids_names.length) {
                    if ($ids_names.attr('type') == 'text') {
                        $ids_names.val(names.join('; '));
                    } else {
                        $ids_names.html(names.join('; '));
                    }
                }
            }

        }
        else {
            if (setSelectionCallBack(ids, names, els)) {
                if ($ids_btn.attr('data-type') % 10 == 1) {
                    ids = [ids[0]];
                    names = [names[0]];
                    els = [els[0]];

                }
                $ids_field.val(ids.join(';'));
                $ids_field.change();
                if ($ids_names.length) {
                    if ($ids_names.attr('type') == 'text') {
                        $ids_names.val(names.join('; '));
                    } else {
                        $ids_names.html(names.join('; '));
                    }
                }
            }
        }
        $popupDiv.dialog('close');
    });

    $('.ajax_create_modulerelation, .ajax_edit_modulerelation').live('click', function() {
        var $this = $(this);
        var url = $(this).attr('data-rel');
        var iframe_edit = $(this).attr('data-iframe');
        var returnfields = $(this).attr('data-returnfields');
        var parent_relation = $(this).parents('body:first').attr('data-alias'); 
        var parent_relation_id = $(this).parents('body:first').find('#id').val();

        current_relation_type = $(this).attr('data-type');
        var ids_field_name = $(this).attr('id').replace(/_create$/, '').replace(/_edit$/, '');
        $ids_field = $('#' + ids_field_name);
        $ids_names = $('#' + ids_field_name + '_names');
        if ($ids_field.length) {
            var related_model_id = $ids_field.val();
        }
        
        if (iframe_edit > 0){
	        var request_obj = {};
            request_obj['parent_relation'] = parent_relation;
            request_obj['parent_relation_id'] = parent_relation_id;
	        request_obj['exclude_relation'] = $this.data('relation');
	        if ($(this).is('.ajax_edit_modulerelation')) {
	        	request_obj['id'] = $ids_field.val();
	        	if (!request_obj['id']) return false;
	        }
	        if ($(this).is('.ajax_create_modulerelation') && 
	        	$(this).data('type') == '11' && 
	        	$ids_field.val() > 0) {
	        	return false;
	        }
                setPopupInitiator($this);
	        var $iframeDiv = $('<div />').iframePopup({
	        		url:document.location.protocol + '//' + document.location.host + url,
	        		data:request_obj,
	        		
	        	});
	       


            closeIframeFunction = function(iframe_id, iframe_string, iframe_url, iframe_data, module_name, model_name) {

                if (iframe_id != undefined && iframe_id > 0) {
                    if (/^[0-9]2$/.test($this.attr('data-type'))) {
                        var els = [];
                        els[els.length] = {
                            name: iframe_string,
                            href: iframe_url,
                            id: iframe_id,
                            extra: iframe_data,
                            module_name: module_name,
                            model_name: model_name,
                        };
                        var ids = [];
                        if (related_model_id != undefined) {
                            ids = related_model_id.split(';');
                        }
                        ids[ids.length] = iframe_id;

                        if (returnfields)
                        {
                            setSelectionCallBackInTable(ids, [iframe_string], els, returnfields)
                        }
                        else
                        {

                            setSelectionCallBack(ids, [iframe_string], els);
                        }
                    } else {

                        $ids_field.val(iframe_id).change();


                        if (iframe_string != undefined && iframe_string != '') {
                            $ids_names.val(iframe_string);
                        }
                    }

                }
                $iframeDiv.dialog('close');
                $iframeDiv.remove();
            }

            ////////////////////////////
        } else
        {
            $.post(url, {
                type: current_relation_type,
                id: related_model_id,
                exclude_relation: ids_field_name,
                returnfields: returnfields,
            }, function(data) {

                $popupDiv.dialog('option', 'title', data.title);
                $popupDiv.html(data.form);
                if (returnfields)
                    $popupDiv.find('#Save_ajax').before($('<input>').attr('name', 'returnfields').attr('type', 'hidden').attr('value', returnfields));

                $popupDiv.find('input[type="file"]').fileupload({
                    dataType: 'json',
                    done: function(e, data) {

                    }
                });

                if (data.hasOwnProperty('calc_script'))
                    eval(data.calc_script);

                setPopupInitiator($this);
                $popupDiv.dialog('open');
                $(datepicker_selector, $popupDiv).datepicker(datepicker_options);
                $(datetimepicker_selector, $popupDiv).datetimepicker(datetimepicker_options);
                $('form', $popupDiv).attr('calculating', '0');
                $(current_date_selector + '[value=""]', $popupDiv).val(today_string);
                $(current_datetime_selector + '[value=""]', $popupDiv).val(today_string + ' ' + todaytime_string);
                $(tommorow_date_selector + '[value=""]', $popupDiv).val(tommorow_string + ' ' + todaytime_string);

            });

        }
    });
/*

	        closeIframeFunction = function(iframe_id,iframe_string, iframe_url, iframe_data, module_name, model_name) {
	        	
	        	if (iframe_id != undefined && iframe_id > 0){
	        		if (/^[0-9]2$/.test($this.attr('data-type'))){
	        		   var els =[];
		        	   els[els.length] = {
		                        name: iframe_string,
		                        href: iframe_url,
		                        id: iframe_id,
		                        extra: null
		                    };
		                var ids = []; 
		                if (related_model_id != undefined){
		                	ids = related_model_id.split(';'); 
		                }   
		                 ids[ids.length] = iframe_id; 
                                 
		                 setSelectionCallBack(ids,[iframe_string],els);
	        		} else {
	        			
			            $ids_field.val(iframe_id).change(); 
			            
			            
			            if (iframe_string != undefined && iframe_string != ''){
			            	$ids_names.val(iframe_string); 
			            }
	        		}
	        	}
	            $iframeDiv.dialog('close');
	            $iframeDiv.remove();
	        }
	        	
        	////////////////////////////
        } else 
		{
        $.post(url, {
            type: current_relation_type,
            id: related_model_id,
            exclude_relation: ids_field_name,
            returnfields: returnfields,
        }, function(data) {

            $popupDiv.dialog('option', 'title', data.title);
            $popupDiv.html(data.form);
            if (returnfields)
                $popupDiv.find('#Save_ajax').before($('<input>').attr('name', 'returnfields').attr('type', 'hidden').attr('value', returnfields));

            $popupDiv.find('input[type="file"]').fileupload({
                dataType: 'json',
                done: function(e, data) {
                    
                }
            });

            if (data.hasOwnProperty('calc_script'))
                eval(data.calc_script);

            setPopupInitiator($this);
            $popupDiv.dialog('open');
            $(datepicker_selector, $popupDiv).datepicker(datepicker_options);
            $(datetimepicker_selector, $popupDiv).datetimepicker(datetimepicker_options);
            $('form', $popupDiv).attr('calculating', '0');
            $(current_date_selector + '[value=""]', $popupDiv).val(today_string);
            $(current_datetime_selector + '[value=""]', $popupDiv).val(today_string+' '+todaytime_string);
            $(tommorow_date_selector + '[value=""]', $popupDiv).val(tommorow_string+' '+todaytime_string);

        });
        
        }
    });
*/
    $popupDiv.on('click', 'table.datatable tr span', function(event) {

        if ($(this).parents('tr:first').find('input[type="checkbox"],  input[type="radio"]').length) {
            $input = $(this).parents('tr:first').find('input[type="checkbox"],  input[type="radio"]');
            if ($input.is(':checked')) {
                $input.removeAttr('checked');
            } else {
                $input.attr('checked', 'checked');
            }
            $input.change();
        }
    });

    $popupDiv.on('submit', 'form.ajaxcreate', function(event) {

        var returnfields = $('input[name="returnfields"]').val();

        var extended_arr = ['url'];
        if (returnfields != undefined && returnfields.length > 0)
        {
            extended_arr[extended_arr.length] = 'fields:' + returnfields;
        }


        var $form = $(this);
        var $target = $($form.attr('data-target'));
        $form.append('<input type="hidden" name="data-extended" value="' + extended_arr.join(';') + '" />');
        $('#Save_ajax').hide();
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            success: function(res) {

                if (res.result) {
                    var relation_type = parseInt(current_relation_type) % 10;
                    
                    if (relation_type == 2) {
                        var ids = $ids_field.val().split(';');
                        ids[ids.length] = res.result.id;
                        var names = '';
                        var els = [];
                        var gr_id = getPopupInitiator().attr('id').split('_').slice(0, 2).join('_');
                        
                        els[els.length] = {
                            name: res.result.string,
                            href: res.extra['url'],
                            id: res.result.id,
                            extra: res.extra
                        };
                        if ($ids_names.length) {
                            names = $ids_names.val().split('; ');
                        } else {
                            // Смотрим есть ли list

                        }
                        names[names.length] = res.result.string;
                        //var urls = 
                    } else {

                        var ids = [res.result.id];
                        var names = [res.result.string];
                    }

                    if (returnfields) {

                        if (setSelectionCallBackInTable(ids, names, els)) {

                            $ids_field.val(ids.join(';')).change();
                            if ($ids_names.length) {
                                if ($ids_names.attr('type') == 'text') {
                                    $ids_names.val(names.join('; '));
                                } else {
                                    $ids_names.html(names.join('; '));
                                }
                            }
                        }
                    }

                    else {

                        if (setSelectionCallBack(ids, names, els)) {

                            $ids_field.val(ids.join(';')).change();
                            if ($ids_names.length) {
                                if ($ids_names.attr('type') == 'text') {
                                    $ids_names.val(names.join('; '));
                                } else {
                                    $ids_names.html(names.join('; '));
                                }
                            }
                        }
                    }
                    $popupDiv.dialog('close');
                } else {
                    $('#Save_ajax').show();
                    alertErrors(res.description);
                    /*
                     if (res.description instanceof Object){
                     var strs = [];
                     for (var key in res.description){
                     $form.find('[name="'+key+'"]').addClass('error');
                     if (res.description[key] instanceof Object){
                     var substrs = [];
                     for (subkey in res.description[key]){
                     
                     if (res.description[key][subkey] instanceof Object){
                     var subsubstrs = [];
                     for (subsubkey in res.description[key][subkey]){
                     subsubstrs[subsubstrs.length] = res.description[key][subkey][subsubkey];
                     }
                     substrs[substrs.length] = subsubstrs.join('\r\n');
                     } else {
                     substrs[substrs.length] = res.description[key][subkey];
                     }
                     
                     }
                     strs[strs.length] = key+':\r\n'+substrs.join('\r\n');
                     } else {
                     strs[strs.length] = key+':1 '+res.description[key];
                     }
                     
                     }
                     alert(strs.join('\r\n'));
                     } else {
                     alert(res.description);
                     }
                     */
                }
            }
        });
        event.preventDefault();
        return false;
    });

    $('.item label.del_button').live('click', function() {
        var target = $(this).attr('for');
        if (target.length && $('#' + target).length) {
            $('#' + target).val(1);
            $(this).parents('.item:first').hide('slow').find('input,select,textarea').addClass('deleted').change();
            if (!/-[0-9]+-delete$/.test($(this).attr('for'))) {
                $(this).parents('.item:first').remove();
            }
        }
    });

    $('body').on('change', '.item.new_item input[type!=checkbox], .item.new_item select, .item.new_item textarea', function() {
        var $div_item = $(this).parents('.item:first');
        $div_item.removeClass('new_item');

        var $label = $div_item.find('label.del_button');
        if ($label.is(':hidden')) {


            var $new_div = $div_item.clone(false);

            var name_regex = /\[new(_?[\d]*)\]/;
            var id_regex = /-new(_?[\d]*)-/;
            var class_regex = [/hasDatepicker/, /ui-autocomplete-input/, /ui-autocomplete-loading/];
            //var class_rege
            var current_timestamp = new Date().getTime();
            var time_name = '[new_' + current_timestamp + ']';
            var time_id = '-new_' + current_timestamp + '-';
            var attr;

            $('input,select,textarea,label, button', $new_div).each(function() {

                attr = $(this).attr('name');
                if (attr != undefined && attr.length) {
                    $(this).attr('name', attr.replace(name_regex, time_name));
                }
                attr = $(this).attr('id');
                if (attr != undefined && attr.length) {
                    $(this).attr('id', attr.replace(id_regex, time_id));
                }
                attr = $(this).attr('for');

                if (attr != undefined && attr.length) {
                    $(this).attr('for', attr.replace(id_regex, time_id));
                }

                attr = $(this).attr('data-name');

                if (attr != undefined && attr.length) {
                    $(this).removeAttr('data-name');
                }

                attr = $(this).attr('class');

                if (attr != undefined && attr.length) {
                    for (i in class_regex) {
                        attr = attr.replace(class_regex[i], '');
                    }
                    $(this).attr('class', attr);
                }
            });
            $new_div.find('input:not([type="checkbox"]), textarea').val('');

            $('input,select,textarea', $new_div).each(function() {
                var attr = $(this).attr('data-default');
                if (attr != undefined && attr.length) {
                    $(this).val(attr);
                }
            });

            $('.autocomplete', $new_div).autocomplete("destroy");


            $new_div.appendTo($div_item.parents('.form_list:first'));
            
            assignAutocomplete($new_div);
            
            $div_item.parents('.form_list:first').trigger('item_cloned.sl');

            $(datepicker_selector, $new_div).datepicker(datepicker_options);
            $(datetimepicker_selector, $new_div).datetimepicker(datetimepicker_options);
            $(current_date_selector + '[value=""]', $new_div).val(today_string);
            $(current_datetime_selector + '[value=""]', $new_div).val(today_string+' '+todaytime_string);
            $(tommorow_date_selector + '[value=""]', $new_div).val(tommorow_string+' '+todaytime_string);
            
            calculator.addNewRelationCalcs($new_div);
            $new_div.addClass('new_item');
            $new_div.removeClass('warning');
            $new_div.removeClass('error');

            $(document).trigger('cloned.sl');

            $label.show('slow');
        }
    });

	addSelectedmodel = function (id,string,$wrapper,extra_options){
        var extras = extra_options || {};
        $('<div>').addClass('selected_data label label-group-select').attr('id','selected_model-'+id).attr(extras).html(string).prepend($('<span/>').addClass('icon-remove_ icon-white pull-right').html('x')).appendTo($wrapper);
        if (!(table_selected_data instanceof Object)) {
            table_selected_data = {}
        }
        if (!table_selected_data.hasOwnProperty(id)) {
            table_selected_data[id] = id;
        }
    }

	getSelectedStrings = function (ids, $wrapper, close_popup){
		
		var default_string = '<i class="icon-loading"></i>';
		$.each(ids, function(i) {
                   		
                        addSelectedmodel(ids[i],default_string, $wrapper);
                                                  
        });
		
        $.post(entry_point, {ids:ids, extended: 'url'}, function(data){
			
            if (data.result) {
                var extras;
                $.each(data.objects, function(id, string) {
                   
                        $('#selected_model-'+id+' i.icon-loading', $wrapper).replaceWith(string);                                    
                                    
                });
                if (close_popup) {
                    $('#popup_div .set_selections').click();
                    
                }

            } else {
                alert(data.description);
            }
        });


    }

	removeSelectedmodel = function (id, $wrapper){

        if (!(table_selected_data instanceof Object)) {
            table_selected_data = {}
        }
		
		if (id instanceof Array){
			$.each(id, function(){
				$('#selected_model-'+this).remove();
				if ($('#data-id-'+this).length) 
						$('#data-id-'+this).find('input[type="checkbox"], input[type="radio"]').removeAttr('checked');
				if (table_selected_data.hasOwnProperty(this)){
					delete table_selected_data[this];
				}
			});
		} else {
			$('#selected_model-'+id).remove();
			if ($('#data-id-'+id).length) 
				$('#data-id-'+id).find('input[type="checkbox"], input[type="radio"]').removeAttr('checked');
        if (table_selected_data.hasOwnProperty(id)) {
            delete table_selected_data[id];
        }
		}
		
		
		if (!$(selected_data_selector,$wrapper).length) {
			//$wrapper.removeClass('had-selected');
		} 
   }

	$(selected_data_selector).live('click',function(){
            
        var id = $(this).attr('id').split('-');
        id = id[1];

        if (id > 0) {
			
			removeSelectedmodel(id, $(this).parents(div_selected_models_wrapper+':first'));
        }
    });



    $('body').on('change', '.datatable tbody input[type="checkbox"]:not(.select_all), .datatable tbody input[type="radio"]:not(.select_all)', function() {
        
        var id = $(this).parents('tr:first').data('real-id');
        var close_popup = false;
        
        //id = id[2];
        
        if (id > 0) {
            if ($(this).is(':checked')) {

                if ($(this).is('input[type="radio"]')) {
                    for (old_id in table_selected_data) {
                        
                        removeSelectedmodel(old_id,$(this).parents(table_controls_wrapper+':first').find(div_selected_models_wrapper));
                    }
                    close_popup = true;
                }
						$wrapper = $(this).parents(table_controls_wrapper+':first').find(list_selected_models_wrapper+':first');	
						getSelectedStrings([id],$wrapper,close_popup);	
                

            } else {

							removeSelectedmodel(id, $(this).parents(table_controls_wrapper+':first').find(div_selected_models_wrapper));				

            }
        }

    });

    /**
     * Красивый input[type="file"]
     */
    $('.btn-file').each(function() {
        var self = this;
        $('input[type=file]', this).change(function() {
            // remove existing file info
            $(self).next().remove();
            // get value
            var value = $(this).val();
            // get file name
            var fileName = value.substring(value.lastIndexOf('/') + 1);
            // get file extension
            var fileExt = fileName.split('.').pop().toLowerCase();
            // append file info
            $('<span><i class="icon-file icon-' + fileExt + '"></i> ' + fileName + '</span>').insertAfter(self);
        });
    });

    $(document).on('click', '.model-delete', function() {
        var $this = $(this);
        var url = $this.attr('data-url');
        $this.parents('tr:first').hide(500);
        $this.attr('data-inprogress', '1');
        $.confirm('Удаление записи', 'Вы уверены, что хотите удалить запись?', undefined, function() {
            $.ajax({
                cache: false,
                type: 'POST',
                url: url,
                success: function(data) {
                    if (data.result) {
                        //$.alert('Запись успешно удалена!');
                        //table.fnDeleteRow($this.parents('tr:first'));
                    } else {
                        $this.parents('tr:first').show(500);
                        $.alert(data.description);
                    }
                }
            });
        }, undefined, function() {
            $this.parents('tr:first').show(500);
        });
    });

    $('table.datatable').on('click', '.model-archive', function() {
        
        addToArchived($(this));
    });

    $(document).on('calculator_start', function(ev, extra) {
        for (var i in extra.data) {
            //elBorderBlink($('#'+extra.data[i].name), 'green', 1000);
        }
    });

    $(document).on('calculator_finish', function(ev, data) {
        //console.log(data);
    });

    // Если установлен аттрибут "по-умолчанию", то применяем значение
    $('[data-default]').each(function() {
        if ($(this).val() == '') {
            $(this).val($(this).attr('data-default'));
        }
    });

   
    //autosize textareas
    $('textarea.autosize').autosize({append: '\n'});
    $('body').on('change', 'textarea.autosize', function() {
        $(this).trigger('autosize.resize');
    });

});

assignAutocomplete = function(wrapper) {

    $('.autocomplete', wrapper).each(function() {
        var $this = $(this);
        var $form_wrapper = $this.parents('form:first');
        
        $this.live('change blur', function() {
            var ids_field_name = $this.attr('id').replace(/_names$/, '');
            $('#' + ids_field_name);
            if ($this.val().length && $this.val() != $('#' + ids_field_name).attr('data-name')) {
                $this.val($('#' + ids_field_name).attr('data-name'));
            }else if(!$this.val().length){
            	$('#' + ids_field_name).attr('data-name','').val('').change();
            	
            }
        });

        $this.autocomplete({
            open: function() {
                $(this).autocomplete('widget').css('z-index', 10000);
                return false;
            },
            minLength: 0,
            source: function(request, responce) {
                var url = $this.attr('data-rel');
                var filter_fields = getFilters($form_wrapper, $this);


                $.ajax({
                    method: 'POST',
                    url: url,
                    data: {
                        name: request.term,
                        filter_fields: filter_fields,
                        quick_search: 1,
                        handling: $this.attr('data-handling')
                    },
                    success: function(data) {
                        $this.removeClass('ui-autocomplete-loading');
                        if (data.result) {
                            var name_index = 3;
                            var value_index = 0;
                            if(data.hasOwnProperty('name_index') && data.name_index) {
                                name_index = data.name_index;
                            }
                            if(data.hasOwnProperty('value_index') && data.value_index) {
                                value_index = data.value_index;
                            }
                            if (data.hasOwnProperty('sort_array')) {
                                for (key in data.sort_array) {
                                    if (data.sort_array[key] == 'name') {
                                        name_index = key;
                                        break;
                                    }
                                }
                            }
                            if (data.aaData.length) {
                                responce($.map(data.aaData, function(item) {
                                    var label;
                                    try {
                                        if($(item[name_index]).length) {
                                            label = $(item[name_index]).text();
                                        } else {
                                            label = item[name_index];
                                        }
                                    } catch(e) {
                                        label = item[name_index];
                                    }
                                    return {
                                        value: item[value_index],
                                        label: label
                                    };
                                }));
                            } else {
                                //responce([{value: 0, label: ''}]);
                            }
                        } else {
                            
                            alert(data.description);
                        }
                    }
                })
            },
            select: function(e, ui) {
                var ids_field_name = $this.attr('id').replace(/_names$/, '');
                $ids_field = $('#' + ids_field_name);
                $this.val(ui.item.label);
                $ids_field.val(ui.item.value);
                $ids_field.attr('data-name', ui.item.label);
                $ids_field.trigger('change');
                return false;
            },
            focus: function(e, ui) {
                $this.val(ui.item.label);
                return false;
            }
        });

    });
}

/**
 * Создает confirm-диалог
* 
* @param string|object title Заголовок или обект настроек
* @param string message Сообщение
* @param string yesText Название кнопки подтверждения
* @param function yesCallback Callback при подтверждении
* @param string noText Заголовок
* @param function noCallback Заголовок
 */
$.confirm = function(title, message, yesText, yesCallback, noText, noCallback) {
    /**
     * Для обратной совместимости. Должен быть объект
     */
    var map = (typeof title == 'object')?title:null,
            cTitle = (map && map.title) || title || 'Title',
            cMessage = message || (map && map.message) || '',
            yText = yesText || (map && map.yesText) || 'Ok',
            nText = noText || (map && map.noText) || 'Cancel',
            yCallback = yesCallback || (map && map.yesCallback) || function() {},
            nCallback = noCallback || (map && map.noCallback) || function() {};

    $.dialog({
        title: cTitle,
        body: cMessage,
        buttons: {
            yes: {
                name: yText,
                click: yCallback
            },
            no: {
                name: nText,
                click: nCallback
            }
        }
    });
};

/**
 * Создает диалог оповещения
 * 
 * @param {string} message Сообщение
 * @param {function|string} callback Callback при подтверждении
 */
$.alert = function(message, callback) {
    var cb = callback || function(){ };
    $.dialog({
        title: 'Сообщение',
        body: message,
        buttons: {
            name: {
                name: 'Ok',
                click: cb
            }
        }
    });
};

$.extend({
    message: function(o, wrapper) {
        var options = $.extend({
            type: 'info',
            message: 'No message',
            clean: true
        }, o, {
            wrapper: wrapper
        });
        if(options.clean) {
            $(options.wrapper).find('.alert.message').remove();
        }
        $(options.wrapper).prepend($('#alertMessage').tmpl({
            message: options.message,
            type: options.type
        }));
    },
    dialog: function(o) {
        var options = $.extend({
            title: 'Dialog title',
            body: 'Dialog body',
            canClose: true,
            buttons: [],
            headControls: false,
            before: function() {},
            after: function() {},
            fn: {
                hide: function(){
                    $div.modal('hide');
                },
                error: function(message, opts){
                    $.message($.extend({
                        wrapper: $div.find('.modal-body:first'),
                        message: message,
                        type: 'error',
                        clean: true
                    }, (opts || {})));
                }
            }
        }, o);
        if(options.before && (typeof options.before === 'function')) {
            options.before(options);
        }
        var $div = $('#alertModal').tmpl({
                        header: options.title,
                        body: options.body,
                        canClose: options.canClose,
                        buttons: options.buttons,
                        headControls: options.headControls
                    }).modal({
                        keyboard: false,
                        backdrop: 'static',
                        hidden: function(){
                            $(this).remove();
                        }
                    });
        
        $.each(options.buttons, function(i, el){
            if(el && el.click) {
                if(typeof el.click == 'function') {
                    $('[data-name="'+i+'"]', $div).click(function(){
                        if(el.click($div, options) !== false) {
                            options.fn.hide();
                        }
                    });
                } else {
                    switch(el.click) {
                        case 'hide':
                            $('[data-name="'+i+'"]', $div).click(options.fn.hide);
                            break;
                    }
                }
            }
        });
        
        if(options.after && (typeof options.after === 'function')) {
            options.after($div, options);
        }
        
        $('.modal-header .close', $div).click(options.fn.hide);
    },
    setLoading: function($wrapper, load) {
        if(load == undefined) load = true;
        var l_class = 'ui-autocomplete-loading';
        if($wrapper.is('input')) {
            load?$wrapper.addClass(l_class):$wrapper.removeClass(l_class);
        } else {
            console.log('Not implemented .... Just do it !!!');
        }
    },
});

function addToArchived ($td)
{
        var $this = $td;
        var url = $this.attr('data-url');
        
        $this.parents('tr:first').hide(500);        
        $this.attr('data-inprogress', '1');
        var title = '', message = '';
        if ($this.attr('data-archived') == '1') {
            title = 'Разархивирование записи';
            message = 'Вы уверены, что хотите извлечь запись из архива?';
        } else {
            title = 'Архивирование записи';
            message = 'Вы уверены, что хотите отправить запись в архив?';
        }
        $.confirm(title, message, undefined, function() {
            $.ajax({
                cache: false,
                type: 'POST',
                url: url,
                success: function(data) {
                    if (data.result) {
                       // $.alert('Запись успешно удалена!');
                        table.fnDeleteRow($this.parents('tr:first'));
                    } else {
                        $this.parents('tr:first').show(500);
                        $.alert(data.description);
                    }
                }
            });
        }, undefined, function() {
            $this.parents('tr:first').show(500);
        });
}

function elBorderBlink(el, color, d) {
    var duration = d || 300;
    var cur_color = $(el).css('border-color');
    var cur_width = $(el).css('border-width');
    $(el).animate({borderColor: color, borderWidth: '2px'}, duration, function() {
        $(el).animate({borderColor: cur_color, borderWidth: cur_width}, duration);
    });
}

function setCookie(name, value, expires, path, domain, secure) {
    document.cookie = name + "=" + escape(value) +
            ((expires) ? "; expires=" + expires : "") +
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            ((secure) ? "; secure" : "");
}

function getCookie(name) {
    var cookie = " " + document.cookie;
    var search = " " + name + "=";
    var setStr = null;
    var offset = 0;
    var end = 0;
    if (cookie.length > 0) {
        offset = cookie.indexOf(search);
        if (offset != -1) {
            offset += search.length;
            end = cookie.indexOf(";", offset)
            if (end == -1) {
                end = cookie.length;
            }
            setStr = unescape(cookie.substring(offset, end));
        }
    }
    return(setStr);
}

alertErrors = function(errors) {
    if (errors instanceof Object) {
        var strs = [];
        for (var key in errors) {
            $form.find('[name="' + key + '"]').addClass('error');
            if (errors[key] instanceof Object) {
                var substrs = [];
                for (subkey in errors[key]) {

                    if (errors[key][subkey] instanceof Object) {
                        var subsubstrs = [];
                        for (subsubkey in errors[key][subkey]) {
                            subsubstrs[subsubstrs.length] = errors[key][subkey][subsubkey];
                        }
                        substrs[substrs.length] = subsubstrs.join('\r\n')/*+'\r\n'*/;
                    } else {
                        substrs[substrs.length] = errors[key][subkey];
                    }

                }
                strs[strs.length] = key + ':\r\n' + substrs.join('\r\n')/*+'\r\n'*/;
            } else {
                strs[strs.length] = key + ':1 ' + errors[key];
            }

        }
        alert(strs.join('\r\n'));
    } else {
        alert(errors);
    }
}

showErrors = function(errors, $wrapper) {



    //            <div><strong>auth: </strong> A value for the identity was not provided prior to authentication with Zend_Auth_Adapter_DbTable.</div>

    var $div = $('<div />').addClass('alert alert-error form-top-errors');
    var button = $('<button />').addClass('close').attr('data-dismiss', 'alert').appendTo($div);
    var i_button = $('<i />').addClass('icon-remove').addClass('icon-white').appendTo(button);

    if (errors instanceof Object) {
        var strs = {};
        for (var key in errors) {
            if (errors[key] instanceof Object) {
                var substrs = [];
                for (subkey in errors[key]) {

                    if (errors[key][subkey] instanceof Object) {
                        var subsubstrs = [];
                        for (subsubkey in errors[key][subkey]) {
                            subsubstrs[subsubstrs.length] = errors[key][subkey][subsubkey];
                        }
                        substrs[substrs.length] = subsubstrs.join('\r\n')/*+'\r\n'*/;
                    } else {
                        substrs[substrs.length] = errors[key][subkey];
                    }

                }
                strs[key] = substrs.join('<br>')/*+'\r\n'*/;
            } else {
                strs[key] = errors[key];
            }

        }
        for (key in strs) {
            var $div_e = $('<div />').html(strs[key]);
            if (key.length) var title_e = $('<strong />').html(key + ': ').prependTo($div_e);
            $div_e.appendTo($div);
        }

    } else {
        var $div_e = $('<div />').html(errors);

        $div_e.appendTo($div);
    }
    $div.prependTo($wrapper);


}

function hideErrors($wrapper) {
    $wrapper.find('.alert.alert-error.form-top-errors').each(function(){
        $(this).hide('fast', function(){
            $(this).remove();
        });
    });
}

function reloadPage() {
    document.location.href = cleanBaseUrl();
}

function cleanBaseUrl() {
    return /#/.test(document.location.href) ?
            (document.location.href.replace(/^(.+)(#.*)$/, '$1'))
            :
            (document.location.href)
}

function SlEntryPoint(wrapper) {
    var _sep = '/';
    var _wr = wrapper || 'body';
    if($ !== undefined) {
        _wr = $(_wr);
    } else if(angular !== undefined) {
        _wr = angular.element(_wr);
    }
    if((_wr.length === 0) || (typeof _wr.attr !== 'function')) {
        throw "Can\'t determine wrapper '"+_wr+"'";
    }
    var _url = _sep+_wr.attr('data-alias').split('.').join(_sep)+_sep;
    
    this.setUrl = function(url) {
        _url = url;
    };
    
    this.getUrl = function(type, opts) {
        var res = _url+type+_sep;
        if(opts && (typeof opts === 'object')) {
            _.each(opts, function(v, k){
                res += [k, v].join(_sep)+_sep;
            });
        }
        return res;
    };
    
    this.getModel = function() {
        return _url.split(_sep)[2];
    };
    
    this.getModule = function() {
        return _url.split(_sep)[1];
    };
    
    this.getAlias = function() {
        return _url.split(_sep).slice(1,3).join('.');
    };
}

// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
function strip_tags(str){
    return str.replace(/<\/?[^>]+>/gi, '');
}

(function($){
    $.extend($.fn, {
        blink: function(opts) {
            var defaults = {
                duration: 300,
                items: {

                },
                cb: function() {
                    
                }
            };
            var options = $.extend(defaults, opts);

            return this.each(function() {
                var obj = $(this);
                if(!obj.data('animating')) {
                    var _cur_vals = {};
                    for(var i in options.items) {
                        _cur_vals[i] = obj.css(i);
                        obj.css(i, obj.css(i));
                    }
                    obj.data('animating', true);
                    obj.animate(options.items, options.duration, function(){
                        obj.animate(_cur_vals, options.duration, function() {
                            obj.data('animating', false);
                            if(typeof options.cb === 'function') {
                                options.cb();
                            }
                        });
                    });
                }
            });
        },
        blinkShadow: function(options) {
            var defaults = {
                duration: 500,
                intensive: 50,
                radius: 20,
                color: 'rgb(200,200,200)',
                repeat: 20
            };
            var options = $.extend(defaults, options);
            var iterations = Math.round(options.duration/(2*options.repeat));
            var cur = 0;
            var reverse = false;
            var int_step = options.intensive/iterations;
            return this.each(function() {
                var stop = false;
                var obj = $(this);
                setTimeout(function(){
                    if(stop) return;
                    if(reverse) {
                        cur--;
                    } else {
                        cur++;
                    }
                    obj.css({
                        boxShadow: '0px 0px '+(int_step*cur)+'px '+(int_step*cur)/5+'px '+options.color
                    });
                    if(reverse) {
                        if(cur <= 0) {
                            stop = true;
                        }
                    } else {
                        if(cur >= iterations) {
                            reverse = true;
                        }
                    }
                    setTimeout(arguments.callee, options.repeat);
                }, options.repeat);
            });
        }
    });
}(jQuery));
