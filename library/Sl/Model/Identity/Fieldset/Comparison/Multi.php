<?php
namespace Sl\Model\Identity\Fieldset\Comparison;

use Sl\Model\Identity\Fieldset;

/**
 * Агрегатор сравнений
 * 
 */
class Multi extends Fieldset\Comparison {
    
    /**
     * Массив сравнений
     * 
     * @var Sl\Model\Identity\Fieldset\Comparison[]
     */
    protected $_comps = array();
    
    /**
     * Признак сравнения
     * 
     * @var int
     */
    protected $_comp;
    
    /**
     * Сравнение И
     */
    const COMPARISON_AND = 1;
    
    /**
     * Сравнение ИЛИ
     */
    const COMPARISON_OR = 2;
    
    /**
     * Возвращает строковые значения всех агрегированых значений, конкатенированных признаком сравнения
     * 
     * @return string
     */
    protected function _stringValue() {
        $glue = ($this->getComparison() == self::COMPARISON_AND)?' AND ':' OR ';
        if(!$this->getEmpty()) {
            return '('.implode($glue, array_map(function($el) { return (string) $el; }, $this->getComps())).')';
        } else {
            return '(1 '.(($this->getComparison() === self::COMPARISON_OR)?'=':'<>').' 1)';
        }
    }

    /**
     * Добавление сравнения
     * 
     * @param \Sl\Model\Identity\Fieldset\Comparison $comp
     * @param string $name
     * @return \Sl\Model\Identity\Fieldset\Comparison\Multi
     */
    protected function addComp(Fieldset\Comparison $comp, $name = null) {
        if(is_null($name)) {
            $name= $comp->getName();
        }
        if($name) {
            if(isset($this->_comps[$name])) {
                $old = $this->_comps[$name];
                $this->_comps[$name] = new Fieldset\Comparison\Multi(array(
                    $old,
                    $comp,
                ));
            } else {
                $this->_comps[$name] = $comp;
            }
        } else {
            $this->_comps[] = $comp;
        }
        return $this;
    }
    
    /**
     * Ароксирует запрос к агрегированым сравнениям
     * 
     * @return bool
     */
    public function getEmpty() {
        $empty = true;
        foreach($this->getComps() as $comp) {
            $empty &= $comp->getEmpty();
        }
        return $empty;
    }
    
    /**
     * Добавляет сравнения
     * @see addComp()
     * 
     * @param array $comps
     * @param type $fieldset
     * @return \Sl\Model\Identity\Fieldset\Comparison\Multi
     */
    public function addComps(array $comps = array(), $fieldset = null) {
        $fieldset = $fieldset?$fieldset:$this->getFieldset();
        foreach($comps as $name=>$comp) {
            if($comp instanceof Fieldset\Comparison) {
                $this->addComp($comp, $name);
            } elseif(is_array($comp)) {
                try {
                    $this->addComp(Fieldset\Comparison\Factory::build($comp, $fieldset), $name);
                } catch (\Exception $e) {
                    // Nothing to do
                }
            }
        }
        return $this;
    }
    
    /**
     * Очищает сравнения
     * 
     * @return \Sl\Model\Identity\Fieldset\Comparison\Multi
     */
    public function cleanComps() {
        $this->_comps = array();
        return $this;
    }
    
    /**
     * Установка сравнений. Тоже, что и addComps, только с предварительной очисткой
     * 
     * @param \Sl\Model\Identity\Fieldset\Comparison[] $comps
     * @param \Sl\Model\Identity\Fieldset $fieldset
     * @return type
     */
    public function setComps(array $comps = array(), $fieldset = null) {
        return $this->cleanComps()->addComps($comps, $fieldset);
    }
    
    /**
     * Возвращает массив сравнений
     * 
     * @return \Sl\Model\Identity\Fieldset\Comparison[]
     */
    public function getComps() {
        return $this->_comps;
    }
    
    /**
     * Возвращает сравнение по имени
     * 
     * @param string $name
     * @return \Sl\Model\Identity\Fieldset\Comparison
     */
    public function getComp($name) {
        return isset($this->_comps[$name])?$this->_comps[$name]:null;
    }
    
    /**
     * Устанавливыает признак сравнения
     * 
     * @param int $comp
     * @return \Sl\Model\Identity\Fieldset\Comparison\Multi
     */
    public function setComparison($comp = null) {
        $this->_comp = $comp;
        return $this;
    }
    
    /**
     * Проксирует ко всем агрегируемым полям
     * 
     * @return array
     */
    public function getFilteredFields() {
        return array_map(function($el) { return $el->getFilteredFields().''; }, $this->getComps());
    }
    
    /**
     * Возвращает признак сравнения
     * 
     * @return int
     */
    public function getComparison() {
        if(is_null($this->_comp) || !in_array($this->_comp, array(self::COMPARISON_AND, self::COMPARISON_OR))) {
            $this->_comp = self::COMPARISON_AND;
        }
        return $this->_comp;
    }
    
    /**
     * Заглушка
     * 
     * @param type $field
     */
    public function setField($field) {
        $this->__notImplemented();
    }
    
    /**
     * Заглушка
     * 
     */
    public function getField() {
        $this->__notImplemented();
    }
    
    /**
     * Заглушка
     * 
     * @param type $value
     */
    public function setValue($value) {
        $this->__notImplemented();
    }
    
    /**
     * Заглушка
     * 
     */
    public function getValue() {
        $this->__notImplemented();
    }
    
    /**
     * Заглушка
     * 
     * @param type $value
     */
    public function checkValue($value) {
        $this->__notImplemented();
    }
    
    /**
     * Исключение для заглушек
     * 
     * @throws \Exception
     */
    private function __notImplemented() {
        throw new \Exception('Not implemented. '.__METHOD__);
    }
}
