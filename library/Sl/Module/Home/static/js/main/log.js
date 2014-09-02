var $table;
$(document).ready(function(){
    if(dtCustomConfig) {
        $.extend(dtConfig, dtCustomConfig);
    }
    
    $table = $('table:first').dataTable(dtConfig);
    
    $('[name="field_name"]').change(function(){
        $table.fnFilter($(this).val(), 1);
    })
});


