<?php

class Default_AuthController extends Zend_Controller_Action
{

 public function init() {
     //echo "init".__METHOD__."\r\n";
    }

    public function indexAction() {
        if(Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/');
        }
        
        
        $form = new Default_Form_Auth();
        $this->view->form = $form;
        
        $this->view->test = Sl_Form_Factory::build(new Sl_Module_Auth_Model_User());

        if($this->_request->isPost()) {

            $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter(), 'users');
            $authAdapter->setCredentialColumn('password');
            $authAdapter->setIdentityColumn('email');

            $authAdapter->setIdentity($this->_request->getParam('email'));
            $authAdapter->setCredential(Application_Service_Common::hash($this->_request->getParam('password')));

            $result = $authAdapter->authenticate();

            if($result->isValid()) {
                $data = $authAdapter->getResultRowObject(array('id', 'role_id'));

                $user = Application_Model_Factory::mapper('user')->find($data->id);
                if(!$user) {
                    throw new Exception('Не удалось найти пользователя');
                }
                Zend_Auth::getInstance()->getStorage()->write($data);
                $this->_redirect('/');
            } else {
                $this->view->message = $result->getMessages();
            //    echo Application_Service_Common::hash('admin');
            }
        }
        //echo Application_Service_Common::hash('admin');
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }


}

