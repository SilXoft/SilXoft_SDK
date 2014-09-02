<?php
namespace Sl\Module\Menu\Event;

class Breadcrumbs extends \Sl_Event_Abstract {
    
    protected $_html;
    protected $_request;
    protected $_buttons = array();
    
    public function __construct($type, array $options = array()) {
        if(!isset($options['html']) || !is_string($options['html'])) {
            throw new \Exception('Param \'html\' is required');
        }
        if(isset($options['buttons']) && is_array($options['html'])) {
            $this->setButtons($options['buttons']);
        }
        if(isset($options['request']) && ($options['request'] instanceof \Zend_Controller_Request_Abstract)) {
            $this->setRequest($options['request']);
        }
        $this->setHtml($options['html']);
        parent::__construct($type, $options);
    }
    
    /**
     * 
     * @param string $html
     */
    public function setHtml($html) {
        $this->_html = $html;
    }
    
    /**
     * 
     * @return string
     */
    public function getHtml() {
        return $this->_html;
    }
    
    /**
     * 
     * @param array $buttons
     */
    public function setButtons(array $buttons) {
        $this->_buttons = $buttons;
    }
    
    public function getButtons() {
        return $this->_buttons;
    }
    
    /**
     * 
     * @param \Zend_Controller_Request_Abstract $request
     * @return \Sl\Module\Menu\Event\Breadcrumbs
     */
    public function setRequest(\Zend_Controller_Request_Abstract $request) {
        $this->_request = $request;
        return $this;
    }
    
    /**
     * 
     * @return \Zend_Controller_Request_Abstract
     */
    public function getRequest() {
        return $this->_request;
    }
    
    public function getCurrentModule($as_object = false) {
        if($this->getRequest()) {
            if($as_object) {
                try {
                    return \Sl_Module_Manager::getInstance()->getModule($this->getCurrentModule());
                } catch(\Exception $e) {
                    return null;
                }
            } else {
                return $this->getRequest()->getModuleName();
            }
        }
        return null;
    }
    
    public function getCurrentAction() {
        if($this->getRequest()) {
            return $this->getRequest()->getActionName();
        }
        return null;
    }
    
    public function getCurrentController() {
        if($this->getRequest()) {
            return $this->getRequest()->getControllerName();
        }
        return null;
    }
    
    public function nowInModule($module) {
        if($module instanceof \Sl_Module_Abstract) {
            return ($this->getCurrentModule() == $module->getName());
        } else {
            return ($this->getCurrentModule() == strval($module));
        }
    }
    
    public function nowInController($controller) {
        return ($this->getCurrentController() == strval($controller));
    }
    
    public function nowInAction($action) {
        return ($this->getCurrentAction() == strval($action));
    }
    
    public function isIdBasedAction() {
        if($this->getRequest()) {
            return (bool) $this->getRequest()->getParam('id', false);
        }
        return false;
    }
    
    public function getSuperType() {
        return 'menu';
    }
    
    public function inIframe() {
        return (bool) $this->getRequest()->getParam('is_iframe', false);
    }
}