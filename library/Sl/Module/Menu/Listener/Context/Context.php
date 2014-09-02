<?php
namespace Sl\Module\Menu\Listener\Context;
use Sl\Module\Menu\Event;

interface Context {
    
    public function onLoadMenu(Event\Context $event);
}