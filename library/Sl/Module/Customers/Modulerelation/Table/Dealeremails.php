<?php
namespace Sl\Module\Customers\Modulerelation\Table;
class Dealeremails extends \Sl\Modulerelation\DbTable {

	protected $_name = 'cust_dealers_emails';
	protected $_primary = 'id';
	protected $_referenceMap = array(
		'Sl\Module\Customers\Model\Dealer' => array(
			'columns' => 'dealer_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Dealer',
			'refColums' => 'id'
		),
		'Sl\Module\Home\Model\Email' => array(
			'columns' => 'email_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\Email',
			'refColums' => 'id'
		),
	);
	
	
}
