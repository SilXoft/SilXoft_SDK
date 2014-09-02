<?php
namespace Sl\Module\Home\Modulerelation\Table;

class Emailemaildetails extends \Sl\Modulerelation\DbTable {
	protected $_name = 'home_emailemaildetails';
	protected $_primary = 'id';
	protected $_referenceMap = array(
			'Sl\Module\Home\Model\Email' => array(
			'columns' => 'email_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\Email',
		'refColums' => 'id'),
                		'Sl\Module\Home\Model\Emaildetails' => array(
			'columns' => 'emaildetails_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\Emaildetails',
				'refColums' => 'id'	),
	);
}