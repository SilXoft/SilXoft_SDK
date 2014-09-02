$(document).ready(function(){
    $('input, select').uniform();
    $('#data_begin,#data_end').wl_Date();

    var $insurersDiv = $('<div></div>').attr({id: 'insurers_div', 'class': 'popup'}).css({display: 'none'});
    var $linesDiv = $('<div></div>').attr({id: 'lines_div', 'class': 'popup'}).css({display: 'none'});
    var $insCDiv = $('<div></div>').attr({id: 'insurers_c_div', 'class': 'popup'}).css({display: 'none'});
    var $linesCDiv = $('<div></div>').attr({id: 'lines_c_div', 'class': 'popup'}).css({display: 'none'});
    $('body').append($insurersDiv);
    $('body').append($insCDiv);
    $('body').append($linesDiv);
    $('body').append($linesCDiv);

    $insurersDiv.dialog({
        title: 'Оберіть страхувальника',
        autoOpen: false,
        width: 600,
        height: 300,
        modal: true
    });

    $linesDiv.dialog({
        title: 'Оберіть вигодонабувача (лінію)',
        autoOpen: false,
        width: 600,
        height: 300,
        modal: true
    });

    $insCDiv.dialog({
        title: 'Оберіть вигодонабувача (лінію)',
        autoOpen: false,
        width: 600,
        height: 300,
        modal: true
    });

    $linesCDiv.dialog({
        title: 'Оберіть вигодонабувача (лінію)',
        autoOpen: false,
        width: 600,
        height: 300,
        modal: true
    });

    $('#insurer-popup').click(function(){
        $.get('/insurer/poplist', function(data){
            $insurersDiv.html(data);
            $insurersDiv.dialog('open');
        });
    });

    $('#line-popup').click(function(){
        $.get('/line/poplist', function(data){
            $linesDiv.html(data);
            $linesDiv.dialog('open');
        });
    });

    $('#insurer_name').keyup(function(){
        if($(this).val() != $('#insurer_id').attr('data-name')) {
            $('#insurer_id').val('');
            $('#insurer_id').attr('data-name', '');
        }
    });

    $('#line_name').keyup(function(){
        if($(this).val() != $('#line_id').attr('data-name')) {
            $('#line_id').val('');
            $('#line_id').attr('data-name', '');
        }
    });

    $('#insurer_id').change(function(){
        if($(this).val().length) {
            $('#insurer-contact-row').show(300);
        } else {
            $('#insurer-contact-row').hide(300);
        }
    });

    $('#line_id').change(function(){
        if($(this).val().length) {
            $('#line-contact-row').show(300);
        } else {
            $('#line-contact-row').hide(300);
        }
    });

    $insurersDiv.delegate('.item', 'click', function(){
        $('#insurer_id').val($(this).attr('data-id'));
        $('#insurer_name').val($(this).attr('data-name'));
        $('#insurer_id').attr('data-name', $(this).attr('data-name'));
        $('#insurer_id').trigger('change');
        $insurersDiv.dialog('close');
    });

    $insCDiv.delegate('.item', 'click', function(){
        $('#insurer_c_id').val($(this).attr('data-id'));
        $('#insurer_c_name').val($(this).attr('data-name'));
        $('#insurer_c_id').attr('data-name', $(this).attr('data-name'));
        $('#insurer_c_id').trigger('change');
        $insCDiv.dialog('close');
    });

    $linesDiv.delegate('.item', 'click', function(){
        $('#line_id').val($(this).attr('data-id'));
        $('#line_id').attr('data-name', $(this).attr('data-name'));
        $('#line_name').val($(this).attr('data-name'));
        $('#line_id').trigger('change');
        $linesDiv.dialog('close');
    });

    $linesCDiv.delegate('.item', 'click', function(){
        $('#line_c_id').val($(this).attr('data-id'));
        $('#line_c_id').attr('data-name', $(this).attr('data-name'));
        $('#line_c_name').val($(this).attr('data-name'));
        $('#line_c_id').trigger('change');
        $linesCDiv.dialog('close');
    });

    $('#insurer_name').wl_Autocomplete({
        source: function(request, responce){
            $.ajax({
                method: 'POST',
                url: '/insurer/ajaxsearch',
                data: {
                    name: request.term
                },
                success: function(data) {
                    responce($.map(data.names, function(item){
                        return {
                            value: item.id,
                            label: item.name
                        };
                    }));
                }
            })
        },
        select: function(e, ui) {
            $('#insurer_name').val(ui.item.label);
            $('#insurer_id').val(ui.item.value);
            $('#insurer_id').attr('data-name', ui.item.label);
            $('#insurer_id').trigger('change');
            return false;
        },
        focus: function(e, ui) {
            $('#insurer_name').val(ui.item.label);
            return false;
        }
    });

    $('#line_name').wl_Autocomplete({
        source: function(request, responce){
            $.ajax({
                method: 'POST',
                url: '/line/ajaxsearch',
                data: {
                    name: request.term
                },
                success: function(data) {
                    responce($.map(data.names, function(item){
                        return {
                            value: item.id,
                            label: item.name
                        };
                    }));
                }
            })
        },
        select: function(e, ui) {
            $('#line_name').val(ui.item.label);
            $('#line_id').val(ui.item.value);
            $('#line_id').attr('data-name', ui.item.label);
            $('#line_id').trigger('change');
            return false;
        },
        focus: function(e, ui) {
            $('#line_name').val(ui.item.label);
            return false;
        }
    });

      $('#item_add').click(function(){
	  var id = $('#itemlist tbody tr:last td:first').text();
          id++;
	  var name = $('#size_id :selected').html();
          var name_id = $('#size_id').val();
	  var ammount = $('#amount_container').val();
      $('#itemlist tbody').append('<tr><td>'+id+'</td><td data-id = "' + name_id + '">'+ name +'</td><td>' + ammount + '</td><td><a class="delete" title="Удалить">X</a></td></tr>');
      });

      $('a.delete').live('click',function(){
        $(this).parents('tr').remove();
      });



});

