<?php
namespace Sl\Module\Api\Event\Listener;

use Sl\Module\Api\Event\Api as ApiEvent;

interface Api {
    
    public function onCheckRequest(ApiEvent $event);
    public function onProcess(ApiEvent $event);
    public function onFallbackPost(ApiEvent $event);
    public function onFallbackGet(ApiEvent $event);
    
}