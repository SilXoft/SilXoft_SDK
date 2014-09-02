<?php
namespace Sl\Model\Identity\Fieldset\Comparison;

use Sl\Model\Identity\Fieldset;

class Eq extends Fieldset\Comparison\Simple {
    
    public function getOperator() {
        return $this->getExtension()?'<>':'=';
    }
    
    public function quoteValue($value) {
        switch($this->getField()->getType()) {
            case 'hidden':
            case 'text':
                return is_int($value)?$value:('\''.strval($value).'\'');
            default:
                return '\''.strval($value).'\'';
        }
    }
    
    public function getExtension() {
        return in_array($this->_extension, array('n', 'not', 'negative'));
    }

}