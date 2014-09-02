<?php
namespace Sl\Model\Identity\Fieldset\Comparison;

use Sl\Model\Identity\Fieldset;

class Isnull extends Fieldset\Comparison\Simple {
    
    public function getOperator() {
        return 'IS '.($this->getValue()?'':' NOT ').' NULL';
    }

    protected function _stringValue() {
        return $this->getField(true).' '.$this->getOperator();
    }
    
    public function getValue() {
        if($this->_value === 'false') {
            $this->_value = false;
        }
        return parent::getValue();
    }
}