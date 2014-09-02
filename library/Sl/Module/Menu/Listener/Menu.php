<?php
namespace Sl\Module\Menu\Listener;

class Menu extends \Sl_Listener_Abstract implements \Sl_Listener_View_Interface, \Sl\Module\Menu\Listener\Pages {
   
    public function onNav(\Sl_Event_View $event) {
        $res = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => 'menu',
            'controller' => 'main',
            'action' => 'sidenav',
        ));
        //print_r($this->_menu);die;
        //error_reporting(E_ALL);
        
       // $menu = \Sl\Module\Menu\Controller\Main::_menu;
       // print_r($menu);die;
        
        if(\Sl_Service_Acl::isAllowed($res)) {
        	if (!$event->getView()->is_iframe){
                       
        /*		$event->getView()->action('sidenav', 'main', 'menu');
                        $navigator = \Zend_Registry::get('Zend_Navigation');
                        //$pages = $navigator->getPages();
                        $page = $navigator->findOneBy('flag_identifier','user');
                        print_r($page); die;
                        $navigator->removePage($page);
                        $current_user_name = \Zend_Auth::getInstance() -> getIdentity()->getLogin();
                        $page->setLabel($current_user_name);
                        $navigator->addPage($page); 
                        //print_R($navigator); die;
                        \Zend_Registry::set('Zend_Navigation', $navigator);*/
                        
                 echo $event->getView()->action('sidenav', 'main', 'menu');
        	}
            
        }
    }
    
    public function onAfterContent(\Sl_Event_View $event) {}
	
	public function onHeadLink(\Sl_Event_View $event) {}
	
    public function onBeforeContent(\Sl_Event_View $event) {
    	
		
    }

    public function onHeader(\Sl_Event_View $event) {}

    public function onLogo(\Sl_Event_View $event) {}

    public function onPageOptions(\Sl_Event_View $event) {}

    public function onBodyBegin(\Sl_Event_View $event) {}

    public function onBodyEnd(\Sl_Event_View $event) {}

    public function onFooter(\Sl_Event_View $event) {}

    public function onHeadScript(\Sl_Event_View $event) {
        $event->getView()->headScript()->appendFile('/menu/context/contextmenu.js');
    }

    public function onHeadTitle(\Sl_Event_View $event) {}

    public function onContent(\Sl_Event_View $event) {
        
    }

    public function onBeforePageHeader(\Sl_Event_View $event) {
        $res = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => 'menu',
            'controller' => 'main',
            'action' => 'breadcrumb',
        ));
        
        if(\Sl_Service_Acl::isAllowed($res)) {
            if (!$event->getView()->is_iframe){
        		echo $event->getView()->action('breadcrumb', 'main', 'menu');
        	} else {
                echo $event->getView()->action('breadcrumb', 'main', 'menu', array('empty' => true));
            }
        }
    }

    public function onPagesPrepare(\Sl\Module\Menu\Event\Pages $event) {
      
        $pages = array_merge_recursive($event->getPages(), array(
            
        ));
        
        $event->setPages($pages);
        
    }
}
