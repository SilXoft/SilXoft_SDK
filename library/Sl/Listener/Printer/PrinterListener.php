<?php

namespace Sl\Listener\Printer;

interface PrinterListener  {
    
   public function onBeforePrintAction(\Sl\Event\Printer $event);
   public function onBeforeGroupPrintAction(\Sl\Event\Printer $event);
}


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
