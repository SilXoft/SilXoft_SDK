<?php

class Sl_Event_Bootstrap extends Sl_Event_Abstract {
    
    protected $_layout;
    
    public function getLayout() {
        return $this->_layout;
    }
    
    public function __construct($type, array $options = array()) {
        if(isset($options['layout']) && ($options['layout'] instanceof Zend_Layout)) {
            $this->_layout = $options['layout'];
        }
        parent::__construct($type, $options);
    }
}

?>
