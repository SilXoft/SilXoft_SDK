<?php
namespace Sl\Listener;

use Sl\Event as E;

interface Fieldset {
    
    public function onPrepare(E\Fieldset $event);
    public function onPrepareAjax(E\Fieldset $event);
    
}

