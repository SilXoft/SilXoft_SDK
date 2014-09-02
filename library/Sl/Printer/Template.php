<?php
namespace Sl\Printer;

abstract class Template {
    
    protected $_name;
    protected $_file;
    protected $_data;
    protected $_paths = array();
    
    
    public function __construct($file=null, $data = false) {
        $name_ar = explode('\\', get_class($this)); 
        $name = ucfirst(array_pop($name_ar)); 
        $this->setTplPath(__DIR__.'/Template/'.$name.'/tpl');
        if(!strval($name)) {
            throw new \Exception('Wrong template name: "'.$name.'"');
        }
       
        $this->setData($data);
        $this->_name = strval($name); 
        $this->_file = $file; 
    }
    
    public function setData($data) {       
        $this->_data = $data; 
        return $this;
    }
    
    public function getData() {
        return $this->_data;
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function addTplPath($path) {
        if(is_dir($path)) {
            $this->_paths[md5(realpath($path))] = $path;
        }
        return $this;
    }
    
    public function setTplPaths(array $paths) {
        $this->cleanTplPaths();
        foreach($paths as $path) {
            $this->addTplPath($path);
        }
        return $this;
    }
    
    public function cleanTplPaths() {
        $this->_paths = array('');
        return $this;
    }
    
    /**
     * Установка базовой директории шаблонов
     * 
     * @param type $path
     * @return type
     */
    public function setTplPath($path) {
        $this->cleanTplPaths();
        return $this->addTplPath($path);
    }
    
    /**
     * Возвращает базовый путь шаблонов
     * 
     * @return type
     */
    public function getTplPaths() {
        return $this->_paths;
    }
    
    /**
     * Возвращает путь к указанному шаблону с учетом базового
     * 
     * @param type $tpl
     * @return null
     */
    public function getTplPath($tpl) {
        if(file_exists($tpl) && is_readable($tpl)) {
            return $tpl;
        }
        foreach($this->getTplPaths() as $path) {
            if(file_exists($path.'/'.$tpl) && is_readable($path.'/'.$tpl)) {
                return $path;
            }
        }
        return null;
    }
    
    public function getTpl() {
        if($this->getTplPath($this->_file)) {
            if($this->_file == $this->getTplPath($this->_file)) {
                return $this->_file;
            }
            return $this->getTplPath($this->_file).'/'.$this->_file;
        }
        return null;
    }
    
    abstract public function render(array $data, array $templates);
}