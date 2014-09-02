<?php
namespace Sl\Module\Home\Modulerelation\Table;

class Parentacnt extends \Sl\Modulerelation\DbTable {
    protected $_name = 'acnt_acnt';
    protected $_primary = 'id';
    protected $_referenceMap = array(
        'Sl\Module\Home\Model\Acnt' => array(
            'columns' => 'acnt_id',
            'refTableClass' => 'Sl\Module\Home\Model\Table\Acnt',
            'refColums' => 'id'
        ),
        'reverse' => array(
            'columns' => 'acnt2_id',
            'refTableClass' => 'Sl\Module\Home\Model\Table\Acnt',
            'refColums' => 'id'
        ),
    );
}
