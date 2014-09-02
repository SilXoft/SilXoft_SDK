<?php
/**
 * "Фабрика" контекстів acl
 * 
 */

class Sl_Assertion_Factory {

    protected static $_assertions;

    /**
     * Повертає об'єкт Assertion для цього ресурса, або null 
     * 
     * @param string $resource_name 
     * @return \Zend_Acl_Assert_Interface
     */
    public static function getAssertion($resource_name) {
         
        foreach(self::_findAssertions() as $data) {
            $data['name'] = str_replace('|', '\\|', $data['name']);
            if(preg_match('/^'.$data['name'].'/', $resource_name)) {
                //echo '/^'.$data['name'].'/'.': '.$resource_name."\r\n";
                return $data['object'];
            }
        }
        return null;
    }

    /**
     * Ищет все существующие Assertion-ы
     * 
     * @TODO: Ищет не все типы. В идеале должен искать все.
     * 
     */
    protected static function _findAssertions() {
        if(!isset(self::$_assertions)) {
            $modules = \Sl_Module_Manager::getModules();
            
            $available_res_types = array(
                \Sl_Service_Acl::RES_TYPE_MVC,
                \Sl_Service_Acl::RES_TYPE_OBJ,
            );

            $data = array();

            foreach($modules as $module_name=>$module) {
                $module_dir = APPLICATION_PATH.'/'.$module->getDir();
                if(is_dir($module_dir)) {
                    if(is_dir($module_dir.'/Assertion')) {
                        foreach($available_res_types as $type) {
                            if(is_dir($module_dir.'/Assertion/'.ucfirst(strtolower($type)))) {
                                $dh = opendir($module_dir.'/Assertion/'.ucfirst(strtolower($type)));
                                if($dh) {
                                    while(false !== ($filename = readdir($dh))) {
                                        if(preg_match('/\.php/', $filename)) {
                                            $namespace = explode('\\', get_class($module));
                                            array_pop($namespace);
                                            $class_name = '\\'.implode('\\', array(
                                                implode('\\', $namespace),
                                                'Assertion',
                                                ucfirst(strtolower($type)),
                                                pathinfo($filename, PATHINFO_FILENAME),
                                            ));
                                            if(class_exists($class_name)) {
                                                $data[] = array(
                                                    'object' => new $class_name(),
                                                    'name' => implode(\Sl_Service_Acl::RES_TYPE_SEPARATOR, array(
                                                        $type,
                                                        implode(\Sl_Service_Acl::RES_DATA_SEPARATOR, array(
                                                            $module_name,
                                                            strtolower(pathinfo($filename, PATHINFO_FILENAME)),
                                                        )),
                                                    ))
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            self::$_assertions = $data;
        }
        return self::$_assertions;
    }
}
