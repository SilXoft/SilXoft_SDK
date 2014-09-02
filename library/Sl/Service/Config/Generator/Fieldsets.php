<?php
namespace Sl\Service\Config\Generator;

class Fieldsets extends \Sl\Service\Config\Generator {
    
    public function getData(\Sl_Model_Abstract $model) {
        return array(
            'default' => array(
                'label' => 'По-умолчанию',
                'fields' => array_keys(\Sl\Service\Config::read($model, 'model', \Sl\Service\Config::MERGE_TYPE_NOMERGE)->toArray()),
            ),
        );
    }

}