<?php
namespace Sl\Module\Customers\Modulerelation\Table;
class Customercountry extends \Sl\Modulerelation\DbTable {

	protected $_name = 'cust_customers_country';
	protected $_primary = 'id';
	protected $_referenceMap = array(
		'Sl\Module\Customers\Model\Customer' => array(
			'columns' => 'customer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Customer',
			'refColums' => 'id'
		),
		'Sl\Module\Home\Model\Country' => array(
			'columns' => 'country_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\Country',
			'refColums' => 'id'
		),
	);
}
