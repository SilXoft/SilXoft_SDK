<?php
namespace Sl\Model\Identity\Field;

abstract class Helper {
    
    protected $_field;
    protected $_type;
    protected $_options = array();
    
    public function __construct(\Sl\Model\Identity\Field $field, array $options = array()) {
        $this->_field = $field;
        $this->setOptions($options);
    }
    
    public function cleanOptions() {
        $this->_options = array();
        return $this;
    }
    
    public function addOption($name, $value) {
        if(isset($this->_options[$name])) {
            throw new \Exception('Such option already set. '.__METHOD__);
        }
        $this->_options[$name] = $value;
        return $this;
    }
    
    public function setOption($name, $value) {
        try {
            return $this->addOption($name, $value);
        } catch (\Exception $e) {
            $this->_options[$name] = $value;
            return $this;
        }
    }
    
    public function addOptions(array $options = array(), $rewrite = false) {
        foreach($options as $name=>$value) {
            try {
                $this->addOption($name, $value);
            } catch (\Exception $e) {
                if($rewrite) {
                    $this->setOption($name, $value);
                }
            }
        }
        return $this;
    }
    
    public function setOptions(array $options = array()) {
        $this->cleanOptions()->addOptions($options);
    }
    
    public function getOptions() {
        return $this->_options;
    }
    
    public function getOption($name, $default = null) {
        return isset($this->_options[$name])?$this->_options[$name]:$default;
    }
    
    public function getField() {
        return $this->_field;
    }
    
    public function getType() {
        if(!isset($this->_type)) {
            $this->_type = lcfirst(str_replace(__NAMESPACE__.'\\Helper\\', '', get_class($this)));
        }
        return $this->_type;
    }
}