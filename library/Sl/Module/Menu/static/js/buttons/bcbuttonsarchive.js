$(document).ready(function(){
    $('#bcbuttonsarchive').click(function(){
        var title = '', message = '', $this = $(this);
        if($this.attr('data-archived') == '0') {
            title = 'Архивирование записи';
            message = 'Вы уверены, что хотите отправить запись в архив?';
        } else {
            title = 'Разархивирование записи';
            message = 'Вы уверены, что хотите извлечь запись из архива?'
        }
        $.confirm(title, message, undefined, function(){
            $.ajax({
                type: 'POST',
                cache: false,
                url: $this.attr('data-rel'),
                data: {},
                success: function(data){
                    if(data.result) {
                        var $dialog = $('<div>Подскажите, пожалуйста, что делать теперь ...</div>');
                        $dialog.dialog({
                            title: 'Что делать дальше',
                            modal: true,
                            width: '400',
                            height: '150',
                            buttons: {
                                0: {
                                    text: 'Остаться на странице',
                                    click: function() {
                                        $(this).dialog('close');
                                        reloadPage();
                                    }
                                },
                                1: {
                                    text: 'Вернуться к списку',
                                    click: function(){
                                        $(this).dialog('close');
                                        document.location.href = $this.attr('data-list-rel');
                                    }
                                }
                            }
                        })
                    } else {
                        $.alert(data.description);
                    }
                }
            });
        });
    });
});


