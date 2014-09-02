<?php

interface Sl_Listener_Bootstrap_Interface {
        
    public function onBeforeRequestInit(Sl_Event_Bootstrap $event);
    public function onAfterRequestInit(Sl_Event_Bootstrap $event);
    
    public function onBeforeTranslationInit(Sl_Event_Bootstrap $event);
    public function onAfterTranslationInit(Sl_Event_Bootstrap $event);
    
    public function onBeforeViewInit(Sl_Event_Bootstrap $event);
    public function onAfterViewInit(Sl_Event_Bootstrap $event);
    
    public function onBeforeLayoutInit(Sl_Event_Bootstrap $event);
    public function onAfterLayoutInit(Sl_Event_Bootstrap $event);
    
    public function onAfterSessionInit(Sl_Event_Bootstrap $event);
    
    public function onAppRun(Sl_Event_Bootstrap $event);
}

?>
