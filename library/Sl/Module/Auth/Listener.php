<?php
namespace Sl\Module\Auth;

interface Listener {
    
    public function onAfterFormsCreation(\Sl\Module\Auth\Event $event);
    public function onBeforeAuthenticate(\Sl\Module\Auth\Event $event);
    public function onAfterAuthenticate(\Sl\Module\Auth\Event $event);
    public function onErrorAuthenticate(\Sl\Module\Auth\Event $event);
    
}
