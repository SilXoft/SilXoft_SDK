<?php

class Dashboard_RoleController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    $this->_forward('read','role','dashboard');
    }
    
    
    public function createAction()
    {
        $form = new Dashboard_Form_Role_Create();
        $this->view->form = $form;    
  if($this->_request->isPost()) {
            if($form->isValid($this->_request->getParams())) {
                $role = Application_Model_Factory::mapper('role');
                $role->setName($this->_request->getParam('name'));
                $role->setNickname($this->_request->getParam('nickname'));              
                $role->setDescription($this->_request->getParam('description'));
                $role->setActive(1);
                try {

                    Application_Model_Factory::mapper('role')->save($role);
                    $this->_redirect('/dashboard/role/read');
                } catch(Exception $e) {
                    $this->view->error = $e->getMessage();
                    $form->populate($this->_request->getParams());
                }
            } else {
                $form->populate($this->_request->getParams());
            }
        }      
    } 
    
    
       
    public function readAction()
    {
        $this->view->roles = Application_Model_Factory::mapper('role')->fetchAll();
    } 
    
     
    public function updateAction()
    {
        
        $role = Application_Model_Factory::mapper('role')->find($this->_request->getParam('id', false));
      
        $form = new Dashboard_Form_Role_Create();
        if(!$role) {
            throw new Application_Exception_Model('Role not found');
        }

       
        $form->getElement('name')->setValue($role->getName());
        $form->getElement('nickname')->setValue($role->getNickname());       
        $form->getElement('description')->setValue($role->getDescription());

        if($this->_request->isPost()) {
            if($form->isValid($this->_request->getParams())) {
                $role->setName($this->_request->getParam('name'));
                $role->setNickname($this->_request->getParam('nickname'));              
                $role->setDescription($this->_request->getParam('description'));
                $role->setActive(1);
                try {

                    Application_Model_Factory::mapper('role')->save($role);
                    $this->_redirect('/dashboard/role/read');
                } catch(Exception $e) {
                    $this->view->error = $e->getMessage();
                    $form->populate($this->_request->getParams());
                }
            } else {
                $form->populate($this->_request->getParams());
            }
        }

        $this->view->form = $form;  
    } 
     public function deleteAction()
    {
        // action body
    }      
    
    public function testAction() {
        
    }
}

