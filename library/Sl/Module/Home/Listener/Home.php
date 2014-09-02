<?php

namespace Sl\Module\Home\Listener;

class Home extends \Sl_Listener_Abstract implements \Sl_Listener_Bootstrap_Interface, \Sl_Listener_Router_Interface, \Sl_Listener_Model_Interface, \Sl_Listener_Acl_Interface, \Sl\Listener\Action\Before\Edit, \Sl\Listener\Action\After\Edit, \Sl_Listener_View_Interface {

    const PAGE_NOT_AVAILABLE = 1000;

    protected $_request_error = null;

    /**
     * Перенаправляем на страницу "/auth" если пользователь не авторизирован
     */
    public function onRouteShutdown(\Sl_Event_Router $event) {
        //return;	
        $request = $event->getOption('request');

        $resource = \Sl_Service_Acl::joinResourceName(array(
                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                    'module' => $request->getModuleName(),
                    'controller' => $request->getControllerName(),
                    'action' => $request->getActionName()
        ));

        \Sl_Service_Acl::setContext($request);

        if (!\Sl_Service_Acl::isAllowed($resource)) {
            if (preg_match('/^ajax.+/', $request->getActionName())) {
                //TODO: Зробити по людськи
                header('content-type:application/json; charset=utf-8');
                echo json_encode(array('result' => false, 'description' => 'Page is not available! ' . $resource . '.' . __METHOD__, 'code' => self::PAGE_NOT_AVAILABLE));
                die;
            } else {
                //TODO: Зробити по людськи
                if(!\Zend_Layout::getMvcInstance()) {
                    \Zend_Layout::startMvc();
                }
                \Zend_Layout::getMvcInstance()->setLayout('error')->getView()->error_message = \Zend_Layout::getMvcInstance()->getView()->partial('error/403.phtml');
                //throw new \Sl_Exception_Acl('Page "'.$resource.'" is not avialable! ' . __METHOD__);
            }
        }
    }

    public function onBeforeEditAction(\Sl_Event_Action $event) {
        
    }

    public function onAfterEditAction(\Sl_Event_Action $event) {
        
    }

    public function onAfterLayoutInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterInit(\Sl_Event_Router $event) {
        if (!($router = $event->getRouter()))
            return;
        if (!$router->hasRoute('home')) {
            $route = new \Zend_Controller_Router_Route_Static('home', array(
                'module' => 'home',
                'controller' => 'main',
                'action' => 'list',
            ));

            $router->addRoute('home', $route);
        }
    }

    public function onBeforeInit(\Sl_Event_Router $event) {
        
    }

    public function onRouteStartup(\Sl_Event_Router $event) {
        
    }

    public function onSetRequest(\Sl_Event_Router $event) {
        
    }

    public function onSetResponse(\Sl_Event_Router $event) {
        
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

    public function onAfterRequestInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterTranslationInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onAfterViewInit(\Sl_Event_Bootstrap $event) {
        if(!$this->_request_error) {
            
            //print_r($event->getLayout());die;
        } else {
            echo 'asd';die;
        }
    }

    public function onBeforeLayoutInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeRequestInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeTranslationInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeViewInit(\Sl_Event_Bootstrap $event) {
        
    }

    public function onBeforeSave(\Sl_Event_Model $event) {
        
    }

    public function onAfterSave(\Sl_Event_Model $event) {
        
    }

    public function onBeforeAclCreate(\Sl_Event_Acl $event) {
        
    }

    //Наповнення Acl
    public function onAfterAclCreate(\Sl_Event_Acl $event) {
        
    }

    public function onAfterContent(\Sl_Event_View $event) {
        
    }

    public function onBeforeContent(\Sl_Event_View $event) {
        echo $event->getView()->partial('partials/errorslist.phtml', array(
            'errors' => \Sl\Module\Home\Service\Errors::getErrors(),
            'success' => \Sl\Module\Home\Service\Success::getMessages()
        ));
        \Sl\Module\Home\Service\Errors::clearErrors();
        \Sl\Module\Home\Service\Success::clearMessages();
    }

    public function onBodyBegin(\Sl_Event_View $event) {
        
    }

    public function onBodyEnd(\Sl_Event_View $event) {
        
    }

    public function onFooter(\Sl_Event_View $event) {
        
    }

    public function onHeadLink(\Sl_Event_View $event) {
        
    }

    public function onHeadScript(\Sl_Event_View $event) {
        
    }

    public function onHeadTitle(\Sl_Event_View $event) {
        
    }

    public function onHeader(\Sl_Event_View $event) {
        
    }

    public function onLogo(\Sl_Event_View $event) {
        
    }

    public function onNav(\Sl_Event_View $event) {
        
    }

    public function onPageOptions(\Sl_Event_View $event) {
        
    }

    public function onContent(\Sl_Event_View $event) {
        echo $event->getView()->layout()->content;
    }

    public function onBeforePageHeader(\Sl_Event_View $event) {
        
    }

    public function onAppRun(\Sl_Event_Bootstrap $event) {
        
    }

    public function onIsAllowed(\Sl_Event_Acl $event) {
        
    }

    public function onAfterSessionInit(\Sl_Event_Bootstrap $event) {
        
    }

}
