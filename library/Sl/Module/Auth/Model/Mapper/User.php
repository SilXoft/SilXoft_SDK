<?php
namespace Sl\Module\Auth\Model\Mapper;

class User extends \Sl_Model_Mapper_Abstract {
    
    protected function _getMappedDomainName() {
        return '\Sl\Module\Auth\Model\User';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Auth\Model\Table\User';
    }
	
    public function findByEmail($email){
        if (strlen($email)){ // якщо значення змінної більше 0 знаків
        $row = $this->_getDbTable()->findByEmail($email); //виклик методу з пошуком по змінній
        $user= $this->_createInstance(is_array($row)?$row:(array)$row); //створюєм об'єкт поточного мапера по знайденим значенням з db 
        return $user; //повертаєм об'єкт
        }
    }
    
     public function findByLogin($login){
        if (strlen($login)){ // якщо значення змінної більше 0 знаків
        $row = $this->_getDbTable()->findByLogin($login); //виклик методу з пошуком по змінній
        $user= $this->_createInstance(is_array($row)?$row:(array)$row); //створюєм об'єкт поточного мапера по знайденим значенням з db 
        return $user; //повертаєм об'єкт
        }
    }
    //метод не вносить в auth_users_log інформацію про зміни, тому краще використовувати newPasswordUpdate()
    // і краще передавати на вхід не id, а user-a.
    public function passwordUpdate($id, $password){
        if (strlen($password)){
            $this->_getDbTable()->update(array('password'=>$password), array('id = ?' => $id));
            return true;
          } else {
             return false; 
          }
    }
    
    public function newPasswordUpdate($user, $password){
        if (strlen($password)){
            $no_userId = true;//на випадок, якщо метод може викликатись незареєстрованим користувачем.
            \Sl\Service\Loger::Log($user, 'password',$user->getPassword(), $password, '', $no_userId);
            self::passwordUpdate($user->getId(), $password);
            return true;
          } else {
             return false; 
          }
    }
            
	public function assignRoles(\Sl\Module\Auth\Model\User $User, array $Roles){
		$roles_ids=array();	
		foreach($Roles as $role) {if ($role instanceof \Sl\Module\Home\Model\Role) $roles_ids[]=$role->getId();}
		return $this->_getDbTable()->assignRoles($User->getId(),$roles_ids);
	}
	
	public function fetchRoles(\Sl\Module\Auth\Model\User $User){
		$rowset = 	$this->_getDbTable()->fetchRoles($User->getId());
		if (count($rowset) == 0)
			return array();
		if (!is_array($rowset))
			$rowset = $rowset -> toArray();
		return array_map(array(\Sl\Module\Home\Model\Role,'_createInstance'), $rowset);
	}
	
    public function save(\Sl_Model_Abstract $object, $return = false, $events = true) {
        $object = parent::save($object, true, $events);
	  // $handling_relations = array(); /* \Sl_Modulerelation_Manager::findHandlingRelations($object); */
      //  $user = \Sl_Model_Factory::mapper($object)->findExtended($object->getId(),$handling_relations);
        if($user) {
            if(\Zend_Auth::getInstance()->getIdentity()->id == $object->getId()) {
                \Zend_Auth::getInstance()->getStorage()->write($user);
            }
        }
        if($return) {
            return $object;
        }
    }
    
}

