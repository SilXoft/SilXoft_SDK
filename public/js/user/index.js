$(document).ready(function(){
    $('.userinfo-wrap').each(function(){
        var id = $(this).attr('data-id');
        var $delete = $(this).find('.close');
        var $edit = $(this).find('.edit');
        
        $delete.click(function(){
            console.log('user #'+id+' deleting ....');
            $.ajax({
                type: 'POST',
                cache: false,
                url: '/user/ajaxdelete',
                data: {id: id},
                success: function(data) {
                    if(data.result) {
                        $('.userinfo-wrap[data-id="'+id+'"]').hide(300, function(){
                            $(this).remove();
                        })
                    } else {
                        console.log(data.description);
                    }
                }
            });
        });
        
        $edit.click(function(){
            document.location.href = '/user/edit/id/'+id;
            //window.open('/user/edit/'+id);
        })
    });
    
    $('button').click(function(){
        document.location.href = '/user/add/';
        //window.open('/user/add/');
    });
});