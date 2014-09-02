<?php
namespace Sl\Module\Customers\Modulerelation\Table;
class Customerphones extends \Sl\Modulerelation\DbTable {

	protected $_name = 'cust_customers_phones';
	protected $_primary = 'id';
	protected $_referenceMap = array(
		'Sl\Module\Customers\Model\Customer' => array(
			'columns' => 'customer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Customer',
			'refColums' => 'id'
		),
		'Sl\Module\Home\Model\Phone' => array(
			'columns' => 'phone_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\Phone',
			'refColums' => 'id'
		),
	);
	
	
}
