<?php
namespace Sl\Module\Home\Model;

class City extends \Sl_Model_Abstract {
    
    protected $_name;
    protected $_code;
	
	
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }
    public function setCode($code) {
        $this->_code = $code;
        return $this;
    }
    public function getName() {
        return $this->_name;
    }
    
	public function getCode() {
        return $this->_code;
    }
	
}

