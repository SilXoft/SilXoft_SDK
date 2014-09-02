<?php
namespace Sl\Model\Identity\Field\Helper;

class Listview extends \Sl\Model\Identity\Field\Helper {
    
    public function listview($type, $value = null, array $options = array()) {
        return \Sl\Serializer\Serializer::render($this->getField(), array('listview', $type), $value, $options);
    }

}