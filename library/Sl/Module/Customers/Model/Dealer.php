<?php
namespace Sl\Module\Customers\Model;

class Dealer extends \Sl_Model_Abstract {
    
    protected $_name;
    protected $_type;
	
	protected $_lists = array(
		'type'=>'DealerTypes'
	);
	
	
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }
    


	public function setType($type) {
        $this->_type = $type;
        return $this;
    }
	

   
    public function getName() {
        return $this->_name;
    }
    
	public function getType() {
        return $this->_type;
    }
	
	 
  
}

