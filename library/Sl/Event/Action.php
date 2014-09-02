<?php

class Sl_Event_Action extends Sl_Event_Abstract {
    
    protected $_model;
	protected $_view;

    
    public function __construct($type, array $options = array()) {
        if(!isset($options['model']) || !($options['model'] instanceof \Sl_Model_Abstract)) {
            throw new Sl_Exception_Model('Param \'model\' is required');
        }
		
		if(!isset($options['view']) || !($options['view'] instanceof \Sl_View)) {
            throw new Sl_Exception_Model('Param \'view\' is required');
        }
		
        $this->setModel($options['model'])->setView($options['view']);
		
        parent::__construct($type, $options);
    }
    
    /**
     * 
     * @param Sl_Model_Abstract $model
     */
    public function setModel(\Sl_Model_Abstract $model) {
        $this->_model = $model;
		return $this;
		
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
     * @param Sl_View $view
     */
    public function setView(\Sl_View $view) {
        $this->_view = $view;
		return $this;
    }
    

	
    /**
     * 
     * @return Sl_View
     */
    public function getView() {
        return $this->_view;
    }
	

	
}
?>
