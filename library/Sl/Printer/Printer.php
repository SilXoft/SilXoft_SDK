<?php
namespace Sl\Printer;

abstract class Printer {
    
    protected $_current_model;
    protected $_current_template;
    protected $_current_addition_models = array();
    protected $_translator;
    protected $_templates = array();
    protected $_extras = array();
    protected $_mask;
    


    const TYPE_SEPARATOR = ':';
    
    /**
     * 
     * @param \Sl\Printer\Template $template
     */
    public function addTemplate(Template $template) {
        $this->_templates[$template->getName()] = $template;
    }
    
    public function fileName(){
        $type = $this->_getCurrentObject()->findModelName();
        $this->_getCurrentObject();
        $this->getMask();
        if($this->getMask()<>null){
        $filename  = $this->getMask().'_'.$this->_getCurrentObject();}
        else {
            $filename  = $type.'_'.$this->_getCurrentObject();
        }
        return $filename;
    }
    
    /**
     * 
     * @param array $templates
     * @return \Sl\Printer\Printer
     * @throws \Exception
     */
    public function addTemplates(array $templates) {
        foreach($templates as $template) {
            if($template instanceof Template) {
                if(isset($this->_templates[$template->getName()])) {
                    throw new \Exception('Template "'.$template->getName().'" already added. '.__METHOD__);
                }
                $this->addTemplate($template);
            }
        }
        return $this;
    }
    
    /**
     * 
     * @return \Sl\Printer\Printer
     */
    public function cleanTemplates() {
        $this->_templates = array();
        return $this;
    }
    public function setMask($mask){
        $this->_mask = $mask;
    }
    
    
    /**
     * 
     * @param array $templates
     * @return \Sl\Printer\Printer
     */
    public function setTemplates(array $templates) {
        $this->cleanTemplates();
        return $this->addTemplates($templates);
    }
    
    /**
     * 
     * @param \Sl\Printer\Template $template
     */
    public function setTemplate(Template $template) {
        if(!isset($this->_templates[$template->getName()])) {
            $this->addTemplate($template);
            $this->_setCurrentTemplate($template);
        }
    }
    
    /**
     * 
     * @param \Sl_Model_Abstract $model
     */
    public function printIt(\Sl_Model_Abstract $model = null, $addition = array(), $save = false) {
        if(!is_null($model)) {
            $this->setCurrentObject($model, $addition);           
        }  //print_r($addition); die;
        $this->_doPrint($save);
    }
    
    /**
     * 
     * @param \Sl_Model_Abstract $model
     * @return \Sl\Printer\Printer
     */
    public function setCurrentObject(\Sl_Model_Abstract $model, $addition = array()) {
        if (count($addition)){
            $this->setAdditionObjects($addition) ;
        } 
        $this->_current_model = $model;
        return $this;
    }
    
    public function setAdditionObjects($addition = array()) {
        $this->_current_addition_models = array_merge($this->_current_addition_models,$addition);
        return $this->_current_addition_models;
    }
    
    public function getAdditionObjects(){
        return $this->_current_addition_models;
    }

    /**
     * 
     * @return \Sl_Model_Abstract
     */
    protected function _getCurrentObject() {
        return $this->_current_model;
    }
    
     public function _getAdditionObjects() {
        
         return $this->_current_addition_models;
    }
    
        
    /**
     * 
     * @throws \Exception
     */
    protected function _doPrint($save = false, $addition = array()) {
        if(!$this->_getCurrentObject()) {
            throw new \Exception('Current object must be set. '.__METHOD__);
        }
        $data = $this->_prepareObjectData(); 
        if(!is_array($data)) {
            
            throw new \Exception(get_class($this).'::_prepareObjectData must return array. '.__METHOD__);
        } 
        $templates = $this->_prepareObjectTemplates();
        if(!is_array($templates)) {
            
            throw new \Exception(get_class($this).'::_prepareObjectTemplates must return array. '.__METHOD__);
        }
        
    /*    $renderer_params = array('imageType' => 'png', 'sendResult' => false);
         $image = \Zend_Matrixcode::render('qrcode', $this->code_params, 'image', $renderer_params);
         ob_start();
         imagepng($image);
         $image = ob_get_clean();
         ?><div>dfdsffvsadfsf</div>
         <img src="data:image/png;base64,<?=base64_encode($image)?>"/> <?
        die; */
        /* if (($this->getType()=='email')||($this->getType()=='txt')){//на випадок, якщо в прінтері нема вкладеного шаблону
            return $this->save($save, null, $data, $templates);
         } */
        
        return $this->save($save, $this->_getCurrentTemplate(), $data, $templates);
    }
    
    /**
     * 
     * @param \Sl\Printer\Template $template
     * @return \Sl\Printer\Printer
     */
    protected function _setCurrentTemplate(Template $template) {
        $this->_current_template = $template;
        return $this;
    }
    
    /**
     * 
     * @return Template
     * @throws \Exception
     */
    protected function _getCurrentTemplate() {
        if(isset($this->_current_template)) {
            return $this->_current_template;
        } elseif(count($this->_templates)) {
            return current($this->_templates);
        } else {
            throw new \Exception('No templates added yet. '.__METHOD__);
         
        }
    }
    
    /**
     * 
     * @return array Массив данных, на которые будем заменять шаблоны
     */
    abstract protected function _prepareObjectData();
    
    /**
     * 
     * @return array Массив шаблонов для замены
     */
    abstract protected function _prepareObjectTemplates();
    
    /**
     * Сохранение результатов в файл / выдача в браузер
     */
    abstract protected function save($save, Template $template, array $data, array $templ_data);
    
    public function getName() {
        return $this->getType();
    }
    
    public function getType() {
        $class = get_class($this);
        
        $data = explode('\\', $class);
        array_shift($data); // remove leading Sl
        array_shift($data); // remove leading Printer
        array_shift($data); // remove leading Printer
        
        return implode(self::TYPE_SEPARATOR, array_map('strtolower', $data));
    }
    
    public function __construct() {
        $this->_translator = \Zend_Registry::get('Zend_Translate');
    }
    
    public function getTranslator(){
		return $this->_translator;
	}
        
    public function getMask(){
        return $this->_mask;
    }    
    
    public function addExtras(array $extras) {
        foreach($extras as $k=>$v) {
            $this->setExtra($k, $v);
        }
        return $this;
    }
    
    public function setExtras(array $extras) {
        return $this->cleanExtras()->addExtras($extras);
    }
    
    public function setExtra($key, $value) {
        $this->_extras[$key] = $value;
        return $this;
    }
    
    public function cleanExtras() {
        $this->_extras = array();
        return $this;
    }
    
    public function getExtras() {
        return $this->_extras;
    }
    
    public function getExtra($key) {
        return isset($this->_extras[$key])?$this->_extras[$key]:'';
    }
    
    public function __clone() {
        $this->_current_model = null;
        $this->_current_template = clone $this->_current_template;
        $this->_current_addition_models = array();
        $this->_templates = array();
    }
}
