<?php
namespace Sl\Module\Menu\Controller;

class Context extends \Sl_Controller_Action {
    
    public function listitemAction() {
        try {
            $menu = new \Zend_Navigation();
            $object = $this->getRequest()->getParam('object');
            
            if(!$object || !($object instanceof \Sl_Model_Abstract)) {
                throw new \Sl_Exception_Model('Can\'t determine object. '.__METHOD__);
            }
            
            $menu = array(
                      array(
                        'module' => $object->findModuleName(),
                        'controller' => $object->findModelName(),
                        'action' => \Sl\Service\Helper::TO_DETAILED_ACTION,
                        'label' => $this->view->translate('Просмотр'),
                        'tag' => 'a',
                        'resource' => \Sl_Service_Acl::joinResourceName(array('type'=>\Sl_Service_Acl::RES_TYPE_MVC, 
                                                                               'module' => $object->findModuleName(),
                                                                               'controller' => $object->findModelName(),
                                                                               'action' => \Sl\Service\Helper::TO_DETAILED_ACTION
                                                                               )), // 'mvc:'.$object->findModuleName().'|'.$object->findModelName().'|edit',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => $object->findModuleName(),
                        'controller' => $object->findModelName(),
                        'action' => \Sl\Service\Helper::TO_DETAILED_ACTION,
                        'label' => $this->view->translate('Просмотр в новом окне'),
                        'tag' => 'a',
                        'class' => 'target-blank',
                        'resource' => \Sl_Service_Acl::joinResourceName(array('type'=>\Sl_Service_Acl::RES_TYPE_MVC, 
                                                                               'module' => $object->findModuleName(),
                                                                               'controller' => $object->findModelName(),
                                                                               'action' => \Sl\Service\Helper::TO_DETAILED_ACTION
                                                                               )), // 'mvc:'.$object->findModuleName().'|'.$object->findModelName().'|edit',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => $object->findModuleName(),
                        'controller' => $object->findModelName(),
                        'action' => \Sl\Service\Helper::TO_EDIT_ACTION,
                        'label' => $this->view->translate('Редактировать'),
                        'tag' => 'a',
                        'resource' => \Sl_Service_Acl::joinResourceName(array('type'=>\Sl_Service_Acl::RES_TYPE_MVC, 
                                                                               'module' => $object->findModuleName(),
                                                                               'controller' => $object->findModelName(),
                                                                               'action' => \Sl\Service\Helper::TO_EDIT_ACTION
                                                                               )), // 'mvc:'.$object->findModuleName().'|'.$object->findModelName().'|edit',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => $object->findModuleName(),
                        'controller' => $object->findModelName(),
                        'action' => \Sl\Service\Helper::TO_EDIT_ACTION,
                        'label' => $this->view->translate('Редактировать в новом окне'),
                        'tag' => 'a',
                        'class' => 'target-blank',
                        'resource' => \Sl_Service_Acl::joinResourceName(array('type'=>\Sl_Service_Acl::RES_TYPE_MVC, 
                                                                               'module' => $object->findModuleName(),
                                                                               'controller' => $object->findModelName(),
                                                                               'action' => \Sl\Service\Helper::TO_EDIT_ACTION
                                                                               )), // 'mvc:'.$object->findModuleName().'|'.$object->findModelName().'|edit',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => $object->findModuleName(),
                        'controller' => $object->findModelName(),
                        'action' => \Sl\Service\Helper::AJAX_DELETE_ACTION,
                        'label' => $this->view->translate('Удалить'),
                        'class' => 'ajax-action confirm',
                        'tag' => 'a',
                        'resource' => \Sl_Service_Acl::joinResourceName(array('type'=>\Sl_Service_Acl::RES_TYPE_MVC, 
                                                                               'module' => $object->findModuleName(),
                                                                               'controller' => $object->findModelName(),
                                                                               'action' => \Sl\Service\Helper::AJAX_DELETE_ACTION
                                                                               )),
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => $object->findModuleName(),
                        'controller' => $object->findModelName(),
                        'action' => \Sl\Service\Helper::AJAX_ARCHIVE_ACTION,
                        'label' => $this->view->translate('Архивировать/Разархивировать'),
                        'tag' => 'a',
                        'class' => 'model-archive ajax-action confirm',
                        'param' => '/toggle/1',
                         'resource' => \Sl_Service_Acl::joinResourceName(array('type'=>\Sl_Service_Acl::RES_TYPE_MVC, 
                                                                               'module' => $object->findModuleName(),
                                                                               'controller' => $object->findModelName(),
                                                                               'action' => \Sl\Service\Helper::AJAX_ARCHIVE_ACTION
                                                                               )),
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),                
                );

             \Sl_Event_Manager::trigger($event = new \Sl_Event_View('BeforeEchoContextMenu', array('view' => $this->view, 'menu' => $menu, 'model'=>$object)));
            $new_menu = $event->getOption('menu'); 
             
            $this->view->menu  = new \Zend_Navigation($new_menu);

        } catch(Exception $e) {
            
        }
    }
    
}