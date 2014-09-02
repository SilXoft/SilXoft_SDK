<?php
namespace Sl\Module\Home\Listener;

class Informer extends \Sl_Listener_Abstract implements \Sl\Listener\View\Informer\Informer, \Sl_Listener_View_Interface 
{
    protected $_view_script;
    protected $_view_help_action;
    public function onInformer(\Sl_Event_View $event) {
        
    }
    
    
    public function onPageOptions(\Sl_Event_View $event){
       
        
    }
    public function onLogo(\Sl_Event_View $event){}
    public function onHeadLink(\Sl_Event_View $event){ 
        $event->getView()->headLink()->appendStylesheet('/css/informer.css');
    }
    public function onHeader(\Sl_Event_View $event){
        
    }
    public function onNav(\Sl_Event_View $event){}
    public function onBeforePageHeader(\Sl_Event_View $event){}
    public function onBeforeContent(\Sl_Event_View $event){}
    public function onAfterContent(\Sl_Event_View $event){}
    public function onHeadTitle(\Sl_Event_View $event){}
    public function onHeadScript(\Sl_Event_View $event){
        
        $event->getView()->headScript()->appendFile('/js/informer.js');
        $resource = \Sl_Service_Acl::joinResourceName(array(
                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                    'module' => 'home',
                    'controller' => 'main',
                    'action' => 'ajaxinformer'
                ));
                
        
        
        echo '<script>var informer_ajax_update = '.intval(\Sl_Service_Acl::isAllowed($resource)).';</script>';
        
        
    }
    public function onBodyBegin(\Sl_Event_View $event){}
    public function onBodyEnd(\Sl_Event_View $event){}
    public function onFooter(\Sl_Event_View $event){}
    public function onContent(\Sl_Event_View $event){}
} 