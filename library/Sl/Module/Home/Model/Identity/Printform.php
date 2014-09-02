<?php
namespace Sl\Module\Home\Model\Identity;

class Printform extends \Sl\Model\Identity\Identity {

    protected $_models_name_cache;
    
    public function getObjectFields($extended = false, $as_object = false, $sorted = false) {
        $data = parent::getObjectFields($extended, $as_object, $sorted);
        foreach($data as $k=>$item) {
            if($item['name'] == 'name') {
                if($item['searchable'] && $item['select']) {
                    if($item['select']) {
                        $data[$k]['options'] = $this->getSelectValues();
                    }
                }
            }
        }
        return $data;
    }
    
    public function getSelectValues() {
        $values = array('' => $this->getTranslator()->translate('All'));
        $available = \Sl_Module_Manager::getAvailableModels();
        foreach($available as $module_name=>$models) {
            $mod = \Sl_Module_Manager::getInstance()->getModule($module_name);
            foreach($models as $model) {
                $can_print = true;
                //Перевірку на доступність прибрано
                /*
                $can_print = \Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                                         'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                         'module' => $module_name,
                                         'controller' => $model,
                                         'action' => 'print' 
                                     )));
                 * 
                 */
                if($can_print) {
                    $values[\Sl\Printer\Manager::type(\Sl_Model_Factory::object($model, $mod))] = $this->getTranslator()->translate('title_'.$model.'_'.$module_name);
                }
            }
        }
        return $values;
    }
}