<?php
namespace Sl\Module\Api\Listener;

class Authorize extends \Sl_Listener_Abstract implements \Sl_Listener_Bootstrap_Interface, \Sl_Listener_Router_Interface {
    
    /**
     * Добавление маршрутов на обработку запросов
     * авторизации по протоколу OAuth2
     */
    public function onRouteStartup(\Sl_Event_Router $event) {
        $router = \Zend_Controller_Front::getInstance()->getRouter();
        /*@var $router \Zend_Controller_Router_Abstract*/
        $router->addRoute('apiget', new \Zend_Controller_Router_Route('apiget/:resmodule/:rescontroller/:resaction/*', array(
            'module' => 'api',
            'controller' => 'oauth',
            'action' => 'getdata',
            'resmodule' => 'home',
            'rescontroller' => 'describe',
            'resaction' => 'list'
        )));
        
        $router->addRoute('apipost', new \Zend_Controller_Router_Route('apipost/:resmodule/:rescontroller/:resaction/*', array(
            'module' => 'api',
            'controller' => 'oauth',
            'action' => 'postdata',
            'resaction' => 'create',
        )));
        
        $router->addRoute('apiauthorize', new \Zend_Controller_Router_Route_Static('oauth2/authorize', array(
            'module' => 'api',
            'controller' => 'oauth',
            'action' => 'authorize',
        )));
        
        $router->addRoute('apitoken', new \Zend_Controller_Router_Route_Static('oauth2/token', array(
            'module' => 'api',
            'controller' => 'oauth',
            'action' => 'token',
        )));
    }
    
    public function onAfterLayoutInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterRequestInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterSessionInit(\Sl_Event_Bootstrap $event) {
        
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

    public function onAfterInit(\Sl_Event_Router $event) {
        
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

    public function onSetRequest(\Sl_Event_Router $event) {
        
    }

    public function onSetResponse(\Sl_Event_Router $event) {
        
    }

}