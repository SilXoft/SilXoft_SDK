<?php
namespace Sl\Service\Config\Generator;

class Filters extends \Sl\Service\Config\Generator {
    
    public function getData(\Sl_Model_Abstract $model) {
        return array(
            'default' => array(
                'name' => 'default',
                'description' => 'По-умолчанию',
                'filter' => array (
                    'type' => 'multi',
                    'comps' => array (
                        0 => array (
                            'type' => 'eq',
                            'field' => 'active',
                            'value' => '1',
                        ),
                        1 => array (
                            'type' => 'eq',
                            'field' => 'archived',
                            'value' => '0',
                        ),
                    ),
                ),
            ),
        );
    }

}