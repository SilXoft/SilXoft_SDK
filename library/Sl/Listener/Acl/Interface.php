<?php

interface Sl_Listener_Acl_Interface {
    
    public function onBeforeAclCreate(\Sl_Event_Acl $event);
    public function onAfterAclCreate(\Sl_Event_Acl $event);
    public function onIsAllowed(\Sl_Event_Acl $event);
	
    
}

