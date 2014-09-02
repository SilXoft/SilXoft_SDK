$(document).ready(function() {
    $('.datatable tr[data-id] td:not(:has(input,a))').live( "tap", function() {
           var popup_div = $(this).parents('#popup_div');
        
        if(popup_div.length==0){     
        var $this = $(this).parent('tr:first');
        var id = $this.attr('data-id');
        var editable = $this.attr('data-editable');
        var controller = $this.attr('data-controller').replace('.', '/');

        if (controller != undefined) {
            if (editable == '1') {
                window.location.href = base_edit_url.replace('controller', controller) + '/id/' + id + (is_iframe ? '/is_iframe/1' : '');
            } else if (editable == '0') {
                window.location.href = base_detailed_url.replace('controller', controller) + '/id/' + id + (is_iframe ? '/is_iframe/1' : '');
            }
        }
        }
    });        
});