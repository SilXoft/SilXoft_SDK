<?php
namespace Sl\View\Control\Lists;

class Item {
    
    protected $_href;
    protected $_rel;
    protected $_label;
    protected $_html_name;
    protected $_html_id;
    protected $_class;
    protected $_icon;
    protected $_subitems = array();
    protected $_data = array();
    
    public function __construct(array $options = array()) {
        foreach($options as $key=>$value) {
            $method_name = 'set'.implode('', array_map('ucfirst', explode("_", $key)));
            if(method_exists($this, $method_name)) {
                try {
                    $this->$method_name($value);
                } catch(\Exception $e) {
                    // Не получилось :)
                }
            }
        }
    }
    public function getSubitems(){
        return $this->_subitems;
    }
    public function getIcon(){
        return $this->_icon;
    }
    public function setIcon($icon){
        $this->_icon = $icon;
        return $this;
    }
    public function setSubitems(array $subitems){
        $this->_subitems = $subitems;
        return $this;
    }
    
    public function addSubitems($subitem){
        $this->_subitems[] = $subitem;
        return $this;
    }
    
    public function setData($data) {
        if(is_array($data)) {
            $this->_data = $data;
        } else {
            $this->_data[] = $data;
        }
        return $this;
    }
    
    public function getData($pretty = false) {
        if($pretty) {
            $data = array();
            foreach($this->getData() as $k=>$v) {
                $data[] = 'data-'.$k.'="'.$v.'"';
            }
            return implode(' ', $data);
        }
        return $this->_data;
    }
    
    public function setHref($href) {
        $this->_href = $href;
        return $this;
    }
    
    public function setLabel($label) {
        $this->_label = $label;
        return $this;
    }
    
    public function setHtmlName($name) {
        $this->_html_name = $name;
        return $this;
    }
    
    public function setHtmlId($id) {
        $this->_html_id = $id;
        return $this;
    }
    
    public function setRel($rel) {
        $this->_rel = $rel;
        return $this;
    }
    public function setClass($class){
         $this->_class = $class;
         return $this;
    }
        public function getHref() {
        return $this->_href;
    }
    
    public function getLabel() {
        return $this->_label;
    }
    
    public function getHtmlName() {
        return $this->_html_name;
    }
    
    public function getHtmlId() {
        if($this->_html_id) {
            return $this->_html_id;
        } else {
            return $this->getHtmlName();
        }
    }
    
    public function getRel() {
        return $this->_rel;
    }
    
    public function getClass(){
        return $this->_class;
    }
}