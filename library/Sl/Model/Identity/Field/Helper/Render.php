<?php
namespace Sl\Model\Identity\Field\Helper;

class Render extends \Sl\Model\Identity\Field\Helper {
    
    public function render($type, array $options = array()) {
        return \Sl\Serializer\Serializer::render($this->getField(), $type, null, $options);
    }
}