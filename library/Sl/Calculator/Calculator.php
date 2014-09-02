<?php
namespace Sl\Calculator;
/**
 * Базовый калькулятор
 * 
 */
abstract class Calculator {

    /**
     * Название/имя базовой модели
     * 
     * @var string
     */
    protected $_model_name;
    
    /**
     * Базовая модель
     * 
     * @var \Sl_Model_Abstract 
     */
    protected $_model;
    
    /**
     * Необходимые поля
     * 
     * @var array
     */
    protected $_required_fields = array();
    
    /**
     * Поля для обновления
     * 
     * @var array
     */
    protected $_updated_fields = array();
    
    
    /**
     * Измененные поля
     * 
     * @var array
     */
    protected $_changed_fields = array();
    
    /**
     * Предупреждения
     * 
     * @var array
     */
    protected $_warnings = array();

    /**
     * Строит свое имя основываясь на классе
     * 
     * @return string
     */
    public function getName() {
        $array = explode('\\', get_class($this));
        return mb_strtolower(array_pop($array));
    }

    /**
     * Возвращает название/имя модели
     * 
     * @return string
     */
    public function getModelName() {
        return $this->_model_name;
    }

    /**
     * Возвращаеи базовую модель
     * 
     * @return \Sl_Model_Abstract
     */
    public function getModel() {
        if(!is_object($this->_model)) {
            $model_name = $this->getModelName();
            $this->_model = \Sl_Model_Factory::object($model_name);
        }
        return $this->_model;
    }

    /**
     * Наполняет модель данными
     * 
     * @param array|\Sl_Model_Abstract $values
     */
    protected function fillModel($values) {
        if (is_array($values))
            $this->_model->setOptions($values);
        elseif ($values instanceof $this->_model_name)
            $this->_model = $values;
    }

    /**
     * Извлекаем какие-то значения
     * 
     * @return array
     */
    public function getValues() {
        $result = array();
        // Идем по полям для обновления
        foreach($this->_updated_fields as $field_name) {
            $method = $this->_model->buildMethodName($field_name, 'get');
            if (method_exists($this->_model, $method)) { // удалось построить getter
                $result[$field_name] = $this->_model->$method();
            } elseif(
            preg_match('/^' . \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX . '_(.*)-' . \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX . '_(.*)/', $field_name))
            {

                $modulerelation_names = explode('-', preg_replace('/^' . \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX . '_/', '', $field_name));
                $relations = $this->_model->fetchRelated($modulerelation_names[0]);

 
                foreach($relations as $id => $values) 
                    {
                           if(is_object($values[$modulerelation_names[1]]))
                            {
                                $obj = $values[$modulerelation_names[1]];
                                $result[$field_name][$id] = $obj->getId();
                                $result[$field_name . \Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR . \Sl_Form_Factory::RELATION_NAMES_SUFFIX][$id] = $obj->getName();                                

                            } 
                    }
               
            } elseif(
                    strpos($field_name, \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX) === 0
                        &&
                    strpos($field_name, '-')
                ) { // Строка соответствует шаблону "ПРЕФИКС-"
                /**
                 * @TODO Переделать на регулярку то, что в условии
                 */
                $modulerelation_names = explode('-', preg_replace('/^' . \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX . '_/', '', $field_name));
                // Берем данные о связи
                $relations = $this->_model->fetchRelated($modulerelation_names[0]);
                $result_relation_values = array();
                // Магия
                foreach($relations as $id => $values) {
                    $result_relation_values[$id] = array();
                    if (is_object($values)) {
                        $method = 'get'.$values->buildMethodName($modulerelation_names[1]);
                        if (method_exists($values, $method)) {
                            $result_relation_values[$id] = $values->$method();
                        }
                    } else {
                        $result_relation_values[$id] = $values[$modulerelation_names[1]];
                    }
                }
                $result[$field_name] = $result_relation_values;
            } elseif(
                    strpos($field_name, \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX) === 0
                        &&
                    !strpos($field_name, '-')
                ) { // Строка соответствует шаблону "ПРЕФИКС[^-]"
                /**
                 * @TODO Переделать на регулярку то, что в условии
                 */
                
                // Магия
                $modulerelation_name = preg_replace('/^' . \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX . '_/', '', $field_name);
                $relation_values = $this->_model->fetchRelated($modulerelation_name);
                $result_relation_values = '';
                $result_relation_names = '';

                if (current($relation_values) && !is_object(current($relation_values))) {
                    $relation_obj_values = array();
                    $relation = \Sl_Modulerelation_Manager::getRelations($this->_model, $modulerelation_name);
                    
                    if ($relation instanceof \Sl\Modulerelation\Modulerelation){
                        $rel_model = $relation->getRelatedObject($this->_model);    
                        foreach ($relation_values as $id => $something){
                            $relation_obj_values[$id] = \Sl_Model_Factory::mapper($rel_model)->find($id);
                        }
                        $relation_values = $relation_obj_values;    
                    } else {
                        throw new \Exception ('There is not ModuleRelation in '.' '.$field_name);    
                    }   
                      
                    
                    
                }
                $result_relation_names = implode('; ', array_map(function($el) {
                                    return $el->__toString();
                                }, $relation_values));

                $result_relation_values = implode(';', array_map(function($el) {
                                    return $el->getId();
                                }, $relation_values));

                $result[$field_name] = $result_relation_values;
                $result[$field_name . \Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR . \Sl_Form_Factory::RELATION_NAMES_SUFFIX] = $result_relation_names;
            }
        }
        return $result;
    }

    /**
     * Проверяем все ли есть из того, что нужно для рассчета
     * 
     * @param array $request
     * @return boolean
     */
    public function isValid($request) {
        
        foreach ($this->_required_fields as $field) {
            if (strpos($field, '-')) {
                $field_name_arr = explode('-', $field);
                $field_name = $field_name_arr[0];
                $field_key = $field_name_arr[1];
                if (!is_array($request[$field_name]))
                    return false;
                  
                $field_finded = false;

                foreach ($request[$field_name] as $array => $items) {
                    if (isset($items[$field_key])) {
                        $field_finded = true;
                        break;
                    } 
                }

            if (!$field_finded){
                return false;
                
            }
            } else {
                if (!isset($request[$field]))
                    return false;
            }
        }
        return true;
    }

    /**
     * Необходимые для рассчета поля
     * 
     * @return array
     */
    public function getRequiredFields() {
        return $this->_required_fields;
    }
    
    
    /**
     * Измененные в объекте поля
     * 
     * @return array
     */
    public function getChangedFields() {
        return $this->_changed_fields;
    }
    
    /**
     * Добавить измененные в объекте поля
     * 
     * 
     */
    public function addChangedFields($changed_fields) {
        if (is_array($changed_fields)){
            $this->_changed_fields = $this->_changed_fields + $changed_fields;
        } elseif (is_string($changed_fields)) {
            $this->_changed_fields = $this->_changed_fields + array($changed_fields);
        }
    }
    
    /**
     * Установить измененные в объекте поля
     * 
     * 
     */
    public function setChangedFields($changed_fields) {
        if (is_array($changed_fields)){
            $this->_changed_fields = $changed_fields;
        } elseif (is_string($changed_fields)) {
            $this->_changed_fields = array($changed_fields);
        }
        return $this;
    }
    
    /**
     * Возвращает предупреждения
     * 
     * @return array
     */
    public function getWarnings() {
        return $this->_warnings;
    }

    /**
     * Установка "предупреждения"
     * 
     * @param string $warning_string
     * @param array $fields
     * @return \Sl\Calculator\Calculator
     */
    protected function setWarnings($warning_string, array $fields) {
        $this->_warnings[] = array('description' => $warning_string, 'fields' => $fields);
        return $this;
    }

    /**
     * Собственно, счет ....
     * 
     * @param mixed $Obj Что считать
     */
    abstract function calculate($Obj);
}

