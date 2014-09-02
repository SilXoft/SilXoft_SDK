<?php

namespace Sl\Event;

class OnlineCalculator extends \Sl_Event_Abstract {
    
    
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
    
   
}
