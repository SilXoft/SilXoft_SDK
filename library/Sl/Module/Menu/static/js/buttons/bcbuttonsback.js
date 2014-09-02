var unlock_url = '/home/locker/ajaxunlockresource' 
$(document).ready(function(){
    var changes = false;
    $('form:first').on('change', 'input, select, textarea', function(){
        changes = true;
    });
    $('#bcbuttonsback').click(function(){
        var $this = $(this);
        var goBack = function(){
            if((is_iframe != undefined) && is_iframe) {
                window.parent.closeIframeFunction();
            } else {
                history.back();
                
            }
        }
        
        var unlock = function($el) {
            if($el.attr('locker_resource') != undefined) {
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: unlock_url,
                    data: {
                        resource: $el.attr('locker_resource')
                    },
                    success: function(data) {
                        if(data.result) {
                            goBack();
                        } else {
                            $.alert(data.description);
                        }
                    }
                });
            } else {
                goBack();
            }
        }
        
        if(changes) {
            $.confirm('Отмена редактирования', 'Вы уверены, что хотите покинуть страницу? Все не сохраненные данные будут потеряны', 'Ok', function(){
                unlock($this);
            });
        } else {
            unlock($this);
        }
        
        
    });
});


