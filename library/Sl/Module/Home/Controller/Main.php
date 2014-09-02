<?php
namespace Sl\Module\Home\Controller;

use Sl\Model\Identity\Fieldset\Comparison as Comps;

class Main extends \Sl_Controller_Action {

    public function ajaxcalculatorAction() {
        if ($this->getRequest()->isPost()) {

            $this->view->result = array();
            try {
                if ($calculators = $this->getRequest()->getParam('model_calculators')) {
                    $calculators_arr = explode(',', $calculators);
                    $changed_fields = $this->getRequest()->getParam('model_changed_fields', array());
                    $values = \Sl_Calculator_Manager::Calculate($calculators_arr, $changed_fields, $this->getRequest()->getParams());
                    $values = \Sl_Calculator_Manager::prepareFieldNames($values);
                    $this->view->result = $values;
                }
            } catch (\Exception $e) {
                $this->view->error = $e->getMessage();
            }
        }
    }

    public function listAction() {
        /* базовий стартовий Action */
    }

    /*
      public function printformsAction() {

      } */

    public function ajaxinformerAction() {
        if ($this->getRequest()->isPost()) {
            $this->view->result = true;
            $this->view->data = array();

            try {

                if ($request = $this->getRequest()->getParam('informer_request', false)) {
                    $informer = new \Sl\Module\Home\Informer\Informer;
                    $informer->setRequest($request);
                    \Sl_Event_Manager::trigger(new \Sl_Event_Informer('informerRequest', array(
                        'informer' => $informer,
                    )));
                    $this->view->data = $informer->getAnswer();
                }
            } catch (\Exception $e) {
                $this->view->result = FALSE;
                $this->view->description = $e->getMessage();
            }
        }
    }

    public function ajaxdescribefilterAction() {
        $this->view->result = true;
        try {
            $alias = $this->getRequest()->getParam('model', '');
            $model = \Sl\Service\Helper::getModelByAlias($alias);
            if (!$model) {
                throw new \Exception('Can\'t determine model. ' . __METHOD__);
            }
            $filter_name = $this->getRequest()->getParam('filter', false);
            if (!$filter_name) {
                throw new \Exception('Filter param must be set. ' . __METHOD__);
            }
            $section = \Sl_Module_Manager::getInstance()->getModule($model->findModuleName())->section('listview');
            if (!$section || !($section instanceof \Zend_Config)) {
                throw new \Exception('Listview section is empty. Fill it before requesting data. ' . __METHOD__);
            }
            $section = $section->toArray();
            $filter_data = @$section[$model->findModelName()]['filters'][$filter_name];
            if (!is_array($filter_data)) {
                throw new \Exception('Something wrong with field data. ' . __METHOD__);
            }
            $filter_data = $this->_prepareFilterData($filter_data, $section[$model->findModelName()]['fields']);
            $this->view->filter = $filter_data;
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }

    public function ajaxdescribemodelAction() {
        $this->view->result = true;
        try {
            $alias = $this->getRequest()->getParam('model', '');
            $model = \Sl\Service\Helper::getModelByAlias($alias);
            if (!$model) {
                throw new \Exception('Can\'t determine model. ' . __METHOD__);
            }

            $path = $this->getRequest()->getParam('path', false);

            $rels = array();
            if ($path) {
                $rels = explode('.', $path);
            }

            $fields = $model->describeFields();
            $fields = array();
            $res_data = array();

            $module = \Sl_Module_Manager::getInstance()->getModule($model->findModuleName());

            $model_data = $module->modelConfig($model, array('listview' => 'fields'))->toArray();

            foreach ($model_data as $name => $data) {
                if (false !== strpos('.', $name)) { // Исключаем связанные поля
                    continue;
                }
                if (\Sl_Service_Acl::isAllowed(array(
                            $model,
                            $name
                                ), \Sl_Service_Acl::PRIVELEGE_READ)) {
                    $fields[$name] = array(
                        'name' => $name,
                        'type' => isset($data['type']) ? (is_array($data['type']) ? array_pop($data['type']) : $data['type']) : 'text',
                        'label' => is_array($data['label']) ? array_pop($data['label']) : $data['label'],
                    );
                }
            }

            foreach (\Sl_Model_Factory::mapper($model)->getAllowedRelations() as $relation) {
                //$rels[] = $relation;
                if (false === array_search($relation, $rels)) {
                    $title = 'title_modulerelation_' . $relation . '_' . $model->findModelName();
                    $related = \Sl_Modulerelation_Manager::getRelations($model, $relation)->getRelatedObject($model);
                    if ($title == $this->view->translate($title)) {
                        // Не удалось найти название связи
                        // Временно меняем на название связанной модели
                        $title = $this->view->translate(implode('_', array(
                                    'title',
                                    $related->findModelName(),
                                    $related->findModuleName(),
                                ))) . ' (' . $relation . ')';
                    } else {
                        $title = $this->view->translate($title);
                    }
                    $fields[$relation] = array(
                        'name' => $relation,
                        'type' => 'relation',
                        'model' => \Sl\Service\Helper::getModelAlias($related),
                        'label' => $title,
                    );
                }
            }
            $this->view->rels = $rels;
            $this->view->fields = $fields;
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
            $this->view->code = $e->getCode();
        }
    }

    protected function _prepareFilterData($filter, $config) {
        if (isset($filter['filter'])) { // Root
            $filter['filter'] = $this->_prepareFilterData($filter['filter'], $config);
        } else {
            if ($filter['type'] == 'multi') {
                foreach ($filter['comps'] as $k => $f) {
                    $filter['comps'][$k] = $this->_prepareFilterData($f, $config);
                }
            } else {
                $filter['field_type'] = isset($config[$filter['field']]['type']) ? $config[$filter['field']]['type'] : 'text';
                $filter['label'] = isset($config[$filter['field']]['label']) ? $config[$filter['field']]['label'] : $filter['field'];
            }
        }
        return $filter;
    }
    
    protected function _map2Config(array $data) {
        $res = array();
        foreach($data as $k=>$v) {
            if(in_array(strtolower($k), array('or', 'and'))) {
                $res[] = array(
                    'type' => 'multi',
                    'comp' => (strtolower($k) == 'and')?Comps\Multi::COMPARISON_AND:Comps\Multi::COMPARISON_OR,
                    'comps' => $this->_map2Config($v),
                );
            } else {
                $res[] = array(
                    'type' => $k,
                    'field' => key($v),
                    'value' => current($v),
                );
            }
        }
        return $res;
    }
    
    public function ajaxsavefilterAction() {
        $this->view->result = true;
        try {
            $filter_data = $this->getRequest()->getParam('filter', array());
            if(!isset($filter_data['name'])) {
                throw new \Exception('Can\'t save filter without name. '.__METHOD__);
            }
            $name = $filter_data['name'];
            unset($filter_data['name']);
            $filter = $this->_map2Config($filter_data);
            $this->view->filter = $filter;
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }

}
