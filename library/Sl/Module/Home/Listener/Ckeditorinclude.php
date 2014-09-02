<?php

namespace Sl\Module\Home\Listener;

class Ckeditorinclude extends \Sl_Listener_Abstract implements \Sl_Listener_View_Interface{
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

        $event->getView()->headScript()->appendFile('/js/libs/ckeditor/ckeditor.js');
        $event->getView()->headScript()->appendFile('/js/libs/ckeditor/adapters/jquery.js');
        $event->getView()->headScript()->appendFile('/js/libs/ckeditor/include_ckeditor.js');
        
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
}