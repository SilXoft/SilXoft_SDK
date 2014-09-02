<?php
namespace Sl\Listener;

use Sl\Event;

interface Saver {
    
    public function onInit(Event\Saver $event);
    public function onBeforeValidate(Event\Saver $event);
    public function onBeforeSave(Event\Saver $event);
    public function onAfterSave(Event\Saver $event);
    public function onError(Event\Saver $event);
    public function onFinish(Event\Saver $event);
}