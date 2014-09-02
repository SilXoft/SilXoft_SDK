<?php
namespace Sl\Event;

class Modelaction extends \Sl_Event_Abstract {
    
    protected $_request;
    protected $_view;
    protected $_model;
    
    public function __construct($type, array $options = array()) {
        if (!isset($options['model']) || !($options['model'] instanceof \Sl_Model_Abstract)) {
            throw new \Exception($this->getTranslator()->translate('Param \'model\' is required'));
        }

        if (!isset($options['view']) || !($options['view'] instanceof \Sl_View)) {
            throw new \Exception($this->getTranslator()->translate('Param \'view\' is required'));
        }

        if(!isset($options['request']) || !($options['request'] instanceof \Zend_Controller_Request_Abstract)) {
            throw new \Exception($this->getTranslator()->translate('Param \'request\' is required'));
        }
        $this->setModel($options['model'])->setView($options['view'])->setRequest($options['request']);
        parent::__construct($type, $options);
    }
    
    public function setModel(\Sl_Model_Abstract $model) {
        $this->_model = $model;
        return $this;
    }
    
    /**
     * Возвращает модель
     * 
     * @return \Sl_Model_Abstract
     */
    public function getModel() {
        return $this->_model;
    }
    
    public function setView(\Sl_View $view) {
        $this->_view = $view;
        return $this;
    }
    
    /**
     * Возвращает объект вида
     * 
     * @return \Sl_View
     */
    public function getView() {
        return $this->_view;
    }
    
    public function setRequest(\Zend_Controller_Request_Abstract $request) {
        $this->_request = $request;
        return $this;
    }
    
    /**
     * Возвращает объект запроса
     * 
     * @return \Zend_Controller_Request_Abstract
     */
    public function getRequest() {
        return $this->_request;
    }
    
    /**
     * 
     * @return string
     */
    public function getCurrentAction() {
        return $this->getRequest()->getActionName();
    }
    
    /**
     * 
     * @return bool
     */
    public function isAjax() {
        return preg_match('/^ajax.+$/', $this->getCurrentAction());
    }
    
    /**
     * 
     * @param string $action
     * @return bool
     */
    public function nowIn($action) {
        return ($this->getCurrentAction() == $action);
    }
}