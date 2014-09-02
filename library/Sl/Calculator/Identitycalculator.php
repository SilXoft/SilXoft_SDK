<?php

namespace Sl\Calculator;

/**
 * Калькулятор помошника
 * 
 */
abstract class Identitycalculator extends Calculator implements \Sl_Model_Identity_Interface_Calculator {

    /**
     * Аггрегируемые поля
     * 
     * @var array
     */
    protected $_aggregated_fields = array();

    /**
     * Контроллер строки
     * 
     * @var string
     */
    protected $_row_controller = null;

    /**
     * Модуль строки
     * 
     * @var array
     */
    protected $_row_module = null;

    /**
     * Переводчик
     * 
     * @var \Zend_Translate
     */
    protected $_translator;
    protected static $_translate;

    public function __construct() {
        //parent::__construct();
        /**
         * @TODO Убрать ибо устарело.
         * @see Identitycalculator::getTranslator()
         */
        $this->_translator = \Zend_Registry::get('Zend_Translate');
    }

    /**
     * Возвращает аггрегированные поля
     * 
     * @return array
     */
    public function getAggregatedFields() {
        return $this->_aggregated_fields;
    }

    /**
     * Возвращает модель
     * 
     * @return \Sl_Model_Abstract
     */
    public function getModel() {
        if (!is_object($this->_model)) {
            $model_name = $this->getModelName();

            $this->_model = \Sl_Model_Factory::identity(\Sl_Model_Factory::object($model_name));
        }
        return $this->_model;
    }

    /**
     * 
     * @param \Sl\Calculator\_model_name $identity
     */
    protected function fillModel($identity) {

        if ($identity instanceof $this->_model_name)
            $this->_model = $identity;
    }

    public function getValues() {
        /**
         * @TODO Что-то сделать, потому как родительский метод что-то все-таки возвращает
         */
    }

    /**
     * Возвращает колонки с учетом необходимых калькулятору
     * 
     * @param array $fields
     * @return array
     */
    public function getRequestColumns($fields) {
        $aggregated_fields = $this->getAggregatedFields();
        $result_fields = $fields;
        
        foreach ($aggregated_fields as $aggregated_field => $required_fields) {
            if (count($fields)) {
                if (false === ($index = array_search($aggregated_field, $fields)))
                    continue;

                unset($result_fields[$index]);
            }

            foreach ($required_fields as $field_name) {
                $field_name_formatted = $field_name;
                if (strpos($field_name, '.')) {

                    list($relation, $name) = explode('.', $field_name);
                    $field_name = array($relation => $name);
                }

                $result_fields[] = $field_name;
            }
        }
        return $result_fields;
    }

    public function getCalculatedColumns($fields) {

        $aggregated_fields = $this->getAggregatedFields();

        foreach ($aggregated_fields as $aggregated_field => $required_fields) {

            foreach ($required_fields as $field_name) {

                $array_key = array_search($field_name, $fields);

                //Якщо агреговане поле спирається на це поле, видалити його
                if ($array_key !== false/* && !(preg_match('/\.id$/', $array_key)) */) {
                    unset($fields[$array_key]);

                    //якщо агрегованого поля в полях немає, додати замість цього
                    if (!in_array($aggregated_field, $fields)) {
                        $fields[$array_key] = $aggregated_field;
                    }
                }
            }
        }
        ksort($fields);

        return $fields;
    }

    public function calculateValues($values) {
        $fields = array_keys($values);
        $calculated_fields = $this->getCalculatedColumns($fields);
        //ksort($calculated_fields);

        foreach ($calculated_fields as $field_name) {
            if (!isset($values[$field_name])) {
                $values[$field_name] = '';
            }
        }

        $values = $this->calculate($values);
        $new_values = array();

        foreach ($calculated_fields as $field_name) {
            $new_values[$field_name] = $values[$field_name];
        }

        return $new_values;
    }

    public function getRowController() {
        return $this->_row_controller;
    }

    public function setRowController($controller_name = null) {
        $this->_row_controller = $controller_name;
    }

    public function getRowModule() {
        return $this->_row_module;
    }

    public function setRowModule($module_name = null) {
        $this->_row_module = $module_name;
    }

    public static function setTranslator(\Zend_Translate $translator) {
        self::$_translate = $translator;
    }

    public static function getTranslator() {
        if (!isset(self::$_translate)) {
            self::$_translate = self::getDefaulTranslator();
        }
        return self::$_translate;
    }

    protected static function getDefaulTranslator() {
        if (!\Zend_Registry::isRegistered('Zend_Translate')) {
            throw new Exception('Default translator not set and Zend_Registry::get(\'Zend_Translate\') is empty');
        }
        return \Zend_Registry::get('Zend_Translate');
    }

}

