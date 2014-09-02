<?php
namespace Sl\Printer;

abstract class Aggregator {
    
    protected $_models = array();
    
    protected $_printer;
    protected $_options = array();
    
    const EC_OPTION_EXISTS = 100;
    
    public function __construct($options) {
        foreach($options as $key=>$option) {
            $method = \Sl_Model_Abstract::buildMethodName($key, 'set');
            if(method_exists($this, $method)) {
                $this->$method($option);
                unset($options[$key]);
            }
        }
        $this->setOptions($options);
    }
    
    public function addModel(\Sl_Model_Abstract $model) {
        $this->_models[$model->getId()] = $model;
    }
    
    public function hasModel(\Sl_Model_Abstract $model) {
        return isset($this->_models[$model->getId()]);
    }
    
    public function addModels(array $models) {
        foreach($models as $model) {
            try {
                $this->addModel($model);
            } catch(\Exception $e) {
                // Do nothing ...
            }
        }
        return $this;
    }
    
    public function cleanModels() {
        $this->_models = array();
        return $this;
    }
    
    public function setModels(array $models) {
        return $this->cleanModels()->addModels($models);
    }
    
    public function getModels() {
        return $this->_models;
    }

    public function setPrinter(\Sl\Printer\Printer $printer) {
        $this->_printer = $printer;
        return $this;
    }
    
    /**
     * 
     * @param bool $clone
     * @return \Sl\Printer\Printer
     * @throws \Exception
     */
    public function getPrinter($clone = false) {
        if(!$this->_printer) {
            throw new \Exception('No printer set. '.__METHOD__);
        }
        return $clone?(clone $this->_printer):$this->_printer;
    }
    
    public function printIt() {
        $dir = '/tmp/'.\Sl\Service\Common::guid();
        mkdir($dir);
        $files = array();
        if(!is_dir($dir)) {
            throw new \Exception('Can\'t create dir "'.$dir.'"');
        }
        foreach($this->getModels() as $k=>$model) {
            try {
                $path = $dir.DIRECTORY_SEPARATOR.$k.'.tmp';
                $printer = $this->getPrinter(true)->setCurrentObject($model);
                \Sl_Event_Manager::trigger(new \Sl\Event\PrinterAggregate('beforePrint', array(
                    'printer' => $printer,
                    'model' => $model,
                    'extra' => $this->getOptions(),
                )));
                $printer->printIt(null, array(), $path);
                $files[] = $path;
            } catch(\Exception $e) {
                // Do nothing ...
            }
        }
        $result = $this->_mergeResult($files);
        \Sl\Service\Common::rmdir($dir);
        return $result;
    }
    
    abstract protected function _mergeResult(array $files);

    /** OPTIONS
     ********************************/
    
    /**
     * 
     * @param type $name
     * @return type
     */
    public function hasOption($name) {
        return isset($this->_options[$name]);
    }
    
    public function addOption($name, $value, $strict = true) {
        try {
            if($this->hasOption($name)) {
                throw new \Exception('Option alredy exists. Use setOption. '.__METHOD__, self::EC_OPTION_EXISTS);
            }
            $this->_options[$name] = $value;
            return $this;
        } catch(\Exception $e) {
            if($strict) {
                throw $e;
            } else {
                return $this;
            }
        }
    }
    
    public function setOption($name, $value) {
        try {
            return $this->addOption($name, $value);
        } catch (\Exception $e) {
            if($e->getCode() == self::EC_OPTION_EXISTS) {
                $this->_options[$name] = $value;
                return $this;
            } else {
                throw $e;
            }
        }
    }
    
    public function addOptions(array $options, $strict = false) {
        foreach($options as $k=>$v) {
            $this->addOption($k, $v, $strict);
        }
        return $this;
    }
    
    public function cleanOptions() {
        $this->_options = array();
        return $this;
    }
    
    public function setOptions(array $options) {
        return $this->cleanOptions()->addOptions($options);
    }
    
    public function getOption($name, $default = null) {
        return $this->hasOption($name)?$this->_options[$name]:$default;
    }
    
    public function getOptions() {
        return $this->_options;
    }
}