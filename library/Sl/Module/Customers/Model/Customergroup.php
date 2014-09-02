<?php
namespace Sl\Module\Customers\Model;

class Customergroup extends \Sl_Model_Abstract {

	protected $_name;

	public function setName ($name) {
		$this->_name = $name;
		return $this;
	}

	public function getName () {
		return $this->_name;
	}



}