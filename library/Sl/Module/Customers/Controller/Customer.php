<?php
namespace Sl\Module\Customers\Controller;

class Customer extends \Sl_Controller_Model_Action {
    
    public function createAction() {
        $this->_helper->viewRenderer->setRender('edit');

        $Obj = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());
		$Obj = \Sl_Model_Factory::mapper($Obj)->prepareNewObject($Obj);
		
        $current_user = \Zend_Auth::getInstance()->getIdentity();
        
        //customeruserresponsible
        $resp_relation = \Sl_Modulerelation_Manager::getRelations($Obj, 'customeruserresponsible');
        if($resp_relation) {
            $Obj->assignRelated('customeruserresponsible', array($current_user));
        }
        
        $form = \Sl_Form_Factory::build($Obj, true);
        
        \Sl_Service_Acl::setContext($this->getRequest());

        if ($this->getRequest()->isPost()) {
        	
            if ($form->isValid($this->getRequest()->getParams())) {
            	
                $Obj->setOptions($this->getRequest()->getParams());

                \Sl_Model_Factory::mapper($Obj)->save($Obj);

                $forward_url = '/';
                
               /*if (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                    'module' => $this->_getModule(),
                                    'controller' => $this->getModelName(),
                                    'action' => 'detailed'
                                )))) {
                    $forward_url = $this->view->url(array(
                                'module' => $this->_getModule(),
                                'controller' => $this->getModelName(),
                                'action' => 'detailed'
                            )) . '/id/' . $Obj->getId();
                } else*/if (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                    'module' => $this->_getModule(),
                                    'controller' => $this->getModelName(),
                                    'action' => 'list'
                                )))) {
                    $forward_url = \Sl\Service\Helper::listUrl($Obj);
                }

                $this->_redirect($forward_url);
            } else {
                
                $form->populate($this->getRequest()->getParams());
                $this->view->errors = $form->getMessages();
            }
        }
		
		$this->view->calc_script=\Sl\Serializer\Serializer::getCalculatorsJS($Obj);
		
		$this->view->form = $form;
    }
    
    public function editAction() {
        //error_reporting(E_ALL);
        parent::editAction();
        
    }
    
    public function ajaxlistAction() {
        if($request_search = (bool) ($this->getRequest()->getParam('quick_search', false))) {
            $name = trim($this->getRequest()->getParam('name', ''));
            $this->getRequest()->setParam('filter_fields', array(
                'customeremails:mail-like-'.$name,
                'customerphones:phone-like-'.$name,
                'description-like-'.$name,
                'customeridentifiercustomer:name-like-'.$name,
            ));
            $this->getRequest()->setParam('use_or_search', true);
        }
        parent::ajaxlistAction();
    }
}