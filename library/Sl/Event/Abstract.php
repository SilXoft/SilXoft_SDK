<?php

/**
 * Абстрактное событие
 */
abstract class Sl_Event_Abstract {
    
    protected $_type;
    protected $_options;
    protected $_translator;
    
    /**
     * Конструктор
     * @param mixed $type тип события. Должно приводиться к типу string
     * @throws Sl_Exception_Event
     */
    public function __construct($type, array $options = array()) {
        $type = strval($type);
        if(!$type) {
            throw new Sl_Exception_Event('Error when determine type');
        }
        $this->setType($type)->setOptions($options);
    }
    
    /**
     * Устанавливает тип события. ПО типу определяется метод для запуска этим событием
     * @param string $type
     * @return Sl_Event_Abstract
     */
    public function setType($type) {
        $this->_type = $type;
        return $this;
    }
    
    /**
     * Возвращает тип события
     * @return string
     */
    public function getType() {
        return $this->_type;
    }
    
    /**
     * Возвращает супертип, который вычтсляется из имени класса.
     * @return string
     */
    public function getSuperType() {
        $class_name = get_class($this); 
        if(preg_match('/\\\/', $class_name)) {
            $data = explode('\\', $class_name);
            //array_shift($data); // Sl
            //array_shift($data); // Event
            while(strtolower(array_shift($data)) != 'event') {
                
            }
            $data = array_map('strtolower', $data);
            $name = array_shift($data);
        } else {
            $data = explode('_', preg_replace('/^Sl_Event_(.+)$/', '$1', $class_name));
            $data = array_map('strtolower', $data);
            $name = array_shift($data);
        }
        while(count($data)) {
            $name .= ucfirst(array_shift($data));
        }
        return $name;
    }
    
    /**
     * Установка свойств события
     * @param array $options
     * @return \Sl_Event_Abstract
     */
    public function setOptions(array $options) {
        $this->_options = $options;
        return $this;
    }
    
    /**
     * Возвращает все свойства
     * @return array
     */
    public function getOptions() {
        return $this->_options?$this->_options:array();
    }
    
    /**
     * Возвращает конкретную опцию
     * @param string $name
     * @return mixed|null
     */
    public function getOption($name) {
        if(!isset($this->_options[$name])) return null;
        return $this->_options[$name];
    }
    
    public function getTranslator() {
        if(!isset($this->_translator)) {
            $this->_translator = \Zend_Registry::get('Zend_Translate');
        }
        return $this->_translator;
    }
    
    public function setTranslator(\Zend_Translate $translator) {
        $this->_translator = $translator;
        return $this;
    }
}

?>
