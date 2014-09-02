<?php
namespace Sl\Listener\Model;

interface Table {
    
    public function onBeforeQuery(\Sl\Event\Table $event);
    
}