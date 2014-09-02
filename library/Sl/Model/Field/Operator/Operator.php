<?php
namespace Sl\Model\Field\Operator;

abstract class Operator {
    
    protected $_name;
    protected $_values = array();
    
    public function getName() {
        return $this->_name;
    }
    
    protected function setName($name) {
        $this->_name = $name;
        return $this;
    }
    
    public function setValues($values) {
        if(is_array($values)) {
            foreach($values as $value) {
                $this->addValue($value);
            }
        } else {
            $this->setValue($values);
        }
        return $this;
    }
    
    public function setValue($value) {
        $this->_values = array($value);
    }
    
    public function addValue($value) {
        $this->_values[] = $value;
        return $this;
    }
    
    public function cleanValues() {
        $this->_values = array();
        return $this;
    }
    
    public function getValues() {
        return $this->_values;
    }
    
    public function getValue() {
        return current($this->getValues());
    }
    
    public function getWhereTemplate() {
        return ' '.$this->_buildWhereTemplate().' ';
    }
    
    protected abstract function _buildWhereTemplate();
}