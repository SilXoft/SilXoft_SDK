<?php

class Users_IndexController extends Zend_Controller_Action
{
  public function init() {
        $context = $this->_helper->AjaxContext();
        foreach(get_class_methods(__CLASS__) as $method) {
            if(preg_match('/^ajax.+Action$/', $method)) {
                $method_name = preg_replace('/^(.*)Action$/', '$1', $method);
                $context->addActionContext($method_name, 'json');
            }
        }
        $context->initContext('json');
    }

    public function indexAction() {
        $roles = Application_Model_Factory::mapper('role')->fetchAll();
        $roles_array = array();
        if($roles) {
            foreach($roles as $role) {
                $roles_array[$role->getId()] = $role;
            }
        }
        unset($roles);
        $this->view->roles = $roles_array;

        $users = Application_Model_Factory::mapper('user')->fetchAll();
        $users_array = array();
        if($users) {
            foreach($users as $user) {
                //---
                $users_array[$user->getId()] = $user;
            }
        }
        unset($users);
        $this->view->users = $users_array;
    }

    public function createAction() {
        $form = new Users_Form_Add();
        $this->view->form = $form;

        if($this->_request->isPost()) {
            if($form->isValid($this->_request->getParams())) {
                $user = Application_Model_Factory::object('user');
                /*@var $user Application_Model_User*/

                $user->setEmail($this->_request->getParam('email'));
                $user->setName($this->_request->getParam('name'));
                $user->setPassword(Application_Service_Common::hash($this->_request->getParam('password')));
                $user->setRoleId($this->_request->getParam('role_id', Application_Service_Acl::USER));
                $user->setLastVisit(new DateTime());
                $user->setActive(1);

                try {

                    Application_Model_Factory::mapper('user')->save($user);
                    $this->_redirect('/user');
                } catch(Exception $e) {
                    $form->populate($this->_request->getParams());
                    $this->view->error = $e->getMessage();
                }
            } else {
                $form->populate($this->_request->getParams());
            }
        }
    }

    public function editAction() {
        $user = Application_Model_Factory::mapper('user')->find($this->_request->getParam('id', false));
        if(!$user) {
            throw new Application_Exception_Model('Пользователь не найден');
        }

        $form = new Application_Form_User_Add();
        $form->getElement('name')->setValue($user->getName());
        $form->getElement('email')->setValue($user->getEmail());
        $form->getElement('role_id')->setValue($user->getRoleId());

        $form->removeElement('password');
        $form->getElement('email')->setAttrib('disabled', 'disabled');

        if($this->_request->isPost()) {
            if($form->isValid($this->_request->getParams())) {
                $user->setEmail($this->_request->getParam('email'));
                $user->setName($this->_request->getParam('name'));
                $user->setRoleId($this->_request->getParam('role_id', Application_Service_Acl::USER));
                $user->setLastVisit(new DateTime());
                $user->setActive(1);
                try {
                    Application_Model_Factory::mapper('user')->save($user);
                    $this->_redirect('/user');
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

    public function deleteAction() {

    }

    public function ajaxdeleteAction() {
        $this->view->result = true;
        try {
            $user = Application_Model_Factory::mapper('user')->find($this->_request->getParam('id'));
            if(!$user) {
                throw new Exception('No such ID. Can\'t delete user.');
            }
            Application_Model_Factory::mapper('user')->delete($user);
        } catch(Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }


}

