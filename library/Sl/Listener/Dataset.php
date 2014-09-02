<?php
namespace Sl\Listener;

use Sl\Event as E;

interface Dataset {
    
    public function onBeforeProcessAll(E\Dataset $event);
    public function onAfterProcessAll(E\Dataset $event);
    public function onBeforeProcessItem(E\Dataset $event);
    public function onAfterProcessItem(E\Dataset $event);
    
}