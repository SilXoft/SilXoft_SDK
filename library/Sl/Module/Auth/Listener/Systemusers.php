<?php
namespace Sl\Module\Auth\Listener;

class Systemusers extends \Sl_Listener_Abstract implements \Sl_Listener_Bootstrap_Interface {
    
    public function onAfterLayoutInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterRequestInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterTranslationInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterViewInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAppRun(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeLayoutInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeRequestInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeTranslationInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeViewInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterSessionInit(\Sl_Event_Bootstrap $event) {
        if(!\Zend_Auth::getInstance()->hasIdentity()) {
            if(!($guest_id = \Sl_Service_Settings::value('GUEST_USER_ID', 0))) {
                die('Please set GUEST USER_ID in SystemSettings');
            }
            $guest = \Sl_Model_Factory::mapper('user', 'auth')->findExtended($guest_id, 'userroles');
            if(!$guest) {
                die('Can\'t find user for guest account.');
            }
            \Zend_Auth::getInstance()->getStorage()->write($guest);
        }
    }

}