<?php
namespace Sl\Model\Identity\Field\Helper;

class Factory {
    
    const EC_BUILD_ERROR = 100;
    
    public static function build($name, $field, array $options = array()) {
        $class_name = __NAMESPACE__.'\\'.ucfirst($name);
        if(class_exists($class_name)) {
            return new $class_name($field, $options);
        } else {
            throw new \Exception('Can\'t build helper "'.$name.'". '.__METHOD__, self::EC_BUILD_ERROR);
        }
    }
}