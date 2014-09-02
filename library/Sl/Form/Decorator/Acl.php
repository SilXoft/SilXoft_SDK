<?php

class Sl_Form_Decorator_Acl extends Zend_Form_Decorator_Abstract {
    
    protected $_acl;
    
    public function __construct($options = null) {
        if(!isset($options['acl'])) {
            throw new Sl_Exception_Decorator('Option "acl" must be set');
        }
        $this->setAcl($options['acl']);
        unset($options['acl']);
        parent::__construct($options);
    }
    
    public function setAcl(Zend_Acl $acl) {
        $this->_acl = $acl;
    }
    
    /**
     * 
     * @return Zend_Acl
     */
    public function getAcl() {
        return $this->_acl;
    }
    
    public function render($content) {
        return $content;
        if($this->getAcl()->isAllowed()) {
            return $content;
        } else {
            $this->getElement()->clearDecorators();
            return 'Access denied !!!';
        }
    }
}

?>
