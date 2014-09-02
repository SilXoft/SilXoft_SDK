<?php
namespace Sl\Module\Home\Listener;
//error_reporting(E_ERROR);
class Cron extends \Sl_Listener_Abstract implements \Sl_Listener_Router_Interface, \Sl_Listener_Acl_Interface, \Sl_Listener_Bootstrap_Interface {
    
    protected $_cron = false;
    
    const CRON_USER_ID = 32;
    
    public function onAfterAclCreate(\Sl_Event_Acl $event) {
        $cron_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $this->getModule()->getName(),
            'controller' => 'cron',
            'action' => 'cron'
        ));
        if(!$event->getAcl()->has($cron_resource)) {
            $event->getAcl()->addResource($cron_resource);
        }
        $event->getAcl()->allow(null, $cron_resource);
    }

    public function onAfterInit(\Sl_Event_Router $event) {
        $cron_route_name = 'cron';
        $index = 0;
        
        while($event->getRouter()->hasRoute($cron_route_name.(($index == 0)?'':$index))) {
            $index++;
        }
        $cron_route_name = $cron_route_name.(($index == 0)?'':$index);
        
        $event->getRouter()->addRoute($cron_route_name, new \Zend_Controller_Router_Route_Static('cron', array(
            'module' => 'home',
            'controller' => 'cron',
            'action' => 'cron',
        )));
    }

    public function onBeforeAclCreate(\Sl_Event_Acl $event) {
        
    }

    public function onBeforeInit(\Sl_Event_Router $event) {
        
    }

    public function onDispatchLoopShutdown(\Sl_Event_Router $event) {
        
    }

    public function onDispatchLoopStartup(\Sl_Event_Router $event) {
        
    }

    public function onGetRequest(\Sl_Event_Router $event) {
        
    }

    public function onGetResponse(\Sl_Event_Router $event) {
        
    }

    public function onPostDispatch(\Sl_Event_Router $event) {
        
    }

    public function onPreDispatch(\Sl_Event_Router $event) {
        
    }

    public function onRouteShutdown(\Sl_Event_Router $event) {
        
    }

    public function onRouteStartup(\Sl_Event_Router $event) {
        if($this->_cron) {
            $this->initUser();
        }
    }

    public function onSetRequest(\Sl_Event_Router $event) {
        if(preg_match('#^/cron$#', $event->getRequest()->getRequestUri())) {
            $this->_cron = true;
        }
    }

    public function onSetResponse(\Sl_Event_Router $event) {
        
    }
    
    protected function initUser($cron_user_id = null) {
        if(is_null($cron_user_id)) {
            $cron_user_id = self::CRON_USER_ID;
        }
        $cur_user = \Zend_Auth::getInstance()->getIdentity();
        if(!\Zend_Auth::getInstance()->hasIdentity() || ($cur_user && ($cur_user->getId() != $cron_user_id))) {
            $user = \Sl_Model_Factory::mapper('user', 'auth')->findExtended($cron_user_id, array('userroles'));
            if(!$user) {
                throw new \Exception('No user is assigned for cron');
            }
            \Zend_Auth::getInstance()->getStorage()->write($user);
            // @TODO: Доработать и сделать нормально
            // Пока не знаю как
            header('Location: /cron');
        }
    }

    public function onAfterLayoutInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterRequestInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterTranslationInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterViewInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeLayoutInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeRequestInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeTranslationInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeViewInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAppRun(\Sl_Event_Bootstrap $event) {
        
    }

    public function onIsAllowed(\Sl_Event_Acl $event) {
        
    }

    public function onAfterSessionInit(\Sl_Event_Bootstrap $event) {
        
    }

}