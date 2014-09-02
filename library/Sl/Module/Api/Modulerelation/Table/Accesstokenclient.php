<?php
namespace Sl\Module\Api\Modulerelation\Table;

class Accesstokenclient extends \Sl\Modulerelation\DbTable {
	protected $_name = 'api_accesstoken_client';
	protected $_primary = 'id';
	protected $_referenceMap = array(
			'Sl\Module\Api\Model\Accesstoken' => array(
			'columns' => 'accesstoken_id',
			'refTableClass' => 'Sl\Module\Api\Model\Table\Accesstoken',
		'refColums' => 'id'),
                		'Sl\Module\Api\Model\Client' => array(
			'columns' => 'client_id',
			'refTableClass' => 'Sl\Module\Api\Model\Table\Client',
				'refColums' => 'id'	),
	);
}