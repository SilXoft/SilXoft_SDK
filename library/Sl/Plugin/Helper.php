<?php

class Sl_Plugin_Helper extends Zend_Controller_Plugin_Abstract {
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $path = '/js/'.$request->getControllerName().'/'.$request->getActionName().'.js';
        if(file_exists(APPLICATION_PATH.'/../public'.$path)) {
            $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
            $view->headScript()->appendFile($path);
        }
        parent::preDispatch($request);
    }
    
}

?>
