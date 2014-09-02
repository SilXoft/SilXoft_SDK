<?php

class Sl_Event_Error extends Sl_Event_Abstract {
    
    protected $_exceptions;
    protected $_request;
	

    
    public function __construct($type, array $options = array()) {
        if(!isset($options['exceptions'])) {
            throw new Sl_Exception_Model('Param \'exceptions\' is required');
        }
		if(!isset($options['request'])) {
            throw new Sl_Exception_Model('Param \'request\' is required');
        }
		
        $this->setExceptions($options['exceptions']);
        $this->setRequest($options['request']);
		
        parent::__construct($type, $options);
    }
    
    
    public function setExceptions($exceptions) {
        $this->_exceptions = $exceptions;
		return $this;
		
    }
    public function setRequest($request) {
        $this->_request = $request;
        return $this;
        
    }

	
    
    public function getExceptions() {
        return $this->_exceptions;
    }
	
	public function getRequest() {
        return $this->_request;
    }
    

	
}
?>
