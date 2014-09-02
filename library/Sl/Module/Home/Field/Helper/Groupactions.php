<?php
namespace Sl\Module\Home\Field\Helper;

class Groupactions extends \Sl\Model\Identity\Field\Helper {
    
    public function listview($type, $value = null, array $options = array()) {
        // @TODO Запустить Event и собрать информацию, если кто-то что-то хочет вывести
        return \Sl\Serializer\Serializer::render($this->getField(), array('listview', $type), $value, $options);
    }
}