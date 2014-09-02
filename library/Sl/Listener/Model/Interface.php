<?php

interface Sl_Listener_Model_Interface {
    
    public function onBeforeSave(Sl_Event_Model $event);
    public function onAfterSave(Sl_Event_Model $event);
	
    
}

?>