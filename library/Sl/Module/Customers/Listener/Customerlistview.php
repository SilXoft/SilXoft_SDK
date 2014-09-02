<?php

namespace Sl\Module\Customers\Listener;

class Customerlistview extends \Sl_Listener_Abstract implements \Sl\Listener\Fieldset, \Sl\Listener\Dataset {

    public function onPrepare(\Sl\Event\Fieldset $event) {

        if ($event->getModel() instanceof \Sl\Module\Customers\Model\Customer) {

            $addition_fields = array(
                'ballance' => array(
                    'label' => '  ',
                    'title' => $this->getTranslator()->translate('История транзакций'),
                    'roles' => array(
                        'render',
                    )
                ),
                'is_dealer' => array(
                    'label' => $this->getTranslator()->translate('Это дилер'),
                    'roles' => array(
                        'from',
                    )
                )
            );

            foreach ($addition_fields as $field => $options) {
                if (!$event->getFieldset()->hasField($field)) {
                    $cur_field = $event->getFieldset()->createField($field);
                } else {
                    $cur_field = $event->getFieldset()->getField($field);
                }
                try {
                $cur_field->fill($options);
                } catch(\Exception $e) {
                    echo $e->getMessage();
                    die;
                }
            }
        }
    }

    public function onAfterProcessAll(\Sl\Event\Dataset $event) {
        
    }

    public function onAfterProcessItem(\Sl\Event\Dataset $event) {
        if ($event->getModel() instanceof \Sl\Module\Customers\Model\Customer) {
            $item = $event->getItem();

            $item['ballance'] = '<a href="#" data-ballance="' . $item['id'] . '" customer_name = "' . $item['name'] . '" class="finoperationballance_btn icon-question-sign" title="История транзакций"></a>';

            if ($item['is_dealer']) {
                // var_dump($item['is_dealer']);
                $item['name'] .= '<span class="htmlify icon-star-empty pull-right" title="' . $this->getTranslator()->translate('Дилер') . '" >&nbsp;</span>';
            }
            $event->setItem($item);
        }
    }

    public function onBeforeProcessAll(\Sl\Event\Dataset $event) {
        
    }

    public function onBeforeProcessItem(\Sl\Event\Dataset $event) {
        
    }

    public function onPrepareAjax(\Sl\Event\Fieldset $event) {
        
    }

}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
