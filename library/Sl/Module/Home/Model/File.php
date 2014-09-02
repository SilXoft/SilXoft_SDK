<?php

namespace Sl\Module\Home\Model;

class File extends \Sl_Model_Abstract {

    protected $_name;
    protected $_type;
    protected $_location;

    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }

    public function setType($type) {
        $this->_type = $type;
        return $this;
    }
    
    public function getType() {
        return $this->_type;
    }
    
    public function setLocation($location) {
        $this->_location = $location;
        return $this;
    }
    
    public function getLocation() {
        return $this->_location;
    }

}