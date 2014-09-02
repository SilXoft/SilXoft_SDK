<?php
return array (
    'ajaxarchive'=> array(
        'autoclear'=>true,
        'icon' => 'inbox'
    ),
    'ajaxunarchive' => array(
        'action' => 'ajaxarchive',
        'autoclear' => true,
        'icon' => 'inbox',
        'params' => array(
            'set_archived' => '0',
        ),
    ),
    'ajaxdelete'=> array(
        'autoclear'=>true,
        'icon' => 'remove'
    ),
    
);