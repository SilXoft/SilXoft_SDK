<?php
namespace Sl\Module\Home\Controller;

use Sl_Module_Manager as ModuleManager;
use Sl_Model_Factory as ModelFactory;
use Sl_Form_Factory as FormFactory;

class Admin extends \Sl_Controller_Action {

    public function testAction() {
        /*
          header('Content-TYpe: text/plain; charset=utf-8');
          try {
          \Sl\Service\DbBuilder::rebuidIndexes();
          } catch(Exception $e) {
          print_r($e);
          die ;
          }
          die ;
         */
        //error_reporting(E_ALL);
        return;
        if (($handle = fopen("/var/www/testcrm/customers.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $result = array('name' => '[noname]', 'firstname' => '[noname]');
                foreach ($data as $key => $value) {
                    $data[$key] = iconv('cp1251', 'utf8', $value);
                }
                if (preg_match('|(\D)(80/)(.+)|', $data[0])) {
                    $result['code'] = preg_replace('|(\D)(80/)(.+)|', '$1$3', $data[0]);
                } else {
                    $result['code'] = $data[0];
                }
                $result['code'] = preg_match('/^\D\d{3}$/', $result['code']) ? $result['code'] . '8' : $result['code'];
                if (strlen(trim($data[1]))) {
                    $result['name'] = $data[1];
                    $names = explode(' ', $data[1]);
                    $result['lastname'] = array_shift($names);
                    $result['firstname'] = array_shift($names);
                    $result['middlename'] = implode(' ', $names);
                }
                $result['city'] = trim($data[2]);
                $result['skype'] = trim($data[7]);
                $result['phones'] = explode(', ', $data[3]);
                $result['emails'] = explode(', ', $data[6]);
                $result['sender_phone'] = $data[9];

                if (strlen($data[8])) {
                    $result['description'] = 'От кого: ' . $data[8];
                }

                if (preg_match('/^([\d]{4,7}),?\s(.+)/', $data[4], $matches)) {
                    //$result['index'] = $matches[1][0];
                    $result['index'] = $matches[1];
                    $result['address'] = $matches[2];
                } else {
                    $result['address'] = $data[4];
                }



                if (strlen($result['city'])) {
                    $cityIdentity = \Sl_Model_Factory::identity('city', \Sl_Module_Manager::getInstance()->getModule('home'));
                    $cityIdentity->field('name')->like($result['city']);


                    $cityIdentity = \Sl_Model_Factory::mapper($cityIdentity)->fetchAllExtended($cityIdentity);
                    $cities = $cityIdentity->getData();
                    if (count($cities)) {
                        $result['city'] = current($cities[0]);
                    } else {
                        $city = \Sl_Model_Factory::object('city', \Sl_Module_Manager::getInstance()->getModule('home'));
                        $city->setName($result['city']);
                        $city = \Sl_Model_Factory::mapper($city)->save($city, true);
                        $result['city'] = $city->getId();
                    }
                }

                $customer = \Sl_Model_Factory::object('customer', \Sl_Module_Manager::getInstance()->getModule('customers'));
                $customer->setName($result['name'])
                        ->setLastName($result['lastname'])
                        ->setFirstName($result['firstname'])
                        ->setMiddleName($result['middlename'])
                        ->setSenderPhone($result['sender_phone'])
                        ->setNotifyEmail(1)
                        ->setSkype($result['skype'])
                        ->setPostCode($result['index'])
                        ->setAddress($result['address']);
                ;
                if ($result['city'] > 0) {
                    $customer->assignRelated('customercity', array($result['city'] => $result['city']));
                }

                //$result['emails']=explode(', ',$data[6]);
                if (count($result['phones'])) {
                    $phones = array();
                    $i = 0;
                    foreach ($result['phones'] as $phone) {
                        $phones['new_' . (++$i)] = array('phone' => trim($phone));
                    }
                    $customer->assignRelated('customerphones', $phones);
                }

                if (count($result['emails'])) {
                    $emails = array();
                    $i = 0;
                    foreach ($result['emails'] as $email) {
                        $emails['new_' . (++$i)] = array('mail' => trim($email));
                    }
                    $customer->assignRelated('customeremails', $emails);
                }

                if (strlen($result['code'])) {
                    $identifier = \Sl_Model_Factory::object('customeridentifier', \Sl_Module_Manager::getInstance()->getModule('logistic'));
                    $identifier->setName($result['code']);
                    $identifier = \Sl_Model_Factory::mapper($identifier)->save($identifier, TRUE);
                    $customer->assignRelated('customeridentifiercustomer', array($identifier->getId() => $identifier->getId()));
                }

                $customer = \Sl_Model_Factory::mapper($customer)->save($customer, true);
                //$customer = \Sl_Model_Factory::mapper($customer)->findExtended($customer->getId(),array('customeridentifiercustomer'));

                echo $customer . '<br>';
            }
            fclose($handle);

            die;
        }
    }

    public function createmodelAction() {

        $system_modules = \Sl_Module_Manager::getInstance()->getModules(false);
        $modules = array();
        foreach ($system_modules as $name => $module) {
            $modules[$name] = $this->view->translate('title_module_'.$name);
        }

        $form = \Sl_Form_Factory::build(array(
                    $this->_getModule(),
                    'createmodel_form'
                        ), true);
        $form->getElement('module_name')->setMultioptions($modules);

        if($inh_element = $form->getElement('inherits')) {
            $existing_model = ModuleManager::getAvailableModels();
            $select_data = array('-' => '-');
            foreach($existing_model as $modulename=>$models) {
                foreach($models as $modelname) {
                    $select_data[$this->view->translate('title_module_'.$modulename)][\Sl\Service\Helper::getModelAlias($modelname, $modulename)]
                        = $this->view->translate('title_'.$modelname.'_'.$modulename);
                }
            }
            $inh_element->setMultiOptions($select_data);
        }
        
        $subForm = new \Sl_Form_SubForm(array(
            'decorators' => \Sl_Form_Factory::$default_subform_group_decorators,
            'label' => 'Fields',
        ));

        $subForm2 = new \Zend_Form_SubForm(array('decorators' => \Sl_Form_Factory::$default_subform_decorators,));

        $subForm2->addElement('text', 'field_name', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
            'label' => 'Name',
            'placeholder' => 'Name',
        ));

        $subForm2->addElement('select', 'field_type', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
            'label' => 'Type',
            'placeholder' => 'Type',
        ));

        $fieldtypes = array(
            'varchar' => 'varchar',
            'int' => 'int',
            'float' => 'float',
            'tinyint' => 'tinyint',
            'text' => 'text',
            'date' => 'date',
            'timestamp' => 'timestamp',
        );

        $subForm2->getElement('field_type')->setMultioptions($fieldtypes);

        $subForm2->addElement('text', 'values', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
            'label' => 'values',
            'placeholder' => 'values',
        ));

        $subForm2->addElement('checkbox', 'null', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_decorators,
            'label' => 'NULL',
        ));

        $subForm2->addElement('hidden', 'delete', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$delete_new_item_decorators,
            'label' => 'x',
        ));
        $class = $subForm2->getDecorator('div')->getOption('class');
        $class = is_array($class) ? $class : array($class);
        $class[] = 'new_item';
        $subForm2->getDecorator('div')->setOption('class', $class);
        $subForm->addSubForm($subForm2, 'new');

        $form->addSubForm($subForm, 'fields');

        //$subforms_group -> addSubForm($subForm, $field_name, self::getSorterValue());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                //umask (0);
                //print_r($this -> getRequest() -> getParams());
                $inherit_model = $this->getRequest()->getParam('inherits', '-');
                $inherits = ($inherit_model !== '-');
                $inherit_model = $inherits?ModelFactory::model($inherit_model):null;
                
                $module = $system_modules[$this->getRequest()->getParam('module_name')];
                $is_logged = $this->getRequest()->getParam('create_log', false);
                $fields = $this->getRequest()->getParam('fields', array());
                if(!$inherits) {
                    $table_name = $this->getRequest()->getParam('table_name');
                    \Sl\Service\ClassCreator::createMysqlTable($table_name, $fields);
                } else {
                    $table_name = ModelFactory::table($inherit_model)->info('name');
                    \Sl\Service\ClassCreator::updateMysqlTable($table_name, $fields, true);
                }

                $model = ucfirst(strtolower($this->getRequest()->getParam('name')));

                try {
                    \Sl\Service\ClassCreator::createDbTable($module, $model, $table_name, $inherit_model);
                    \Sl\Service\ClassCreator::createMapper($module, $model, $inherit_model);
                    \Sl\Service\ClassCreator::createIdentity($module, $model, $inherit_model);
                    \Sl\Service\ClassCreator::createModel($module, $model, $fields, $is_logged, $inherit_model);
                    if ($this->getRequest()->getParam('create_controller', false)) {
                        \Sl\Service\ClassCreator::createController($module, $model, $inherit_model);
                    }

                    $model = ModelFactory::object($model, $module);
                    \Sl\Service\Loger::createLogTable($model, $inherit_model);
                    $model->fillEmptyFieldInfo();
                } catch (\Exception $e) {
                    throw new \Exception("Error Processing Request: " . $e->getMessage(), 1);
                }

                //die();
                $this->_redirect($this->getRequest()->getRequestUri());
            } else {
                print_r($form->getMessages());
                die;
            }
        }

        $this->view->form = $form;
    }

    public function updatemodelAction() {
        error_reporting(E_ERROR);
        $system_modules = \Sl_Module_Manager::getInstance()->getModules(false);
        $modules = array();
        foreach ($system_modules as $name => $module) {
            $modules[$name] = $name;
        }

        $form = new \Sl\Form\Form;

        $available_models = \Sl_Module_Manager::getAvailableModels();
        $select_models = array();
        foreach ($available_models as $module_name => $models) {
            $module = \Sl_Module_Manager::getInstance()->getModule($module_name);

            foreach ($models as $model) {


                $select_models[$this->view->translate('title_module_' . $module_name)][\Sl\Service\Helper::getModelAlias($model, $module)]
                        = $this->view->translate('title_' . $model . '_' . $module_name);
            }
        }

        $form->addElement('submit', 'Save', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$hidden_decorators,
            'label' => 'Сохранить'
        ));
        $form->addElement('select', 'model', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_decorators,
            'label' => $this->view->translate('Модель'),
            'title' => $this->view->translate('Модель'),
        ));
        $form->getElement('model')->setMultioptions($select_models);

        $subForm = new \Sl_Form_SubForm(array(
            'decorators' => \Sl_Form_Factory::$default_subform_group_decorators,
            'label' => $this->view->translate('Поля'),
        ));

        $subForm2 = new \Zend_Form_SubForm(array('decorators' => \Sl_Form_Factory::$default_subform_decorators,));

        $subForm2->addElement('text', 'field_name', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
            'label' => 'Name',
            'placeholder' => 'Name',
        ));

        $subForm2->addElement('select', 'field_type', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
            'label' => 'Type',
            'placeholder' => 'Type',
        ));

        $fieldtypes = array(
            'varchar' => 'varchar',
            'int' => 'int',
            'float' => 'float',
            'tinyint' => 'tinyint',
            'text' => 'text',
            'date' => 'date',
            'timestamp' => 'timestamp',
        );

        $subForm2->getElement('field_type')->setMultioptions($fieldtypes);

        $subForm2->addElement('text', 'values', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
            'label' => 'values',
            'placeholder' => 'values',
        ));

        $subForm2->addElement('checkbox', 'null', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_decorators,
            'label' => 'NULL',
        ));

        $subForm2->addElement('hidden', 'delete', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$delete_new_item_decorators,
            'label' => 'x',
        ));
        $class = $subForm2->getDecorator('div')->getOption('class');
        $class = is_array($class) ? $class : array($class);
        $class[] = 'new_item';
        $subForm2->getDecorator('div')->setOption('class', $class);
        $subForm->addSubForm($subForm2, 'new');

        $form->addSubForm($subForm, 'fields');

        //$subforms_group -> addSubForm($subForm, $field_name, self::getSorterValue());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                //umask (0);
                //print_r($this -> getRequest() -> getParams());

                $model = \Sl\Service\Helper::getModelNameByAlias($this->getRequest()->getParam('model'));
                $module = $modle_name = \Sl\Service\Helper::getModuleByAlias($this->getRequest()->getParam('model'));
                $fields = $this->getRequest()->getParam('fields', array());
                $table_name = \Sl_Model_Factory::build('dbTable', $model, $module)->info('name');

                \Sl\Service\ClassCreator::updateMysqlTable($table_name, $fields, true);

                $model = \Sl\Service\Helper::getModelByAlias($this->getRequest()->getParam('model'));
                try {

                    \Sl\Service\ClassCreator::updateModel($model, $fields);
                    \Sl\Service\ClassCreator::updateModelConfigs($model, $fields);
                } catch (\Exception $e) {
                    throw new \Exception("Error Processing Request: " . $e->getMessage(), 1);
                }

                $new_fields = array();
                foreach ($fields as $property_array) {
                    if ($property_name['delete'] || !strlen($property_array['field_name']))
                        continue;
                    $new_fields[] = strtolower($property_array['field_name']);
                }


                $model->fillEmptyFieldInfo($new_fields);
                $this->_redirect($this->getRequest()->getRequestUri());
            } else {
                print_r($form->getMessages());
                die;
            }
        }

        $this->view->form = $form;
    }

    public function createmodulerelationAction() {
        error_reporting(E_ERROR);

        $modules = \Sl_Module_Manager::getModules();
        $modules_resources = array();
        $models = array();
        $modules_list = array();

        foreach ($modules as $module_name => $module) {
            // Створення ресурсів MVC:module|controller|action
            $modules_list[strtolower($module->getName())] = $module->getName();
            // Створення ресурсів OBJ:module|name|field
            if (is_dir($module->getDir() . '/Model')) {
                $dh = opendir($module->getDir() . '/Model');
                if ($dh) {

                    while (false !== ($filename = readdir($dh))) {
                        $matches = array();
                        if (preg_match('/(.+)\.php$/', $filename, $matches)) {
                            $model_name = strtolower($matches[1]);
                            $models[$module_name . '\\' . $model_name] = $module_name . '\\' . $model_name;
                        }
                    }
                }
            }
        }

        $form = \Sl_Form_Factory::build(array(
                    $this->_getModule(),
                    'createmodulerelation_form'
                        ), true);

        $relation_types = array(
            \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE => 'RELATION_ONE_TO_ONE',
            \Sl_Modulerelation_Manager::RELATION_ONE_TO_MANY => 'RELATION_ONE_TO_MANY',
            \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE => 'RELATION_MANY_TO_ONE',
            \Sl_Modulerelation_Manager::RELATION_MANY_TO_MANY => 'RELATION_MANY_TO_MANY',
            \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER => 'RELATION_ITEM_OWNER',
            \Sl_Modulerelation_Manager::RELATION_MODEL_ITEM => 'RELATION_MODEL_ITEM',
            \Sl_Modulerelation_Manager::RELATION_FILE_ONE => 'RELATION_FILE_ONE',
            \Sl_Modulerelation_Manager::RELATION_FILE_MANY => 'RELATION_FILE_MANY',
        );

        $model_name = str_replace('.', '\\', $this->getRequest()->getParam('default_model_name', false));
        if ($model_name) {
            $form->removeElement('model_name');

            $form->addElement('hidden', 'model_name', array(
                'value' => $model_name,
                'disableLoadDefaultDecorators' => true,
                'decorators' => \Sl_Form_Factory::$hidden_decorators,
            ));
        } else {
            $form->getElement('model_name')->setMultioptions($models);
        }

        if ($form->getElement('module_name'))
            $form->getElement('module_name')->setMultioptions($modules_list);

        $form->getElement('target_model_name')->setMultioptions($models);

        if (($relation_type = $this->getRequest()->getParam('default_relation_type', false)) && isset($relation_types[$relation_type])) {
            $form->removeElement('relation_type');
            $form->addElement('hidden', 'relation_type', array(
                'value' => $relation_type,
                'disableLoadDefaultDecorators' => true,
                'decorators' => \Sl_Form_Factory::$hidden_decorators,
            ));
        } else {

            $form->getElement('relation_type')->setMultioptions($relation_types);
        }

        $subForm = new \Sl_Form_SubForm(array(
            'decorators' => \Sl_Form_Factory::$default_subform_group_decorators,
            'label' => $this->view->translate('Options'),
        ));

        $options = $this->getRequest()->getParam('default_options', array());
        //$relation_options = array();

        foreach ($options as $option) {
            if ($option['delete'] || !strlen(trim($option['name'])))
                continue;
            /* $option['value'] = trim($option['value']);
              $relation_options[$option['name']] = $option['is_array'] ? explode(PHP_EOL, $option['value']) : $option['value'];
             */
            $subForm2 = new \Sl_Form_SubForm(array('decorators' => \Sl_Form_Factory::$default_subform_decorators));

            $subForm2->addElement('text', 'name', array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
                'label' => $this->view->translate('Option name'),
                'title' => $this->view->translate('Option name'),
                'placeholder' => $this->view->translate('Option name'),
                'value' => $option['name'],
                'readonly' => 'readonly'
                    //'validators' => $model -> validators($key),
            ));

            $subForm2->addElement('textarea', 'value', array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
                'label' => $this->view->translate('Option value'),
                'title' => $this->view->translate('Option value'),
                'placeholder' => $this->view->translate('Option value'),
                    //'validators' => $model -> validators($key),
            ));

            if (isset($option['value'])) {
                $subForm2->getElement('value')->setOptions(array('value' => trim($option['value']), 'readonly' => 'readonly'));
            }
            if (isset($option['is_array'])) {
                $subForm2->addElement('hidden', 'is_array', array(
                    'disableLoadDefaultDecorators' => true,
                    'decorators' => \Sl_Form_Factory::$hidden_decorators,
                    'label' => $this->view->translate('is Array'),
                    'title' => $this->view->translate('is Array'),
                    'placeholder' => $this->view->translate('is Array'),
                    'value' => $option['is_array'],
                    'readonly' => 'readonly'
                        //'validators' => $model -> validators($key),
                ));
            } else {
                $subForm2->addElement('checkbox', 'is_array', array(
                    'disableLoadDefaultDecorators' => true,
                    'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
                    'label' => $this->view->translate('is Array'),
                    'title' => $this->view->translate('is Array'),
                    'placeholder' => $this->view->translate('is Array'),
                        //'validators' => $model -> validators($key),
                ));
            }

            /*
              $subForm2 -> addElement('hidden', 'delete', array(
              'disableLoadDefaultDecorators' => true,
              'decorators' =>  \Sl_Form_Factory::$delete_item_decorators,
              'label' => 'x',
              )); */

            $subForm->addSubForm($subForm2, $option['name']);
        }

        $subForm2 = new \Sl_Form_SubForm(array('decorators' => \Sl_Form_Factory::$default_subform_decorators,));

        $subForm2->addElement('text', 'name', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
            'label' => $this->view->translate('Option name'),
            'title' => $this->view->translate('Option name'),
            'placeholder' => $this->view->translate('Option name'),
                //'validators' => $model -> validators($key),
        ));

        $subForm2->addElement('textarea', 'value', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
            'label' => $this->view->translate('Option value'),
            'title' => $this->view->translate('Option value'),
            'placeholder' => $this->view->translate('Option value'),
                //'validators' => $model -> validators($key),
        ));

        $subForm2->addElement('checkbox', 'is_array', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$default_subform_text_field_decorators,
            'label' => $this->view->translate('is Array'),
            'title' => $this->view->translate('is Array'),
            'placeholder' => $this->view->translate('is Array'),
        ));
        $subForm2->addElement('hidden', 'delete', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => \Sl_Form_Factory::$delete_new_item_decorators,
            'label' => 'x',
        ));

        $subForm2->getDecorator('div')->setOption('class', array(
            'item',
            'new_item'
        ));
        $subForm->addSubForm($subForm2, 'new');

        $form->addSubForm($subForm, 'options');

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())/* &&   $this -> getRequest() -> getParam('model_name') != $this -> getRequest() -> getParam('target_model_name') */ && $this->getRequest()->getParam('table_name', false) && $this->getRequest()->getParam('modulerelation_name', false)) {

                $module_name = $this->getRequest()->getParam('module_name');
                $module = \Sl_Module_Manager::getInstance()->getModule($module_name);

                $module_class_arr = explode('\\', get_class($module));

                $module_dir = implode('\\', array(
                    array_shift($module_class_arr),
                    array_shift($module_class_arr),
                    array_shift($module_class_arr)
                ));
                $model_name = $this->getRequest()->getParam('model_name');
                $model_names = explode('\\', $model_name);
                $target_model_names = explode('\\', $this->getRequest()->getParam('target_model_name'));
                $table_name = $this->getRequest()->getParam('table_name');
                $relation_type = $this->getRequest()->getParam('relation_type');

                $field_id = $model_names[1] . '_id';
                $target_field_id = ($target_model_names[1] == $model_names[1]) ? $model_names[1] . '2_id' : $target_model_names[1] . '_id';

                \Sl\Service\ClassCreator::createMysqlModulerelationTable($table_name, $field_id, $target_field_id);

                $modulerelation_name = ucfirst(strtolower($this->getRequest()->getParam('modulerelation_name')));
                $options = $this->getRequest()->getParam('options', array());
                $relation_options = array();
                foreach ($options as $option_key => $option) {
                    if ($option['delete'] || !strlen(trim($option['name'])))
                        continue;
                    $option['value'] = trim($option['value']);
                    $relation_options[$option['name']] = $option['is_array'] ? explode(PHP_EOL, $option['value']) : $option['value'];
                }

                try {
                    \Sl\Service\ClassCreator::createModulerelationDbTable($module, $modulerelation_name, \Sl_Module_Manager::getInstance()->getModule($model_names[0]), $model_names[1], \Sl_Module_Manager::getInstance()->getModule($target_model_names[0]), $target_model_names[1], $table_name, $field_id, $target_field_id);
                    \Sl\Service\ClassCreator::createModulerelation($module, $modulerelation_name);

                    $module = \Sl_Module_Manager::getInstance()->getModule($module_name);

                    $new_relation_arr = array(
                        'type' => $relation_type,
                        'db_table' => $module->getType('\\') . '\\' . ucfirst($module_name) . '\Modulerelation\Table\\' . $modulerelation_name,
                        'options' => $relation_options
                    );
                    try {
                        $rel_config = \Sl\Service\Config::read($module, 'relations')->toArray();
                        $rel_config[] = $new_relation_arr;
                        \Sl\Service\Config::write($module, 'relations', $rel_config);
                    } catch (\Exception $e) {
                        if ($e->getCode() === \Sl\Service\Config::EC_NOT_EXISTS) {
                            \Sl\Service\Config::write($module, 'relations', array($new_relation_arr), true);
                        } else {
                            throw $e;
                        }
                    }
                    $module->updateModulerelationSection(array($new_relation_arr));
                } catch (\Exception $e) {
                    throw new \Exception("Error Processing Request: " . $e->getMessage(), 1);
                }

                if ($this->getRequest()->getParam('register_tx_relation', false)) {
                    \Sl\Service\Accounting::getInstance()->registerTxRelation($modulerelation_name, $target_model_names[1], $target_model_names[0]);
                }

                //die();

                if ($this->getRequest()->getParam('is_iframe', false)) {

                    $this->_forward('closeiframe');
                } else {
                    $this->_redirect($this->getRequest()->getRequestUri());
                }
            } else {

                print_r($form->getMessages());
                die;
            }
        }

        $this->view->form = $form;
    }

    public function ajaxcreatelogsAction() {

        try {
            $modules = \Sl_Module_Manager::getModules();

            foreach ($modules as $module_name => $module) {

                if (is_dir($module->getDir() . '/Model')) {
                    $dh = opendir($module->getDir() . '/Model');
                    if ($dh) {

                        while (false !== ($filename = readdir($dh))) {
                            $matches = array();
                            if (preg_match('/(.+)\.php$/', $filename, $matches)) {
                                $model_name = strtolower($matches[1]);
                                $model_class_name = $module->getModelClassName($model_name);
                                if (class_exists($model_class_name)) {
                                    $model = \Sl_Model_Factory::object($model_class_name);
                                    \Sl\Service\Loger::createLogTable($model);
                                }
                            }
                        }
                    }
                }
            }

            $this->view->result = true;
        } catch (Exception $e) {
            $this->view->description = $e->getMessage();
        }
    }

    public function reconfigureAction() {
        try {
            $modules = \Sl_Module_Manager::getAvailableModels();
            echo '<pre>';
            foreach ($modules as $module_name => $models) {
                $module_lv_options = \Sl_Module_Manager::getInstance()->getModule($module_name)->section('listview_options');
                foreach ($models as $model_name) {
                    echo '<strong>' . $module_name . '.' . $model_name . "</strong>\r\n";
                    $model_lv_options = isset($module_lv_options->$model_name) ? $module_lv_options->$model_name : new \Zend_Config(array(), true);
                    $model_options = new \Zend_Config(\Sl_Model_Factory::object($model_name, $module_name)->describeFields(), true);
                    /* @var $model_option \Zend_Config */
                    foreach ($model_options->toArray() as $k => $v) {
                        if (in_array($k, array('id', 'create', 'active')))
                            continue;
                        if (preg_match('/^[-A-Z_0-9]+/', $v['label']))
                            continue;
                        if (isset($model_lv_options->$k)) {
                            // Какой-то конфиг для этого поля уже есть
                            $config = $model_lv_options->$k->toArray();
                            if (isset($config['visible']) && !$config['visible']) {
                                if (!isset($config['hidable'])) {
                                    echo "<strong>$k</strong>: need be hidable\r\n";
                                    echo "\r\n----------------------\r\n";
                                }
                            }
                        } else {
                            // Никакого конфига нет
                            echo '\'' . $k . '\' => array(' . "\r\n";
                            echo '      \'label\' => \'' . $model_options->$k->label . '\',' . "\r\n";
                            echo '      \'order\' => 0,' . "\r\n";
                            echo '      \'visible\' => false,' . "\r\n";
                            echo '      \'hidable\' => true,' . "\r\n";
                            echo '),' . "\r\n";
                        }
                    }
                    echo "\r\n******************************\r\n";
                }
            }
            die;
            //print_r($modules);
            echo '</pre>';
        } catch (\Exception $e) {
            echo $e->getMessage() . "\r\n";
            die('Error');
        }
        die('Ok');
    }

    public function createarchivequeryAction() {
        $query = array();
        $query2 = array();
        foreach (\Sl_Module_Manager::getAvailableModels() as $sModule => $models) {
            foreach ($models as $sModel) {
                $oModel = \Sl_Model_Factory::object($sModel, $sModule);
                if ($oModel && ($oModel instanceof \Sl_Model_Abstract)) {
                    $oTable = \Sl_Model_Factory::dbTable($oModel);
                    /* @var $oTable \Sl\Model\DbTable\DbTable */
                    try {
                        $sTableName = $oTable->info(\Zend_Db_Table::NAME);
                        $aTableCols = $oTable->info(\Zend_Db_Table::COLS);
                        if (!in_array('archived', $aTableCols)) {
                            $query[] = 'ALTER TABLE `' . $sTableName . '` ADD `archived` TINYINT(4) NULL DEFAULT 0;';
                        } else {
                            $query[] = 'ALTER TABLE `' . $sTableName . '` CHANGE `archived` `archived` TINYINT(4) NULL DEFAULT 0;';
                        }
                    } catch (\Exception $e) {
                        
                    }
                }
            }
        }
        echo implode("\r\n", $query);
        die;
    }

    public function updateprettydbdumpAction() {
        $data = array();
        foreach (\Sl_Module_Manager::getAvailableModels() as $sModule => $models) {
            foreach ($models as $sModel) {
                $oModel = \Sl_Model_Factory::object($sModel, $sModule);
                if ($oModel && ($oModel instanceof \Sl_Model_Abstract)) {
                    $oTable = \Sl_Model_Factory::dbTable($oModel);
                    /* @var $oTable \Sl\Model\DbTable\DbTable */
                    try {
                        $sTableName = $oTable->info(\Zend_Db_Table::NAME);
                        $aTableData = $oTable->info(\Zend_Db_Table::METADATA);
                        foreach ($aTableData as $sFieldName => $aDefinitions) {
                            foreach ($aDefinitions as $sDefName => $sDefValue) {
                                if ($sDefValue) {
                                    $data[$sTableName][$sFieldName][strtolower($sDefName)] = $sDefValue;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        
                    }
                }
            }
        }

        $config = new \Zend_Config(require $this->_getDbDumpConfigPath(), true);
        if ($config) {
            $config = $config->toArray();
        } else {
            $config = array();
        }

        $data_iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($data));

        $add = $update = array();

        $delete = $data;

        foreach ($data_iterator as $key => $value) {
            $sTableName = $data_iterator->getSubIterator(0)->key();
            $sFieldName = $data_iterator->getSubIterator(1)->key();
            $sDefName = $key;
            $sDefValue = $value;
            if (!@isset($config[$sTableName][$sFieldName][$sDefName])) {
                $add[$sTableName][$sFieldName][$sDefName] = $sDefValue;
            } elseif ($config[$sTableName][$sFieldName][$sDefName] !== $sDefValue) {
                $update[$sTableName][$sFieldName][$sDefName] = $sDefValue;
            }
            unset($delete[$sTableName][$sFieldName][$sDefName]);
            if (count($delete[$sTableName][$sFieldName]) == 0) {
                unset($delete[$sTableName][$sFieldName]);
            }
            if (count($delete[$sTableName]) == 0) {
                unset($delete[$sTableName]);
            }
        }

        $this->_updateDbPatchFile($add, $update, $delete);
        try {
            $configWriter = new \Zend_Config_Writer_Array(array(
                'config' => new \Zend_Config($data, true),
                'filename' => $this->_getDbDumpConfigPath(),
            ));
            $configWriter->write();
        } catch (\Exception $e) {
            echo $e->getMessage() . "\r\n";
            die;
        }
        die("\r\n*********************************\r\nSuccess");
    }

    public function checkextendvalueAction() {
       
        $adapter = \Zend_Db_Table::getDefaultAdapter();
        
        $model_data = \Sl_Module_Manager::getAvailableModels();
        foreach($model_data as $modulename=>$models) {
            foreach($models as $model) {
                
                $table = \Sl_Model_Factory::dbTable($model, $modulename);
                $object = \Sl_Model_Factory::object($model, $modulename);
                if ($object->findModelName() != 'log' && $object->findModelName() != 'transaction') {
                    try {
                      $extend = \Sl\Service\Helper::getModelInheritanceAlias($object);                        
                       $adapter->query("UPDATE `".$table->info('name')."` SET extend='".$extend."'");
                    } catch (\Exception $e) {
                        echo $object->findModelName() ;
                        print_r($table);
                        die();
                    }
                }                
            }
        }
        die('done');
    }
    
    public function checkextendfieldAction() {
        header('Content-type: text/plain; charset=utf-8');
        $adapter = \Zend_Db_Table::getDefaultAdapter();
        
        $model_data = \Sl_Module_Manager::getAvailableModels();
        foreach($model_data as $modulename=>$models) {
            foreach($models as $model) {
                try {
                    $table = \Sl_Model_Factory::dbTable($model, $modulename);
                    /*@var $table \Zend_Db_Table_Abstract*/
                    $table_data = $table->info(\Zend_Db_Table::METADATA);
                    echo $table->info('name')."\r\n";
                    $col_exists = false;
                    foreach($table_data as $column) {
                        if($col_exists) continue;
                        if($column['COLUMN_NAME'] === 'extend') {
                            $col_exists = true;
                        }
                    }
                    if(!$col_exists) {
                        echo "Need update\r\n";
                        $adapter->query("ALTER TABLE `".$table->info('name')."` ADD `extend` TEXT NULL DEFAULT NULL");
                        echo "\r\nUpdated\r\n";
                    } else {
                        echo "Column already exists\r\n";
                    }
                    echo "\r\n";
                } catch (\Exception $e) {
                    echo 'Can\'t build table for '.$modulename.'.'.$model.' cause "'.$e->getMessage().'"'."\r\n";
                }
            }
        }
        die;
    }
    
    protected function _getDbDumpConfigPath() {
        return APPLICATION_PATH . '/../db/dump.php';
    }

    protected function _getDbPatchConfigPath() {
        return APPLICATION_PATH . '/../db/patch.php';
    }

    protected function _updateDbPatchFile(array $add = array(), array $update = array(), array $delete = array()) {
        $config = new \Zend_Config(require $this->_getDbPatchConfigPath(), true);
        if ($config) {
            $config = $config->toArray();
        } else {
            $config = array();
        }
        foreach ($add as $sTableName => $aColumns) {
            foreach ($aColumns as $sFieldName => $aDefinitions) {
                foreach ($aDefinitions as $sDefName => $sDefValue) {
                    $config['add'][$sTableName][$sFieldName][$sDefName] = $sDefValue;
                }
            }
        }
        foreach ($update as $sTableName => $aColumns) {
            foreach ($aColumns as $sFieldName => $aDefinitions) {
                foreach ($aDefinitions as $sDefName => $sDefValue) {
                    $config['update'][$sTableName][$sFieldName][$sDefName] = $sDefValue;
                }
            }
        }
        foreach ($delete as $sTableName => $aColumns) {
            foreach ($aColumns as $sFieldName => $aDefinitions) {
                foreach ($aDefinitions as $sDefName => $sDefValue) {
                    $config['delete'][$sTableName][$sFieldName][$sDefName] = $sDefValue;
                }
            }
        }
        try {
            $configWriter = new \Zend_Config_Writer_Array(array(
                'config' => new \Zend_Config($config, true),
                'filename' => $this->_getDbDumpConfigPath(),
            ));
            $configWriter->write();
        } catch (\Exception $e) {
            echo $e->getMessage() . "\r\n";
            die;
        }
    }

    protected function _patchToSql(array $data, $type = 'add') {
        $method_name = '_' . $type . 'PatchToSql';
        if (method_exists($this, $method_name)) {
            return $this->$method_name($data);
        } else {
            throw new \Exception('Not implemented. ' . __CLASS__ . '::' . $method_name);
        }
        return array();
    }

    protected function _addPatchToSql(array $data) {
        $query = array();
        foreach ($data as $sTableName => $aColumns) {
            foreach ($aColumns as $sFieldName => $aDefinitions) {
                $typedef = '';
                switch ($aDefinitions['data_type']) {
                    case 'decimal':
                        $prec = isset($aDefinitions['precision']) ? intval($aDefinitions['precision']) : 10;
                        $scale = isset($aDefinitions['scale']) ? intval($aDefinitions['scale']) : 2;
                        $typedef = 'DECIMAL(' . $prec . ', ' . $scale . ')';
                        break;
                    case 'varchar':
                        $length = isset($aDefinitions['length']) ? intval($aDefinitions['length']) : 250;
                        $typedef = 'VARCHAR(' . $length . ')';
                        break;
                    case 'int':
                    default:
                        $typedef = ' ' . strtoupper($aDefinitions['data_type']) . ' ';
                        break;
                }
                $null_str = ' NULL DEFAULT NULL ';
                $query[] = 'ALTER TABLE `' . $sTableName . '` ADD `' . $sFieldName . '` ' . $typedef . ' ' . $null_str;
            }
        }
        return $query;
    }

    protected function _updatePatchToSql(array $data) {
        // @TODO: Доработать, чтобы можно было отслеживать и менять имена колонок
        $query = array();
        foreach ($data as $sTableName => $aColumns) {
            foreach ($aColumns as $sFieldName => $aDefinitions) {
                $typedef = '';
                switch ($aDefinitions['data_type']) {
                    case 'decimal':
                        $prec = isset($aDefinitions['precision']) ? intval($aDefinitions['precision']) : 10;
                        $scale = isset($aDefinitions['scale']) ? intval($aDefinitions['scale']) : 2;
                        $typedef = 'DECIMAL(' . $prec . ', ' . $scale . ')';
                        break;
                    case 'varchar':
                        $length = isset($aDefinitions['length']) ? intval($aDefinitions['length']) : 250;
                        $typedef = 'VARCHAR(' . $length . ')';
                        break;
                    case 'int':
                    default:
                        $typedef = ' ' . strtoupper($aDefinitions['data_type']) . ' ';
                        break;
                }
                $null_str = ' NULL DEFAULT NULL ';
                $query[] = 'ALTER TABLE `' . $sTableName . '` CHANGE `' . $sFieldName . '` ' . $typedef . ' ' . $null_str;
            }
        }
        return $query;
    }

    public function configAction() {
        $this->view->title = $this->view->translate('Редактирование конфигурации');
        $this->view->subtitle = '';

        $this->view->modules = array_keys(\Sl_Module_Manager::getAvailableModels());
    }

    public function rebildconfigAction() {
        $data = \Sl_Module_Manager::getAvailableModels();
        foreach ($data as $modulename => $models_array) {
            try {
                echo $modulename . "\r\n";
                $module = \Sl_Module_Manager::getInstance()->getModule($modulename);
                // Данные о моделях
                $models = array();
                foreach ($models_array as $modelname) {
                    $models[$modelname] = array(
                        'model' => \Sl_Model_Factory::object($modelname, $module),
                        'fields' => \Sl_Model_Factory::object($modelname, $module)->describeFields(),
                    );
                }
                // Данные из module.php
                try {
                    $form_section = $module->section('forms');
                    $listview_section = $module->section('listview_options');
                    $duplicate_section = $module->section('duplicate');
                    $gractions_section = $module->section('group_actions');
                    $custom_config = $module->section('custom_configs');
                    $list_status_config = $module->section('lists_statuses');
                    $lists_section = $module->section('lists');
                    $relations_section = $module->section('modulerelations');
                    $detailed_section = $module->section('detailed');
                } catch (\Exception $e) {
                    echo "Error fetching old data. " . $e->getMessage() . "\r\n";
                }

                foreach ($models as $modelname => $modeldata) {
                    // Пишем {modelname}/model.php
                    \Sl\Service\Config::write($modeldata['model'], 'model', $modeldata['fields']);
                    // Пишем {modelname}/form.php
                    $c_data = null;
                    $key = 'model_' . $modelname . '_form';
                    if (isset($form_section->$key)) {
                        $c_data = $form_section->$key->toArray();
                    }
                    if ($custom_config && ($custom_config instanceof \Zend_Config)) {
                        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($custom_config->toArray()));
                        foreach ($iterator as $k => $v) {
                            if ($iterator->getSubIterator(0)->key() == 'userroles') {
                                if ($iterator->getSubIterator(2)->key() == 'forms') {
                                    if ($iterator->getSubIterator(3)->key() == $key) {
                                        if (!isset($c_data[\Sl\Service\Config::ROLES_KEY])) {
                                            $c_data[\Sl\Service\Config::ROLES_KEY] = array();
                                        }
                                        if (!isset($c_data[\Sl\Service\Config::ROLES_KEY][$iterator->getSubIterator(1)->key()])) {
                                            $c_data[\Sl\Service\Config::ROLES_KEY][$iterator->getSubIterator(1)->key()] = $iterator->getSubIterator(3)->current();
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($c_data) {
                        \Sl\Service\Config::write($modeldata['model'], 'form', $c_data);
                    }
                    // Пишем {modelname}/listview.php
                    $c_data = null;
                    $key = $modeldata['model']->findModelName();
                    if (isset($listview_section->$key)) {
                        $c_data = $listview_section->$key->toArray();
                    }
                    if ($custom_config && ($custom_config instanceof \Zend_Config)) {
                        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($custom_config->toArray()));
                        foreach ($iterator as $k => $v) {
                            if ($iterator->getSubIterator(0)->key() == 'userroles') {
                                if ($iterator->getSubIterator(2)->key() == 'listview_options') {
                                    if ($iterator->getSubIterator(3)->key() == $key) {
                                        if (!isset($c_data[\Sl\Service\Config::ROLES_KEY])) {
                                            $c_data[\Sl\Service\Config::ROLES_KEY] = array();
                                        }
                                        if (!isset($c_data[\Sl\Service\Config::ROLES_KEY][$iterator->getSubIterator(1)->key()])) {
                                            $c_data[\Sl\Service\Config::ROLES_KEY][$iterator->getSubIterator(1)->key()] = $iterator->getSubIterator(3)->current();
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($c_data) {
                        \Sl\Service\Config::write($modeldata['model'], 'listview', $c_data);
                    }
                    // Пишем filters
                    try {
                        \Sl\Service\Config::read($modeldata['model'], 'filters');
                    } catch (\Exception $e) {
                        // Только если настроек еще нет
                        \Sl\Service\Config::write($modeldata['model'], 'filters', array(
                            '_default' => array(
                                'name' => '_default',
                                'description' => 'По-умолчанию',
                                'filter' => array(
                                    'type' => 'multi',
                                    'comparison' => 1, // AND
                                    'comps' => array(
                                        '_system' => array(
                                            'type' => 'multi',
                                            'comparison' => 1, // AND
                                            'comps' => array(
                                                'active' => array(
                                                    'type' => 'eq',
                                                    'field' => 'active',
                                                    'value' => 1
                                                ),
                                            ),
                                        ),
                                        '_user' => array(
                                            'type' => 'multi',
                                            'comparison' => 2, //OR,
                                            'comps' => array(
                                                '_custom' => array(
                                                    'type' => 'multi',
                                                    'comparison' => 1, // AND
                                                    'comps' => array(
                                                    ),
                                                ),
                                                '_id' => array(
                                                    'type' => 'in',
                                                    'field' => 'id',
                                                    'value' => array(),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            )
                        ));
                    }
                    // Пишем fieldsets
                    try {
                        \Sl\Service\Config::read($modeldata['model'], 'fieldsets');
                    } catch (\Exception $e) {
                        // Пишем только если файла еще нет
                        \Sl\Service\Config::write($modeldata['model'], 'fieldsets', array(
                            '_default' => array(
                                'fields' => array_diff(array_keys($modeldata['fields']), array('id', 'active', 'archived', 'create', 'timestamp')),
                                'name' => '_default',
                                'label' => 'По-умолчанию',
                            ),
                        ));
                    }
                    // Пишем {modelname}/duplicate.php
                    $key = $modeldata['model']->findModelName();
                    if ($duplicate_section && isset($duplicate_section->$key)) {
                        \Sl\Service\Config::write($modeldata['model'], 'duplicate', $duplicate_section->$key->toArray());
                    }
                    // Пишем {modelname}/detailed.php
                    $c_data = null;
                    $key = $modeldata['model']->findModelName();
                    if ($detailed_section && isset($detailed_section->$key)) {
                        \Sl\Service\Config::write($modeldata['model'], 'detailed', $detailed_section->$key->toArray());
                    }
                }
                // Пишем groupactions.php
                if ($gractions_section) {
                    \Sl\Service\Config::write($module, 'groupactions', $gractions_section->toArray());
                }
                // Пишем list_status.php
                if ($list_status_config) {
                    \Sl\Service\Config::write($module, 'list_status', $list_status_config->toArray());
                }
                // Пишем lists.php
                if ($lists_section) {
                    \Sl\Service\Config::write($module, 'lists', $lists_section->toArray());
                }
                // Пишем relations.php
                if ($relations_section) {
                    \Sl\Service\Config::write($module, 'relations', $relations_section->toArray());
                }
            } catch (\Exception $e) {
                echo "Rebuild error. " . $e->getMessage() . "\r\n";
            }
        }
        die("\r\nDONE\r\n");
    }

    public function ajaxgetoptionsAction() {
        $this->view->result = true;
        try {
            $available_models = \Sl_Module_Manager::getAvailableModels();
            $available_sections = array(
                'model' => array(
                    'model',
                    'form',
                    'list',
                ),
                'module' => array(
                    'groupactions',
                    'lists',
                    'list_status',
                    'relations',
                ),
            );
            $path_data = explode('|', $this->getRequest()->getParam('path'));
            array_shift($path_data);
            $data = array();
            switch ($this->getRequest()->getParam('type')) {
                case 'module':
                    $module = \Sl_Module_Manager::getInstance()->getModule($this->getRequest()->getParam('value', ''));
                    if (!$module) {
                        throw new \Exception('Can\'t determine module. ' . __METHOD__);
                    }
                    $models = $available_models[$module->getName()];
                    $data = array();
                    foreach ($models as $model) {
                        $data[] = array(
                            'label' => $this->view->translate('title_' . $model . '_' . $module->getName()),
                            'value' => $model,
                            'type' => 'model',
                        );
                    }
                    foreach ($available_sections['module'] as $k => $section) {
                        $data[] = array(
                            'label' => $this->view->translate('title_section_module_' . $section),
                            'value' => $section,
                            'type' => 'section',
                        );
                    }
                    break;
                case 'model':
                    foreach ($available_sections['model'] as $k => $section) {
                        $data[] = array(
                            'label' => $this->view->translate('title_section_model_' . $section),
                            'value' => $section,
                            'type' => 'section',
                        );
                    }
                    break;
                case 'section':
                    switch ($this->getRequest()->getParam('ptype')) {
                        case 'module':
                            $module = $path_data[0];
                            $t_data = \Sl\Service\Config::read($module, $this->getRequest()->getParam('value', ''), \Sl\Service\Config::MERGE_TYPE_MODULE_DATA);
                            $data = $this->_mapConfig($t_data->toArray(), array('stateOpened' => true, 'stateLoad' => true));
                            break;
                        case 'model':
                            $model = \Sl\Service\Helper::getModelByAlias($path_data[1], $path_data[0]);
                            if (!$model) {
                                throw new \Exception('Can\'t determine model. ' . __METHOD__);
                            }
                            $t_data = \Sl\Service\Config::read($model, $this->getRequest()->getParam('value', ''), \Sl\Service\Config::MERGE_TYPE_NOMERGE);
                            $data = $this->_mapConfig($t_data->toArray(), array('stateLoad' => true));
                            array_walk($data, function($v) {
                                        $v['stateOpened'] = true;
                                    });
                            break;
                    }
                    break;
            }
            $this->view->data = $data;
            \Sl\Service\Benchmark::save('before responce');
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->code = $e->getCode();
            $this->view->description = $e->getMessage();
        }
    }

    public function ajaxsetoptionsAction() {
        $this->view->result = true;
        try {
            $model = \Sl\Service\Helper::getModelByAlias($this->getRequest()->getParam('model', ''));
            if (!$model) {
                throw new \Exception('Wrong model alias given. ' . __METHOD__);
            }
            $section = $subsection = null;
            list($subsection, $section) = explode('.', $this->getRequest()->getParam('section', ''));

            $config = new \Sl\Config(array(), true);
            $config->$section = $this->_prepareConfig($this->getRequest()->getParam('data', array()));
            print_r($config->toArray());
            die;
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }

    public function ajaxsetallpermissionAction() {

        $role_id = $this->getRequest()->getParam('role_id');
        $action = $this->getRequest()->getParam('res_action');
        $priv = $this->getRequest()->getParam('priv');

        if ((!$role_id) || (!$action) || (!in_array($priv, array('0', '1')))) {
            die('Somethig wrong with entry parameters');
        }

        $module = \Sl_Module_Manager::getAvailableModels();
        $res_tpl = array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'action' => $action
        );
        $res = array();
        foreach ($module as $modulename => $models) {
            foreach ($models as $modelname) {
                $res[] = \Sl_Service_Acl::joinResourceName(array_merge($res_tpl, array(
                            'module' => $modulename,
                            'controller' => $modelname
                )));
            }
        };
        //print_R($res); die;
        //foreach ($res as $key => $resource) {
            //if (!\Sl_Service_Acl::isAllowed($resource, $priv)) {
error_reporting(E_ERROR);
        foreach ($res as $key => $resource_name) {
            $resource_o = \Sl_Model_Factory::mapper('resource', 'auth')->findByName($resource_name);
                if(!$resource_o) continue;
$resource = $resource_o->getId();


$permissions = \Sl_Model_Factory::mapper ( 'permission', 'auth' )->fetchAllByRoleResource ( $role_id, $resource );
                        $saved = array();
            if (count ( $permissions )) {
                                foreach ( $permissions as $permission ) {
                                        $permission->setPrivilege ( $priv );
                                        $permission->setActive ( 1 );

                                        $saved[] = \Sl_Model_Factory::mapper ($permission)->save ( $permission, true );
                                }
                        } else {
                                $obj = \Sl_Model_Factory::object ( 'permission', 'auth' );
                                $obj->setRoleId ( $role_id );
                                $obj->setResourceId ( $resource );
                                $obj->setPrivilege ( $priv );

                                $saved[] = \Sl_Model_Factory::mapper ($obj)->save ( $obj, true );
                        }
/*
                $resource_obj = \Sl_Model_Factory::mapper('resource', 'auth')->findByName($resource);
                if ($resource_obj instanceof \Sl_Model_Abstract) {
                    $resource_id = $resource_obj->getId();
                    $permission = \Sl_Model_Factory::object('permission', 'auth');
                    $permission->setRoleId($role_id);
                    $permission->setResourceId($resource_id);
                    $permission->setPrivilege($priv);

                    \Sl_Model_Factory::mapper($permission)->save($permission, true);
                }*/
            //}
        }
	print_r($saved);
        die('done');
    }

    public function fixdealernamesAction() {
        error_reporting(E_ERROR);
        $dealers = \Sl_Model_Factory::mapper('dealer', 'customers')->fetchAll('active = 1');
        foreach ($dealers as $key => $dealer) {
            $name = $dealer->getName();
            if (!preg_match('([0-9])', $name)) {
                $dealer = \Sl_Model_Factory::mapper($dealer)->findRelation($dealer, 'customerisdealer');
                $customers = $dealer->fetchRelated('customerisdealer');
                if (count($customers)) {
                    $customer = current($customers);
                    if ($customer instanceof \Sl\Module\Customers\Model\Customer) {
                        $name = $customer->getName();
                        $mass[$name] = $dealer->getName();
                        $dealer->setName($name);
                        \Sl_Model_Factory::mapper($dealer)->save($dealer);
                    }
                }
            }
        }

        print_R($mass);
        die('done');
    }

    protected function _mapConfig($data, $extras = array()) {
        $result = array();
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $result[] = array_merge(array(
                    'label' => $k,
                    'items' => $this->_mapConfig($v, $extras),
                        ), $extras);
            } else {
                $result[] = array_merge(array(
                    'label' => $k,
                    'value' => $v
                        ), $extras);
            }
        }
        return $result;
    }

    protected function _prepareArray($data) {
        $result = array();
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $result[] = array(
                    'name' => $k,
                    'value' => '-',
                    'options' => $this->_prepareArray($v),
                );
            } else {
                $result[] = array(
                    'name' => $k,
                    'value' => $v,
                    'options' => array(),
                );
            }
        }
        return $result;
    }

    protected function _prepareConfig($data) {
        $result = array();
        foreach ($data as $v) {
            if (isset($v['options']) && is_array($v['options']) && count($v['options'])) {
                $result[$v['name']] = $this->_prepareConfig($v['options']);
            } else {
                $result[$v['name']] = $v['value'];
            }
        }
        return $result;
    }
    
    public function createmoduleAction() {
        $form = FormFactory::build(array(ModuleManager::find('home'), 'createmodule'));
        
        $form->getElement('name')
                ->addValidator(new \Sl\Validate\InArray(array(
                    'inverse' => true,
                    'haystack' => array_map(function($el){ return $el->getName(); }, ModuleManager::getModules()),
                )))
                ->setRequired(true);
        
        $this->view->form = $form;
        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getParams())) {
                $controlers = array();
                if($this->getRequest()->getParam('create_main_controller', false)) {
                    $controlers[] = 'main';
                }
                if($this->getRequest()->getParam('create_admin_controller', false)) {
                    $controlers[] = 'admin';
                }
                $name = $this->getRequest()->getParam('name', '');
                $activate = $this->getRequest()->getParam('activate', false);
                
                \Sl\Service\ClassCreator::createModule($name, $controlers, $activate);
            } else {
                $form->populate($this->getRequest()->getParams());
                $this->view->errors = $form->getMessages();
            }
        }
    }

}
