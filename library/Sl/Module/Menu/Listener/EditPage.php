<?php
namespace Sl\Module\Menu\Listener;

class EditPage extends \Sl_Listener_Abstract implements \Sl_Listener_Navigatepage_Interface{ //,\Sl\Listener\View\Controller\Listtable {
     
    public function onEditPage(\Sl_Event_Navigatepage $event) {
                        
                        
                        $page = $event->getPage(); 
                        $current_user_name = \Zend_Auth::getInstance() -> getIdentity()->getLogin();
                          $array = $page->toArray();
                          if ($array['id']=='usersettings'){
                              $page = $page->setLabel($current_user_name);
                              $event->setPage($page);
                          }
                       
        	}

    public function onBeforeListViewTableGroupButton(\Sl_Event_View $event) {
        
    }
    
            
        }
   
    
    