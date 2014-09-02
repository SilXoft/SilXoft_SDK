<?php
namespace Sl\Model\Identity\Fieldset\Comparison;

class In extends \Sl\Model\Identity\Fieldset\Comparison\Simple {

    protected function _stringValue() {
        if ($this -> getEmpty()) {
            return '1 <> 1';
        }
        
        $values = is_array($this -> getValue()) ? $this -> getValue() : array($this -> getValue());
        $extra = '';
        if (false !== ($ind = array_search('null', $values))) {
            $extra = $this -> getField(true) . ' IS'.($this->getExtension()?' NOT':'').' NULL ';
            unset($values[$ind]);

        }
        if (count($values)) {
            return '('.$this -> getField(true) . ' ' . $this -> getOperator() . ' (' . implode(', ', $values) . ')'.(strlen($extra)?' OR '.$extra:'').')';
        } else {
            return $extra;    
        }

        
    }
    
    public function getEmpty() {
        return (count((array)$this -> getValue()) === 0);
    }

    public function getOperator() {
        return ($this->getExtension() ? ' NOT ' : '').' IN ';
    }

}
