<?php

class Sl_Event_Informer extends Sl_Event_Abstract {
    
    protected $_informer;
	

    
    public function __construct($type, array $options = array()) {
        if(!isset($options['informer']) || !($options['informer'] instanceof \Sl\Module\Home\Informer\Informer)) {
            throw new Sl_Exception_Model('Param \'informer\' is required');
        }
		
		
        $this->setInformer($options['informer']);
		
        parent::__construct($type, $options);
    }
    
    /**
     * 
     * @param \Sl\Module\Home\Informer\Informer $informer
     */
    public function setInformer(\Sl\Module\Home\Informer\Informer $informer) {
        $this->_informer = $informer;
		return $this;
		
    }
    

	
    /**
     * 
     * @return \Sl\Module\Home\Informer\Informer
     */
    public function getInformer() {
        return $this->_informer;
    }
	
	

	
}
?>
