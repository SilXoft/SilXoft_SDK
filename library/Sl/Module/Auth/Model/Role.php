<?php
namespace Sl\Module\Auth\Model;

class Role extends \Sl_Model_Abstract  {
    
    protected $_name;
    protected $_parent;
	protected $_description='';
   
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }
    
    public function setParent($parent) {
        $this->_parent = $parent;
        return $this;
    }
	
	public function setDescription($description) {
        $this->_description = $description;
        return $this;
    }
	
    public function getName() {
        return $this->_name;
    }
    
    public function getParent() {
        return $this->_parent;
    }
	
	public function getDescription() {
        return $this->_description;
    }
    
    public function isValid() {
        $result = parent::isValid();
        $result &= (bool) $this->getName();
        return $result;
    }
}
