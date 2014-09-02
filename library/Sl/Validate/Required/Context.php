<?php

namespace Sl\Validate\Required;

class Context extends \Zend_Validate_Abstract {

    protected $_context;

    const WRONG_CONTEXT = 'wrongContext';

    /**
     * Текст ошибки
     * @var array 
     */
    protected $_messageTemplates = array(
        self::WRONG_CONTEXT => 'Value has wrong context value',
    );

    public function __construct($context) {
        $this->_loadContext($context);
    }

    public function isValid($value, $context = null) {
        $result = true;
        foreach($this->_context as $field=>$values) {
            if(!$result) break;
            if(!in_array($context[$field], $values)) {
                $result = false;
                $this->_error(self::WRONG_CONTEXT);
            }
        }
        return $result;
    }
    
    protected function _loadContext($context) {
        if(!is_array($context)) {
            throw new \Exception('context must be an array of arrays where key is field name and values are valid values');
        }
        foreach($context as $field=>$values) {
            if(!is_array($values)) {
                $values = array($values);
            }
            $this->_context[$field] = $values;
        }
        return $this;
    }

}