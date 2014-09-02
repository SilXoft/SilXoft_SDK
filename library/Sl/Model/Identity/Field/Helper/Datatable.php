<?php
namespace Sl\Model\Identity\Field\Helper;

class Datatable extends \Sl\Model\Identity\Field\Helper {
    
    public function datatable($type, array $options = array()) {
        return \Sl\Serializer\Serializer::render($this->getField(), 'datatable', null, array_merge($options, array('type' => $type)));
    }
}