<?php
namespace Sl\Module\Customers\Modulerelation\Table;

class Contactemail extends \Sl\Modulerelation\DbTable {
	protected $_name = 'cust_contactemail';
	protected $_primary = 'id';
	protected $_referenceMap = array(
			'Sl\Module\Customers\Model\Contact' => array(
			'columns' => 'contact_id',
			'refTableClass' => 'Sl\Module\Customers\Model\Table\Contact',
		'refColums' => 'id'),
                		'Sl\Module\Home\Model\Email' => array(
			'columns' => 'email_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\Email',
				'refColums' => 'id'	),
	);
}