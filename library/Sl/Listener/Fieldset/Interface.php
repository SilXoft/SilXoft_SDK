<?php
namespace Sl\Listener\Fieldset;

interface Fieldset_Interface {
    
    public function onListPrepare(\Sl\Event\Fieldset $event);
    public function onBeforeDatasetProcess(\Sl\Event\Fieldset $event);
    public function onBeforeDatasetProcessItem(\Sl\Event\Fieldset $event) ;
    public function onAfterDatasetProcessItem(\Sl\Event\Fieldset $event) ;
}

?>
