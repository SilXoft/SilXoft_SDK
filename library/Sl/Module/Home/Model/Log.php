<?php
namespace Sl\Module\Home\Model;

class Log extends \Sl_Model_Abstract {
    
    
    protected $_field_name;
	protected $_old_value;
    protected $_new_value;
    protected $_user_id;
	protected $_object_id=0;
	protected $_action;
	
	protected $loged = false; //не вести логи моделі
	
	public function setFieldName($field_name) {
        $this->_field_name = $field_name;
        return $this;
    }
	
	public function setOldValue($old_value) {
        $this->_old_value = $old_value;
        return $this;
    }
	public function setNewValue($new_value) {
        $this->_new_value = $new_value;
        return $this;
    }
	public function setUserId($user_id) {
        $this->_user_id = $user_id;
        return $this;
    }
    
	public function setObjectId($object_id) {
        $this->_object_id = $object_id;
        return $this;
    }
	
	public function setAction($action) {
        $this->_action = $action;
        return $this;
    }
	
 	public function getFieldName() {
       return $this->_field_name;
    }
	
	public function getOldValue() {
       return $this->_old_value;
    }
	public function getNewValue() {
       return $this->_new_value;
    }
 
	public function getUserId() {
        return $this->_user_id;
    }
	
	public function getObjectId() {
        return $this->_object_id;
    }
	
	public function getAction() {
        return $this->_action;
    }
}

