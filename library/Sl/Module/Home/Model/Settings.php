<?php
namespace Sl\Module\Home\Model;

class Settings extends \Sl_Model_Abstract {

	protected $_name;
	protected $_value;
	protected $_type;

	public function setName ($name) {
		$this->_name = $name;
		return $this;
	}
	public function setValue ($value) {
		$this->_value = $value;
		return $this;
	}
	public function setType ($type) {
		$this->_type = $type;
		return $this;
	}

	public function getName () {
		return $this->_name;
	}
	public function getValue () {
		return $this->_value;
	}
	public function getType () {
		return $this->_type;
	}



}