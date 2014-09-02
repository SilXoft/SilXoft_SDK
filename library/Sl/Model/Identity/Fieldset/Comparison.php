<?php
namespace Sl\Model\Identity\Fieldset;

/**
 * сравнение для поля
 * 
 */
abstract class Comparison {
    
    /**
     * Имя
     * 
     * @var string 
     */
    protected $_name;
    
    /**
     * Поле для сравнения
     * 
     * @var \Sl\Model\Identity\Field
     */
    protected $_field;
    
    /**
     * Значение для сравнения
     * 
     * @var mixed
     */
    protected $_value;
    
    /**
     * Набор полей
     * 
     * @var \Sl\Model\Identity\Fieldset
     */
    protected $_fieldset;
    
    /**
     * Признак пустоты
     * 
     * @var bool
     */
    protected $_empty;
    
    /**
     * Конструктор
     * 
     * @param array $data
     * @param \Sl\Model\Identity\Fieldset $fieldset
     */
    public function __construct(array $data = array(), \Sl\Model\Identity\Fieldset $fieldset = null) {
        if($fieldset) {
            $this->setFieldset($fieldset);
        }
        foreach($data as $key=>$value) {
            $method = \Sl_Model_Abstract::buildMethodName($key, 'set');
            if(method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
    
    /**
     * Установка набора полей
     * 
     * @param \Sl\Model\Identity\Fieldset $fieldset
     * @return \Sl\Model\Identity\Fieldset\Comparison
     */
    public function setFieldset(\Sl\Model\Identity\Fieldset $fieldset) {
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
     * Устанавливает имя сравнения
     * 
     * @param string $name
     * @return \Sl\Model\Identity\Fieldset\Comparison
     */
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }
    
    /**
     * Возвращает имя сравнения
     * 
     * @return string
     */
    public function getName() {
        return $this->_name;
    }
    
    /**
     * Устанавливает поле для сравнения
     * 
     * @param type $field
     * @return \Sl\Model\Identity\Fieldset\Comparison
     */
    public function setField($field) {
        if(!($field instanceof \Sl\Model\Identity\Field)) {
            if(is_string($field)) {
                if($this->getFieldset()) {
                    if($this->getFieldset()->getField($field)) {
                        $field = $this->getFieldset()->getField($field)->addRole('compare');
                    } else {
                        $field = $this->getFieldset()->createField($field)->addRole('compare');
                        //$this->getFieldset()->addField($field);
                    }
                } else {
                    $field = null;
                }
            } else {
                $field = null;
            }
        }
        $this->_field = $field;
        return $this;
    }
    
    /**
     * Возвращает поле сравнения
     * 
     * @return \Sl\Model\Identity\Field
     * @throws \Exception
     */
    public function getField() {
        if(!$this->_field || !($this->_field instanceof \Sl\Model\Identity\Field)) {
            throw new \Exception('Field not set for this comparison ('.get_class($this).'). '.__METHOD__);
        }
        return $this->_field;
    }
    
    /**
     * возвращает поле(я), попавшие под сравнение
     * 
     * @return type
     */
    public function getFilteredFields() {
        return $this->getField();
    }
    
    /**
     * Устанавливает значение сравнения
     * 
     * @param mixed $value
     * @return \Sl\Model\Identity\Fieldset\Comparison
     * @throws \Exception
     */
    public function setValue($value) {
        if(!$this->checkValue($value)) {
            throw new \Exception('Such value not supported. '.__METHOD__);
        }
        $this->_value = $value;
        return $this;
    }
    
    /**
     * Возвращает значение сравнения
     * 
     * @return mixed
     */
    public function getValue() {
        return $this->quoteValue($this->_value);
    }
    
    /**
     * Приводит сравнение к string-у
     * 
     * @return string
     */
    public function __toString() {
        try {
            return $this->_stringValue();
        } catch(\Exception $e) {
            return ' -error: '.$e->getMessage().'- ';
        }
    }
    
    /**
     * Проверка значения для поля
     * 
     * @param mixed $value
     * @return boolean
     */
    public function checkValue($value) {
        return true;
    }
    
    /**
     * Предобработка значения
     * 
     * @param mixed $value
     * @return mixed
     */
    public function quoteValue($value) {
        if($this->getField()->getType() == 'date') {
            return '\''.$value.'\'';
        }
        return $value;
    }
    
    /**
     * Установка флага пустоты
     * 
     * @param bool $empty
     * @return \Sl\Model\Identity\Fieldset\Comparison
     */
    public function setEmpty($empty) {
        $this->_empty = $empty;
        return $this;
    }
    
    /**
     * Возвращает флаг пустоты
     * 
     * @return type
     */
    public function getEmpty() {
        return $this->_empty;
    }
    
    /**
     * Получение строкового значения сравнения
     */
    abstract protected function _stringValue();
}