<?php
namespace Sl\Module\Customers\Modulerelation\Table;

class Contactphone extends \Sl\Modulerelation\DbTable {
	protected $_name = 'cust_contactphone';
	protected $_primary = 'id';
	protected $_referenceMap = array(
			'Sl\Module\Customers\Model\Contact' => array(
			'columns' => 'contact_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Contact',
		'refColums' => 'id'),
                		'Sl\Module\Home\Model\Phone' => array(
			'columns' => 'phone_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\Phone',
				'refColums' => 'id'	),
	);
}