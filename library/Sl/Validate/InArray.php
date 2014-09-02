<?php
namespace Sl\Validate;

use Sl_Model_Abstract as AbstractModel;

class InArray extends \Zend_Validate_Abstract {
    
    protected $_inverse = false;
    protected $_haystack = array();
    
    const IN_ARRAY = 'slInArray';
    const NOT_IN_ARRAY = 'slNotInArray';
    
    protected $_messageTemplates = array(
        self::NOT_IN_ARRAY => 'Value must not be one of: %values%',
        self::IN_ARRAY => 'Value must be one of: %values%'
    );
    
    protected $_messageVariables = array(
        'values' => 'values',
    );
    
    protected $values = '';
    
    public function __construct(array $options = array()) {
        foreach($options as $name=>$data) {
            $method_name = AbstractModel::buildMethodName($name, 'set');
            if(method_exists($this, $method_name)) {
                $this->$method_name($data);
            }
        }
    }
    
    public function setHaystack(array $haystack = array()) {
        $this->_haystack = $haystack;
        $this->values = implode(', ', $this->getHaystack());
        return $this;
    }
    
    public function getHaystack() {
        return $this->_haystack;
    }
    
    public function setInverse($inverse = true) {
        $this->_inverse = (bool) $inverse;
        return $this;
    }
    
    public function getInverse() {
        return $this->_inverse;
    }

    public function isValid($value) {
        $in_array = in_array($value, $this->getHaystack());
        if(!$this->getInverse()) {
            if(!$in_array) {
                $this->_error(self::IN_ARRAY);
                return false;
            }
            return true;
        } else {
            if($in_array) {
                $this->_error(self::NOT_IN_ARRAY);
                return false;
            }
            return true;
        }
    }

}