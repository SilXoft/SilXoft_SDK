var ajax_url = '/auth/admin/';
$(document).ready(function(){
    $('.tabs').tabs({
        beforeLoad: function( event, ui ) {
            ui.jqXHR.error(function() {
                ui.panel.html(
                    "Couldn't load this tab. We'll try to fix this as soon as possible. " +
                    "If this wouldn't be a demo.");
            });
        },
        load: function( event, ui ) {
            $(ui.panel).find('.sub_tabs').tabs();
            $(ui.panel).find('select, input').uniform();
        }
    });
    
    $('input.mvc_permission, input.custom_permission').live('change', function() {
        var ids = $(this).attr('id').split('_');
        var obj = $(this);
        if (ids.length > 1) {
            var res_id = ids[0].split('-');
            var role_id = ids[1].split('-');
            if (res_id[1] > 0 && role_id[1] > 0) {

                $.post(ajax_url+($(this).is(':checked') ? 'ajaxcreatepermission' : 'ajaxdeletepermission'), {
                    resource_id : res_id[1],
                    role_id : role_id[1],
                    value:$(this).val()
                }, function(data) {
                    if (data.result == undefined || data.result==false){
                        obj.is(':checked')?obj.removeAttr('checked'):obj.attr('checked','checked');
                        if (data.description != undefined) alert(data.description);
                    }
                }, 'json');

            }
        }
    });
    //Обробка типів field, obj
    $('input.field_permission, input.obj_permission').live('change', function() {
        var ids = $(this).attr('id').split('_');
        var obj = $(this);


        if (ids.length > 1) {
            var res_id = ids[0].split('-');
            var role_id = ids[1].split('-');
            if (res_id[1] > 0 && role_id[1] > 0) {

                $.post(ajax_url+($(this).val() ? 'ajaxcreatepermission' : 'ajaxdeletepermission'), {
                    resource_id : res_id[1],
                    role_id : role_id[1],
                    value : $(this).val()
                }, function(data) {
                    if (data.result == undefined || data.result==false){
                        obj.is(':checked')?obj.removeAttr('checked'):obj.attr('checked','checked');
                        if (data.description != undefined) alert(data.description);
                    }
                }, 'json');

            }
        } 
    });
    
    $('#rebuild').click(function(){
    	
        $.ajax({
            url: '/auth/admin/ajaxrebuild',
            type: 'POST',
            cache: false,
            success: function(data){
                if(data.result) {
                	
                    window.location.reload(); //href = document.location.href;
                } else {
                    alert(data.description);
                }
            }
        });
    });
});


