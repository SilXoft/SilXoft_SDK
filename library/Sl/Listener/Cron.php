<?php
namespace Sl\Listener;

interface Cron {
    
    public function onRun(\Sl\Event\Cron $event);
}