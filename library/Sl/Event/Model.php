<?php

class Sl_Event_Model extends Sl_Event_Abstract {
    
    protected $_model;
	protected $_model_before_update;
	protected $_extra;
    
    public function __construct($type, array $options = array()) {
        if(!isset($options['model']) || !($options['model'] instanceof \Sl_Model_Abstract)) {
            throw new Sl_Exception_Model('Param \'model\' is required');
        }
		
		
        $this->setModel($options['model']);
		
		
		$this->setModelBeforeUpdate($options['model_before_update']);
		
		
		if (isset($options['extra']) && is_array($options['extra']))
		$this->setExtra($options['extra']);
		
        parent::__construct($type, $options);
    }
    
    /**
     * 
     * @param Sl_Model_Abstract $model
     */
    public function setModel(\Sl_Model_Abstract $model) {
        $this->_model = $model;
		
    }
    

	
    /**
     * 
     * @return Sl_Model_Abstract
     */
    public function getModel() {
        return $this->_model;
    }
	
		
    /**
     * 
     * @return Sl_Model_Abstract
     */
    public function getModelBeforeUpdate() {
        return $this->_model_before_update;
    }
	
	 /** 
     * @param Sl_Model_Abstract $model
     */
    public function setModelBeforeUpdate(\Sl_Model_Abstract $model) {
        $this->_model_before_update = $model;
		
    }
    


	
}
?>
