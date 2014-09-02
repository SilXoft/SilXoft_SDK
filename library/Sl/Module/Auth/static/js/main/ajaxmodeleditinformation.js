var tooltip_ids = [];
var tooltipDataLoading = false;
var tooltipUpdateInterval;

$(function() {
	$('table.table.datatable:not(.custom_tooltip)').on('mouseenter', 'tr', function() {
        var $this = $(this);
        if(!tooltipDataLoading) {
            if(!$this.attr('data-original-title')) {
                if($.inArray($this.attr('data-id'), tooltip_ids) == -1) {
                    tooltip_ids[tooltip_ids.length] = $this.attr('data-id');
                }
            }
        }
    });
    
    tooltipUpdateInterval = setInterval(function(){
        tooltipDataLoading = true;
        if(tooltip_ids.length == 0) {
            tooltipDataLoading = false;
            return;
        }
        if(!$('table.datatable:not(.custom_tooltip) tbody tr:first').attr('data-controller')) {
            tooltipDataLoading = false;
            return;
        }
        var model_data = $('table.datatable:not(.custom_tooltip) tbody tr:first').attr('data-controller').split('.');
        $.ajax({
            type: 'POST',
            cache: false,
            url: '/auth/main/ajaxmodelseditinformation',
            data: {
                model_name : model_data[0],
                module_name : model_data[1],
                ids: tooltip_ids
            },
            success: function(data){
                tooltipDataLoading = false;
                if(data.result) {
                    for(var i in data.tooltips) {
                        $('table.table.datatable:not(.custom_tooltip) tr[data-id="'+i+'"]').attr('data-original-title', data.tooltips[i]);
                        $('table.table.datatable:not(.custom_tooltip) tr[data-id="'+i+'"]').tooltip({
                            html: true,
                            placement: 'top',
                            trigger: 'hover'
                        });
                        if($('table.table.datatable:not(.custom_tooltip) tr[data-id="'+i+'"]').is(':hover')) {
                            $('table.table.datatable:not(.custom_tooltip) tr[data-id="'+i+'"]').tooltip('show');
                        }
                    }
                }
            }
        });
        tooltip_ids = [];
    }, 100);
    
    
    
    setInterval(function(){
        $('table.table.datatable:not(.custom_tooltip) tr[data-id][data-original-title]').each(function(){
            if(!$(this).is(':hover')) {
                $(this).tooltip('destroy');
                $(this).attr('data-original-title', null);
            }
        });
    }, 5000);
});
