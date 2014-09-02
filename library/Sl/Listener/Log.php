<?php
namespace Sl\Listener;

interface Log {
    
    public function onBeforeInit(\Sl\Event\Log $event);
    public function onAfterInit(\Sl\Event\Log $event);
    
}