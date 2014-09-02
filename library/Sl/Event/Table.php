<?php
namespace Sl\Event;

class Table extends \Sl_Event_Abstract {
    
    protected $_query;
    protected $_model;
    
    public function __construct($type, array $options = array()) {
        if(!isset($options['query']) || !($options['query'] instanceof \Zend_Db_Select)) {
            throw new \Exception('Param \'query\' is required');
        }
        $this->setQuery($options['query']);
        if(!isset($options['model']) || !($options['model'] instanceof \Sl_Model_Abstract)) {
            throw new \Exception('Param \'model\' is required');
        }
        $this->setModel($options['model']);
        parent::__construct($type, $options);
    }
    
    /**
     * 
     * @param \Zend_Db_Select $query
     */
    public function setQuery(\Zend_Db_Select $query) {
        $this->_query = $query;
    }
    
    /**
     * 
     * @return \Zend_Db_Select
     */
    public function getQuery() {
        return $this->_query;
    }
    
    /**
     * 
     * @param \Sl_Model_Abstract $model
     */
    public function setModel(\Sl_Model_Abstract $model) {
        $this->_model = $model;
    }
    
    /**
     * 
     * @return \Sl_Model_Abstract
     */
    public function getModel() {
        return $this->_model;
    }
}