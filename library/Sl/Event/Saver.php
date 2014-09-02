<?php
namespace Sl\Event;

class Saver extends \Sl_Event_Abstract {
    
    /**
     *
     * @var array
     */
    protected $_data = array();
    
    /**
     *
     * @var \Sl_Model_Abstract
     */
    protected $_model;
    
    /**
     *
     * @var \Sl\Form\Form
     */
    protected $_form;
    
    /**
     *
     * @var array
     */
    protected $_shared = array();
    
    /**
     *
     * @var bool
     */
    protected $_check_existence;
    
    public function __construct($type, array $options = array()) {
        parent::__construct($type, $options);
        if(!isset($options['model']) || !($options['model'] instanceof \Sl_Model_Abstract)) {
            throw new \Exception('"model" param is required. '.__METHOD__);
        }
        $this->setModel($options['model']);
        foreach($options as $name=>$value) {
            $method_name = \Sl_Model_Abstract::buildMethodName($name, 'set');
            if(method_exists($this, $method_name)) {
                $this->$method_name($value);
            }
        }
    }
    
    /**
     * 
     * @param array $data
     * @return \Sl\Event\Saver
     */
    public function setData(array $data = array()) {
        $this->_data = $data;
        return $this;
    }
    
    /**
     * 
     * @param \Sl_Model_Abstract $model
     * @return \Sl\Event\Saver
     */
    public function setModel(\Sl_Model_Abstract $model = null) {
        $this->_model = $model;
        return $this;
    }
    
    /**
     * 
     * @param \Sl\Form\Form $form
     * @return \Sl\Event\Saver
     */
    public function setForm(\Sl\Form\Form $form = null) {
        $this->_form = $form;
        return $this;
    }
    
    /**
     * 
     * @param array $shared
     * @return \Sl\Event\Saver
     */
    public function setShared(array $shared = array()) {
        $this->_shared = $shared;
        return $this;
    }
    
    /**
     * 
     * @param bool $check_existence
     * @return \Sl\Event\Saver
     */
    public function setCheckExistence($check_existence) {
        $this->_check_existence = $check_existence;
        return $this;
    }


    /**
     * 
     * @return array
     */
    public function getData() {
        return $this->_data;
    }
    
    /**
     * 
     * @return \Sl_Model_Abstract
     */
    public function getModel() {
        return $this->_model;
    }
    
    /**
     * 
     * @return \Sl\Form\Form
     */
    public function getForm() {
        return $this->_form;
    }
    
    /**
     * 
     * @return array
     */
    public function getShared() {
        return $this->_shared;
    }
    
    /**
     * 
     * @return bool
     */
    public function getCheckExistence() {
        return (bool) $this->_check_existence;
    }
}