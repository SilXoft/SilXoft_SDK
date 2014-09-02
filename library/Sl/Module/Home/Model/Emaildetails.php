<?php
namespace Sl\Module\Home\Model;

class Emaildetails extends \Sl_Model_Abstract {

	protected $_name;
        protected $_company;
        protected $_country;
        protected $_city;
        protected $_phone;
        protected $_ballans;

	public function setName ($name) {
		$this->_name = $name;
		return $this;
	}
	public function setCountry ($country) {
		$this->_country = $country;
		return $this;
	}        
	public function setCompany ($company) {
		$this->_company = $company;
		return $this;
	}
	public function setCity ($city) {
		$this->_city = $city;
		return $this;
	}
	public function setPhone ($phone) {
		$this->_phone = $phone;
		return $this;
	}
	public function setBallans ($ballans) {
		$this->_ballans = $ballans;
		return $this;
	}        
	public function getName () {
		return $this->_name;
	}
	public function getCountry () {
                return $this->_country;
		 
	}        
	public function getCompany () {
		return $this->_company;
	}
	public function getCity () {
		return $this->_city;
	}
	public function getPhone () {
		return $this->_phone;
	}
	public function getBallans () {
		return $this->_ballans;
	}     


}