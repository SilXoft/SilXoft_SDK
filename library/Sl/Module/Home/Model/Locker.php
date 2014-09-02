<?php
namespace Sl\Module\Home\Model;

class Locker extends \Sl_Model_Abstract {
    
    protected $_name;
    protected $_user_id;
	
	protected $loged = false; //не вести логи моделі
	
    public function setName($name) {
    		
    	if (is_array($name) || $name instanceof \Sl_Model_Abstract){	
	    	if (is_array($name)){
	    		$obj_class_name = $name[0];
				$obj_id = $name[1]; 
	    	}elseif($name instanceof \Sl_Model_Abstract){
	    		$obj_class_name = get_class($name);
				$obj_id = $name->getId(); 
	    	}
			$name = md5($obj_class_name.$obj_id);
		}
		
        $this->_name = $name;
        return $this;
    }
    public function setUserId($user_id) {
        $this->_user_id = $user_id;
        return $this;
    }
    public function getName() {
        return $this->_name;
    }
    
	public function getUserId() {
        return $this->_user_id;
    }
	
}

