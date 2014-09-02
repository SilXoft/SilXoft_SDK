<?php
namespace Sl\Model\Identity\Field\Context;

class Html extends \Sl\Model\Identity\Field\Context {
    
    
    protected $_visible = true;
    protected  $_filled;
    
    public function __construct(){
        parent::__construct();
        $this->setType('html');    
    }
    
    public function getVisible() {
        return $this->_visible;
    }
    
    public function isFilled() {
        return $this->_filled;
    }
    
    public function setVisible($visible) {
        $this->_visible = $visible;
        return $this;
    }
    
   
}