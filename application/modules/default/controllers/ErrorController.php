<?php

class Default_ErrorController extends Zend_Controller_Action
{

  public function errorAction() {
        $handler = $this->_request->getParam('error_handler');

        if($handler) {
            foreach($handler as $ex) {
                //echo get_class($ex)."\r\n";
                if($ex instanceof Application_Exception_Acl) {
                    $this->view->header = 'Вы не имеете прав на просмотр этой страницы';
                    $this->view->ex = $ex;
                } elseif($ex instanceof Application_Exception_Model) {
                    $this->view->header = 'Ошибка доступа к данным';
                    $this->view->ex = $ex;
                } elseif($ex instanceof Exception) {
                    $this->view->header = 'Системная ошибка';
                    $this->view->ex = $ex;
                }
            }
        }
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

