<?php

abstract class Sl_Controller_Action extends Zend_Controller_Action {

    protected $_module;

    public function init() {
        // Включаем json-контекст для методов, начинающихся с ajax
        $context = $this->_helper->ContextSwitch();
        foreach (get_class_methods($this) as $method) {
            $matches = array();
            if(preg_match('/^(ajax.+)Action$/', $method, $matches)) {
                $context->addActionContext($matches[1], 'json');
            }
        }
        if((bool) $this->getRequest()->getParam('ajaxed', false)) {
            $context->addActionContext($this->getRequest()->getActionName(), 'json');
        }
        $context->initContext('json');
        
        // Для методов, начинающихся в popup выключаем layout
        if (preg_match('/^popup.+$/i', $this->getRequest()->getActionName())) {
            $this->_helper->layout()->disableLayout();
            
        }
        if(preg_match('/^ajax.+$/i', $this->getRequest()->getActionName())) {
            $this->view->benchmark = \Sl\Service\Benchmark::get();
        }
        //$module_name = $this->getRequest()->getModuleName();
        $controller_name = $this->getRequest()->getControllerName();
        $action_name = $this->getRequest()->getActionName();

        // Признак iframe-а
        $this->view->is_iframe = (bool) $this->getRequest()->getParam('is_iframe', false);

        $this->view->action = $action_name;
        
        // Заголовок страницы
        $this->view->title = implode(' ', array(
                                            $this->view->translate('title_action_' . $action_name),
                                            $this->view->translate('title_controller_' . $controller_name),
                                            )
                            );
        
        
    }

    public function postDispatch() {
        try {
            $this->_helper->viewRenderer->render();
        } catch (\Zend_View_Exception $e) {
            // пошук view
            // Подменяем view-script на базовый, если не нашли в текущем модуле

            $action = $this->getRequest()->getActionName();

            if (!preg_match('/^ajax.+$/i', $action)) {

                $action = (in_array($action, array('create', 'detailed'))) ? 'edit' : $action;

                $View_directory = 'View';
                $file_extention = '.phtml'; // @TODO Это можно взять где-то из настроек Zend_View
                $controller = $this->getRequest()->getControllerName();
                $module_directory = \Zend_Controller_Front::getInstance()->getDispatcher()->getDispatchDirectory();
                if (!file_exists(implode('/', array($module_directory, $View_directory, $controller, $action . $file_extention)))) {


                    $this->_helper->viewRenderer->setNoController(true);
                    $this->_helper->viewRenderer->setRender('main/' . $action);
                }
            }
        }
    }
    
    public function render($action = null, $name = null, $noController = false) {
        parent::render($action, $name, $noController);
    }

    public function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        // Настройка вида
        parent::__construct($request, $response, $invokeArgs);

        // Установка текущего модуля
        $module_name = $request->getModuleName();
        $module = Sl_Module_Manager::getInstance()->getModule($module_name);
        $this->_setModule($module);
        $this->view->addScriptPath(Sl_Module_Manager::getViewDirectory($module_name));


        $action_name = $this->getRequest()->getActionName();
        $action_name = in_array($action_name, array('create', 'detailed')) ? 'edit' : $action_name;
        // ПОдключение JS
        $this->_includeDefaults();
    }

    /**
     * Устанавливает модуль
     * 
     * @param \Sl_Module_Abstract $module
     * @return \Sl_Controller_Action
     */
    protected function _setModule(\Sl_Module_Abstract $module) {
        $this->_module = $module;
        return $this;
    }

    /**
     * Возвращает модуль
     * 
     * @return \Sl_Module_Abstract
     */
    protected function _getModule() {
        return $this->_module;
    }

    public function fetchIdBasedActions() {
        $customs = $this->getIdBasedActions();
        if(!is_array($customs)) {
            throw new \Exception('Method "'.get_class($this).'::getIdBasedActions" must return array.');
        }
        return array_merge($this->getIdBasedActions(), $this->_getDefaultIdBasedActions());
    }
    
    public function getIdBasedActions() {
        return array();
    }
    
    protected function _getDefaultIdBasedActions() {
        return array();
    }
    
    protected function _includeDefaults() {
        list($default_module, $default_controller) = array(
            \Zend_Controller_Front::getInstance()->getDefaultModule(),
            \Zend_Controller_Front::getInstance()->getDefaultControllerName(),
        );
        
        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();
        
        // @TODO: Убрать и забыть
        $action = in_array($action, array('create', 'detailed'))?'edit':$action;
        
        $this->_includeFiles($default_module, $default_controller, $action, 'js');
        $this->_includeFiles($default_module, $default_controller, $action, 'css');
        $this->_includeFiles($this->_getModule()->getName(), $controller, $action, 'js');
        $this->_includeFiles($this->_getModule()->getName(), $controller, $action, 'css');
    }
    
    /**
     * Подключает скрипты и стили, если они лежат в нужных местах
     * 
     */
    protected function _includeFiles($module, $controller, $action, $type) {
        try {
            if(!in_array($type, array('js', 'css'))) {
                throw new \Exception('Undefined includes type. '.__METHOD__);
            }
            $path = realpath(APPLICATION_PATH.'/'.\Sl_Module_Manager::getInstance()->getModule($module)->getDir());
            $path .= DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, array(
                'static',
                $type,
                $controller,
                $action.'.'.$type,
            ));
            if(file_exists($path)) {
                $link = '/'.implode('/', array(
                    $module,
                    $controller,
                    $action.'.'.$type,
                ));
                switch($type) {
                    case 'js':
                        $this->view->headScript()->appendFile($link);
                        break;
                    case 'css':
                        $this->view->headLink()->appendStylesheet($link);
                        break;
                    default:
                        throw new \Exception('Undefined includes type. '.__METHOD__);
                }
            }
        } catch(\Exception $e) {
            // Ничего не делаем. Пока ....
        }
    }
    
    protected function _isAjaxed() {
        return (bool) $this->getRequest()->getParam('ajaxed', false);
    }
}

?>
