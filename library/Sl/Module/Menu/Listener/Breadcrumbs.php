<?php
namespace Sl\Module\Menu\Listener;

interface Breadcrumbs {
    
    public function onBeforeBreadcrumbs(\Sl\Module\Menu\Event\Breadcrumbs $event);
}