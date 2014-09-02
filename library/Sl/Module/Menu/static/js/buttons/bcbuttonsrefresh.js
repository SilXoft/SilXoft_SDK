$(function(){
    $('#bcbuttonsrefresh').click(function(){
        $('table.datatable').each(function(){
            var ctrl = $(this).data('controller');
            if(ctrl) {
                ctrl.redrawTable();
            } else {
            	$(this).dataTable().fnDraw();
            }
        });
    });
});
