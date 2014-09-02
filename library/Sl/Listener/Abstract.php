<?php

/**
 * Абстрактный слушатель событий
 */
abstract class Sl_Listener_Abstract {
    
    protected $_module;
    protected $_translator;
    
    public function __construct(Sl_Module_Abstract $module) {
        $this->_module = $module;
    }
    
    /**
     * 
     * @return Sl_Module_Abstract
     */
    public function getModule() {
        return $this->_module;
    }
    
    /**
     * 
     * @param \Zend_Translate $translator
     * @return \Sl_Listener_Abstract
     */
    public function setTranslator(\Zend_Translate $translator) {
        $this->_translator = $translator;
        return $this;
    }
    
    /**
     * 
     * @return \Zend_Translate
     */
    public function getTranslator() {
        if(!isset($this->_translator)) {
            $this->setTranslator(\Zend_Registry::get('Zend_Translate'));
        }
        return $this->_translator;
    }
    
    /**
     * Возвращает поддерживаемые события
     * 
     * @return type
     */
    public function getSupportedEvents($debug = false) {
        $result = array();
        foreach(class_implements($this) as $interface) {
            $sep = preg_replace('/.+(\\\|_).+/', '$1', $interface);
            $interface_data = @array_slice(explode($sep, $interface), 2);
            if($sep == '_') {
                array_pop($interface_data); // Убираем "Interface"
            }
            if(count($interface_data) > 1) {
                $result[] = strtolower(array_pop($interface_data));
            }
            $super_type = array_shift($interface_data);
            $result[] = strtolower($super_type);
        }
        return array_unique($result);
    }
}

?>
