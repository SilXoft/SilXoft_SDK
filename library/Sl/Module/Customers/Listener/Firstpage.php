<?php
namespace Sl\Module\Customers\Listener;

class Firstpage extends \Sl_Listener_Abstract implements \Sl_Listener_Router_Interface {
    public function onAfterInit(\Sl_Event_Router $event) {
        if (\Zend_Auth::getInstance()->hasIdentity()){
			$resource = \Sl_Service_Acl::joinResourceName(array(
                'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                'module' => 'customers',
                'controller' => 'customer',
                'action' => 'list',
            ));
            if(\Sl_Service_Acl::isAllowed($resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
                if (!($router = $event -> getRouter()))
                    return;
                if($router->hasRoute('home')) {
                    $router->removeRoute('home');
                }
                if (!$router -> hasRoute('home')) {
                    $route = new \Zend_Controller_Router_Route_Static('home', array(
                        'module' => 'customers',
                        'controller' => 'customer',
                        'action' => 'list',
                    ));
                    $router -> addRoute('home', $route);
                }
            }
		}
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
        
    }

    public function onSetRequest(\Sl_Event_Router $event) {
        
    }

    public function onSetResponse(\Sl_Event_Router $event) {
        
    }
}