<?php
namespace Sl\Module\Home\Controller;

class File extends \Sl_Controller_Model_Action {

    const UPLOAD_PATH = '/../uploads';
    
    public function createAction() {
        throw new \Exception('Not implemented. Only try AJAX-style. '.__METHOD__);
        /*
        $this->_helper->viewRenderer->setRender('edit');

        $Obj = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());
		$Obj = \Sl_Model_Factory::mapper($Obj)->prepareNewObject($Obj);
		
        $form = \Sl_Form_Factory::build($Obj, true);
        
        $form->getElement('location')->setDestination(APPLICATION_PATH.self::UPLOAD_PATH);
        
        if($this->getRequest()->isPost()) {
        	if($form->isValid($this->getRequest()->getParams())) {
                $fileinfo = pathinfo($form->getElement('location')->getFileName());
                $new_name = 'f-'.md5(time().time()).'.'.$fileinfo['extension'];
                $form->getElement('location')->addFilter('Rename', $new_name);
                try {
                    $form->getElement('location')->receive();
                    $Obj->setType($form->getElement('location')->getMimeType());
                    $name = $this->getRequest()->getParam('name', false);
                    if(!$name) {
                        $name = $fileinfo['filename'];
                    }
                    $Obj->setName($name);
                    $Obj->setLocation(realpath($fileinfo['dirname'].'/'.$new_name));
                    \Sl_Model_Factory::mapper($Obj)->save($Obj);
                } catch(\Exception $e) {
                    throw $e;
                }

                $forward_url = '/';

               if (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
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
                } elseif (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
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
		$this->view->form = $form;*/
    }
    
    public function editAction() {
        $model = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())
                        ->find($this->getRequest()->getParam('id', 0));
        if(!$model) {
            throw new \Exception('Required parameter not set (Id). '.__METHOD__);
        }
        $this->_redirect(\Sl\Service\Helper::modelEditViewUrl($model));
        /*
        $Obj = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())->findAllowExtended($this->getRequest()->getParam('id', 0));
		
        if (!$Obj) {
            if (false === $this->getRequest()->getParam('id', false)) {
                $Obj = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());
				$Obj = \Sl_Model_Factory::mapper($Obj)->prepareNewObject($Obj);
            } else
                throw new \Sl_Exception_Model('Illegal ' . $this->getModelName() . ' id');
        }
		
		$Locker = \Sl_Model_Factory::object('\Sl\Module\Home\Model\Locker');
		if (!\Sl_Model_Factory::mapper($Locker)->checkModel($Obj)){
			throw new Exception(\Zend_Registry::get('Zend_Translate')->translate("Форму обрабатывает другой пользователь!"), 1);
			
		}
		$this->view->headScript()->appendFile('/home/main/locker.js');
		
		\Sl_Event_Manager::trigger(new \Sl_Event_Action('beforeEditAction', array(
                    'model' => $Obj,
                    'view' => $this->view,
                )));
		
				
		
        $form = \Sl_Form_Factory::build($Obj, true);
        $this->view->form = $form;
		$this->view->subtitle = $Obj->__toString();
		
		
        if ($this->getRequest()->isPost()) {
        	
            if ($form->isValid($this->getRequest()->getParams())) {
				
                $Obj->setOptions($this->getRequest()->getParams());
				
					
                \Sl_Model_Factory::mapper($Obj)->save($Obj);
				
				 
				 $forward_url = '/';

	                if (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
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
		
		\Sl_Event_Manager::trigger(new \Sl_Event_Action('afterEditAction', array(
                    'model' => $Obj,
                    'view' => $this->view,
                )));
        */
    }
    
    public function ajaxcreateAction() {
        $this->view->result = true;
        try {
	        $Obj = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());
			$exclude_relation = $this->getRequest()->getParam('exlude_relation',false);
			$form = \Sl_Form_Factory::build($Obj, true,false,true, array($exclude_relation));
			
			$form ->setAction($this->view->url(array('module'=>$Obj->findModuleName(),'controller'=>$Obj->findModelName(),'action'=>$this->getRequest()->getActionName())));
			$form->getElement('location')->setDestination(APPLICATION_PATH.self::UPLOAD_PATH);
            
	        $fileinfo = pathinfo($form->getElement('location')->getFileName());
            $new_name = 'f-'.\Sl\Service\Common::guid().'.'.$fileinfo['extension'];
            $form->getElement('location')->addFilter('Rename', $new_name);
            
            if(!$form->getElement('location')->receive()) {
                throw new \Exception(print_r($form->getElement('location')->getMessages(), true));
            }
            $Obj->setType($form->getElement('location')->getMimeType());
            $name = $this->getRequest()->getParam('name', false);
            if(!$name) {
                $name = $fileinfo['filename'];
            }
            $Obj->setName($fileinfo['basename']);
            $Obj->setLocation(realpath($fileinfo['dirname'].'/'.$new_name));
            $Obj = \Sl_Model_Factory::mapper($Obj)->save($Obj, true);
                
            $this->view->files = array(
                array(
                    'id' => $Obj->getId(),
                    'name' => $Obj->getName(),
                )
            );
		} catch(\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
    
    public function detailedAction() {
        $file = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())
                        ->find($this->getRequest()->getParam('id', 0));
        if(!$file) {
            throw new \Exception('Required parameter not set (Id). '.__METHOD__);
        }
        \Sl\Module\Home\Service\File::render($file);
    }
    
    public function ajaxeditAction() {
        $this->view->result = true;
		try {
            $file = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())
                        ->findAllowExtended($this->getRequest()->getParam('id', 0));
            
			$exclude_relation = $this->getRequest()->getParam('exlude_relation', false);
			
	        $form = \Sl_Form_Factory::build($file, true, false, true, array($exclude_relation));
			
            $form->removeElement('location');
            $form->removeElement('active');
            $form->removeElement('create');
            
			if($this->getRequest()->isPost()) {
	        	if($form->isValid($this->getRequest()->getParams())) {
	                $file->setOptions($this->getRequest()->getParams());
	
	                $file = \Sl_Model_Factory::mapper($file)->save($file, true);
                    $this->view->file = $file->toArray();
	            } else {
	                $this->view->description = print_r($form->getMessages(), true);
	            }
	        } else {
				$this->view->calc_script = \Sl\Serializer\Serializer::getCalculatorsJS($file);
				$this->view->form = ''.$form;	        	
	        }
		} catch(Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
}