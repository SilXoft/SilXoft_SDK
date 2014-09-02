<?php

class Sl_Event_Router extends Sl_Event_Abstract {

    protected $_router;
    protected $_request;

    /**
     * 
     * @return \Zend_Controller_Router_Rewrite
     */
    public function getRouter() {
        return $this->_router;
    }

    /**
     * 
     * @return \Zend_Controller_Request_Http
     */
    public function getRequest() {
        return $this->_request;
    }

    public function __construct($type, array $options = array()) {
        if (isset($options['router']) && ($options['router'] instanceof Zend_Controller_Router_Rewrite)) {
            $this->_router = $options['router'];
        }
        if (isset($options['request']) && ($options['request'] instanceof Zend_Controller_Request_Abstract)) {
            $this->_request = $options['request'];
        }
        parent::__construct($type, $options);
    }

}

?>
