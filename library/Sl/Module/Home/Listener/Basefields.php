<?php
namespace Sl\Module\Home\Listener;

use Sl_Service_Acl as Acl;

class Basefields extends \Sl_Listener_Abstract implements \Sl\Listener\Fieldset, \Sl\Listener\Dataset {

    protected $_base_fields = array(
        'id',
        'active',
        'archived',
        'extend',
    );

    public function onPrepare(\Sl\Event\Fieldset $event) {
        foreach ($this->_base_fields as $field) {
            if (!$event->getFieldset()->hasField($field)) {
                $event->getFieldset()->createField($field, array(
                    'visible' => false,
                    'type' => 'hidden',
                    'roles' => array(
                        'from',
                        'render',
                        'system'
                    ),
                ));
            } else {
                $event->getFieldset()->getField($field)->addRole('from');
            }
        }
        foreach($event->getModel()->ListsAssociations() as $fieldname=>$listname) {
            if(!$event->getFieldset()->hasField($fieldname)) {
                $f = $event->getFieldset()->createField($fieldname);
            } else {
                $f = $event->getFieldset()->getField($fieldname);
            }
            $f->addRole('from');
        }
        foreach($event->getFieldset()->getFields() as $field) {
            if(!$field->isRelated()) {
                if($listname = $event->getModel()->ListsAssociations($field->getName())) {
                    $options = \Sl\Service\Lists::getList($listname);
                    $options['-1'] = $this->getTranslator()->translate('All');
                    ksort($options);
                    $field->setOption('options', $options);
                }
            }
        }
    }

    public function onAfterProcessAll(\Sl\Event\Dataset $event) {
        
    }

    public function onAfterProcessItem(\Sl\Event\Dataset $event) {
        
    }

    public function onBeforeProcessAll(\Sl\Event\Dataset $event) {
        
    }

    public function onBeforeProcessItem(\Sl\Event\Dataset $event) {
        $item = $event->getItem();

        $model = $event->getModel();
        
        if ($item['extend']) {
            $extend_arr = explode('|', $item['extend']);
        } else {
            $extend_arr = explode('|', \Sl\Service\Helper::getModelExtend($model));
        }
        $extend = $extend_arr[count($extend_arr) - 2];

        $data_alias = $extend;
        $model = \Sl\Service\Helper::getModelByAlias($data_alias);

        $resource_data = array(
            'type' => Acl::RES_TYPE_MVC,
            'module' => $model->findModuleName(),
            'controller' => $model->findModelName(),
        );

        $resources = array(
            'edit' => Acl::joinResourceName(array_merge($resource_data, array('action' => \Sl\Service\Helper::TO_EDIT_ACTION))),
            'detailed' => Acl::joinResourceName(array_merge($resource_data, array('action' => \Sl\Service\Helper::TO_DETAILED_ACTION))),
            'list' => Acl::joinResourceName(array_merge($resource_data, array('action' => \Sl\Service\Helper::LIST_ACTION))),
        );

        $link = '#';
        if (Acl::isAllowed($resources['edit'], Acl::PRIVELEGE_ACCESS)) {
            $link = \Sl\Service\Helper::buildModelUrl($model, 'edit', array(
                        'id' => $item['id'],
            ));
        } elseif (Acl::isAllowed($resources['detailed'], Acl::PRIVELEGE_ACCESS)) {
            $link = \Sl\Service\Helper::buildModelUrl($model, 'detailed', array(
                        'id' => $item['id'],
            ));
        } elseif (Acl::isAllowed($resources['list'], Acl::PRIVELEGE_ACCESS)) {
            $link = \Sl\Service\Helper::buildModelUrl($model, 'list');
        }

        $metas = isset($item['_meta']) ? $item['_meta'] : array();
        $metas = array_merge($metas, array(
            'data' => array(
                'id' => $item['id'],
                'real-id' => $item['id'],
                'active' => (int) $item['active'],
                'archived' => (int) $item['archived'],
                'alias' => \Sl\Service\Helper::getModelAlias($model),
                'as-string' => isset($model) ? ((string) $model) : '',
                'editable' => (int) Acl::isAllowed($resources['edit'], Acl::PRIVELEGE_ACCESS),
                'link' => $link
            ),
        ));
        $item['_meta'] = $metas;
        $event->setItem($item);
    }

    public function onPrepareAjax(\Sl\Event\Fieldset $event) {
        
    }

}
