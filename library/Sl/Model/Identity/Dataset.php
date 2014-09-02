<?php
namespace Sl\Model\Identity;

/**
 * Набор данных
 * 
 * @uses \Sl\Model\Identity\Fieldset Description
 */
abstract class Dataset {
    
    /**
     * Необработанные данные
     * 
     * @var array
     */
    protected $_raw_data;
    
    /**
     * Обработанные данные
     * 
     * @var array
     */
    protected $_data;
    
    /**
     * Флаг обработки данных
     * 
     * @var bool
     */
    protected $_processed;
    
    /**
     * Опции
     * 
     * @var type 
     */
    protected $_options = array();
    
    /**
     * Набор полей, для которого строяться данные
     * 
     * @var \Sl\Model\Identity\Fieldset
     */
    protected $_fieldset;
    
    /**
     * Добавление опции
     * 
     * @param string $name Ключ
     * @param mixed $value Значение
     * @param bool $force Перезаписать, если значение с таким ключем уже установлено
     * @return \Sl\Model\Identity\Dataset 
     * @throws \Exception Если ключ занят и не установлен параметр $force
     */
    public function addOption($name, $value, $force = false) {
        if(isset($this->_options[$name]) && !$force) {
            throw new \Exception('Option "'.$name.'" alredy set. Use force param or set method. '.__METHOD__);
        }
        $this->_options[$name] = $value;
        return $this;
    }
    
    /**
     * Добавление опции с перезаписью
     * @see addOption()
     * 
     * @param string $name
     * @param string $value
     * @return \Sl\Model\Identity\Dataset
     */
    public function setOption($name, $value) {
        return $this->addOption($name, $value, true);
    }
    
    /**
     * Возвращает значение по ключу
     * 
     * @param string $name
     * @return mixed
     */
    public function getOption($name, $default = null) {
        return isset($this->_options[$name])?$this->_options[$name]:$default;
    }
    
    /**
     * Чистит опции
     * 
     * @return \Sl\Model\Identity\Dataset
     */
    public function cleanOptions() {
        $this->_options = array();
        return $this;
    }
    
    /**
     * Добавляет массив опций
     * @see addOption()
     * 
     * @param array $options
     * @return \Sl\Model\Identity\Dataset
     */
    public function addOptions(array $options = array()) {
        foreach($options as $name=>$value) {
            $this->addOption($name, $value);
        }
        
        return $this;
    }
    
    /**
     * Устанавливает опции
     * @see addOptions()
     * 
     * @param array $options
     * @return \Sl\Model\Identity\Dataset
     */
    public function setOptions(array $options = array()) {
        return $this->cleanOptions()->addOptions($options);
    }
    
    /**
     * Возварает все установленные опции
     * 
     * @return mixed[]
     */
    public function getOptions() {
        return $this->_options;
    }
    
    /**
     * Устанавливает набор полей
     * 
     * @param \Sl\Model\Identity\Fieldset $fieldset Набор полей
     * @return \Sl\Model\Identity\Dataset
     * @throws \Exception Если данные уже обработаны
     */
    public function setFieldset(Fieldset $fieldset) {
        if($this->getProcessed()) {
            throw new \Exception('Data already processed. You can\'t set anouther fieldset now. '.__METHOD__);
        }
        $this->_fieldset = $fieldset;
        return $this;
    }
    
    /**
     * Возвращает набор полей
     * 
     * @return \Sl\Model\Identity\Fieldset
     */
    public function getFieldset() {
        return $this->_fieldset;
    }
    
    /**
     * Устанавливает необработанные данные
     * 
     * @param array $data
     * @return \Sl\Model\Identity\Dataset
     */
    public function setData(array $data = array()) {
        $this->_raw_data = $data;
        $this->_setProcessed(false);
        return $this;
    }
    
    /**
     * Возвращает данные
     * 
     * @param bool $raw Флаг обработанные/не обработанные. По-умолчанию false
     * @return array
     */
    public function getData($raw = false) {
        if($raw) {
            return $this->_raw_data;
        } else {
            
            $this->_process();
            return is_array($this->_data)?$this->_data:array();
        }
    }
    
    /**
     * Устанавливает флаг обработки
     * 
     * @param bool $processed
     * @return \Sl\Model\Identity\Dataset
     */
    protected function _setProcessed($processed) {
        $this->_processed = (bool) $processed;
        return $this;
    }
    
    /**
     * Возвращает флаг обработки данных
     * 
     * @return bool
     */
    public function getProcessed() {
        return $this->_processed;
    }
    
    /**
     * Функция обработки данных
     * 
     */
    protected function _process() {
        if(!$this->getProcessed()) {
            \Sl_Event_Manager::trigger(new \Sl\Event\Dataset('beforeProcessAll', array(
                'dataset' => $this
            )));
            foreach($this->_raw_data as $key=>$item) {
                $beforeEvent = new \Sl\Event\Dataset('beforeProcessItem', array(
                    'dataset' => $this,
                    'item' => $item,
                    'key' => $key
                ));
                
                \Sl_Event_Manager::trigger($beforeEvent);
                
                $_item = $this->_processItem($beforeEvent->getItem(), $beforeEvent->getKey());
                
                $afterEvent = new \Sl\Event\Dataset('afterProcessItem', array(
                    'dataset' => $this,
                    'item' => $_item,
                    'key' => $beforeEvent->getKey()
                ));
                \Sl_Event_Manager::trigger($afterEvent);
				
                $this->_data[(int) $afterEvent->getKey()] = $afterEvent->getItem();
            }
            \Sl_Event_Manager::trigger(new \Sl\Event\Dataset('afterProcessAll', array(
                'dataset' => $this
            )));
        }
    }
    
    
    
    /**
     * Для реализации конкретным классом
     */
    abstract protected function _processItem($item, $key);
}
