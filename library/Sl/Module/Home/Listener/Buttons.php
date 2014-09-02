<?php

namespace Sl\Module\Home\Listener;

use \Sl\View\Control as Button;
use \Sl\Service\Groupactions as Ga;

class Buttons extends \Sl_Listener_Abstract implements \Sl\Listener\View\Controller\Listtable, \Sl_Listener_View_Interface, \Sl\Listener\Fieldset, \Sl\Listener\Dataset {

    public function onHeadScript(\Sl_Event_View $event) {

        $event->getView()->headScript()->appendFile('/home/main/groupactions.js');
    }

    public function onBeforeProcessAll(\Sl\Event\Dataset $event) {
        
    }

    public function onAfterProcessAll(\Sl\Event\Dataset $event) {
        
    }

    public function onBeforeProcessItem(\Sl\Event\Dataset $event) {
        
    }

    public function onAfterProcessItem(\Sl\Event\Dataset $event) {
        $item = $event->getItem();
        $fieldname = Ga::GROUP_ACTION_CONFIG_KEY;
        @$item['_meta']['_by_field']['classes'][$fieldname][] = 'no_sort';
        switch (intval($event->getDataset()->getOption('popup'))) {
            case \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE:
            case \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE:
            case \Sl_Modulerelation_Manager::RELATION_MODEL_ITEM:
                $type = 1; // Radio
                break;
            default:
                $type = 0; // checbox
                break;
        }

        if ($event->getFieldset()->hasField($fieldname)) {
            $item[$fieldname] = \Sl\Serializer\Serializer::renderListviewSelector($type);
            //$item[$fieldname] = \Sl\Serializer\Serializer::renderListviewCheckbox();
            $event->setItem($item);
        }
    }

    public function onPrepare(\Sl\Event\Fieldset $event) {
        $fieldname = Ga::GROUP_ACTION_CONFIG_KEY;
        if (!$event->getFieldset()->hasField($fieldname)) {
            $field = $event->getFieldset()->createField($fieldname);
        } else {
            $field = $event->getFieldset()->getField($fieldname);
        }

        $field->fill(array(
            'type' => 'html',
            'sortable' => false,
            'searchable' => false,
            'width' => '14px',
            'roles' => array('render', 'fixed')
                ), true);

        $field = $event->getFieldset()->getField($fieldname);
        $event->getFieldset()->moveField($fieldname, 'first');

        $list_button = new \Sl\View\Control\Lists(array(
            'icon_class' => 'ok',
            'title' => $this->getTranslator()->translate('Групповая обработка'),
            'small' => true,
            'drop_dir' => 'down',
            'badge_text' => '0',
            'class' => 'groupbtn',
        ));
        // @TODO Переделать на \Sl\Service\Config !!!
        $group_actions_config = \Sl\Service\Groupactions::getGroupActions($event->getModel())->toArray();

        $list_button->addItem(new Button\Lists\Item(array(
            'label' => $this->getTranslator()->translate('Выбрать все на стр.'),
            'href' => '#',
            'icon' => 'check',
            'html_name' => 'pef_select_all',
            'class' => 'select_all',
        )));

        $res_ajaxselecteditems = \Sl_Service_Acl::joinResourceName(array(
                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                    'module' => $event->getModel()->findModuleName(),
                    'controller' => $event->getModel()->findModelName(),
                    'action' => 'ajaxselecteditems',
        ));

        if (\Sl_Service_Acl::isAllowed($res_ajaxselecteditems)) {
            $list_button->addItem(new Button\Lists\Item(array(
                'label' => $this->getTranslator()->translate('Выбрать все'),
                'href' => '#',
                'icon' => 'refresh',
                'html_name' => 'pef_select_allpage',
                'class' => 'select_allpage',
            )));
        }

        $list_button->addItem(new Button\Lists\Item(array(
            'label' => $this->getTranslator()->translate('Снять выделение'),
            'href' => '#',
            'icon' => 'minus',
            'html_name' => 'pef_select_all',
            'class' => 'deselect_all',
        )));

        $list_button->addItem(new Button\Lists\Item(array(
            'label' => $this->getTranslator()->translate('Спрятать'),
            'href' => '#',
            'icon' => 'eye-close',
            'html_name' => 'pef_select_all',
            'class' => 'hide_selected',
        )));

        if (count($group_actions_config)) {
            foreach ($group_actions_config as $action => $conf_array) {
                $ref_action = $action;
                if (isset($conf_array['action']) && $conf_array['action']) {
                    $ref_action = $conf_array['action'];
                }
                $resource = \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $event->getModel()->findModuleName(),
                            'controller' => $event->getModel()->findModelName(),
                            'action' => $ref_action
                ));

                if (\Sl_Service_Acl::isAllowed($resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
                    
                    $params = array();
                    if(isset($conf_array['params'])){
                        foreach ($conf_array['params'] as $key => $value) {
                            $params[] = $key;
                            $params[] = $value;
                        }                        
                    }

                    $list_button->addItem(new Button\Lists\Item(array(
                        'label' => $this->getTranslator()->translate('title_action_' . $action . '_btn'),
                        'href' => '#',
                        'rel' => implode('/', array_merge(array(
                            '',
                            $event->getModel()->findModuleName(),
                            $event->getModel()->findModelName(), $ref_action,
                                        ), ($params)
                                )
                        ),
                        'html_name' => 'pef_' . $event->getModel()->findModuleName() . '_' . $event->getModel()->findModelName(),
                        'icon' => $conf_array['icon'] ? $conf_array['icon'] : '',
                        'class' => 'action' . ($conf_array['autoclear'] ? ' autoclear' : ''),
                    )));
                }
            }

            /* Окрема обробка: доступні прінтформи: */
            $resource = \Sl_Service_Acl::joinResourceName(array(
                        'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                        'module' => $event->getModel()->findModuleName(),
                        'controller' => $event->getModel()->findModelName(),
                        'action' => \Sl\Service\Helper::GROUPPRINT_ACTION,
            ));

            if (\Sl_Service_Acl::isAllowed($resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
                $printforms = \Sl_Model_Factory::mapper('printform', 'home')
                        ->fetchAllByNameType(\Sl\Printer\Manager::type($event->getModel()), 'email');
                if (count($printforms)) {
                    $items = array();

                    foreach ($printforms as $printform) {
                        /* @var $printform \Sl\Module\Home\Model\Printform */
                        if ($printform->getRole() != $printform::GROUP_ROLE) {
                            continue;
                        }
                        $items[] = new Button\Lists\Item(array(
                            'label' => $printform->getDescription(),
                            'href' => '#',
                            'html_name' => $event->getModel()->findModuleName() . '_' . $event->getModel()->findModelName() . '_pf_' . $printform->getId(),
                            'class' => 'print_action',
                            'rel' => \Sl\Service\Helper::returnGroupPrintUrl($event->getModel(), $printform),
                        ));
                    }

                    if (count($items) == 1) {
                        $item = current($items);
                        $item->setIcon('print');
                        $list_button->addItem(current($items));
                    } elseif (count($items) > 1) {
                        $html_name = 'pef_' . $event->getModel()->findModuleName() . '_' . $event->getModel()->findModelName();
                        $list_item = new Button\Lists\Item(array(
                            'label' => $this->getTranslator()->translate('title_action_' . \Sl\Service\Helper::GROUPPRINT_ACTION),
                            'href' => '#',
                            'icon' => 'print',
                            'rel' => implode('/', array('', $event->getModel()->findModuleName(), $event->getModel()->findModelName(), \Sl\Service\Helper::GROUPPRINT_ACTION,)),
                            'html_name' => $html_name,
                        ));
                        $list_item->setSubitems($items);
                        $list_button->addItem($list_item);
                    }
                }
            }
        }
        $field->setHtml($list_button);
    }

    public function onListViewTableLegendButtons(\Sl_Event_View $event) {
        $model = $event->getOption('object');


        if (!$model)
            return;
        $group_actions_config = \Sl\Service\Groupactions::getGroupActions($model)->toArray();
        $event->getView()->legend_buttons;


        $html_name = 'pef_deselect_all';
        $event->getView()->legend_buttons[] = new Button\Simple(array(
            'attribs' => array('title' => $this->getTranslator()->translate('Снять выделение'),),
            'icon_class' => 'minus',
            'html_name' => $html_name,
            'like_btn' => true,
            'class' => 'deselect_all',
        ));

        $html_name = 'pef_hide_selected';
        $event->getView()->legend_buttons[] = new Button\Simple(array(
            'attribs' => array('title' => $this->getTranslator()->translate('Спрятать'),),
            'pull_right' => false,
            'icon_class' => 'eye-close',
            'html_name' => $html_name,
            'like_btn' => true,
            'class' => 'hide_selected',
        ));

        if (!$event->getView()->is_popup && count($group_actions_config)) {

            foreach ($group_actions_config as $action => $conf_array) {
                $resource = \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $model->findModuleName(),
                            'controller' => $model->findModelName(),
                            'action' => $action
                ));

                if (\Sl_Service_Acl::isAllowed($resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
                    $html_name = 'pef_' . $model->findModuleName() . '_' . $model->findModelName();
                    $event->getView()->legend_buttons[] = new Button\Simple(array(
                        'attribs' => array('title' => $this->getTranslator()->translate('title_action_' . $action . '_btn'),),
                        'rel' => $event->getView()->url(array(
                            'module' => $model->findModuleName(),
                            'controller' => $model->findModelName(),
                            'action' => $action
                        )),
                        'html_name' => $html_name,
                        'like_btn' => true,
                        'icon_class' => $conf_array['icon'] ? $conf_array['icon'] : '',
                        'class' => 'action' . ($conf_array['autoclear'] ? ' autoclear' : ''),
                    ));
                }
            }


            $resource = \Sl_Service_Acl::joinResourceName(array(
                        'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                        'module' => $model->findModuleName(),
                        'controller' => $model->findModelName(),
                        'action' => \Sl\Service\Helper::GROUPPRINT_ACTION,
            ));

            if (\Sl_Service_Acl::isAllowed($resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {

                $printforms = \Sl_Model_Factory::mapper('printform', 'home')
                        ->fetchAllByNameType(\Sl\Printer\Manager::type($model), 'email');
                if (count($printforms)) {
                    $items = array();

                    foreach ($printforms as $printform) {

                        if ($printform->getRole() != $printform::GROUP_ROLE) {
                            continue;
                        }
                        $html_name = $model->findModuleName() . '_' . $model->findModelName() . '_pf_' . $printform->getId();
                        /*   $items[] = new Button\Lists\Item(array(
                          'label' => $printform->getDescription(),
                          'href' => '#',
                          'html_name' => $html_name,
                          'class' =>'print_action',
                          'rel'=> \Sl\Service\Helper::returnPrintUrl($model, $printform),

                          ));
                         */
                        $event->getView()->legend_buttons[] = new Button\Simple(array(
                            'attribs' => array('title' => $printform->getDescription(),),
                            'html_name' => $html_name,
                            'like_btn' => true,
                            'icon_class' => 'print',
                            'class' => 'print_action',
                            'rel' => \Sl\Service\Helper::returnGroupPrintUrl($model, $printform),
                        ));
                    }

                    /*
                      if (count($items) == 1){
                      $item = current($items);
                      $item -> setIcon('print');
                      $list_button -> addItem(current($items));
                      } elseif(count($items) > 1){
                      $html_name = 'pef_' . $model -> findModuleName() . '_' . $model -> findModelName();
                      $list_item = new Button\Lists\Item( array(
                      'label' => $event -> getView() -> translate('title_action_' . \Sl\Service\Helper::PRINT_ACTION),
                      'href' => '#', //\Sl\Service\Helper::returnEmailFileUrl($model, $printform),
                      'icon' => 'print',
                      'rel' => $event->getView()->url(array(
                      'module' => $model -> findModuleName(),
                      'controller' => $model -> findModelName(),
                      'action' => $action
                      )),
                      'html_name' => $html_name,

                      ));
                      $list_item -> setSubitems($items);
                      $list_button -> addItem($list_item);
                      } */
                }
            }
        }
    }

    public function onBeforeListViewTableGroupButton(\Sl_Event_View $event) {

        $model = $event->getOption('object');


        if (!$model)
            return;

        if (!($model instanceof \Sl_Model_Abstract))
            $model = \Sl\Service\Helper::getModelByAlias($model);
        $group_actions_config = \Sl\Service\Groupactions::getGroupActions($model)->toArray();
        $list_button = $event->getView()->list_button;

        $html_name = 'pef_select_all';
        $list_button->addItem(new Button\Lists\Item(array(
            'label' => $this->getTranslator()->translate('Выбрать все на стр.'),
            'href' => '#', //\Sl\Service\Helper::returnEmailFileUrl($model, $printform),
            'icon' => 'check',
            'html_name' => $html_name,
            'class' => 'select_all',
        )));
        $res_ajaxselecteditems = \Sl_Service_Acl::joinResourceName(array(
                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                    'module' => $model->findModuleName(),
                    'controller' => $model->findModelName(),
                    'action' => 'ajaxselecteditems',
        ));
        // var_dump($res_ajaxselecteditems);
        if (\Sl_Service_Acl::isAllowed($res_ajaxselecteditems)) {
            $html_name = 'pef_select_allpage';
            $list_button->addItem(new Button\Lists\Item(array(
                'label' => $this->getTranslator()->translate('Выбрать все'),
                'href' => '#', //\Sl\Service\Helper::returnEmailFileUrl($model, $printform),
                'icon' => 'refresh',
                'html_name' => $html_name,
                'class' => 'select_allpage',
            )));
        }
        $html_name = 'pef_select_all';
        $list_button->addItem(new Button\Lists\Item(array(
            'label' => $this->getTranslator()->translate('Снять выделение'),
            'href' => '#', //\Sl\Service\Helper::returnEmailFileUrl($model, $printform),
            'icon' => 'minus',
            'html_name' => $html_name,
            'class' => 'deselect_all',
        )));
        $html_name = 'pef_select_all';
        $list_button->addItem(new Button\Lists\Item(array(
            'label' => $this->getTranslator()->translate('Спрятать'),
            'href' => '#', //\Sl\Service\Helper::returnEmailFileUrl($model, $printform),
            'icon' => 'eye-close',
            'html_name' => $html_name,
            'class' => 'hide_selected',
        )));

        if (!$event->getView()->is_popup && count($group_actions_config)) {

            foreach ($group_actions_config as $action => $conf_array) {
                $ref_action = $action;
                if ($conf_array['action']) {
                    $ref_action = $conf_array['action'];
                }
                $resource = \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $model->findModuleName(),
                            'controller' => $model->findModelName(),
                            'action' => $ref_action
                ));

                if (\Sl_Service_Acl::isAllowed($resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
                    $html_name = 'pef_' . $model->findModuleName() . '_' . $model->findModelName();
                    $list_button->addItem(new Button\Lists\Item(array(
                        'label' => $event->getView()->translate('title_action_' . $action . '_btn'),
                        'href' => '#', //\Sl\Service\Helper::returnEmailFileUrl($model, $printform),
                        'rel' => $event->getView()->url(array_merge(array(
                            'module' => $model->findModuleName(),
                            'controller' => $model->findModelName(),
                            'action' => $ref_action,
                                        ), (isset($conf_array['params']) ? $conf_array['params'] : array()))),
                        'html_name' => $html_name,
                        'icon' => $conf_array['icon'] ? $conf_array['icon'] : '',
                        'class' => 'action' . ($conf_array['autoclear'] ? ' autoclear' : ''),
                    )));
                }
            }

            /* Окрема обробка: доступні прінтформи: */
            $resource = \Sl_Service_Acl::joinResourceName(array(
                        'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                        'module' => $model->findModuleName(),
                        'controller' => $model->findModelName(),
                        'action' => \Sl\Service\Helper::GROUPPRINT_ACTION,
            ));

            if (\Sl_Service_Acl::isAllowed($resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {

                $printforms = \Sl_Model_Factory::mapper('printform', 'home')
                        ->fetchAllByNameType(\Sl\Printer\Manager::type($model), 'email');
                if (count($printforms)) {
                    $items = array();

                    foreach ($printforms as $printform) {

                        /* @var $printform \Sl\Module\Home\Model\Printform */
                        if ($printform->getRole() != $printform::GROUP_ROLE) {
                            continue;
                        }
                        $html_name = $model->findModuleName() . '_' . $model->findModelName() . '_pf_' . $printform->getId();
                        $items[] = new Button\Lists\Item(array(
                            'label' => $printform->getDescription(),
                            'href' => '#',
                            'html_name' => $html_name,
                            'class' => 'print_action',
                            'rel' => \Sl\Service\Helper::returnGroupPrintUrl($model, $printform),
                        ));
                    }
                    if (count($items) == 1) {
                        $item = current($items);
                        $item->setIcon('print');
                        $list_button->addItem(current($items));
                    } elseif (count($items) > 1) {
                        $html_name = 'pef_' . $model->findModuleName() . '_' . $model->findModelName();
                        $list_item = new Button\Lists\Item(array(
                            'label' => $event->getView()->translate('title_action_' . \Sl\Service\Helper::GROUPPRINT_ACTION),
                            'href' => '#', //\Sl\Service\Helper::returnEmailFileUrl($model, $printform),
                            'icon' => 'print',
                            'rel' => $event->getView()->url(array(
                                'module' => $model->findModuleName(),
                                'controller' => $model->findModelName(),
                                'action' => \Sl\Service\Helper::GROUPPRINT_ACTION,
                            )),
                            'html_name' => $html_name,
                        ));
                        $list_item->setSubitems($items);
                        $list_button->addItem($list_item);
                    }
                }
            }
        }

        $event->getView()->list_button = $list_button;
        //   echo $list_button;
    }

    public function onPageOptions(\Sl_Event_View $event) {
        
    }

    public function onLogo(\Sl_Event_View $event) {
        
    }

    public function onHeadLink(\Sl_Event_View $event) {
        
    }

    public function onHeader(\Sl_Event_View $event) {
        
    }

    public function onNav(\Sl_Event_View $event) {
        
    }

    public function onBeforePageHeader(\Sl_Event_View $event) {
        
    }

    public function onBeforeContent(\Sl_Event_View $event) {
        
    }

    public function onAfterContent(\Sl_Event_View $event) {
        
    }

    public function onHeadTitle(\Sl_Event_View $event) {
        
    }

    public function onBodyBegin(\Sl_Event_View $event) {
        
    }

    public function onBodyEnd(\Sl_Event_View $event) {
        
    }

    public function onFooter(\Sl_Event_View $event) {
        
    }

    public function onContent(\Sl_Event_View $event) {
        
    }

    public function onPrepareAjax(\Sl\Event\Fieldset $event) {
        
    }

}
