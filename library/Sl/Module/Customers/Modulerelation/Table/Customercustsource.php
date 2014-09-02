<?php
namespace Sl\Module\Customers\Modulerelation\Table;
class Customercustsource extends \Sl\Modulerelation\DbTable {

	protected $_name = 'cust_customers_custsource';
	protected $_primary = 'id';
	protected $_referenceMap = array(
		'Sl\Module\Customers\Model\Customer' => array(
			'columns' => 'customer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Customer',
			'refColums' => 'id'
		),
		'Sl\Module\Customers\Model\Customersource' => array(
			'columns' => 'source_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Customersource',
			'refColums' => 'id'
		),
	);
	
	
}
