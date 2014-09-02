<?php
namespace Sl\Listener\Printer;

interface Aggregate {
    
    public function onBeforePrint(\Sl\Event\PrinterAggregate $event);
}