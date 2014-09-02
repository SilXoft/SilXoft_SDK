<?php
namespace Sl\Module\Customers\Modulerelation\Table;
class Customerdealer extends \Sl\Modulerelation\DbTable {

	protected $_name = 'cust_customers_dealers';
	protected $_primary = 'id';
	protected $_referenceMap = array(
		'Sl\Module\Customers\Model\Customer' => array(
			'columns' => 'customer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Customer',
			'refColums' => 'id'
		),
		'Sl\Module\Customers\Model\Dealer' => array(
			'columns' => 'dealer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Dealer',
			'refColums' => 'id'
		),
	);
	
	
}
