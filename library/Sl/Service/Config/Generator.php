<?php
namespace Sl\Service\Config;

abstract class Generator {
    
    /**
     * 
     * @param type $type
     * @return \Sl\Service\Config\Generator
     */
    public static function factory($type) {
        $class_name = __CLASS__.'\\'.ucfirst(strtolower($type));
        if(class_exists($class_name)) {
            return new $class_name();
        }
        return null;
    }
    
    public function generate(\Sl_Model_Abstract $model, $return = true) {
        $path = '';
        if($model instanceof \Sl_Module_Abstract) {
            $path = $this->_buildModulePath($model);
        } else {
            $path = $this->_buildModelPath($model);
        }
        $config = null;
        if(file_exists($path)) {
            try {
                $config = new \Zend_Config(require $path, true);
            } catch (\Exception $e) {
                $config = new \Zend_Config(array(), true);
            }
        } else {
            $config = new \Zend_Config(array(), true);
        }
        $data = $this->getData($model);
        if($data) {
            if(is_array($data)) {
                $config->merge(new \Zend_Config($data, true));
            } elseif($data instanceof \Zend_Config) {
                $config->merge($data);
            }
        }
        $writer = new \Zend_Config_Writer_Array();
        $writer->write($path, $config);
        if($return) {
            return $config;
        }
    }
    
    public function getName() {
        $class_data = explode('\\', get_class($this));
        while('generator' !== ($temp_part = strtolower(array_shift($class_data)))) {}
        return implode('.', array_map('strtolower', $class_data));
    }
    
    protected function _buildModulePath(\Sl_Module_Abstract $module) {
        return implode(DIRECTORY_SEPARATOR, array(
            APPLICATION_PATH,
            $module->getDir(),
            \Sl\Service\Config::CONFIG_DIR_NAME,
            strtolower($this->getName()),
        )).'.php';
    }
    
    protected function _buildModelPath(\Sl_Model_Abstract $model) {
        return implode(DIRECTORY_SEPARATOR, array(
            APPLICATION_PATH,
            \Sl_Module_Manager::getInstance()->getModule($model->findModuleName())->getDir(),
            \Sl\Service\Config::CONFIG_DIR_NAME,
            $model->findModelName(),
            strtolower($this->getName()),
        )).'.php';
    }
    
    abstract public function getData(\Sl_Model_Abstract $model);
}