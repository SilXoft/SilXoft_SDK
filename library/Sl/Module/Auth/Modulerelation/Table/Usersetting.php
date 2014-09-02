<?php

namespace Sl\Module\Auth\Modulerelation\Table;

class Usersetting extends \Sl\Modulerelation\DbTable {

    protected $_name = 'auth_usersetting';
    protected $_primary = 'id';
    
    protected $_referenceMap = array(
        'Sl\Module\Auth\Model\User' => array(
            'columns' => 'user_id',
            'refTableClass' => 'Sl\Module\Auth\Model\Table\User',
            'refColums' => 'id'),
        'Sl\Module\Auth\Model\Setting' => array(
            'columns' => 'setting_id',
            'refTableClass' => 'Sl\Module\Auth\Model\Table\Setting',
            'refColums' => 'id'),
    );

}
