<?php
namespace Sl\Module\Customers\Modulerelation\Table;

class Customercontact extends \Sl\Modulerelation\DbTable {
	protected $_name = 'cust_customercontact';
	protected $_primary = 'id';
	protected $_referenceMap = array(
			'Sl\Module\Customers\Model\Customer' => array(
			'columns' => 'customer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Customer',
		'refColums' => 'id'),
                		'Sl\Module\Customers\Model\Contact' => array(
			'columns' => 'contact_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Contact',
				'refColums' => 'id'	),
	);
}