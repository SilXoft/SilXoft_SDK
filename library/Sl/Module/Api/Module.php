<?php

namespace Sl\Module\Api;

class Module extends \Sl_Module_Abstract {

    public function getListeners() {
        return array(
            array(
                'listener' => new Listener\Acl($this),
                'order' => 5,
            ),
            new Listener\Forms($this),
            new Listener\Authorize($this),
        );
    }

    public function getModulerelations() {
        try {
            return \Sl\Service\Config::read($this, 'relations')->toArray();
        } catch(\Exception $e) {
            return array();
        }
    }

    public function getCalculators() {
        return array(
            
        );
    }

}
