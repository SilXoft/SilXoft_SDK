<?php
namespace Sl\View\Control;

class Html extends \Sl\View\Control {
    
    public function setScriptBasePath(array $paths = array()) {
        foreach($paths as $path) {
            $this->getView()->addScriptPath($path);
        }
        return $this;
    }
    
    public function __toString() {
        try {
            $this->_prepareViewData();
            return $this->getView()->partial($this->getPath(), array('button' => $this));
        } catch(\Exception $e) {
            print_r($e->getMessage());
            die;    
            return '';
        }
    }
}