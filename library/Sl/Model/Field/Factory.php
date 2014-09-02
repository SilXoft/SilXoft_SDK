<?php
namespace Sl\Model\Field\Operator;

class Factory {
    
    /**
     * 
     * @param string $name
     * @return \Sl\Model\Field\Operator\Operator
     * @throws Exception
     */
    public static function get($name) {
        $classname = ucfirst($name);
        if(class_exists($classname)) {
            return new $classname();
        } else {
            throw new Exception('Class "'.$classname.'" doesn\'t exists. '.__METHOD__);
        }
    }
}