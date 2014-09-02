<?php

/**
 * @de
 */
class Sl_Autoloader implements Zend_Loader_Autoloader_Interface {
    
    protected static $_paths;
    
    const NAMESPACE_SEPARATOR = '\\';
    
    public static function setModulesPaths(array $paths) {
        self::$_paths = $paths;
    }
    
    public static function addModulePath($path) {
        self::$_paths[] = $path;
    }
    
    public static function getModulePaths() {
        if(!isset(self::$_paths)) {
            /*self::$_paths = array(
                APPLICATION_PATH.'/../library/Sl/Module',
                
            );*/
            self::$_paths = \Sl_Module_Manager::getModulesDirectories();
        }
        return self::$_paths;
    }
    
    public function autoload($class) {
        if(preg_match('/Controller$/', $class)) {
            if(preg_match('/_/', $class)) {
                // Modules
                preg_match('/^(.+)_(.+)/', $class, $matches);
                if(count($matches) != 3) return false;
                $filename = APPLICATION_PATH.'/modules/'.strtolower($matches[1]).'/controllers/'.$matches[2].'.php';
            } elseif(preg_match('/\\\/', $class)) {
                if(count($data = explode(self::NAMESPACE_SEPARATOR, $class)) > 1) {
                    $path = APPLICATION_PATH.'/../library/'.implode('/', $data).'.php';
                    if(file_exists($path)) {
                        $content = file_get_contents($path);
                        include_once $path;
                        return class_exists($class);
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                // Simple
//                $filename = APPLICATION_PATH;
//                if(count(Zend_Controller_Front::getInstance()->getControllerDirectory()) > 1) {
//                    $filename .= '/modules/'.Zend_Controller_Front::getInstance()->getDefaultModule();
//                }
                //echo $class."\r\n";die;
                $filename .= '/controllers/'.$class.'.php';
            }
            echo $filename."\r\n";
            if(file_exists($filename)) {
                include_once $filename;
                return class_exists($class);
            }
            return false;
        } elseif(preg_match('/Module/', $class)) {
           // echo $class. ' whant<br>';    
            $name_array = explode(preg_replace('/.+(\\\|_).+/', '$1', $class), $class);
            //echo ' - '. preg_replace('/.+(\\\|_).+/', '$1', $class).'<br>';
            if (count($name_array) > 2) {
                //replace prefix from classname    
                array_shift($name_array);
                array_shift($name_array);
                
            }

            foreach(self::getModulePaths() as $path) {
                
                $filename = $path.'/'.implode(DIRECTORY_SEPARATOR, $name_array).'.php';
                //echo $filename.' l<br>';
                if(file_exists($filename)) {
                	$content = file_get_contents($filename);
					if (preg_match('/\?>(\s)*$/',$content)){
						$content = preg_replace('/\?>(\s)*$/','',$content);
						$f=fopen($filename, 'w');
						fwrite($f, $content);
						fclose($f);
						
					}
                    include_once $filename;
                    return class_exists($class);
                }
                
            }
        }
        
        // Для поддержки NAMESPACES
        if(count($data = explode(self::NAMESPACE_SEPARATOR, $class)) > 1) {
            $path = APPLICATION_PATH.'/../library/'.implode('/', $data).'.php';
            //echo "\r\n".$path."\r\n";
            if(file_exists($path)) {
            	
            	$content = file_get_contents($path);
				
					if (preg_match('/\?>(\s)*$/',$content)){
						
						$content = preg_replace('/\?>(\s)*$/','',$content);
						$f=fopen($path, 'w');
						fwrite($f, $content);
						fclose($f);
						
					}
				
                include_once $path;
                return class_exists($class);
            }
        } else {
            $path = LIBRARY_PATH.'/'.$class.'/'.$class.'.php';
            if(file_exists($path)) {
                include_once $path;
                return class_exists($class);
            } else {
                // пробуем загрузить нормально
                if(!class_exists($class)) {
                    $name_arr = explode('_', $class);
                    if(file_exists(LIBRARY_PATH.'/'.implode('/', $name_arr).'.php')) {
                        include_once LIBRARY_PATH.'/'.implode('/', $name_arr).'.php';
                        return class_exists($class);
                    }
                }
            }
        }
        
        if($class) {
          //  \Sl\Service\ClassCreator::createIdentity($class);
            // @TODO: Дописать вызов автолоадера повторно.
        }
    
        return false;
    }
}
