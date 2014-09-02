<?php
namespace Sl\Module\Api\Event;

class Api extends \Sl_Event_Abstract {
    
    /**
     *
     * @var \Zend_Controller_Request_Abstract
     */
    protected $_request;
    
    /**
     *
     * @var array
     */
    protected $_data = array();
    
    /**
     *
     * @var bool
     */
    protected $_result;
    
    protected $_error_message;
    
    public function __construct($type, array $options = array()) {
        parent::__construct($type, $options);
        foreach($options as $name=>$data) {
            $method_name = \Sl_Model_Abstract::buildMethodName($name, 'set');
            if(method_exists($this, $method_name)) {
                $this->$method_name($data);
            }
        }
    }
    
    /**
     * 
     * @param \Zend_Controller_Request_Abstract $request
     * @return type
     */
    public function setRequest(\Zend_Controller_Request_Abstract $request) {
        $this->_request = $request;
        return $pthis;
    }
    
    /**
     * 
     * @param type $result
     * @return \Sl\Module\Api\Event\Api
     */
    public function setResult($result) {
        $this->_result = $result;
        return $this;
    }
    
    /**
     * 
     * @param array $data
     * @return \Sl\Module\Api\Event\Api
     */
    public function setData(array $data = array()) {
        $this->_data = $data;
        return $this;
    }
    
    public function setErrorMessage($message) {
        $this->_error_message = $message;
        return $this;
    }
    
    /**
     * 
     * @return \Zend_Controller_Request_Abstract
     */
    public function getRequest() {
        return $this->_request;
    }
    
    /**
     * 
     * @return bool
     */
    public function getResult() {
        return (bool) $this->_result;
    }
    
    /**
     * 
     * @return array
     */
    public function getData() {
        return (array) $this->_data;
    }
    
    public function getErrorMessage() {
        return $this->_error_message;
    }
    
}