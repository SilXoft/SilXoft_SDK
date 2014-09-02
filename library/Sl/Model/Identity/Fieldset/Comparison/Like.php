<?php
namespace Sl\Model\Identity\Fieldset\Comparison;

use Sl\Model\Identity\Fieldset;

/**
 * Сравнение ПОХОЖ НА
 * 
 */
class Like extends Fieldset\Comparison\Simple {
    
    /**
     * Признак отрицания
     * 
     * @var bool
     */
    protected $_negative = false;
    
    /**
     * Признак НАЧИНАЕТСЯ С
     * 
     * @var bool
     */
    protected $_begins = false;
    
    /**
     * Признак ЗАКАНЧИВАЕТСЯ НА
     * 
     * @var bool
     */
    protected $_ends = false;
    
    public function getOperator() {
        return 'like';
    }

    /**
     * Рализация родительского метода
     * 
     * @return string
     */
    protected function _stringValue() {
        $str  = $this->getField(true).' ';
        $str .= ($this->getExtension('n')?'not ':'').$this->getOperator().' ';
        $str .= '\''.($this->getExtension('b')?'':'%');
        $str .= $this->getValue();
        $str .= ($this->getExtension('e')?'':'%').'\'';
        return $str;
    }
    
    /**
     * Разбор расширения
     * 
     * @param string|array|bool $extension 
     */
    public function setExtension($extension) {
        if(false === $extension) {
            $this->_negative = false;
            $this->_begins = false;
            $this->_ends = false;
        } elseif(in_array($extension, array('negative', 'n'))) {
            $this->setNegative();
        } elseif(in_array($extension, array('begins', 'b'))) {
            $this->setBegins();
        } elseif(in_array($extension, array('ends', 'e'))) {
            $this->setEnds();
        }
        return $this;
    }
    
    public function setNegative() {
        $this->clearExtension();
        $this->_negative = true;
        return $this;
    }
    
    public function setBegins() {
        $this->clearExtension();
        $this->_begins = true;
        return $this;
    }
    
    public function setEnds() {
        $this->clearExtension();
        $this->_ends = true;
        return $this;
    }
    
    public function clearExtension() {
        return $this->setExtension(false);
    }
    
    /**
     * Возвращает соответствующий признак
     * 
     * @param mixed $extension
     * @return bool
     */
    public function getExtension($extension) {
        if($extension === false) {
            return ($this->_negative || $this->_begins || $this->_ends);
        } elseif(in_array($extension, array('negative', 'n'))) {
            return (bool) $this->_negative;
        } elseif(in_array($extension, array('begins', 'b'))) {
            return (bool) $this->_begins;
        } elseif(in_array($extension, array('ends', 'e'))) {
            return (bool) $this->_ends;
        }
    }
}