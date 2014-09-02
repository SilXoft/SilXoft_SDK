<?php
namespace Sl\Module\Home\Model;

class Address extends \Sl_Model_Abstract {

	protected $_name; 
protected $_locality;

 
protected $_type;


	protected $_region;
	protected $_street;
	protected $_zip;
	protected $_loged = false;
        protected $_lists = array('type' => 'home_address_type');

	

	

	public function setLocality ($locality) {
		$this->_locality = $locality;
		return $this;
	}

	public function setType ($type) {
		$this->_type = $type;
		return $this;
	}

	public function setName ($name) {
		$this->_name = $name;
		return $this;
	}
	public function setRegion ($region) {
		$this->_region = $region;
		return $this;
	}
	public function setStreet ($street) {
		$this->_street = $street;
		return $this;
	}
	public function setZip ($zip) {
		$this->_zip = $zip;
		return $this;
	}

	public function getName () {
		return $this->_name;
	}
	public function getRegion () {
		return $this->_region;
	}
	public function getStreet () {
		return $this->_street;
	}
	public function getZip () {
		return $this->_zip;
	}



	public function getType () {
		return $this->_type;
	}
	public function isEmpty() {
		
		$values = $this->toArray();	
		unset($values['active']);
		unset($values['create']);
                unset($values['type']);
		if (count(array_diff($values,array('')))) return false;
		
		$relations = $this->fetchRelated();
		foreach($relations as $relation => $relates){
			if (count($relates)) return false;
		}
		
		return true;
	}        
	public function getLocality () {
		return $this->_locality;
	}
}