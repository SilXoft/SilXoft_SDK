<?php
namespace Sl\Validate;

class Validate extends \Zend_Validate {
    
    /**
     * 
     * @param type $classBaseName
     * @param array $args
     * @param type $namespaces
     * @return \Zend_Validate_Interface
     * @throws \Sl\Validate\Zend_Validate_Exception
     * @throws Zend_Validate_Exception
     */
    public static function factory($classBaseName, array $args = array(), $namespaces = array()) {
        $namespaces = array_merge((array) $namespaces, self::$_defaultNamespaces, array('Zend_Validate', 'Sl\\Validate'));
        $className  = ucfirst($classBaseName);
		
        try {
            if (!class_exists($className, false)) {
                require_once 'Zend/Loader.php';
                foreach($namespaces as $namespace) {
                    $sep = preg_replace('/^.+(_|\\\).+$/', '$1', $namespace);
                    $class = $namespace.$sep.$className;
                    $file  = str_replace($sep, DIRECTORY_SEPARATOR, $class).'.php';
                    if (\Zend_Loader::isReadable($file)) {
                        \Zend_Loader::loadClass($class);
                        $className = $class;
                        break;
                    }
                }
            }
            
            $class = new \ReflectionClass($className);
            if ($class->implementsInterface('Zend_Validate_Interface')) {
                if ($class->hasMethod('__construct')) {
                    $keys    = array_keys($args);
                    $numeric = false;
                    foreach($keys as $key) {
                        if (is_numeric($key)) {
                            $numeric = true;
                            break;
                        }
                    }

                    if ($numeric) {
                        $object = $class->newInstanceArgs($args);
                    } else {
                        $object = $class->newInstance($args);
                    }
                } else {
                    $object = $class->newInstance();
                }
                return $object;
            }
        } catch (\Zend_Validate_Exception $ze) {
            // if there is an exception while validating throw it
            throw $ze;
        } catch (\Exception $e) {
            // fallthrough and continue for missing validation classes
        }

        require_once 'Zend/Validate/Exception.php';
        throw new \Zend_Validate_Exception("Validate class not found from basename '$classBaseName'");
    }
    
    public static function is($value, $classBaseName, array $args = array(), $namespaces = array()) {
        return self::factory($classBaseName, $args, $namespaces)->isValid($value);
    }
}