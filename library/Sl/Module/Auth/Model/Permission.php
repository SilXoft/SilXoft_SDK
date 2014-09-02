<?php
namespace Sl\Module\Auth\Model;

class Permission extends \Sl_Model_Abstract  {
    
    protected $_role_id;
	protected $_resource_id;
	protected $_privilege;
	protected $_assert='';
   	
   
	 
   
    public function setRoleId($role_id) {
        $this->_role_id = $role_id;
        return $this;
    }
	public function setResourceId($resource_id) {
        $this->_resource_id = $resource_id;
        return $this;
    }
	public function setPrivilege($privilege) {
        $this->_privilege = $privilege;
        return $this;
    }
	
	public function setAssert($assert) {
        $this->_assert = $assert;
        return $this;
    }
	
  
    public function getRoleId() {
        return $this->_role_id;
    }
	public function getResourceId() {
        return $this->_resource_id;
    }
	public function getPrivilege() {
        return $this->_privilege;
    }
	
	public function getAssert() {
        return $this->_assert;
    }
	
	/**
	 * Возвращает объект в виде массива
	 * @return array
	 */
	public function toArray() {
		$methods = get_class_methods($this);
		$getters = array();
		foreach ($methods as $k => $method) {
			if (preg_match('/^get/', $method)) {
				$getters[] = $method;
			}
		}
		$result = array();
		foreach ($getters as $getter) {
			$name = explode('_', strtolower(preg_replace('/([A-Z])/', '_$1', $getter)));
			array_shift($name);
			$name = implode('_', $name);
			$result[$name] = $this -> $getter();
		}
		return $result;
	}
  
	
}

