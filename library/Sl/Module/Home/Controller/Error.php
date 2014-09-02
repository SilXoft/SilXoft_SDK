<?php
namespace Sl\Module\Home\Controller;

class Error extends \Sl_Controller_Action {
    
    public function init() {
        
    }
    
   public function errorAction() {
        $exception = $this->getRequest()->getParam('error_handler')->exception;
        //print_r($exception);
        //die;
        
        $rec_stop = false;
        $rec_counter = 10;
        $exceptions = array($exception);
        $cur_exception = $exception;
        while(!$rec_stop && ($rec_counter > 0)) {
            if($cur_exception instanceof \Exception) {
                /*@var $cur_exception \Exception*/
                if($cur_exception = $cur_exception->getPrevious()) {
                    $exceptions[] = $cur_exception;
                }
            } else {
                $rec_stop = true;
            }
            $rec_counter--;
        }
     
        
            \Sl_Event_Manager::trigger(new \Sl_Event_Error('error', array(
                    'exceptions' => $exceptions,
                    'request' => $this->getRequest()->getParams(),
                )));
        if($this->_helper->ContextSwitch()->getCurrentContext() !== 'json') {
            $this->view->exceptions = $exceptions;
        }
        return;
        /*
        $this->view->exception = $exception;
        if ($exception ->getPrevious()){
            echo $exception ->getPrevious()->getTraceAsString();
        } 
        
        echo $exception->getTraceAsString();
        
        
        die;
        //print_r($exception);die;
        if ( $exception instanceof \Sl_Exception_Event){
            $prev = $exception->getPrevious();
            $this->view->prev = $prev;
            //$message = $prev->getMessage();
        } elseif($exception instanceof \Sl_Exception_Acl) {
            $message = $this->view->translate('Извините, но у Вас нет прав для просмотра этой страницы.');
        } else {
            $message = $exception->getMessage();
        }
        $this->view->message = $message;
        $this->view->params = $this->getRequest()->getParams();*/
    }

  
}

