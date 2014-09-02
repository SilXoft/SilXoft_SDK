<?php
namespace Sl\Model\Identity\Field;

abstract class Context {
    
    public function getType() {
        return lcfirst(array_pop(explode('\\', get_class($this))));
    }
    
    public function __toString() {
        return $this->getType();
    }
}