<?php
namespace Sl\Module\Customers\Listener;
//error_reporting(E_ALL);
class Customlist extends \Sl_Listener_Abstract implements \Sl_Listener_Acl_Interface, \Sl_Listener_View_Interface {
    
    const RES_SHOW_CUSTOMERS = 'showcustomers';
    
    //const RES_ONLY_FINAL_STATUS = 'onlyfinalstatus';
    
    public function onAfterAclCreate(\Sl_Event_Acl $event) {
        if(!\Zend_Auth::getInstance()->hasIdentity()) return;
        $res_tpl = \Sl_Model_Factory::object('resource', \Sl_Module_Manager::getInstance()->getModule('auth'));
        
        $resources = \Sl_Model_Factory::mapper($res_tpl)->fetchAllByTypeModule(\Sl_Service_Acl::RES_TYPE_CUSTOM, $this->getModule()->getName());
        
        
        if(!\Sl_Service_Acl::acl()->has(self::RES_SHOW_CUSTOMERS)) {
            try {
                $need_add = true;
                $resource_name = \Sl_Service_Acl::joinResourceName(array(
                                    'type' => \Sl_Service_Acl::RES_TYPE_CUSTOM,
                                    'module' => $this->getModule()->getName(),
                                    'name' => self::RES_SHOW_CUSTOMERS
                ));
                if($resources) {
                    foreach($resources as $resource) {
                        if($need_add && ($resource->getName() == $resource_name)) {
                            $need_add = false;
                        }
                    }
                }
                if($need_add) {
                    $res = clone $res_tpl;
                    $res->setName($resource_name);
                    
                    
                    \Sl_Model_Factory::mapper($res)->save($res);
                }
            } catch(\Exception $e) {
                $translator = \Zend_Registry::get('Zend_Translate');
                $message = $translator->translate('Не удалось добавить ресурс'.' '.$resource_name.' ('.$e->getMessage().')').__METHOD__;
                throw new \Exception($message);
            }
        }
        
        
    }

    public function onBeforeAclCreate(\Sl_Event_Acl $event) {
        
    }

    public function onAfterContent(\Sl_Event_View $event) {
        
    }

    public function onBeforeContent(\Sl_Event_View $event) {
        
    }

    public function onBeforePageHeader(\Sl_Event_View $event) {
        
    }

    public function onBodyBegin(\Sl_Event_View $event) {
        
    }

    public function onBodyEnd(\Sl_Event_View $event) {
        
    }

    public function onContent(\Sl_Event_View $event) {
        
    }

    public function onFooter(\Sl_Event_View $event) {
        
    }

    public function onHeadLink(\Sl_Event_View $event) {
        
    }

    public function onHeadScript(\Sl_Event_View $event) {
    }

    public function onHeadTitle(\Sl_Event_View $event) {
        
    }

    public function onHeader(\Sl_Event_View $event) {
        
    }

    public function onLogo(\Sl_Event_View $event) {
        
    }

    public function onNav(\Sl_Event_View $event) {
        
    }

    public function onPageOptions(\Sl_Event_View $event) {
        
    }

    public function onIsAllowed(\Sl_Event_Acl $event) {
        
    }

}