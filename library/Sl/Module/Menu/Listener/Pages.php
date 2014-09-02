<?php
namespace Sl\Module\Menu\Listener;

interface Pages {
    
    public function onPagesPrepare(\Sl\Module\Menu\Event\Pages $event);
    
    
}
