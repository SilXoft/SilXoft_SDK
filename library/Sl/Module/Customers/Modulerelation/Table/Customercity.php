<?php
namespace Sl\Module\Customers\Modulerelation\Table;
class Customercity extends \Sl\Modulerelation\DbTable {

	protected $_name = 'cust_customers_city';
	protected $_primary = 'id';
	protected $_referenceMap = array(
		'Sl\Module\Customers\Model\Customer' => array(
			'columns' => 'customer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Customer',
			'refColums' => 'id'
		),
		'Sl\Module\Home\Model\City' => array(
			'columns' => 'city_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\City',
			'refColums' => 'id'
		),
	);
	
	
}
