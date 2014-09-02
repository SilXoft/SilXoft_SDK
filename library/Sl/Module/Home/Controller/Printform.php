<?php

namespace Sl\Module\Home\Controller;

class Printform extends \Sl_Controller_Model_Action {

    public function ajaxprintformhelpAction() {

        $fullname = $this->getRequest()->getParam('name');
        ////////////////////////////формуємо масив властивостей моделі, яку обробляє прінтформа для виводу її на сторінку.    
        $name = explode(\Sl\Service\Helper::MODEL_ALIAS_SEPARATOR, $fullname); ////////////////////////////формуємо масив властивостей моделі, яку обробляє прінтформа для ви
        $model_name = $name[1];
        $module_name = $name[0];
        $model = \Sl_Model_Factory::object($model_name, $module_name);
        $config_options = \Sl_Module_Manager::getInstance()
                ->getCustomConfig($module_name, 'detailed');

        if (!$config_options) {
            $config_options = \Sl_Module_Manager::getInstance()
                    ->getModule($module_name)
                    ->generateDetailedOptions();
        }


        $config_options = \Sl_Module_Manager::getInstance()
                ->getCustomConfig($module_name, 'detailed', $model_name);


        if (!$config_options) {
            $config_options = \Sl_Module_Manager::getInstance()
                    ->getModule($module_name)
                    ->generateDetailedOptions($model);
        }

        $config_options = \Sl_Module_Manager::getInstance()
                ->getCustomConfig($module_name, 'detailed', $model_name);

        $config_options = $config_options->toArray();
        $object = \Sl_Model_Factory::object($model_name, $module_name);
        $form_model_options = $object->describeFields();
        $copy_form_model_options = $form_model_options;


        $form_options = array_merge_recursive($config_options, $form_model_options);

        /*   $form_options_copy=$form_options;
          foreach ($form_options as $key=>$value){
          $field_resource = \Sl_Service_Acl::joinResourceName(array(
          'type' => \Sl_Service_Acl::RES_TYPE_FIELD,
          'module' => $module_name,
          'name' => $model_name,
          'field' => $key,
          ));
          $priv_read = \Sl_Service_Acl::isAllowed(array($model,$key), \Sl_Service_Acl::PRIVELEGE_READ);
          if (!$priv_read){unset($form_options[$key]);

          }

          }
          print_r($form_options_copy);die; */
        foreach ($form_options as $key => $option) {
            if (!(strpos($key, \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX) === 0)) {
                $priv_read = \Sl_Service_Acl::isAllowed(array(
                            $object,
                            $key
                                ), \Sl_Service_Acl::PRIVELEGE_READ);
                if (isset($option['label']) && ($option['type'] != 'hidden') && ($priv_read)) {
                    if (is_array($option['label'])) {
                        $template_names['%' . $key . '%'] = $this->view->translate(current($option['label']));
                    } else {
                        $template_names['%' . $key . '%'] = $this->view->translate($option['label']);
                    }
                }
            }
        }

        $this->view->template_array = $template_names;
        $this->view->content = $this->view->render('partials/template_array.phtml');
        /////////////////// сформували массив у template_names    
    }

}