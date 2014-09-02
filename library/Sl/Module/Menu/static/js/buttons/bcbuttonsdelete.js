$(document).ready(function(){
    $('#bcbuttonsdelete').click(function(){
        var title = '', message = '', $this = $(this);
        
        title = 'Удаление записи';
        message = 'Вы уверены, что хотите удалить запись?'
        
        $.confirm(title, message, undefined, function(){
            $.ajax({
                type: 'POST',
                cache: false,
                url: $this.attr('data-rel'),
                data: {},
                success: function(data){
                    if(data.result) {
                        document.location.href = $this.attr('data-list-rel');
                    } else {
                        $.alert(data.description);
                    }
                }
            });
        });
    });
});


