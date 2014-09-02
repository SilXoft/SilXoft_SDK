<?php

class Sl_Plugin_Eventer extends Zend_Controller_Plugin_Abstract {
    
    protected $_em;
    protected static $_default_em;
    
    public function __construct(Sl_Event_Manager $em = null) {
        if(!$em) {
            $em = self::getDefaultEventManager();
        }
        $this->_em = $em?$em:$this->getDefaultEventManager();
    }
    
    public static function setDefaultEventManager(Sl_Event_Manager $em) {
        self::$_default_em;
    }
    
    public static function getDefaultEventManager() {
        if(!isset(self::$_default_em)) {
            self::$_default_em = Sl_Event_Manager::getInstance();
        }
        return self::$_default_em;
    }
    
    public function routeStartup(\Zend_Controller_Request_Abstract $request) {
        $this->_em->triggerEvent(new Sl_Event_Router('routeStartup', array(
            'request' => $request,
        )));
        parent::routeStartup($request);
    }
    
    public function routeShutdown(\Zend_Controller_Request_Abstract $request) {
        $this->_em->triggerEvent(new Sl_Event_Router('routeShutdown', array(
            'request' => $request,
        )));
        parent::routeShutdown($request);
    }
    
    public function preDispatch(\Zend_Controller_Request_Abstract $request) {
        $this->_em->triggerEvent(new Sl_Event_Router('preDispatch', array(
            'request' => $request,
        )));
        parent::preDispatch($request);
    }
    
    public function postDispatch(\Zend_Controller_Request_Abstract $request) {
        $this->_em->triggerEvent(new Sl_Event_Router('postDispatch', array(
            'request' => $request,
        )));
        parent::postDispatch($request);
    }
    
    public function dispatchLoopStartup(\Zend_Controller_Request_Abstract $request) {
        $this->_em->triggerEvent(new Sl_Event_Router('dispatchLoopStartup', array(
            'request' => $request,
        )));
        parent::dispatchLoopStartup($request);
    }
    
    public function dispatchLoopShutdown() {
        $this->_em->triggerEvent(new Sl_Event_Router('dispatchLoopShutdown'));
        parent::dispatchLoopShutdown();
    }
    
    public function getRequest() {
        $this->_em->triggerEvent(new Sl_Event_Router('getRequest'));
        parent::getRequest();
    }
    
    public function getResponse() {
        $this->_em->triggerEvent(new Sl_Event_Router('getResponse'));
        parent::getResponse();
    }
    
    public function setRequest(\Zend_Controller_Request_Abstract $request) {
        $this->_em->triggerEvent(new Sl_Event_Router('setRequest', array(
            'request' => $request,
        )));
        parent::setRequest($request);
    }
    
    public function setResponse(\Zend_Controller_Response_Abstract $response) {
        $this->_em->triggerEvent(new Sl_Event_Router('setResponse', array(
            'response' => $response,
        )));
        parent::setResponse($response);
    }
}

?>
