<?php
namespace Sl\Module\Api\Listener;

use Sl_Service_Acl as AclService;

class Acl extends \Sl_Listener_Abstract implements \Sl_Listener_Acl_Interface {
    
    protected $_allowed_resources = array();
    
    protected $_allowed_actions = array(
        'authorize',
        'token',
        'getdata',
        'postdata',
        'test',
    );
    
    public function __construct(\Sl_Module_Abstract $module) {
        parent::__construct($module);
        // Наполняем разрешенными ресурсами
        $res_tpl = array(
            'type' => AclService::RES_TYPE_MVC,
            'module' => $this->getModule()->getName(),
            'controller' => 'oauth'
        );
        foreach($this->_allowed_actions as $action) {
            $this->_allowed_resources[] = AclService::joinResourceName(array_merge($res_tpl, array(
                'action' => $action,
            )));
        }
    }

    public function onIsAllowed(\Sl_Event_Acl $event) {
        // Разрешаем доступ к ресурсам из нашего списка
        if($res_name = $event->getOption('resource')) {
            if(in_array($res_name, $this->_allowed_resources)) {
                AclService::acl()->allow(null, $res_name, AclService::PRIVELEGE_ACCESS);
            }
        }
    }
    
    public function onAfterAclCreate(\Sl_Event_Acl $event) {}
    public function onBeforeAclCreate(\Sl_Event_Acl $event) {}

}