<?php
namespace Sl\Model\Identity\Field\Context;

class Listview extends \Sl\Model\Identity\Field\Context {
    
    protected $_sortable;
    protected $_searchable;
    protected $_hidable;
    protected $_width;
    protected $_visible;
    protected $_html;
    
    public function setSortable($sortable) {
        $this->_sortable = $sortable;
        return $this;
    }
    
    public function setSearchable($searchable) {
        $this->_searchable = $searchable;
        return $this;
    }
    
    public function setHidable($hidable) {
        $this->_hidable = $hidable;
        return $this;
    }
    
    public function setVisible($visible) {
        $this->_visible = $visible;
        return $this;
    }
    
    public function setHtml($html) {
        $this->_html = $html;
        return $this;
    }
    
    public function getSortable() {
        return $this->_sortable;
    }
    
    public function getSearchable() {
        return $this->_searchable;
    }
    
    public function getHidable() {
        return $this->_hidable;
    }
    
    public function getVisible() {
        if(isset($this->_visible)) {
            return (bool) $this->_visible; 
        }
        return true;
    }
    
    public function setWidth($width) {
        $this->_width = $width;
        return $this;
    }
    
    public function getWidth() {
        return $this->_width;
    }
    
    public function getHtml() {
        return $this->_html;
    }
}