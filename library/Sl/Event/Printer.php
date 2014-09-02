<?php

namespace Sl\Event;

class Printer extends \Sl_Event_Abstract {
    
    protected $_model;
    protected $_printer;
    protected $_printform;
    
    
    public function __construct($type, array $options = array()) { 
        if(!isset($options['model']) || !($options['model'] instanceof \Sl_Model_Abstract)) {
            throw new \Sl_Exception_Model('Param \'model\' is required');
        }
        if(!isset($options['printer']) || !($options['printer'] instanceof \Sl\Printer\Printer)) {
            throw new \Sl_Exception_Model('Param \'printer\' is required');
        }
        if(!isset($options['printform']) || !($options['printform'] instanceof \Sl\Module\Home\Model\Printform)) {
            throw new \Sl_Exception_Model('Param \'prinform\' is required');
        }
        
        $this->setModel($options['model']);
        $this->setPrinter($options['printer']);
        $this->setPrintform($options['printform']);  
        
        parent::__construct($type, $options);
        
    }
     public function setModel(\Sl_Model_Abstract $model) {
        $this->_model = $model;
		return $this;
		
    }
    


    public function getModel() {
        return $this->_model;
        
  }
  
  public function setPrinter(\Sl\Printer\Printer $printer) {
        $this->_printer = $printer;
		return $this;
		
    }
    
    
    /**
     * 
     * @return \Sl\Printer\Printer
     */
    public function getPrinter() {
        return $this->_printer;
  }
  
  public function setPrintform(\Sl\Module\Home\Model\Printform $printform){
        $this->_printform = $printform;
		return $this;
		
    }
    


    public function getPrintform() {
        return $this->_printform;
  }
  
  
   
}
