<?php
namespace Sl\Module\Api\Modulerelation\Table;

class Authcodeclient extends \Sl\Modulerelation\DbTable {
	protected $_name = 'api_authcode_client';
	protected $_primary = 'id';
	protected $_referenceMap = array(
			'Sl\Module\Api\Model\Authcode' => array(
			'columns' => 'authcode_id',
			'refTableClass' => 'Sl\Module\Api\Model\Table\Authcode',
		'refColums' => 'id'),
                		'Sl\Module\Api\Model\Client' => array(
			'columns' => 'client_id',
			'refTableClass' => 'Sl\Module\Api\Model\Table\Client',
				'refColums' => 'id'	),
	);
}