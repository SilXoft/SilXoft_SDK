<?php
namespace  Sl\View\Control;

class Simple extends \Sl\View\Control {
    
    protected $_id;
    protected $_like_btn;
    protected $_on_click;
    protected $_class_span;
    protected $_pull_rigth = true;
    protected $_icon = true;
    public function setId($id) {
        $this->_id = $id;
        return $this;
    }

    public function getIcon() {
        return $this->_icon;
    }    
    public function getOnClick() {
        return $this->_on_click;
    }     
    public function getClassSpan() {
        return $this->_class_span;
    }      
    public function getId() {
        return $this->_id;
    }
    public function setClassSpan($class_span) {
        $this->_class_span = $class_span;
        return $this;
    }    
    public function setIcon($icon = true) {
        $this->_icon = $icon;
        return $this;
    }
    public function setOnclick($on_click = '') {
        $this->_on_click = $on_click;
        return $this;
    }    
    public function setLikeBtn($like_btn = true) {
        $this->_like_btn = $like_btn;
        return $this;
    }
    
    public function getLikeBtn() {
        return $this->_like_btn;
    }
    
    protected function _prepareViewData() {
        parent::_prepareViewData();
        $attribs = array();
        foreach($this->getAttribs() as $k=>$v) {
            $attribs[] = $k.'="'.$this->getView()->escape($v).'"';
        }
        $this->getView()->attribs = implode(' ', $attribs);
    }
}