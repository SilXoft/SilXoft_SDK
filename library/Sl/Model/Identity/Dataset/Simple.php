<?php
namespace Sl\Model\Identity\Dataset;

class Simple extends \Sl\Model\Identity\Dataset {
    
    protected function _processItem($item, $key) {
        return $item;
    }

}