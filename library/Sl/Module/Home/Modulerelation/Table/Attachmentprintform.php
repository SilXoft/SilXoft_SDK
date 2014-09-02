<?php
namespace Sl\Module\Home\Modulerelation\Table;
class Attachmentprintform extends \Sl\Modulerelation\DbTable {

	protected $_name = 'printformselfattachment';
	protected $_primary = 'id';
	protected $_referenceMap = array(
        'Sl\Module\Home\Model\Printform' => array(
            'columns' => 'printform_id',
            'refTableClass' => 'Sl\Module\Home\Model\Table\Printform',
            'refColums' => 'id'
        ),
        'reverse' => array(
            'columns' => 'printform2_id',
            'refTableClass' => 'Sl\Module\Home\Model\Table\Printform',
            'refColums' => 'id'
        ),
    );
	
	
}
