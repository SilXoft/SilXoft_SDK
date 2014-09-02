<?php
namespace Sl\Model\Identity\Dataset;

class Autocomplete extends \Sl\Model\Identity\Dataset {
    
    protected function _processItem($item, $key) {
        $item['name_index'] = 'name';
        return $item;
    }

}