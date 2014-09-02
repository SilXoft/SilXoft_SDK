<?php

/**
 * "Фабрика" объектов приложения
 */

use Sl\Service as Service;
use Sl_Module_Manager as ModuleManager;

use Sl_Module_Abstract as AbstractModule;
use Sl_Model_Abstract as AbstractModel;
use Sl_Model_Mapper_Abstract as AbstractMapper;
use Sl\Model\DbTable\DbTable as AbstractTable;
use Sl\Model\Identity\Identity as AbstractIdentity;

use Exception as Exception;

class Sl_Model_Factory {

    protected static $_prefix = '\\Sl\\Model';
    protected static $_prefix_temporary = null;
    protected static $_objects = array();

    public static function build($what, $model, $module = null, array $options = array()) {
        $class_name = null;
        $data = self::_collectData($model, $module);
        $what = ($what === 'dbTable')?'table':$what;
        switch($what) {
            case 'model':
            case 'object':
                $class_name = '\\'.implode('\\', array(
                    $data['module']->getType('\\'),
                    ucfirst(strtolower($data['module']->getName())),
                    'Model',
                    ucfirst(strtolower($data['model'])),
                ));
                break;
            default:
                $class_name = '\\'.implode('\\', array(
                    $data['module']->getType('\\'),
                    ucfirst(strtolower($data['module']->getName())),
                    'Model',
                    ucfirst(strtolower($what)),
                    ucfirst(strtolower($data['model'])),
                ));
                break;
        }
        if(class_exists($class_name)) {
            // @TODO Отказаться от Identity и убрать этот КОСТЫЛЬ
            if($what === 'identity') {
                return new $class_name(null, $options);
            }
            $reflection = new \ReflectionClass($class_name);
            $first_param = current($reflection->getMethod('__construct')->getParameters());
            if($first_param && !$first_param->isArray()) {
                return $reflection->newInstanceArgs($options);
            }
            return new $class_name($options);
        }
        return null;
    }
    
    public static function _collectData($source, $subsource = null, array $options = array()) {
        $base_source = $source;
        if(is_string($source)) {
            if(false !== strpos($source, Service\Helper::MODEL_ALIAS_SEPARATOR)) {
                // Либо alias
                $model = Service\Helper::getModelByAlias($source);
                if(!$model || !($model instanceof AbstractModel)) {
                    throw new \Exception('Wrong alias given "'.$source.'". '.__METHOD__);
                }
                return self::_collectData($model->findModelName(), ModuleManager::find($model->findModuleName()));
            } elseif((false !== strpos($source, '\\')) || (false !== strpos($source, '_'))) {
                // Либо название класса
                if(!class_exists($source) && (0 !== strpos($source, '\\'))) {
                    $source = '\\'.$source;
                }
                if(!class_exists($source)) {
                    throw new Exception('No such class exists "'.$base_source.'". '.__METHOD__);
                }
                $class_data = array_values(array_diff(explode('\\', $source), array('')));
                
                $module_name = strtolower($class_data[(int) array_search('Module', $class_data)+1]);
                $model_name = strtolower($class_data[count($class_data)-1]);
                
                return self::_collectData($model_name, $module_name);
            } else {
                // Либо название модели
                // Ищем модуль
                if(is_null($subsource)) {
                    return self::_collectData($source, ModuleManager::find('home'));
                } elseif(is_string($subsource)) {
                    $module = ModuleManager::find($subsource);
                    if(!$module || !($module instanceof AbstractModule)) {
                        throw new \Exception('Wrong module name given "'.$subsource.'". '.__METHOD__);
                    }
                    return self::_collectData($source, $module);
                } elseif($subsource instanceof AbstractModule) {
                    return array(
                        'model' => $source,
                        'module' => $subsource
                    );
                } else {
                    throw new Exception('Unknown $subsource param type given. '.__METHOD__);
                }
            }
        } elseif(is_object($source)) {
            return self::_collectData(get_class($source));
        } else {
            throw new Exception('Unknown $source params');
        }
    }
    
    public static function __callStatic($name, $arguments) {
        array_unshift($arguments, $name);
        return call_user_func_array(array(__CLASS__, 'build'), $arguments);
    }

    /**
     * @deprecated 1.0.1
     * Установка префикса для создания объектов
     * @param string $prefix
     * @return string Старый префикс
     */
    public static function setPrefix($prefix) {
        $old_prefix = self::$_prefix ? self::$_prefix : null;
        self::$_prefix = $prefix;
        return $old_prefix;
    }

    /**
     * @deprecated 1.0.1
     * Возвращает текущий преффикс
     * @return string
     */
    public static function getPrefix() {
        return self::$_prefix;
    }

    /**
     * @deprecated 1.0.1
     * Build subtype Object|Mapper
     * @param string $type
     * @param string $subtype
     * @return Sl_Model_Abstract|Sl_Model_Mapper_Abstract|null
     */
    public static function _build($type, $subtype, \Sl_Module_Abstract $module = null, array $options = array()) {
        $type = strval($type);
        $subtype = strval($subtype);
        $prefix = $module ? $module->prefixModel() : null;
        switch ($type) {
            case 'object' :
                if(!isset(self::$_objects[$type][$subtype])) {
                    $obj = self::_buildObject($subtype, $prefix);
                    if($obj) {
                        self::$_objects[$type][$subtype] = $obj;
                    } else {
                        return null;
                    }
                }
                return clone self::$_objects[$type][$subtype];
                break;
            case 'mapper' :
                if (!isset(self::$_objects[$type][$subtype])) {
                    $obj = self::_buildMapper($subtype, $prefix);
                    if ($obj)
                        return self::$_objects[$type][$subtype] = $obj;
                    return null;
                }
                return self::$_objects[$type][$subtype];
                break;
            case 'identity':
                return self::_buildIdentity($subtype, $prefix, $options);
                break;
            case 'dbTable':
                return self::_buildDbTable($subtype, $prefix);
                break;
        }
        return false;
    }
    
    /**
     * @deprecated 1.0.1
     * @param type $_prefix
     */
    public static function setTemporaryPrefix($_prefix) {
        self::$_prefix_temporary = $_prefix;
    }

    /**
     * @deprecated 1.0.1
     * @return type
     */
    public static function getTemporaryPrefix() {
        $prefix = self::$_prefix_temporary;
        self::$_prefix_temporary = null;
        return $prefix;
    }

    /**
     * @deprecated 1.0.1
     * Фабрика менеджеров
     * @param string $type
     * @param string|\Sl_Module_Abstract $module
     * @return Sl_Model_Mapper_Abstract|null
     */
    public static function _mapper($type, $module = null) {
        if(!is_null($module)) {
            if(!($module instanceof \Sl_Module_Abstract)) {
                if(is_string($module)) {
                    $module = \Sl_Module_Manager::getInstance()->getModule($module);
                    if(!$module) {
                        $module = null;
                    }
                }
            }
        }
        if ($type instanceof Sl_Model_Abstract ) {
            // Преобразовываем в удобный вид - ???????????????
            $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
        } elseif($type instanceof Sl_Controller_Model_Action){
        	 $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
			array_pop($name_array);
			$name_array[] = 'Model';			
            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
        } 
        
        elseif ($type instanceof \Sl\Model\DbTable\DbTable) {

            $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            unset($name_array[count($name_array) - 1]);

            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
        } elseif ($type instanceof \Sl\Model\Identity\Identity) {

            $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            unset($name_array[count($name_array) - 1]);

            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
        }

        return self::build('mapper', $type, $module);
    }

    /**
     * @deprecated 1.0.1
     * Создает "идентити"
     * 
     * @param mixed $type
     * @param \Sl_Module_Abstract|string $module
     * @return \Sl\Model\Identity\Identity
     */
    public static function _identity($type, $module = null, array $options = array()) {
        if(!is_null($module)) {
            if(!($module instanceof \Sl_Module_Abstract)) {
                if(is_string($module)) {
                    $module = \Sl_Module_Manager::getInstance()->getModule($module);
                    if(!$module) {
                        $module = null;
                    }
                }
            }
        }
        if ($type instanceof Sl_Model_Abstract) {
            // Преобразовываем в удобный вид - ???????????????
            $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
        } elseif ($type instanceof \Sl\Model\DbTable\DbTable) {
            $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            unset($name_array[count($name_array) - 1]);

            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
        } elseif($type instanceof \Zend_Controller_Request_Abstract) {
            $module = \Sl_Module_Manager::getInstance()->getModule($type->getModuleName());
            $type = $type->getControllerName();
            $module_type = $module->getType();
            $name_sep = preg_replace('/^.+(\\|_).+$/', '$1', $module_type);
            $name_array = explode($name_sep, $module_type);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
        }
        return self::build('identity', $type, $module, $options);
    }

    /**
     * @deprecated 1.0.1
     * Фабрика объектов
     * @param \Sl_Module_Abstract|string $type
     * @return Sl_Model_Abstract|null
     */
    public static function _object($type, $module = null) {
        if(!is_null($module)) {
            if(!($module instanceof \Sl_Module_Abstract)) {
                if(is_string($module)) {
                    $module = \Sl_Module_Manager::getInstance()->getModule($module);
                    if(!$module) {
                        $module = null;
                    }
                }
            }
        }
        if ($type instanceof Sl_Model_Abstract) {
            // Преобразовываем в удобный вид - ???????????????
            $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
        } elseif ($type instanceof \Sl\Model\DbTable\DbTable || 
        		  $type instanceof \Sl_Model_Mapper_Abstract || 
        		  $type instanceof \Sl\Model\Identity\Identity) {

            $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            unset($name_array[count($name_array) - 1]);

            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
      /*  } elseif ($type instanceof \Sl\Model\Identity\Identity) {
            $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            unset($name_array[count($name_array) - 1]);
            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix); */
        } elseif (is_string($type)) {
            $class = $type;
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);

            self::setTemporaryPrefix($prefix);
        }
        return self::build('object', $type, $module);
    }
    
    /**
     * @deprecated 1.0.1
     * @param type $type
     * @param type $module
     * @return type
     */
    public static function _dbTable($type, $module = null) {
        if(!is_null($module)) {
            if(!($module instanceof \Sl_Module_Abstract)) {
                if(is_string($module)) {
                    $module = \Sl_Module_Manager::getInstance()->getModule($module);
                    if(!$module) {
                        $module = null;
                    }
                }
            }
        }
        if ($type instanceof Sl_Model_Abstract) {
            $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
        } elseif ($type instanceof \Sl\Model\DbTable\DbTable) {
            $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            unset($name_array[count($name_array) - 1]);

            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
        } elseif ($type instanceof \Sl\Model\Identity\Identity) {
            $class = get_class($type);
            $name_array = explode('\\', $class);
            $name = array_pop($name_array);
            unset($name_array[count($name_array) - 1]);

            $type = lcfirst($name);
            $prefix = implode('\\', $name_array);
            self::setTemporaryPrefix($prefix);
        }
        return self::build('dbTable', $type, $module);
    }

    /**
     * @deprecated 1.0.1
     * Строит объект по короткому имени
     * @param string $subtype
     * @return Sl_Model_Abstract
     * @throws Sl_Exception_Model
     */
    protected static function _buildObject($subtype, $prefix = null) {
        if (!$prefix) {
            $prefix = self::getTemporaryPrefix();
        }
        $className = $prefix . '\\' . ucfirst($subtype);
        if (!class_exists($className)) {
            throw new Sl_Exception_Model('No such object found "' . $className . '"');
        }
        return new $className();
    }

    /**
     * @deprecated 1.0.1
     * Строит менеджер по короткому имени
     * @param string $subtype
     * @return Sl_Model_Mapper_Abstract
     * @throws Sl_Exception_Model
     */
    protected static function _buildMapper($subtype, $prefix = null) {
        if (!$prefix) {
            $prefix = self::getTemporaryPrefix();
        }
        $className = ($prefix ? $prefix : self::$_prefix) . '\\Mapper\\' . ucfirst($subtype);
        if (!class_exists($className)) {
            throw new Sl_Exception_Model('No such mapper found "' . $className . '"');
        }
        return new $className();
    }

    public static function _buildIdentity($subtype, $prefix = null, array $options = array()) {
        if (!$prefix) {
            $prefix = self::getTemporaryPrefix();
        }
        $className = ($prefix ? $prefix : self::$_prefix) . '\\Identity\\' . ucfirst($subtype);
        if (!class_exists($className)) {
            throw new Sl_Exception_Model('No such identity found "' . $className . '"');
        }
        return new $className(null, $options);
    }
    
    /**
     * @todo Подружить с сервисом Sl\Service\DbTable
     * @deprecated 1.0.1
     * 
     * @param type $subtype
     * @param type $prefix
     * @return type
     * @throws Sl_Exception_Model
     */
    public static function _buildDbTable($subtype, $prefix = null) {
        if (!$prefix) {
            $prefix = self::getTemporaryPrefix();
        }
        $className = ($prefix ? $prefix : self::$_prefix) . '\\Table\\' . ucfirst($subtype);
        if (!class_exists($className)) {
            throw new Sl_Exception_Model('No such db table found "' . $className . '"');
        }
        return \Sl\Service\DbTable::get($className);
    }

}

?>
