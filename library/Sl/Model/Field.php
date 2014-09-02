<?php
namespace Sl\Model;

/**
 * 
 * @todo Поменять сравнения на объекты \Sl\Model\Field\Operator\Operator
 * 
 */
class Field {
    
    protected $_name = null;
    protected $_operator = null;
    protected $_comps = array();
    
    protected $_operators = array();
    
    protected $_sort = false;
    protected $_sort_dir = 'asc';
    
    public function __construct($fieldname) {
        $this->_setName($fieldname);
    }
    
    public function addTest($operator, $value, $and = true) {
        if(is_string($value)) {
            $this->_comps[] = array(
                'name' => $this->_getName(),
                'operator' => $operator,
                'value' => $value,
                'and' => $and
            );
        } else {
            $this->_comps[] = array(
                'name' => $this->_getName(),
                'operator' => $operator,
                'operator2' => isset($value['operator2'])?$value['operator2']:'',
                'name2' => isset($value['name2'])?$value['name2']:'',
                'value2' => isset($value['values2'])?$value['values2']:'',
                'related' => isset($value['related'])?$value['related']:'',
                'value' => isset($value['value'])?$value['value']:$value,
                'and' => $and
            );
        }
       // $this->_operators[] = Field\Operator\Factory::get($operator)->setValues($value);
    }
    
    public function getComps() {
        return $this->_comps;
    }
    
    public function getSortString() {
        return $this->_getName().' '.$this->_sort_dir;
    }
    
    public function isIncomplete() {
        return empty($this->_comps);
    }
    
    public function sort($dir) {
        $this->_sort = true;
        $this->_sort_dir = $dir;
    }
    
    public function isSorted() {
        return $this->_sort?$this->_sort_dir:false;
    }
    
    protected function _setName($name) {
        $this->_name = $name;
        return $this;
    }
    
    protected function _getName() {
        return $this->_name;
    }
}