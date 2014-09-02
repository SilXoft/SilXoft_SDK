<?php
namespace Sl\Listener;

interface Modelaction {
    
    public function onBefore(\Sl\Event\Modelaction $event);
    public function onAfter(\Sl\Event\Modelaction $event);
    public function onBeforePost(\Sl\Event\Modelaction $event);
    public function onAfterPost(\Sl\Event\Modelaction $event);
}
