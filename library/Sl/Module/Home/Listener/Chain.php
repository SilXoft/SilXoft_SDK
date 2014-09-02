<?php
namespace Sl\Module\Home\Listener;

class Chain extends \Sl_Listener_Abstract implements \Sl\Listener\Modelaction {
    
    protected $_translator;
    
    public function onAfter(\Sl\Event\Modelaction $event) {
        
    }

    public function onAfterPost(\Sl\Event\Modelaction $event) {
        
    }

    public function onBefore(\Sl\Event\Modelaction $event) {
        if(!$event->isAjax()) {
            if(in_array($event->getCurrentAction(), array('create', 'edit'))) {
                $form = $event->getView()->form;
                if($form && ($form instanceof \Sl\Form\Form) && ($el = $form->getElement('command'))) {
                    /*@var $el \Zend_Form_Element*/
                    $data = $this->getDataForSelect();
                    $el->setMultiOptions($data);
                }
            }
        }
    }

    public function onBeforePost(\Sl\Event\Modelaction $event) {
        
    }
    
    protected function getDataForSelect() {
        $modules = \Sl_Module_Manager::getModules();
        $data = array();
        foreach($modules as $module_name=>$module) {
            $controllers_dir = APPLICATION_PATH.'/'.$module->getDir().'/Controller';
            if(is_dir($controllers_dir)) {
                $dh = opendir($controllers_dir);
                if($dh) {
                    while(false !== ($filename = readdir($dh))) {
                        if(preg_match('/\.php$/', $filename)) {
                            $controller_name = strtolower(pathinfo($filename, PATHINFO_FILENAME));
                            $class_name = '\\'.implode('\\', array(
                                'Sl',
                                'Module',
                                ucfirst(strtolower($module_name)),
                                'Controller',
                                ucfirst($controller_name),
                            ));
                            if(class_exists($class_name)) {
                                foreach(get_class_methods($class_name) as $method) {
                                    $matches = array();
                                    if(preg_match('/^(.+)Action$/', $method, $matches)) {
                                        if(!isset($data['titles_module_'.$module_name])) {
                                            $data['titles_module_'.$module_name] = array();
                                        }
                                        $data[$this->_translate('title_'.$controller_name.'_'.$module_name)][$module_name.'/'.$controller_name.'/'.$matches[1]] = $this->_translate('title_action_'.$matches['1']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }
    
    /**
     * 
     * @return \Zend_Translate
     */
    public function getTranslator() {
        if(!isset($this->_translator)) {
            $this->_translator = \Zend_Registry::get('Zend_Translate');
        }
        return $this->_translator;
    }
    
    /**
     * 
     * @param \Zend_Translate $translate
     * @return \Sl\Module\Home\Listener\Chain
     */
    public function setTranslator(\Zend_Translate $translate) {
        $this->_translator = $translate;
        return $this;
    }
    
    /**
     * 
     * @param string $key
     * @return string
     */
    protected function _translate($key) {
        return $this->getTranslator()->translate($key);
    }
}