<?php
namespace Sl\Module\Api\Grant;

class Manager {
    
    protected static $_supported_grants = array();
    
    public static function addSupportedGrant(\Sl\Module\Api\Grant $grant, $type = null) {
        if(is_null($type)) {
            $matches = array();
            if(false !== preg_match_all('/[A-Z][^A-Z]+/', array_pop(explode('\\', get_class($grant))), $matches, PREG_SET_ORDER)) {
                $type = implode('_', array_map(function($el) { return strtolower(current($el)); }, $matches));
            } else {
                $type = get_class($grant);
            }
            
        }
        self::$_supported_grants[$type] = $grant;
    }
    
    public static function getProcessor(\Zend_Controller_Request_Abstract $request, $type) {
        if(!\Sl\Module\Api\Grant::checkContextType($type)) {
            return null;
        }
        foreach(self::$_supported_grants as $name=>$grant) {
            if($grant->isValid($request, $type)) {
                return $grant;
            }
        }
        return null;
    }
}