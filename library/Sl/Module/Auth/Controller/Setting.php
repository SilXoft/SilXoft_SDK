<?php
namespace Sl\Module\Auth\Controller;

use Sl\Service\Config as Config;
use Sl\Module\Auth\Service\Usersettings as AuthSettings;

use Sl\Model\Identity\Fieldset\Comparison as Comp;

class Setting extends \Sl_Controller_Action {
    
    public function ajaxsavefoldersAction() {
        $this->view->result = true;
        try {
            $alias = $this->getRequest()->getParam('alias', '');
            $model = \Sl\Service\Helper::getModelByAlias($alias);
            if(!$model) {
                throw new \Exception('Can\'t build model from such alias "'.$alias.'".');
            }
            $path = $this->getRequest()->getParam('path', '');
            $parent = $this->getRequest()->getParam('parent', false);
            $data = $this->getRequest()->getParam('data', array());
            if(!is_array($data)) {
                throw new \Exception('Wrong data param. '.__METHOD__);
            }
            if(!$parent) {
                throw new \Exception('Parent param is required. '.__METHOD__);
            }
            if('true' === ($this->getRequest()->getParam('simple', false))) {
                // Только название и описание
                $config_data = Config::read($model, $path)
                                            ->merge(AuthSettings::read($model, $path))
                                            ->merge(new \Zend_Config(array(
                                                'name' => $data['name'],
                                                'label' => $data['name'],
                                                'description' => $data['description']
                                            ), true))->toArray();
                AuthSettings::write($model, 'filters/'.$data['name'], $config_data);
                try {
                    AuthSettings::clean($model, $path);
                } catch (\Exception $e) {

                }
                $this->view->data = Config::read($model, $path)
                                            ->merge(AuthSettings::read($model, $path))
                                                ->toArray();
            } else {
                // Нужно смерджить из parent-а
                $config = \Sl\Service\Config::read($model, $parent);
                $parent_data = $config->merge(AuthSettings::read($model, $parent));
                if(isset($data['filter']['comps'])) {
                    $comps = $data['filter']['comps'];
                    unset($data['filter']['comps']);
                    foreach($comps as $comp) {
                        @$data['filter']['comps']['_user']['comps']['_custom']['comps'][md5($data['name'].microtime(true))] = $comp;
                    }
                }
                $data = $parent_data->merge(new \Sl\Config($data, true))->toArray();
                $this->view->data = AuthSettings::write($model, $path, $data)->toArray();
            }
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
    
    public function ajaxsavefieldsetAction() {
        $this->view->result = true;
        try {
            $alias = $this->getRequest()->getParam('alias', '');
            $model = \Sl\Service\Helper::getModelByAlias($alias);
            if(!$model) {
                throw new \Exception('Can\'t build model from such alias "'.$alias.'".');
            }
            $path = $this->getRequest()->getParam('path', '');
            $data = $this->getRequest()->getParam('data', array());
            if(!is_array($data)) {
                throw new \Exception('Wrong data param. '.__METHOD__);
            }
            $type = (bool) $this->getRequest()->getParam('popup', false);
            if($type) {
                $data['type'] = 'popup';
            }
            try {
                // Если путь существует - стираем, чтобы не оставалось старых данных
                AuthSettings::clean($model, $path);
            } catch (\Exception $e) {
                //
            }
            $this->view->data = AuthSettings::write($model, $path, $data)->toArray();
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
    
    public function ajaxsavestateAction() {
        $this->view->result = true;
        try {
            $alias = $this->getRequest()->getParam('alias', '');
            $model = \Sl\Service\Helper::getModelByAlias($alias);
            if(!$model) {
                throw new \Exception('Can\'t build model from such alias "'.$alias.'".');
            }
            $path = $this->getRequest()->getParam('path', '');
            $data = $this->getRequest()->getParam('data', array());
            AuthSettings::write($model, $path, $data);
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
    
    public function ajaxcleanAction() {
        $this->view->result = true;
        try {
            $alias = $this->getRequest()->getParam('alias', '');
            $model = \Sl\Service\Helper::getModelByAlias($alias);
            if(!$model) {
                throw new \Exception('Can\'t build model from such alias "'.$alias.'".');
            }
            $path = $this->getRequest()->getParam('path', '');
            AuthSettings::clean($model, $path);
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
    
    /**
     * Возвращает данные о наборах в зависимости от типа
     * 
     * Возможно, стоит вынести создание копий наборов по-умолчанию куда-то в другое место.
     * Но пока не придумал куда. Хотя тот-же код дублируется в контроллере.
     * 
     * @throws \Exception
     */
    public function ajaxgetcolumnsAction() {
        $this->view->result = true;
        try {
            $alias = $this->getRequest()->getParam('alias', '');
            $model = \Sl\Service\Helper::getModelByAlias($alias);
            if(!$model) {
                throw new \Exception('Can\'t build model from such alias "'.$alias.'".');
            }
            $type = (bool) $this->getRequest()->getParam('popup', false);
            $default_config_part = $type?'_popup':'_default';
            $default_config_path = 'fieldsets/'.$default_config_part;
            
            $default_user_part = $type?'current_popup':'current';
            $default_user_path = 'fieldsets/'.$default_user_part;
            
            $fields_config = Config::read($model, 'listview', Config::MERGE_FIELDS);//->merge(AuthSettings::read($model, 'listview'));
            // Возможные варианты
            // 1.   Нет никаких данных. Данные пользователя пустые, данные из конфигов тоже
            //      Нужно бы создать
            // 2.   Есть данные в конфигах, но нет в настройках пользователя
            //      Нужно скопировать из конфигов в настройки
            // 3.   Все есть - ничего не делаем
            try {
                $data = Config::read($model, $default_config_path);
                if(!count($data)) {
                    throw new \Exception('');
                }
            } catch (\Exception $e) {
                // Нет базового конфига
                // Падаем замертво - пусть кто-то другой разбирается :)
                throw new \Exception('No data in config files. Please check it and try again. '.__METHOD__);
            }
            try {
                $user_data = AuthSettings::read($model, $default_user_path);
                if(!count($user_data)) {
                    throw new \Exception('');
                }
            } catch (\Exception $e) {
                // Переписываем в настройки пользователя
                $data_c = $data->toArray();
                $data_c = array_merge($data_c, array(
                    'name' => $default_user_part,
                ), $type?array(
                    'type' => 'popup'
                ):array(
                    
                ));
                AuthSettings::write($model, $default_user_path, $data_c);
            }
            $fieldsets_config = Config::read($model, 'fieldsets')->merge(AuthSettings::read($model, 'fieldsets'));
            // Можно читать
            if(!$type) {
                foreach($fieldsets_config as $k=>$v) {
                    if($v->type && ($v->type === 'popup')) {
                        unset($fieldsets_config->$k);
                    }
                }
            } else {
                foreach($fieldsets_config as $k=>$v) {
                    if(!isset($v->type) || ($v->type !== 'popup')) {
                        unset($fieldsets_config->$k);
                    }
                }
            }
            $this->view->type = (int) $type;
            $fields = array();
            $fs = \Sl\Model\Identity\Fieldset\Factory::build($model, 'listview');
            foreach($fields_config->toArray() as $name=>$field) {
                if(!$fs->hasField($name)) {
                    $fs->createField($name);
                }
                if(!$fs->getField($name)->isBlocked()) {
                    $fields[$name] = $field;
                }
            }
            $this->view->fields = $fields;
            $fieldsets = array();
            foreach($fieldsets_config->toArray() as $name=>$fs_data) {
                $fs_data['fields'] = array_values(array_unique(array_intersect($fs_data['fields'], array_keys($fields))));
                $fieldsets[$name] = $fs_data;
            }
            $this->view->fieldsets = $fieldsets;
            $this->view->state = AuthSettings::read($model, 'state/fieldset');
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
    
    public function ajaxaddtofolderAction() {
        $this->view->result = true;
        try {
            $alias = $this->getRequest()->getParam('alias', '');
            $model = \Sl\Service\Helper::getModelByAlias($alias);
            if(!$model) {
                throw new \Exception('Can\'t build model from such alias "'.$alias.'".');
            }
            $ids = (array) $this->getRequest()->getParam('id', array());
            $folder = $this->getRequest()->getParam('folder', false);
            if(!$folder) {
                throw new \Exception('No folder name given. '.__METHOD__);
            }
            // Читаем информацию о нужной папке
            $data = AuthSettings::read($model, 'filters/'.$folder)->toArray();
            
            $value = (array) $data['filter']['comps']['_user']['comps']['_id']['value'];
            $value = array_unique(array_merge($value, $ids));
            $data['filter']['comps']['_user']['comps']['_id']['value'] = $value;
            $this->view->data = AuthSettings::write($model, 'filters/'.$folder, $data)->toArray();
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
    
    public function ajaxpushstateAction() {
        $this->view->result = true;
        try {
            $alias = $this->getRequest()->getParam('alias', '');
            $model = \Sl\Service\Helper::getModelByAlias($alias);
            if(!$model) {
                throw new \Exception('Can\'t build model from such alias "'.$alias.'".');
            }
            $cols = $this->getRequest()->getParam('cols', array());
            if(count($cols) == 0) {
                throw new \Exception('No data to save. '.__METHOD__);
            }
            // Пока сохраняем порядок и ширину колонок
            $state = AuthSettings::read($model, 'state');
            $path = 'fieldsets/'.$state->fieldset.'/fields';
            $cur_fields = AuthSettings::read($model, $path)->toArray();
            $fields = array();
            foreach($cols as $col_data) {
                if(isset($col_data['name']) && in_array($col_data['name'], $cur_fields)) {
                    $fields[] = $col_data['name'];
                }
                if(isset($col_data['width']) && $col_data['width']) {
                    AuthSettings::write($model, 'listview/'.$col_data['name'].'/width', $col_data['width']);
                }
            }
            AuthSettings::write($model, $path, $fields);
            $this->view->state = AuthSettings::read($model, 'state');
            $this->view->fields = Config::read($model, 'listview')->merge(AuthSettings::read($model, 'listview'))->toArray();
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
}