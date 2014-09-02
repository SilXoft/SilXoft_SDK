<?php
namespace Sl\Model\Identity\Fieldset\Comparison;

use Sl\Model\Identity\Fieldset;

/**
 * Сравнение вхождения в диапазон
 */
class Between extends Fieldset\Comparison\Simple {
    
    /**
     * От
     * 
     * @var mixed
     */
    protected $_begin = false;
    
    /**
     * До
     * 
     * @var mixed 
     */
    protected $_end = false;
    
    /**
     * Константа для преобразования времени
     */
    const FORMAT_DATE = 'Y-m-d';
    
    public function getOperator() {
        return 'between';
    }

    protected function _stringValue() {
        $type = $this->getField()->getType();
        switch($type) {
            case 'date':
                return $this->getField(true).' '.$this->getOperator().' \''.$this->getBegin().'\' AND \''.$this->getEnd().'\'';
            case 'text':
            default:
                return 'CAST('.$this->getField(true).' AS UNSIGNED) '.$this->getOperator().' \''.$this->getBegin().'\' AND \''.$this->getEnd().'\'';
        }
        
    }
    
    /**
     * Установка значения ОТ.
     * Может принимать дату в формате, установленном константой
     * 
     * @param mixed $begin
     * @return \Sl\Model\Identity\Fieldset\Comparison\Between
     */
    public function setBegin($begin) {
        $o_begin = $begin;
        if(!($begin instanceof \DateTime)) {
            $begin = \DateTime::createFromFormat(self::FORMAT_DATE, $begin);
            if(!$begin) {
                $begin = $o_begin;
            }
        }
        $this->_begin = $begin;
        return $this;
    }
    
    /**
     * Установка значения ДО.
     * Может принимать дату в формате, установленном константой
     * 
     * @param mixed $end
     * @return \Sl\Model\Identity\Fieldset\Comparison\Between
     */
    public function setEnd($end) {
        $o_end = $end;
        if(!($end instanceof \DateTime)) {
            $end = \DateTime::createFromFormat(self::FORMAT_DATE, $end);
            if(!$end) {
                $end = $o_end;
            }
        }
        $this->_end = $end;
        return $this;
    }
    
    /**
     * Прокси для внутренних методов
     * 
     * @param mixed $value
     * @throws \Exception
     */
    public function setValue($value) {
        if(!is_array($value)) {
            throw new \Exception('Value for between filter must be array. '.__METHOD__);
        }
        if(count($value) < 2) {
            throw new \Exception('Value array too short to build compare. '.__METHOD__);
        }
        $this->setBegin(array_shift($value));
        $this->setEnd(array_shift($value));
    }
    
    /**
     * Возможность установки формата.<br />
     * Используется при выводе значений<br />
     * <b style="color: red;">Нужно бы доделать</b>
     * 
     * @return mixed
     */
    public function getExtension() {
        if(!isset($this->_extension)) {
            $this->setExtension(self::FORMAT_DATE);
        }
        return $this->_extension;
    }
    
    public function getBegin() {
        if($this->_begin instanceof \DateTime) {
            try {
                return $this->_begin->format($this->getExtension());
            } catch (\Exception $e) {
                return '0000-00-00';
            }
        }
        return $this->_begin;
    }
    
    public function getEnd() {
        if($this->_end instanceof \DateTime) {
            try {
                return $this->_end->format($this->getExtension());
            } catch (\Exception $e) {
                return '0000-00-00';
            }
        }
        return $this->_end;
    }
}