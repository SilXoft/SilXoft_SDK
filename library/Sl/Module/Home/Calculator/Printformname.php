<?php
namespace Sl\Module\Home\Calculator;

class Printformname extends \Sl\Calculator\Identitycalculator {

    protected $_model_name = 'Sl\\Module\\Home\\Model\\Identity\\Printform';
    protected $_updated_fields = array(
        'name'
    );
    
    protected $_models_name_cache;
    protected $_translator;
    
    public function calculate($Obj) {
        if (isset($Obj['name']) && strpos($Obj['name'],\Sl\Service\Helper::MODEL_ALIAS_SEPARATOR)) {
            //$available = $this->getAvailableModels();
            $name = explode(\Sl\Service\Helper::MODEL_ALIAS_SEPARATOR, $Obj['name']);
            $module_name = $name[0] ;
            $model_name = $name[1];
           
                $Obj['name'] = self::getTranslator()->translate('title_'.$model_name.'_'.$module_name);
            
        }
        return $Obj;
    }
    
    public function getAvailableModels() {
        if(!isset($this->_models_name_cache)) {
            $names_cache = array();
            $available = \Sl_Module_Manager::getAvailableModels();
            foreach($available as $module_name=>$models) {
                $mod = \Sl_Module_Manager::getInstance()->getModule($module_name);
                foreach($models as $model) {
                    $names_cache[\Sl\Printer\Manager::type(\Sl_Model_Factory::object($model, $mod))] = self::getTranslator()->translate('title_'.$model.'_'.$module_name);
                }
            }
            $this->_models_name_cache = $names_cache;
        }
        return $this->_models_name_cache;
    }
    /*
    public function getTranslator() {
        if(!isset($this->_translator)) {
            $this->_translator = \Zend_Registry::get('Zend_Translate');
        }
        return $this->_translator;
    }
    */
    public function getModel() {
        if(!isset($this->_model)) {
            $this->_model = new $this->_model_name;
        }
        return $this->_model;
    }
}

