<?php
namespace Sl\Service;
class Groupactions {

    protected static $_instance;
    protected static $_config_path;
    protected static $_config;
    const GROUP_ACTION_CONFIG_KEY = 'group_actions';
    
    

    /**
     *
     * @return Sl_Module_Manager
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
/*
    public static function reloadModuleInformation() {
        $dirs = self::getModulesDirectories();
        //print_r($dirs);
        $modules = array();

        foreach ($dirs as $type => $dir) {
            try {
                $dh = opendir($dir);
                if ($dh) {

                    while (false !== ($filename = readdir($dh))) {
                        if (preg_match('/^[A-Z][a-z\d]+$/', $filename)) {
                            $mainFile = $dir . '/' . $filename . '/Module.php';
                            if (file_exists($mainFile)) {
                                $modules[strtolower($filename)] = array('file' => $dir . '/' . $filename . '/Module.php', 'type' => $type, 'dir' => $dir . '/' . $filename, );

                            }
                        }
                    }
                } else {
                    throw new Exception(implode('; ', error_get_last()));
                }
            } catch(Exception $e) {
                throw new Exception($e -> getMessage());
            }
        }
        $old_config = self::getConfig();
        $modules_duple = $modules;
        $to_update = false;

        foreach ($old_config as $module_name => $options) {
            if (isset($modules_duple[$module_name]) && !count(array_diff($modules_duple[$module_name] , $options->toArray()))) {
                unset($modules_duple[$module_name]);
            } else {
                $to_update = true;
                break;
            }
        }
    
        if ($to_update || count($modules_duple)) {
            foreach ($old_config as $module_name => $options) {unset($old_config->$module_name);}
                    
            foreach($modules as $name=>$data) {$old_config->$name = $data;} 
            
            $writer = new Zend_Config_Writer_Array( array('config' => $old_config));

            try {
                $writer -> write(self::getConfigPath());
            } catch(Exception $e) {
                die($e -> getMessage());
            }
        }

    }
*/
  

    /**
     *
     * @return Zend_Config
     */
    public static function getConfig() {
        try {
            self::$_config = new \Zend_Config(require self::getConfigPath(), true);
            return self::$_config;
        } catch(Exception $e) {
            die('Can\'t read modules config file');
        }
    }

    public static function getConfigPath() {
        if (!isset(self::$_config_path)) {
            self::$_config_path = APPLICATION_PATH . '/configs/groupactions.php';
        }
        return self::$_config_path;
    }

    public static function setConfigPath($path) {
        if (file_exists($path) && preg_match('/\.php$/', $path)) {
            self::$_config_path = $path;
        } else {
            throw new Exception('Not an ini-file: ' . $path);
        }
    }

    public static function getGroupActions($model, $module=null){
        
        $alias = Helper::getModelAlias($model,$module);
        $module_config = \Sl_Module_Manager::getInstance()->getCustomConfig(Helper::getModulenameByAlias($alias), self::GROUP_ACTION_CONFIG_KEY, Helper::getModelnameByAlias($alias)) ;
        $config = self::getInstance()-> getConfig();
        if ($module_config) $config -> merge($module_config);
        return $config;
    } 
    
    public static function getGroupActionNames($model, $module=null){
        $alias = Helper::getModelAlias($model,$module);
        $module_config = \Sl_Module_Manager::getInstance()->getCustomConfig(Helper::getModulenameByAlias($alias), self::GROUP_ACTION_CONFIG_KEY, Helper::getModelnameByAlias($alias)) ;
        $config = self::getInstance()-> getConfig();
        if ($module_config) $config -> merge($module_config);
        
        return array_keys($config->toArray());
    } 
    
    
        
}
