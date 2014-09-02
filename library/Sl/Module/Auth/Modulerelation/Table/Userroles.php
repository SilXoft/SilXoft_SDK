<?php
namespace Sl\Module\Auth\Modulerelation\Table;
class Userroles extends \Sl\Modulerelation\DbTable {

	protected $_name = 'auth_users_roles';
	protected $_primary = 'id';
	protected $_referenceMap = array(
		'Sl\Module\Auth\Model\User' => array(
			'columns' => 'user_id',
			'refTableClass' => 'Sl\Module\Auth\Model\Table\User',
			'refColums' => 'id'
		),
		'Sl\Module\Auth\Model\Role' => array(
			'columns' => 'role_id',
			'refTableClass' => 'Sl\Module\Auth\Model\Table\Role',
			'refColums' => 'id'
		),
	);
	
	
}
