<?php
namespace Sl\Listener\View\Informer;

interface Informer {
    
    public function onInformer(\Sl_Event_View $event);
}