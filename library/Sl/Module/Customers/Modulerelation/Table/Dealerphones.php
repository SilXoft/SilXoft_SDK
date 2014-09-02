<?php
namespace Sl\Module\Customers\Modulerelation\Table;
class Dealerphones extends \Sl\Modulerelation\DbTable {

	protected $_name = 'cust_dealers_phones';
	protected $_primary = 'id';
	protected $_referenceMap = array(
		'Sl\Module\Customers\Model\Dealer' => array(
			'columns' => 'dealer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Dealer',
			'refColums' => 'id'
		),
		'Sl\Module\Home\Model\Phone' => array(
			'columns' => 'phone_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\Phone',
			'refColums' => 'id'
		),
	);
	
	
}
