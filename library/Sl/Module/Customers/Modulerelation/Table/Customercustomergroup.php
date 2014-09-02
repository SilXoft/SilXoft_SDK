<?php
namespace Sl\Module\Customers\Modulerelation\Table;

class Customercustomergroup extends \Sl\Modulerelation\DbTable {
	protected $_name = 'cust_customercustomergroup';
	protected $_primary = 'id';
	protected $_referenceMap = array(
			'Sl\Module\Customers\Model\Customer' => array(
			'columns' => 'customer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Customer',
		'refColums' => 'id'),
                		'Sl\Module\Customers\Model\Customergroup' => array(
			'columns' => 'customergroup_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Customergroup',
				'refColums' => 'id'	),
	);
}