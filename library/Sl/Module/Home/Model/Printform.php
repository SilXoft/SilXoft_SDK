<?php

namespace Sl\Module\Home\Model;

class Printform extends \Sl_Model_Abstract {

    protected $_name;
    protected $_type;
    protected $_description;
    protected $_mask;
    protected $_data;
    
    protected $_role;
    protected $_lists = array(
        'type' => 'home_printform_types',
        'role' => 'home_printform_roles'
    );
    const EMAIL_ROLE = 1;
    const STANDART_ROLE = 0;
    const GROUP_ROLE = 2;
    
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }
    
    public function setRole($role) {
        $this->_role = $role;
        return $this;
    }
    
    public function setType($type) {
        $this->_type = $type;
        return $this;
    }

    public function setDescription($description) {
        $this->_description = $description;
        return $this;
    }
    
    public function setMask($mask) {
        $this->_mask = $mask;
        return $this;
    }

    public function setData($data) {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $this->_data = $data;
        return $this;
    }


    public function getName() {
        return $this->_name;
    }

    public function getType($pretty = false) {
        return $this->_type;
    }

    public function getDescription() {
        return $this->_description;
    }
    
    public function getMask() {
        return $this->_mask;
    }
    public function getData($as_array = false) {
        if ($as_array) {
            return (array) json_decode($this->getData(), true);
        }
        return $this->_data;
    }

    public function isEmail() {
        return $this->_role == self::EMAIL_ROLE;
    }
    
    public function getRole(){
        return $this->_role;
    } 
    
}