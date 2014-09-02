<?php
namespace Sl\Module\Home\Model;

class Cronjob extends \Sl_Model_Abstract {

	protected $_name;
	protected $_minute;
	protected $_hour;
	protected $_day;
	protected $_month;
	protected $_command;
	protected $_description;

    protected $_lists = array(
        'command' => 'empty',
    );
    
	public function setName ($name) {
		$this->_name = $name;
		return $this;
	}
	public function setMinute ($minute) {
		$this->_minute = $minute;
		return $this;
	}
	public function setHour ($hour) {
		$this->_hour = $hour;
		return $this;
	}
	public function setDay ($day) {
		$this->_day = $day;
		return $this;
	}
	public function setMonth ($month) {
		$this->_month = $month;
		return $this;
	}
	public function setCommand ($command) {
		$this->_command = $command;
		return $this;
	}
	public function setDescription ($description) {
		$this->_description = $description;
		return $this;
	}

	public function getName () {
		return $this->_name;
	}
	public function getMinute () {
		return $this->_minute;
	}
	public function getHour () {
		return $this->_hour;
	}
	public function getDay () {
		return $this->_day;
	}
	public function getMonth () {
		return $this->_month;
	}
	public function getCommand () {
		return $this->_command;
	}
	public function getDescription () {
		return $this->_description;
	}



}