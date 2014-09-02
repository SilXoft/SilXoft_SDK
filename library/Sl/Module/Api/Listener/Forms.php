<?php
namespace Sl\Module\Api\Listener;

class Forms extends \Sl_Listener_Abstract implements \Sl\Listener\Modelaction {
    
    public function onBefore(\Sl\Event\Modelaction $event) {
        if($event->getModel()->findModuleName() == $this->getModule()->getName()) {
            if($event->getCurrentAction() == 'create') {
                $form = $event->getView()->form;
                if($form && ($form instanceof \Sl\Form\Form)) {
                    if($event->getModel() instanceof \Sl\Module\Api\Model\Client) {
                        $client = \Sl\Module\Api\Service\Generator::client();
                        $form->getElement('name')->setOptions(array(
                            'value' => $client->getName(),
                        ));
                        $form->getElement('secret')->setOptions(array(
                            'value' => $client->getSecret(),
                        ));
                    }
                }
            }
        }
    }

    public function onAfter(\Sl\Event\Modelaction $event) {}
    public function onAfterPost(\Sl\Event\Modelaction $event) {}
    public function onBeforePost(\Sl\Event\Modelaction $event) {}

}