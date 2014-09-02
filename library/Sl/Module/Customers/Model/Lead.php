<?php
namespace Sl\Module\Customers\Model;

class Lead extends \Sl_Model_Abstract {

	protected $_name;
	protected $_email;
    
    protected $_destination_country;
    protected $_destination_city;
    protected $_country;
    protected $_delivery_type;
    protected $_weight;
    protected $_volume;
    protected $_category;

	public function setName ($name) {
		$this->_name = $name;
		return $this;
	}
	public function setEmail ($email) {
		$this->_email = $email;
		return $this;
	}

	public function getName () {
		return $this->_name;
	}
	public function getEmail () {
		return $this->_email;
	}

    public function __toString() {
        return $this->getEmail();
    }
    
    public function setDestinationCountry($country) {
        $this->_destination_country = $country;
        return $this;
    }
    
    public function setDestinationCity($city) {
        $this->_destination_city = $city;
        return $this;
    }
    
    public function setCountry($country) {
        $this->_country = $country;
        return $this;
    }
    
    public function setDeliveryType($type) {
        $this->_delivery_type = $type;
        return $this;
    }
    
    public function setWeight($weight) {
        $this->_weight = $weight;
        return $this;
    }
    
    public function setVolume($volume) {
        $this->_volume = $volume;
        return $this;
    }
    
    public function setCategory($category) {
        $this->_category = $category;
        return $this;
    }
    
    public function getDestinationCountry() {
        return $this->_destination_country;
    }
    
    public function getDestinationCity() {
        return $this->_destination_city;
    }
    
    public function getCountry() {
        return $this->_country;
    }
    
    public function getDeliveryType() {
        return $this->_delivery_type;
    }
    
    public function getWeight() {
        return $this->_weight;
    }
    
    public function getVolume() {
        return $this->_volume;
    }
    
    public function getCategory() {
        return $this->_category;
    }
}