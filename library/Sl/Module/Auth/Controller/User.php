<?php
namespace Sl\Module\Auth\Controller;
use Sl\Service as Service;

class User extends \Sl_Controller_Model_Action {
	

  

    public function passwordAction(){
    	
		
    	//print_r(Service\Helper::hash('123456'));
        $form = \Sl_Form_Factory::build( array($this->_getModule(),'password_form'));
        //$f = new \Zend_Validate_StringLength();
         
        
        $this->view->form = $form;
        if($this->getRequest()->isPost()) {
        	$current_user=\Zend_Auth::getInstance()->getIdentity();
			$entered_pass = Service\Helper::hash($this->getRequest()->getParam('current_password', ''));
			$entered_new_pass = Service\Helper::hash($this->getRequest()->getParam('new_password', ''));
			$entered_new_pass_confirm = Service\Helper::hash($this->getRequest()->getParam('password_confirm', ''));
            if($form->isValid($this->getRequest()->getParams())){
			  if ( $entered_pass == $current_user->getPassword() 
            	   && $entered_new_pass==$entered_new_pass_confirm ) {
					$current_user->setPassword($entered_new_pass);
					$current_user = \Sl_Model_Factory::mapper($current_user)->save($current_user);
					 //\Zend_Auth::getInstance()->getStorage()->write($current_user);
					 
				}else {
					$this->view->errors = array('Пароли не совпадают',
					
					);
				}
           
            } else {
              //  $form->populate($this->getRequest()->getParams());
                $this->view->errors = $form->getMessages();
            }
        }
    	
    }
    
  	public function editcurrentuserAction(){
  		$current_user=\Zend_Auth::getInstance()->getIdentity();
		
		$this -> getRequest()->setParam('id', $current_user->getId());
		$this->_forward('edit');
  	} 


   
}