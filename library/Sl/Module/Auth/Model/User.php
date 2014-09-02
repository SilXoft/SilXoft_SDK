<?php
namespace Sl\Module\Auth\Model;

class User extends \Sl_Model_Abstract {
    
    protected $_name; 
protected $_system;

 
protected $_blocked;


	protected $_login;
    protected $_password;
    protected $_email;
	protected $_phone;
	protected $_roles;
    
	
	

	

	public function setSystem ($system) {
		$this->_system = $system;
		return $this;
	}

	public function setBlocked ($blocked) {
		$this->_blocked = $blocked;
		return $this;
	}

	public function fetchRelations(){
		return array('roles');
	}
	
	
	
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }
    
    
    /*
	public function setRoleId($role) {
        $this->_role_id = $role;
        return $this;
    }
	
	
	public function assignRoles(array $roles) {
        $this->_roles = $roles;
        return $this;
    }
	*/
	public function setPhone($phone) {
        $this->_phone = $phone;
        return $this;
    }
	
    public function setLogin($login) {
        $this->_login = $login;
        return $this;
    }
    
    public function setPassword($password) {
        $this->_password = $password;
        return $this;
    }
    
    public function setEmail($email) {
        $this->_email = $email;
        return $this;
    }
   /*
	public function getRoleId() {
        return $this->_role_id;
    }
	
	
	public function fetchRoles() {
        return $this->_roles;
    }
	*/
    public function getName() {
        return $this->_name;
    }
    
	public function getPhone() {
        return $this->_phone;
    }
	
    public function getLogin() {
        return $this->_login;
    }
    
    public function getPassword() {
        return $this->_password;
    }
    
    public function getEmail() {
        return $this->_email;
    }
    
    public function getResourceId() {
        return '';
    }
	public function getBlocked () {
		return $this->_blocked;
	}
	public function getSystem () {
		return $this->_system;
	}
}