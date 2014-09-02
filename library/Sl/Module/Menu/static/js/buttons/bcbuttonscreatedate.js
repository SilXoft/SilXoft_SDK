var date_format = '';

$(document).ready(function(){
    var field = '#'+$('#bcbuttonscreatedate').attr('data-field');
    var field_clone = field+'_bcbuttons';
    if($(field).parents('.control-group:first').length) {
        $(field).parents('.control-group:first').hide();
    } else {
        $(field).hide();
    }
    if($(field_clone).attr('data-editable') == '1') {
        $(field_clone).datepicker({
            dateFormat:'yy-mm-dd',
            changeYear: true,
            yearRange: '-65:+1',
            beforeShow: function() {
                setTimeout(function(){
                    $('.ui-datepicker').css('z-index', 3000);
                }, 0);
            }
        });
    } else {
        $(field_clone).attr('disabled', 'disabled');
    }
    $(field_clone).val(filterDate($(field).val()));
    $(field_clone).change(function(){
        $(field).val($(this).val());
    });
    
});

function filterDate(date) {
    var d = new Date();
    if(Date.parse(date) === NaN) {
        return '';
    }
    d.setTime(Date.parse(date));
    var da = [];
    da[0] = d.getFullYear()+'';
    da[1] = parseInt(d.getMonth() + 1)+''; //Months are zero based
    da[2] = d.getDate()+'';
    if(da[1].length == 1) {
        da[1] = '0'+da[1];
    }
    if(da[2].length == 1) {
        da[2] = '0'+da[2];
    }
    return da.join('-');
}


