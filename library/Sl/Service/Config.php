<?php
namespace Sl\Service;

class Config {
    
    const CONFIG_PATH_SEPARATOR = '/';
    const CONFIG_DIR = '_configs';
    
    const ROLES_KEY = '_roles_';
    const PRIORITY_KEY = '_priority_';
    
    const EC_NOT_EXISTS = 1001;
    
    /**
     * Ничего не сливать
     */
    const MERGE_NO_MERGE = 0;
    
    /**
     * Сливаем "роле-зависимые" настройки, если есть
     */
    const MERGE_ROLES = 1;
    
    /**
     * Сливаем все по полям
     * Пока недоработано
     */
    const MERGE_FIELDS = 2;
    
    const DEFAULT_PATH = 'model';
    
    public static function read(\Sl_Model_Abstract $model, $path = self::DEFAULT_PATH, $merge_type = self::MERGE_ROLES) {
        // Нечего сливать. Можно даже не проверять
        if($path === self::DEFAULT_PATH) {
            $merge_type = self::MERGE_NO_MERGE;
        }
        $path = self::_preparePath($path);
        $filename = self::_buildFilename($model, array_shift($path));
        
        if(!file_exists($filename)) {
            throw new \Exception('File "'.$filename.'" doesn\'t exists. '.__METHOD__);
        }
        try {
            $config = new \Sl\Config(require $filename, true);
            if($merge_type == self::MERGE_ROLES) {
                $config = self::_mergeRoles($config);
            } elseif($merge_type == self::MERGE_FIELDS) {
                /**
                 * Слить все, что можно.
                 * Пока только по полям
                 * 
                 * Например:
                 * 
                 * спросили list
                 * в нем есть поле packagecustomer.name = array('type' => 'hidden')
                 * Т.о. у нас есть толко тип поля. Все остальное можно и нужно подтянуть из конфига list-а обекта на другой стороне связи
                 * Т.е. определяем, что нам нужен customer, берем его list и сливаем информацию по полю name. В идеале тоже смердженый
                 * Т.о. избегаем дублирования
                 */
                // @TODO Доделать
                // А пока ...
                $config = self::read($model)->merge($config);
            }
            if($config) {
                foreach($path as $path_part) {
                    if(isset($config->$path_part)) {
                        $config = $config->$path_part;
                    } else {
                        throw new \Exception('Doesn\'t exists. '.__METHOD__);
                    }
                }
                return $config;
            }
            throw new \Exception('Config file is corrupted. '.__METHOD__);
        } catch(\Exception $e) {
            return new \Sl\Config(array(), true);
        }
    }
    
	/**
	 * 
	 * @param \Sl_Model_Abstract $model
	 * @param type $path
	 * @param \Sl\Service\Config $data
	 * @param type $force_creation
	 * @return \Sl\Config|boolean
	 * @throws \Exception
	 */
    public static function write(\Sl_Model_Abstract $model, $path, $data, $force_creation = true) {
        $path = self::_preparePath($path);
        $filename = self::_buildFilename($model, array_shift($path));
        
        if(!file_exists($filename)) {
            if($force_creation) {
                self::_createEmpty($filename);
            }
            if(!file_exists($filename)) {
                throw new \Exception('File "'.$filename.'" doesn\'t exists. '.__METHOD__);
            }
        }
        try {
            $config = new \Sl\Config(require $filename, true);
            if(!$config) {
                throw new \Exception('Config file is corrupted. '.__METHOD__);
            }
            if(is_array($data)) {
                $to_write = new \Sl\Config($data, true); 
            } elseif($data instanceof \Sl\Config) {
                $to_write = $data;
            } else {
                if(count($path) == 0) {
                    throw new \Exception('You can\'t write string value at this level. '.__METHOD__);
                } else {
                    $to_write = $data;
                }
            }
            $path = array_reverse($path);
            foreach($path as $path_part) {
                $to_write = new \Sl\Config(array(
                    $path_part => $to_write,
                ), true);
            }
            $config->merge($to_write);
            $writer = new \Zend_Config_Writer_Array();
            $writer->write($filename, $config);
            return $config;
        } catch(\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
    
    protected function _mergeRoles(\Sl\Config $config) {
        if(isset($config->{self::ROLES_KEY})) {
            $role_ids = \Sl_Service_Acl::getCurrentRoles(true);
            $res_config = new \Sl\Config(array(), true);
            $roles_data = $config->{self::ROLES_KEY};
            $priority_data = array();
            $roles_data->rewind();
            for($i = 0; $i < $roles_data->count(); $i++) {
                if(!in_array($roles_data->key(), $role_ids)) continue;
                $key = null;
                if(isset($roles_data->current()->{self::PRIORITY_KEY})) {
                    $key = 100*($roles_data->count() + 1 - $roles_data->current()->{self::PRIORITY_KEY});
                    while(array_key_exists($key, $priority_data)) {
                        $key--;
                    }
                    $priority_data[$key] =  $roles_data->key();
                } else {
                    $priority_data[$roles_data->count() + 1 - $i] = $roles_data->key();
                }
                $roles_data->next();
            }
            ksort($priority_data);
            foreach($priority_data as $v) {
                $res_config->merge($roles_data->$v);
            }
            unset($res_config->{self::PRIORITY_KEY});
            unset($config->{self::ROLES_KEY});
            return $config->merge($res_config);
        }
        return $config;
    }
    
    protected static function _preparePath($path) {
        if(!is_array($path)) {
            $path = explode(self::CONFIG_PATH_SEPARATOR, $path);
        }
        if(count($path) == 0) {
            throw new \Exception('empty path given. '.__METHOD__);
        }
        return $path;
    }
    
    protected static function _buildFilename(\Sl_Model_Abstract $model, $part) {
        $path_array = array();
        $path_array[] = APPLICATION_PATH;
        if($model instanceof \Sl_Module_Abstract) {
            $path_array[] = $model->getDir();
            $path_array[] = self::CONFIG_DIR;
        } else {
            $path_array[] = \Sl_Module_Manager::getInstance()->getModule($model->findModuleName())->getDir();
            $path_array[] = self::CONFIG_DIR;
            $path_array[] = strtolower($model->findModelName());
        }
        $path_array[] = (string) $part;
        return implode(DIRECTORY_SEPARATOR, $path_array).'.php';
    }
    
    protected static function _createEmpty($path) {
        if(!file_exists($path)) {
            $dir = dirname($path);
            if(!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $fh = fopen($path, 'a+');
            if($fh) {
                fwrite($fh, '<?php return array();');
                fclose($fh);
            } else {
                return false;
            }
            chmod($path, 0777);
            return true;
        }
    }
}