<?php
namespace Sl\Module\Home\Modulerelation\Table;

class Printformfile extends \Sl\Modulerelation\DbTable {
	protected $_name = 'home_printform_file';
	protected $_primary = 'id';
	protected $_referenceMap = array(
			'Sl\Module\Home\Model\Printform' => array(
			'columns' => 'printform_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\Printform',
		'refColums' => 'id'),
                		'Sl\Module\Home\Model\File' => array(
			'columns' => 'file_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\File',
				'refColums' => 'id'	),
	);
}