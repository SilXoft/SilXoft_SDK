var table;
var search_last_value;
var change_date_filters;
var addsearch;
$(document).ready(function() {
    // column calculations
    $('body').on('click', selected_cnt_bage_selector+'.badge', function(){
    	$(this).parents(div_selected_models_wrapper).toggleClass('opened');
    });
    
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

    if (dtCustomConfig) {
        $.extend(dtConfig, dtCustomConfig);
    }

    var dp_options = $.extend(datepicker_options, {
        showOn: "button",
        buttonImage: "/css/images/icons/dark/calendar.png",
        buttonImageOnly: true
    });

    $('thead .dt_search_input[type="text"].date').datepicker(dp_options).keyup(function(e) {
        if (e.keyCode == 8 || e.keyCode == 46) {
            $.datepicker._clearDate(this);
        }
    }).change(function() {
        if ($(this).val() != '') {
            $(this).next('img').addClass('date_selected');
        } else {
            $(this).next('img').removeClass('date_selected');
        }
        $(this).next('img').attr('title', $(this).val());
        //$(this).attr('title', $(this).val());
    }).each(function() {
        if ($(this).val() != '') {
            $(this).next('img').addClass('date_selected');
            $(this).next('img').attr('title', $(this).val());
        } else {
            $(this).next('img').removeClass('date_selected');
        }
    });

    var has_filters = ($('table thead .dt_search_input').length > 0);
    if (!has_filters) {
        $('.clean_filters.btn').hide();
    }
	
	
	//datetimepicker
	
	
    var dtp_options = $.extend(datetimepicker_options, {
        showOn: "button",
        buttonImage: "/css/images/icons/dark/calendar.png",
        buttonImageOnly: true,
        onSelect: function(){
        	$(this).trigger('selectdate');
        }
    });
	
    $('thead .dt_search_input[type="text"].datetime').datetimepicker(dtp_options).hide().change(function() {
        if ($(this).val() != '') {
            $(this).next('img').addClass('date_selected');
        } else {
            $(this).next('img').removeClass('date_selected');
        }
        $(this).next('img').attr('title', $(this).val());
        //$(this).attr('title', $(this).val());
    }).each(function() {
        if ($(this).val() != '') {
            $(this).next('img').addClass('date_selected');
            $(this).next('img').attr('title', $(this).val());
        } else {
            $(this).next('img').removeClass('date_selected');
        }
    });
	

	
	//end of datetimepicker
    $('.clean_filters.btn').click(function() {
        $(document).trigger('beforecleanfilters.sl');
        /*$('.table th .dt_search_input').each(function(){
         var $this = $(this);сщті
         setCookie($this.attr('id')+document.location.href, '');
         });*/

        $('#hide_columns_div .items[data-searchable="1"]').each(function() {
            if($(this).is('[data-type="date"]') || $(this).is('[data-type="datetime"]')) {
                setCookie($(this).attr('data-desc') + '_begin' + cleanBaseUrl(), '');
                setCookie($(this).attr('data-desc') + '_end' + cleanBaseUrl(), '');
            } else {
                setCookie($(this).attr('data-desc') + cleanBaseUrl(), '');
            }
        });
        $(document).trigger('aftercleanfilters.sl');
        reloadPage();
    });

  /*  setInterval(function() {
        $('thead .dt_search_input[type="text"].date').each(function() {
            var $this = $(this);
            var $img = $this.next('img');
            if ($this.val() != '') {
                //$img.css('border', '1px solid red');
            } else {
                //$img.css('border', 'none');
            }
        });
    }, 500);
*/
    $('body').on('dblclick','.datatable tr[data-id]', function() {
        var $this = $(this);
        var id = $this.attr('data-id');
        var editable = $this.attr('data-editable');
        var controller = $this.attr('data-controller').split('.').reverse().join('/');
        

        if (controller != undefined) {
            if (is_iframe){
             if (editable == '1') {

                    window.open( base_edit_url.replace('controller', controller) + '/id/' + id);
                } else if (editable == '0') {
                    window.open( base_detailed_url.replace('controller', controller) + '/id/' + id);
                }
                
            } else{
                if (editable == '1') {
                
                        window.location.href = base_edit_url.replace('controller', controller) + '/id/' + id + (is_iframe ? '/is_iframe/1' : '');
                    } else if (editable == '0') {
                        window.location.href = base_detailed_url.replace('controller', controller) + '/id/' + id + (is_iframe ? '/is_iframe/1' : '');
                    }
            }
            
        }
    });

    $('.datatable tr[data-id] td:first').live('touchend', function() {
        var $this = $(this);
        var id = $this.attr('data-id');
        var editable = $this.attr('data-editable');
        var controller = $this.attr('data-controller').split('.').reverse().join('/');

        if (controller != undefined) {
            if (editable == '1') {
                window.location.href = base_edit_url.replace('controller', controller) + '/id/' + id + (is_iframe ? '/is_iframe/1' : '');
            } else {
                window.location.href = base_detailed_url.replace('controller', controller) + '/id/' + id + (is_iframe ? '/is_iframe/1' : '');
            }
        }
    });
    /*
     if(iOS) {
     $('.datatable tr[data-id]').unbind('dblclick');
     $('.datatable tr[data-id]').live('click', function(){
     var $this = $(this);
     var id = $this.attr('data-id');
     var editable = $this.attr('data-editable');
     var controller = $this.attr('data-controller').replace('.','/');
     
     if (controller !=undefined){
     if(editable == '1') {
     window.location.href=base_edit_url.replace('controller',controller)+'/id/'+id+(is_iframe?'/is_iframe/1':'');
     } else  {
     window.location.href=base_detailed_url.replace('controller',controller)+'/id/'+id+(is_iframe?'/is_iframe/1':'');
     }
     }
     });
     }*/

    table = $('.datatable:first').dataTable(dtConfig);

    $('.table th .dt_search_input').each(function() {
        var $this = $(this);
        var $table = $this.parents('table.dataTable').dataTable();
        var prev_value = getCookie($this.attr('id') + cleanBaseUrl());
        if (prev_value != null) {

            $this.val(prev_value);

            if ($table) {
                if ($this.is('.date') || $this.is('.datetime')) {
                    prev_value = $this.parents('th').first().find('.date, .datetime').map(function(index, el){
                        $(el).change();
                        return $(el).val().trim();
                    }).get().join('::');
                } else {
                    //$table.fnFilter(prev_value, $this.attr('data-ind'));
                }
                $table.fnFilter(prev_value, $this.attr('data-ind'));
            }
        }
    });

    var t = table || false;

    if (t) {

        use_prev_data = false;
        t.fnDraw();
    }


    updateHiddenColumns();





    $('thead').on('keyup', '.dt_search_input[type="text"]:not(.date, .datetime)', function() {
        checkTableFilters();
        search_last_value = $(this).val();
        setTimeout(addsearch, 500, $(this).val(), $(this).attr('data-ind'), $(this).parents('table.dataTable').dataTable());
        setCookie($(this).attr('id') + document.location.href, $(this).val());
        //console.log($(this).attr('id')+document.location.href+' :: '+$(this).val());
        //table.fnFilter($(this).val(), $(this).attr('data-ind'));
    });

    $('thead .dt_search_input[type="text"].date').on('change', function() {
    	change_date_filters($(this));
        
    });
    $('thead .dt_search_input[type="text"].datetime').on('selectdate', function() {
    	change_date_filters($(this));
       
    });

    $('thead select.dt_search_input:not(.datetime)').on('change', function() {
    	console.log($(this));
        checkTableFilters();
        var $this = $(this);
        var val = $this.val();
        var ind = $this.attr('data-ind');
        setCookie($this.attr('id') + document.location.href, val);
        //console.log($this.attr('id')+document.location.href+' :: '+val);
        table.fnFilter(val, ind);
    });
    
    $('thead select.dt_search_input.datetime').on('selectdate', function() {
        checkTableFilters();
        var $this = $(this);
        var val = $this.val();
        var ind = $this.attr('data-ind');
        setCookie($this.attr('id') + document.location.href, val);
        //console.log($this.attr('id')+document.location.href+' :: '+val);
        table.fnFilter(val, ind);
    });
    
    //$('input, select').uniform();

    $('.export').click(function() {
       
        var $this = $(this);
        $(this).prop('disabled', 'disabled');
 //
        var limit = parseInt($('#export_confirm_limit').val());
        var need_confirm = false;
        if (limit && limit > 0) {
            if (table.dataTable().fnSettings()._iRecordsDisplay != undefined) {
                if (table.dataTable().fnSettings()._iRecordsDisplay > limit) {
                    need_confirm = true;
                }
            }
        }
        
        var export_function = function() {
            var params = table.oApi._fnAjaxParameters(table.dataTable().fnSettings());

            params = custServerParams(params, true);
            
            var $form = $('<form action="' + export_entry_point + '" method="POST" />');
            for (var i in params) {
                var $input = $('<input type="hidden" />').attr(params[i]).appendTo($form);
            }
            $form.appendTo($('body'));
            $form.submit();
            $this.prop('disabled', null);
        };

        if (need_confirm) {
            //title, message, yesText, yesCallback, noText, noCallback
            $.confirm('Подтвердите, пожалуйста ...', 'Запрос содержит более '+limit+' записей и может долго обрабатываться.', null, function(){
                export_function();
            }, null, function(){
                $this.prop('disabled', null);
            });
        } else {
            export_function();
        }
    });
    
    $('.export_page').click(function(){
        var params = table.oApi._fnAjaxParameters(table.dataTable().fnSettings());

        params = custServerParams(params, true);

        var $form = $('<form action="' + export_entry_point + '" method="POST" />');
        for (var i in params) {
            var $input = $('<input type="hidden" />').attr(params[i]).appendTo($form);
        }
        $('<input type="hidden" name="page_only" value="1" />').appendTo($form);
        
        $('tbody tr[data-real-id]', $(table)).each(function(){
            $('<input type="hidden" name="export_ids[]" value="'+$(this).attr('data-real-id')+'" />').appendTo($form);
        });
        $form.appendTo($('body'));
        $form.submit();
        $this.prop('disabled', null);
    })

    $('.archived_switcher').click(function() {
        $('#switch_archived').val($(this).attr('data-value'));
        table.fnDraw();
    });

    checkTableFilters();
});


addsearch = function(search_val, data_ind, table) {

    if (search_val == search_last_value) {
        table.fnFilter(search_val, data_ind);
    }
}

function checkTableFilters() {
    var filers_set = false;
    $('#hide_columns_div .items[data-searchable="1"]').each(function() {
        var cookie_name;
        if($(this).is('[data-type="date"]') || $(this).is('[data-type="datetime"]')) {
            cookie_name = $(this).attr('data-desc') + '_begin' + cleanBaseUrl();
            if(getCookie(cookie_name) != null && getCookie(cookie_name).length > 0) {
                filers_set = true;
            }
            cookie_name = $(this).attr('data-desc') + '_end' + cleanBaseUrl();
            if(getCookie(cookie_name) != null && getCookie(cookie_name).length > 0) {
                filers_set = true;
            }
        } else {
            cookie_name = $(this).attr('data-desc') + cleanBaseUrl();
            if(getCookie(cookie_name) != null && getCookie(cookie_name).length > 0) {
                filers_set = true;
            }
        }
    });
    $('.clean_filters.btn').attr('disabled', filers_set ? null : 'disabled');
}

change_date_filters = function($this){
	checkTableFilters();
        
        var val = $this.val();
        var ind = $this.attr('data-ind');
        if (!$this.hasClass('single_date_search')) {
            val = $this.parents('th').first().find('.date, .datetime').map(function(index, el) {
                return $(el).val().trim();
            }).get().join('::');
        }
        setCookie($this.attr('id') + document.location.href, $this.val());
        //console.log($this.attr('id')+document.location.href+' :: '+val);
        $this.parents('table.dataTable').dataTable().fnFilter(val, ind);
        checkTableFilters();
        //table.fnFilter(val, ind);	
}
