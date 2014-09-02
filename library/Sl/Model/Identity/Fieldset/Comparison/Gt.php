<?php
namespace Sl\Model\Identity\Fieldset\Comparison;

use Sl\Model\Identity\Fieldset;

class Gt extends Fieldset\Comparison\Simple {
    
    public function getOperator() {
        return '>'.($this->getExtension()?'=':'');
    }
    
    public function getExtension() {
        return (bool) parent::getExtension();
    }
}