<?php
namespace Sl\Module\Auth\Modulerelation\Table;

class Restrictionroles extends \Sl\Modulerelation\DbTable {
	protected $_name = 'auth_restriction_role';
	protected $_primary = 'id';
	protected $_referenceMap = array(
			'Sl\Module\Auth\Model\Restriction' => array(
			'columns' => 'restriction_id',
			'refTableClass' => 'Sl\Module\Auth\Model\Table\Restriction',
		'refColums' => 'id'),
                		'Sl\Module\Auth\Model\Role' => array(
			'columns' => 'role_id',
			'refTableClass' => 'Sl\Module\Auth\Model\Table\Role',
				'refColums' => 'id'	),
	);
}