<?php
namespace Sl\View;

abstract class Control {
    
    protected $_view;
    protected $_path;
    protected $_label;
    protected $_class;
    protected $_id;
    protected $_rel;
    protected $_icon_class;
    protected $_badge = false;
    protected $_badge_text;
    protected $_pull_right;
    protected $_attribs = array();
    protected $_options = array();
    
    public function __construct(array $options = array()) {
        $this->setOptions($options);
    }
    
    public function getRel() {
        return $this->_rel;
    }
    public function setRel($rel) {
        $this->_rel = $rel;
        return $this;
    }
    public function setIconClass($class) {
        $this->_icon_class = $class;
        return $this;
    }
    
    public function getIconClass() {
        return $this->_icon_class;
    }
    
    public function setClass($class) {
        $this->_class = $class;
        return $this;
    }
    
    public function getPullRight() {
        return $this->_pull_right;
    }
    
    public function setPullRight($pull_right) {
        $this->_pull_right = $pull_right;
        return $this;
    }
    
    public function getClass() {
        return $this->_class;
    }
    public function setId($id) {
        $this->_id = $id;
        return $this;
    }
    
    public function getId() {
        return $this->_id;
    }
    public function setOptions(array $options) {
        return $this->cleanOptions()->addOptions($options);
    }
    
    public function setBadge($badge) {
        
        $this->_badge = $badge;
        return $this;
    }
    
    public function getBadge() {
        return $this->_badge;
        
    }
    
    public function setBadgeText($badge_text) {
        
        $this->_badge_text = $badge_text;
        return $this;
    }
    
    public function getBadgeText() {
        return $this->_badge_text;
        
    }
    
    public function addOption($key, $value) {
        $method_name = 'set'.implode('', array_map('ucfirst', explode("_", $key)));
        if(method_exists($this, $method_name)) {
            try {
                $this->$method_name($value);
            } catch(\Exception $e) {
                // Не получилось :)
            }
        }
        return $this->setOption($key, $value);
    }
    
    public function setOption($key, $value) {
        $this->_options[$key] = $value;
        return $this;
    }
    
    public function addOptions(array $options) {
        foreach($options as $key=>$value) {
            $this->addOption($key, $value);
        }
        return $this;
    }
    
    public function cleanOptions() {
        $this->_options = array();
        return $this;
    }
    
    public function getOption($key) {
        if(isset($this->_options[$key])) {
            return $this->_options[$key];
        }
        return null;
    }
    
    public function getOptions() {
        return $this->_options;
    }
    
    public function __toString() {
        try {
            $this->_prepareViewData();
            
            return $this->getView()->render($this->getType().'.phtml');
        } catch(\Exception $e) {
            print_r($e->getMessage());
            die;    
            return '';
        }
    }
    
    protected function _prepareViewData() {
        $this->getView()->button = $this;
    }
    
    /**
     * 
     * @return \Sl_View
     */
    public function getView() {
        if(!isset($this->_view)) {
            $this->_view = new \Sl_View(array('scriptPath' =>LIBRARY_PATH.\Sl\Serializer\Serializer::SCRIPT_VIEW_BASE_PATH.'/Control/'));
        }
        return $this->_view;
    }
    
    public function setView(\Sl_View $view) {
        $this->_view = $view;
        return $this;
    }
    
    public function getPath() {
        if(!isset($this->_path)) {
            $this->_path = $this->getType().'.phtml';
        }
        return $this->_path;
    }
    
    public function setPath($path) {
        $this->_path = $path;
        return $this;
    }
    
    public function getType() {
        $matches = array();
        if(preg_match('/(\\\|_)/', get_class($this), $matches)) {
            $classname_sep = $matches[1];
            $classname_data = explode($classname_sep, get_class($this));
            $classname_data = array_slice($classname_data, 3);
            return lcfirst(implode('', array_map('ucfirst', $classname_data)));
        } else {
            throw new \Exception('Can\'t determine button type. '.__METHOD__);
        }
    }
    
    public function setLabel($label) {
        $this->_label = $label;
        return $this;
    }
    
    public function getLabel() {
        return $this->_label;
    }
    
    public function setAttrib($key, $value) {
        $this->_attribs[$key] = $value;
        return $this;
    }
    
    public function addAttrib($key, $value) {
        return $this->setAttrib($key, $value);
    }
    
    public function addAttribs(array $attribs) {
        foreach($attribs as $k=>$v) {
            $this->addAttrib($k, $v);
        }
        return $this;
    }
    
    public function cleanAttribs() {
        $this->_attribs = array();
        return $this;
    }
    
    public function setAttribs(array $attribs) {
        return $this    ->cleanAttribs()
                        ->addAttribs($attribs);
    }
    
    public function getAttribs() {
        return $this->_attribs;
    }
}