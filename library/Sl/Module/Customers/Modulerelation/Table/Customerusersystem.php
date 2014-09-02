<?php
namespace Sl\Module\Customers\Modulerelation\Table;

class Customerusersystem extends \Sl\Modulerelation\DbTable {
	protected $_name = 'customer_customer_user_system';
	protected $_primary = 'id';
	protected $_referenceMap = array(
			'Sl\Module\Customers\Model\Customer' => array(
			'columns' => 'customer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Customer',
		'refColums' => 'id'),
                		'Sl\Module\Auth\Model\User' => array(
			'columns' => 'user_id',
			'refTableClass' => 'Sl\Module\Auth\Model\Table\User',
				'refColums' => 'id'	),
	);
}