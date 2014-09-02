<?php
namespace Sl\Model\Identity\Field\Context;

class Factory {
    
    public static function build($src, array $data = array()) {
        if($src instanceof Context) {
            return clone $src;
        } elseif(is_string($src)) {
            $class_name = __NAMESPACE__.'\\'.ucfirst($src);
            if(!class_exists($class_name)) {
                throw new \Exception('Can\'t build context from such source. '.__METHOD__);
            }
            return new $class_name($data);
        } else {
            throw new \Exception('Can\'t build context from such source. '.__METHOD__);
        }
    }
}