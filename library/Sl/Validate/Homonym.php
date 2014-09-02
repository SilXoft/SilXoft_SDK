<?php
namespace Sl\Validate;

class Homonym extends \Zend_Validate_Abstract {
    
    protected $_check_fields = array();
    protected $_model;
    
    const EXISTS = 'alreadyExists';
   

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::EXISTS => "Such field combination alredy exists",
       
    );
    
    public function __construct(array $options = array()) {
        if(!isset($options['model']) || !($options['model'] instanceof \Sl_Model_Abstract)) {
            throw new \Exception('Model param is required. '.__METHOD__);
        }
        $this->setModel($options['model']);
        unset($options['model']);
        
        if(isset($options['fields']) && is_array($options['fields'])) {
            $this->setCheckFields($options['fields']);
        } else {
            $this->setCheckFields($options);
        }
    }
    
    public function setCheckFields(array $fields = array()) {
        return $this->cleanCheckFields()->addCheckFields($fields);
    }
    
    public function addCheckField($field) {
        $this->_check_fields[] = (string) $field;
        $this->_check_fields = array_unique($this->_check_fields);
        return $this;
    }
    
    public function getCheckFields() {
        return $this->_check_fields;
    }
    
    public function addCheckFields(array $fields = array()) {
        foreach($fields as $field) {
            $this->addCheckField($field);
        }
        return $this;
    }
    
    public function cleanCheckFields() {
        $this->_check_fields = array();
        return $this;
    }
    
    public function isValid($value, $context = null) {
        if(is_null($context)) {
            return false;
        }
        $identity = \Sl_Model_Factory::identity($this->getModel());
        
        try {
            foreach($this->getCheckFields() as $field_name) {
                $matches = array();
                $operator = 'eq';
                
                $value = isset($context[$field_name])?$context[$field_name]:'';
                if(preg_match('/^(modulerelation_([^-]+))_(.+)$/', $field_name, $matches)) {
                    if(isset($context[$matches[1]])) {
                        if(is_array($context[$matches[1]])) {
                            $operator = 'in';
                            $value = array_diff(array_map(function($el) use($matches) { return isset($el[$matches[3]])?$el[$matches[3]]:''; }, $context[$matches[1]]), array(''));
                        } else {
                            $value = $context[$matches[1]];
                        }
                    } else {
                        $value = '';
                    }
                    if($value) {
                        $identity->field(array($matches[2] => $matches[3]))->$operator($value);
                    } else {
                        $identity->field(array($matches[2] => $matches[3]))->isnull(true);
                    }
                } else {
                    if(isset($context[$field_name])) {
                        $value = $context[$field_name];
                    } else {
                        $value = '';
                    }
                    $identity->field($field_name)->$operator($value);
                }
            }
        } catch(\Exception $e) {
            return false;
        }
        
        $identity = \Sl_Model_Factory::mapper($this->getModel())->fetchAllExtended($identity);
        if(count($identity->getData(true))) {
            $this->_setValue($value);
            $this->_error(self::EXISTS);
            return false;
        }
        return true;
    }
    
    public function setModel(\Sl_Model_Abstract $model) {
        $this->_model = $model;
        return $this;
    }
    
    public function getModel() {
        return $this->_model;
    }
}