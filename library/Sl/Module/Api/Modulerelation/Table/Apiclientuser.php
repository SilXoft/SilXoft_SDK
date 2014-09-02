<?php
namespace Sl\Module\Api\Modulerelation\Table;

class Apiclientuser extends \Sl\Modulerelation\DbTable {
	protected $_name = 'api_client_user';
	protected $_primary = 'id';
	protected $_referenceMap = array(
			'Sl\Module\Api\Model\Client' => array(
			'columns' => 'client_id',
			'refTableClass' => 'Sl\Module\Api\Model\Table\Client',
		'refColums' => 'id'),
                		'Sl\Module\Auth\Model\User' => array(
			'columns' => 'user_id',
			'refTableClass' => 'Sl\Module\Auth\Model\Table\User',
				'refColums' => 'id'	),
	);
}