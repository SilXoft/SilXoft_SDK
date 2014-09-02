  $(document).ready(function(){
                    $('.archived_switcher').on('click', function(){
                        $('#switch_archived').val($(this).attr('data-value'));
                        $('table:first').dataTable().fnDraw();
                       // return false;
                    });
                });