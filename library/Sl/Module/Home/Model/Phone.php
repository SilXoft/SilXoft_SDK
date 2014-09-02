<?php
namespace Sl\Module\Home\Model;

class Phone extends \Sl_Model_Abstract {
    
    protected $_phone;
    protected $_main;
    
    public function setPhone($phone) {
        $this->_phone = $phone;
        return $this;
    }
    public function setMain($main) {
        $this->_main = $main;
        return $this;
    } 
    public function getMain () {
	return $this->_main;
	}
    public function getPhone() {
        return $this->_phone;
    }
    
    	public function isEmpty() {
		
		$values = $this->toArray();	
		unset($values['active']);
		unset($values['create']);
                unset($values['main']);
		if (count(array_diff($values,array('')))) return false;
		
		$relations = $this->fetchRelated();
		foreach($relations as $relation => $relates){
			if (count($relates)) return false;
		}
		
		return true;
	}
    
}

