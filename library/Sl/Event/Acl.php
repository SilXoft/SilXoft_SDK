<?php

class Sl_Event_Acl extends Sl_Event_Abstract {
    
    protected $_acl;
    
    public function __construct($type, array $options = array()) {
        if (isset($options['acl']) && $options['acl'] instanceof \Zend_Acl)
        $this->setAcl($options['acl']);
        parent::__construct($type, $options);
    }
    
    /**
     * 
     * @param \Zend_Acl $acl
     */
    public function setAcl(\Zend_Acl $acl) {
        $this->_acl = $acl;
        return $this;
    }
    
    /**
     * 
     * @return \Zend_Acl
     */
    public function getAcl() {
        return $this->_acl;
    }
}

?>
