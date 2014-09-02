<?php

namespace Sl\Module\Menu\Listener;

class Context extends \Sl_Listener_Abstract implements \Sl\Listener\View\Controller\Controller, \Sl_Listener_View_Interface, \Sl_Listener_View_ContextMenu_Interface {

    public function onBeforeListViewTable(\Sl_Event_View $event) {
        
    }

    public function onBeforeListViewTableButtons(\Sl_Event_View $event) {
        
    }

    public function onAfterListViewTable(\Sl_Event_View $event) {


        if (!$event->getView()->is_iframe && !$event->getView()->is_popup)
            echo $event->getView()->action('listitem', 'context', 'menu', array('object' => $event->getOption('object')));
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
        $event->getView()->headScript()->appendFile('/menu/context/contextmenu.js');
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

    public function onBeforeEchoContextMenu(\Sl_Event_view $event) {
        
    }

}