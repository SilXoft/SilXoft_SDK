<?php
namespace Sl\Module\Auth\Model\Table;

class User extends \Sl\Model\DbTable\DbTable {

	protected $_primary = 'id';
	
    const TABLE_NAME = 'auth_users';
    
	public function __construct($config = array()) {
        $this->_name = self::TABLE_NAME;
        parent::__construct($config);
    }
    
	public function assignRoles($user_id, $roles_ids) {
		$this -> getAdapter() -> delete('auth_users_roles', array('user_id=?' => $user_id));
		foreach ($roles_ids as $role_id)
			$this -> getAdapter() -> insert('auth_users_roles', array(
				'user_id' => $user_id,
				'role_id' => $role_id
			));
	}
        
        public function findByEmail($email){
           
        $this->_cleanJoinNames();    //почистили стек
        $select = $this->getAdapter()->select(); // загнали в $select запит select для SQL
        $select ->from($this->_name, '*'); // шукаєм усі поля по цій таблиці 
        $select -> where('UPPER(email) like ?',strtoupper($email));// шукаєм по колонці "email" потрібне значенння
        $select -> where('active >0'); // + умова, щоб значення 'active' було >0
       // echo $select; die;
        return $this->getAdapter()->fetchRow($select); // повертаєм рядок результату запиту
       }
       
       public function findByLogin($login){
        $this->_cleanJoinNames();    //почистили стек
        $select = $this->getAdapter()->select(); // загнали в $select запит select для SQL
        $select ->from($this->_name, '*'); // шукаєм усі поля по цій таблиці 
        $select -> where('UPPER(login) like ?',strtoupper($login));// шукаєм по колонці "login" потрібне значенння
        $select -> where('active >0'); // + умова, щоб значення 'active' було >0
       // echo $select; die;
        return $this->getAdapter()->fetchRow($select); // повертаєм рядок результату запиту    
       }
                    
        public function passwordUpdate($user_id,$password){
          $this->update(array('password'=>$password), array('id = ?' => $user_id));
        }
        
	public function fetchRoles($user_id) {
		$select = $this -> getAdapter() -> select();
		$select -> from(array('r' => 'roles')) -> join(array('r_u' => 'auth_users_roles'), ' r.id=r_u.role_id', array('')) -> where('r_u.user_id=?', $user_id);

		return $this -> getAdapter() -> fetchAll($select);
	}

	public function findExtended($object_id) {
		$row=$this->find($object_id)->current();
		if (!$row) return NULL;
		$rowsets=array();
		
	}

}
