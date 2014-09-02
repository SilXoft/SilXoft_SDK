<?php
namespace Sl\Module\Customers\Modulerelation\Table;
class Customeremails extends \Sl\Modulerelation\DbTable {

	protected $_name = 'cust_customers_emails';
	protected $_primary = 'id';
	protected $_referenceMap = array(
		'Sl\Module\Customers\Model\Customer' => array(
			'columns' => 'customer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Customer',
			'refColums' => 'id'
		),
		'Sl\Module\Home\Model\Email' => array(
			'columns' => 'email_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\Email',
			'refColums' => 'id'
		),
	);
	
	
}
