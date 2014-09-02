<?php

class Sl_View_Factory {
    
    public static function build($data) {
        if($data instanceof Zend_Controller_Request_Abstract) {
            return self::_fromRequest($data);
        } elseif($data instanceof Sl_Module_Abstract) {
            return self::_fromModule($data);
        } elseif(is_array($data)) {
            throw new Sl_Exception_View('Not implemented');
        } elseif(is_string($data)) {
            throw new Sl_Exception_View('Not implemented');
        } else {
            throw new Sl_Exception_View('Can\'t build view from :'.print_r($data, true));
        }
    }
    
    protected static function _fromRequest(Zend_Controller_Request_Abstract $request) {
        $view = new Sl_View();
        try {
            $view_dir = Sl_Module_Manager::getViewDirectory($request->getModuleName());
            $view->setScriptPath($view_dir);
            return $view;
        } catch(Sl_Exception_View $e) {
            die($e->getMessage());
        }
    }
    
    protected static function _fromModule(Sl_Module_Abstract $module) {
        
    }
}

?>
