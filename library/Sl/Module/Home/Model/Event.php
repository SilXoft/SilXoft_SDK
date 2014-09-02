<?php
namespace Sl\Module\Home\Model;

class Event extends \Sl_Model_Abstract {

	protected $_description;
	protected $_detail_description;
	protected $_request;
	protected $_user_id;
	protected $_loged = false;

	public function setDescription ($description) {
		$this->_description = $description;
		return $this;
	}
	public function setDetailDescription ($detail_description) {
		$this->_detail_description = $detail_description;
		return $this;
	}
	public function setRequest ($request) {
		$this->_request = $request;
		return $this;
	}
	public function setUserId ($user_id) {
		$this->_user_id = $user_id;
		return $this;
	}

	public function getDescription () {
		return $this->_description;
	}
	public function getDetailDescription () {
		return $this->_detail_description;
	}
	public function getRequest () {
		return $this->_request;
	}
	public function getUserId () {
		return $this->_user_id;
	}



}