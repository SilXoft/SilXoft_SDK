<?php
namespace Sl\Model\Identity\Fieldset\Comparison;

/**
 * Шаблон простого сравнения
 * 
 */
abstract class Simple extends \Sl\Model\Identity\Fieldset\Comparison {
    
    /**
     * Расширение
     * 
     * @var mixed
     */
    protected $_extension;
    
    /**
     * Установка расширения
     * 
     * @param mixed $extension
     * @return \Sl\Model\Identity\Fieldset\Comparison\Simple
     */
    public function setExtension($extension) {
        $this->_extension = $extension;
        return $this;
    }
    
    /**
     * Возвращает расширение
     * 
     * @return mixed
     */
    public function getExtension() {
        return $this->_extension;
    }
    
    /**
     * Реализация родительского метода
     * 
     * @return string
     */
    protected function _stringValue() {
        return $this->getField(true).' '.$this->getOperator().' '.$this->getValue();
    }
    
    /**
     * Расширение родительского getField()
     * @see parent::getField()
     * 
     * @param bool $with_alias
     * @return mixed
     */
    public function getField($with_alias = false) {
        if($with_alias) {
            return \Sl\Model\DbTable\DbTable::buildDestinationAlias($this->getField());
        }
        return parent::getField();
    }
    
    /**
     * Поле всегда непустое
     * 
     * @return boolean
     */
    public function getEmpty() {
        return false;
    }
    
    abstract public function getOperator();
}