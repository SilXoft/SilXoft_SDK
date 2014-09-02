<?php
use Sl\Model\Identity\Field;
use Sl\Model\Identity\Fieldset;
use Sl\Model\Identity\Fieldset\Filter as FieldFilter;
use Sl\Model\Identity\Fieldset\Comparison as FieldComp;
use Sl\Service\Config as Config;
use Sl\Module\Auth\Service\Usersettings as AuthSettings;

abstract class Sl_Controller_Model_Action extends Sl_Controller_Action {

    protected $_modelName;
    
    const EC_AJAXED = 3000;

    public function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        // Настройка вида
        parent::__construct($request, $response, $invokeArgs);
        
        \Sl\Service\Benchmark::save('in controller');
        
        $this->setModelName($this->getRequest()->getControllerName());
        $this->view->model_alias = \Sl\Service\Helper::getModelAlias($this->getModelName(), $this->_getModule());
        if(in_array($this->getRequest()->getActionName(), $this->fetchIdBasedActions())) {
            $model = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())
                        ->find($this->getRequest()->getParam('id'));
            if($model) {
                \Sl_Event_Manager::trigger(new \Sl_Event_Idbased('afterConstruct', array('model' => $model, 'view' => $this->view)));
            }
        }
        $resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $this->getRequest()->getModuleName(),
            'controller' => $this->getRequest()->getControllerName(),
            'action' => \Sl\Service\Helper::LOG_ACTION,
        ));
        if(\Sl_Service_Acl::isAllowed($resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            if(in_array($this->getRequest()->getActionName(), $this->_getDefaultIdBasedActions())) {
                $model = \Sl_Model_Factory::mapper($this->getRequest()->getControllerName(), $this->getRequest()->getModuleName())
                                    ->find($this->getRequest()->getParam('id', 0));
                if($model) {
                    $this->view->log_title = $this->view->translate('История изменений ').$this->view->translate('title_controller_'.$this->getRequest()->getControllerName()).': '.$model.'';
                    $this->view->log_url = \Sl\Service\Helper::logUrl($model);
                }
            }
        }
        foreach($this->getRequest()->getParams() as $k=>$v) {
			$this->getRequest()->setParam($k, is_string($v)?trim($v):$v);
        }
    }

    /**
     * Устанавливаем имя/название текущем модели
     * 
     * @param string $modelName
     * @return \Sl_Controller_Model_Action
     */
    public function setModelName($modelName) {
        $this->_modelName = $modelName;
        return $this;
    }

    /**
     * Возвращает название/имя текущей модели
     * 
     * @return type
     */
    public function getModelName() {
        return $this->_modelName;
    }

    /**
     * Табличный вывод
     * 
     * @TODO Нужно убрать. ПОтому как не используется
     */
    public function listAction() {
        if (\Sl_Service_Settings::value('USE_NEW_LISTVIEW') == 1) {
            $this->getRequest()->setActionName('filters');
            $this->view->headScript()->prependFile('/home/main/filters.js');
            //$this->headScript()->offsetSetFile(100, '/home/main/filters.js');
            $this->view->headLink()->appendStylesheet('/home/main/filters.css');
            return $this->filtersAction();
        }
        $this->_forward('nlist');
        return;
    }

    /**
     * Детализированый вид
     */
    public function detailedAction() {
        $this->_helper->viewRenderer->setRender('edit');
        $errors = $this->getRequest()->getParam('errors', array());
        $Obj = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())->findAllowExtended($this->getRequest()->getParam('id', 0));
        //print_r($Obj);
       // die;
        if (!$Obj) {
            throw new \Sl_Exception_Model('Illegal ' . $this->getModelName() . ' id');
        }
        if (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                                'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                'module' => $this->_getModule(),
                                'controller' => $this->getModelName(),
                                'action' => 'edit'
                            )))) {
                $forward_url = $this->view->url(array(
                    'module' => $this->_getModule(),
                    'controller' => $this->getModelName(),
                    'action' => 'edit',
                    'id' => $Obj->getId()
                        ));
                $this->view->editable=True;
            }
        $this->view->url =  $forward_url;  
        $this->view->subtitle = $Obj->__toString(); 
        //$this->view->content = \Sl\Serializer\Serializer::getDetailedTemplate($Obj);
        $form = \Sl_Form_Factory::build($Obj, true, false, false, array(), array(), true);
        
        $this->view->form = $form;
    }
    
    
    /**
     * Валідація форми
     */
    public function ajaxvalidateAction() {
        $this->view->result = false;
        

        try {

            if (!$Obj) {
               $Obj = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());
            }

            
            $form = \Sl_Form_Factory::build($Obj);
            \Sl_Form_Factory::removeFileelements($form);

            $form->setAction($this->view->url(array('module' => $Obj->findModuleName(), 'controller' => $Obj->findModelName(), 'action' => $this->getRequest()->getActionName())));

            $this->view->form = $form;
            
            \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('before', array(
                'model' => $Obj,
                'view' => $this->view,
                'request' => $this->getRequest(),
            )));
            if ($this->getRequest()->isPost()) {
                \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('beforePost', array(
                    'model' => $Obj,
                    'view' => $this->view,
                    'request' => $this->getRequest(),
                )));
                if ($form->isValid($this->getRequest()->getParams())) {
                    $this->view->result = true;
                    \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('afterPost', array(
                        'model' => $Obj,
                        'view' => $this->view,
                        'request' => $this->getRequest(),
                    )));
                } else {
                    $translated_form_messages = array();
                    
                $form_name = strtolower('model_' . $this->getModelName() . '_form');
                $form_options = \Sl_Module_Manager::getInstance()->getCustomConfig($this->_getModule()->getName(), 'forms', $form_name);
                
                    foreach ($form->getMessages() as $field_name => $messages) {
                        

                        if ($form->getSubForm($field_name) && strrpos( $field_name, \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX ) === false ) {
                            
                            $subform = $form->getSubForm($field_name);
 
                            $sub_messages = $subform->getMessages();
                            
                            foreach ($sub_messages[$subform->getName()] as $k => $message) {
                                
                                foreach ($message as $k_s => $message_s) {
                                    $translated_form_messages[$this->view->translate('error_'.$k) ? $this->view->translate('error_'.$k) : 'error_'.$k][$this->view->translate($k)] = $message_s;
                                }
                            }
                            
                        } else {

                            if ($el = $form->getElement($field_name)) {
                                $field_name = $el->getLabel();
                            }
                           foreach ($messages as $k => $message) {
                                $translated_form_messages[$form_options->modulerelation_accountphone ? $form_options->modulerelation_accountphone->get('label') : $field_name][$this->view->translate($k)] = $message;
                            }
                        }
                    }
                    
                    $this->view->description = $translated_form_messages;
                }
            }
            \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('before', array(
                'model' => $Obj,
                'view' => $this->view,
                'request' => $this->getRequest(),
            )));
        } catch (Exception $e) {
            
            $this->view->description = $e->getMessage() . ($e->getPrevious() ? PHP_EOL . $e->getPrevious()->getMessage() : '');
            

        }
    }
    
    /**
     * Форма добавления/редактирования
     */
    public function editAction() {
        
        try {
            if($this->_isAjaxed()) {
                $this->view->result = true;
            }
            $errors = $this->getRequest()->getParam('errors', array());

            $Obj = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())->findAllowExtended($this->getRequest()->getParam('id', 0));
            $exclude_relation = $this->getRequest()->getParam('exclude_relation', false);

            if (!$Obj) {
                if (false === $this->getRequest()->getParam('id', false)) {
                    $Obj = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());
                    $Obj = \Sl_Model_Factory::mapper($Obj)->prepareNewObject($Obj);
                } else
                    throw new \Sl_Exception_Model('Illegal ' . $this->getModelName() . ' id');
            }

            $Locker = \Sl_Model_Factory::object('\Sl\Module\Home\Model\Locker');
            if (!\Sl_Model_Factory::mapper($Locker)->checkModel($Obj)) {
                if (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                    'module' => $this->_getModule(),
                                    'controller' => $this->getModelName(),
                                    'action' => 'detailed'
                        )))) {
                    $error_url = $this->view->url(array(
                        'module' => $this->_getModule(),
                        'controller' => $this->getModelName(),
                        'action' => 'detailed',
                        'id' => $Obj->getId()
                    ));
                    \Sl\Module\Home\Service\Errors::addError('Форму обрабатывает другой пользователь!');
                    if(!$this->_isAjaxed()) {
                        $this->_redirect($error_url);
                    }
                } else {
                    if(!$this->_isAjaxed()) {
                        $this->_redirect($forward_url);
                    }
                    throw new Exception(\Zend_Registry::get('Zend_Translate')->translate("Форму обрабатывает другой пользователь!"), 1);
                }
            }
        
        $this->view->headScript()->appendFile('/home/main/locker.js');
        $this->view->headScript()->appendFile('/home/main/edittabs.js');

            \Sl_Event_Manager::trigger(new \Sl_Event_Action('beforeEditAction', array(
                        'model' => $Obj,
                        'view' => $this->view,
                    )));



            $form = \Sl_Form_Factory::build($Obj, true,false,false,array($exclude_relation));
            $this->view->form = $form;
            $this->view->subtitle = $Obj->__toString();
            \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('before', array(
                'model'     => $Obj,
                'view'      => $this->view,
                'request'   => $this->getRequest(),
            )));
            if ($this->getRequest()->isPost()) {

                \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('beforePost', array(
                    'model'     => $Obj,
                    'view'      => $this->view,
                    'request'   => $this->getRequest(),
                )));
                if ($form->isValid($this->getRequest()->getParams())) {

                    $Obj->setOptions($this->getRequest()->getParams());

                    try {
                        \Sl_Model_Factory::mapper($Obj)->save($Obj);
                    } catch (\Exception $e) {
                        throw new Exception($e->getMessage(), 1);
                    }
                    \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('afterPost', array(
                        'model'     => $Obj,
                        'view'      => $this->view,
                        'request'   => $this->getRequest(),
                    )));
                    $forward_url = $this->getRequest()->getParam(\Sl_Form_Factory::AFTER_SAVE_URL_INPUT, '');

                    if (!strlen($forward_url)) {
                        $forward_url = '/';

                        \Sl_Service_Acl::setContext($this->getRequest());

                        if (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                            'module' => $this->_getModule(),
                                            'controller' => $this->getModelName(),
                                            'action' => 'list'
                                        )))) {
                            $forward_url = \Sl\Service\Helper::listUrl($Obj);
                            /* $this->view->url(array(
                              'module' => $this->_getModule(),
                              'controller' => $this->getModelName(),
                              'action' => 'list',
                              'params'=>array()
                              )); */
                        }
                    }
                    if ($this->view->is_iframe) {
                        $this->_forward('closeiframe');
                    } else {
                        if(!$this->_isAjaxed()) {
                            $this->_redirect($forward_url);
                        }
                    }
                } else {

                    $form->populate($this->getRequest()->getParams());
                    $this->view->errors = $errors;


                    $this->view->errors += $form->getMessages();
                }
            }
        
        
        $this->view->calc_script .= \Sl\Serializer\Serializer::getCalculatorsJS($Obj);

            \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('after', array(
                'model'     => $Obj,
                'view'      => $this->view,
                'request'   => $this->getRequest(),
            )));

            \Sl_Event_Manager::trigger(new \Sl_Event_Action('afterEditAction', array(
                        'model' => $Obj,
                        'view' => $this->view,
                    )));

            //$this->view->script = ''.$form;
        } catch(\Exception $e) {
            
            if($this->_isAjaxed()) {
                $this->view->result = false;
                $this->view->description = $e->getMessage();
            } else {
                throw $e;
            }
        }
    }

    /**
     * Дублювання моделі
     */
    public function duplicateAction() {

        $Obj = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())->findExtended($this->getRequest()->getParam('id', 0));

        if (!$Obj) {
            if (false === $this->getRequest()->getParam('id', false)) {
                throw new \Sl_Exception_Model('Illegal ' . $this->getModelName() . ' id');
            }
        }

        if ($new_obj = \Sl_Model_Factory::mapper($Obj)->duplicate($Obj)) {

            $forward_url = '/';

            \Sl_Service_Acl::setContext($this->getRequest());

            if (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                                'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                'module' => $this->_getModule(),
                                'controller' => $this->getModelName(),
                                'action' => 'edit'
                            )))) {
                $forward_url = $this->view->url(array(
                    'module' => $this->_getModule(),
                    'controller' => $this->getModelName(),
                    'action' => 'edit',
                    'id' => $new_obj->getId()
                        ));
            } elseif (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                                'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                'module' => $this->_getModule(),
                                'controller' => $this->getModelName(),
                                'action' => 'detailed'
                            )))) {
                $forward_url = $this->view->url(array(
                    'module' => $this->_getModule(),
                    'controller' => $this->getModelName(),
                    'action' => 'detailed',
                    'id' => $new_obj->getId()
                        ));
            } elseif (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                                'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                'module' => $this->_getModule(),
                                'controller' => $this->getModelName(),
                                'action' => 'list'
                            )))) {
                $forward_url = \Sl\Service\Helper::listUrl($Obj);
            }
            $this->_redirect($forward_url);
        } else {
            throw new \Sl_Exception_Model('Duplicate ' . $this->getModelName() . ' error');
        }
    }

    /**
     * Повернення до редагування
     */
    public function returntoeditAction() {

        $Obj = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())->find($this->getRequest()->getParam('id', 0));

        if (!$Obj) {
            if (false === $this->getRequest()->getParam('id', false)) {
                throw new \Sl_Exception_Model('Illegal ' . $this->getModelName() . ' id');
            }
        }

        if (!$Obj->isEditable() && $current_status = $Obj->findControlStatus()) {
            $Obj->fetchControlStatusEditable();
            \Sl_Model_Factory::mapper($Obj)->save($Obj);
        }

        $forward_url = '/';
        $iframe_addition = array();
        if ($this->view->is_iframe) {
            $iframe_addition ['is_iframe'] = '1';
        }

        \Sl_Service_Acl::setContext($this->getRequest());

        if (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $this->_getModule(),
                            'controller' => $this->getModelName(),
                            'action' => 'edit'
                        )))) {
            $forward_url = $this->view->url(array(
                'module' => $this->_getModule(),
                'controller' => $this->getModelName(),
                'action' => 'edit',
                'id' => $Obj->getId()
                    ) + $iframe_addition);
        } elseif (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $this->_getModule(),
                            'controller' => $this->getModelName(),
                            'action' => 'detailed'
                        )))) {
            $forward_url = $this->view->url(array(
                'module' => $this->_getModule(),
                'controller' => $this->getModelName(),
                'action' => 'detailed',
                'id' => $Obj->getId()
                    ) + $iframe_addition);
        } elseif (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $this->_getModule(),
                            'controller' => $this->getModelName(),
                            'action' => 'list'
                        )))) {
            if ($this->view->is_iframe) {
                $this->_forward('closeiframe');
            } else {
                $forward_url = \Sl\Service\Helper::listUrl($Obj);
            }
        }

        $this->_redirect($forward_url);
    }

    /**
     * Форма добавления/редактирования
     */
    public function createAction() {

        // $this->_helper->viewRenderer->setNoController(true);
        $this->_helper->viewRenderer->setRender('edit');

        $Obj = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());
        $Obj = \Sl_Model_Factory::mapper($Obj)->prepareNewObject($Obj);
        $exclude_relation = $this->getRequest()->getParam('exclude_relation', false);
        
        \Sl_Event_Manager::trigger(new \Sl_Event_Action('beforeCreateAction', array(
                    'model' => $Obj,
                    'view' => $this->view,
                    'request'   => $this->getRequest(),
                )));    
        
        $form = \Sl_Form_Factory::build($Obj, true,false,false,array($exclude_relation));
        $this->view->form = $form;
        \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('before', array(
            'model'     => $Obj,
            'view'      => $this->view,
            'request'   => $this->getRequest(),
        )));
        if ($this->getRequest()->isPost()) {
            \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('beforePost', array(
                'model'     => $Obj,
                'view'      => $this->view,
                'request'   => $this->getRequest(),
            )));
            

            if ($form->isValid($this->getRequest()->getParams())) {

                $Obj->setOptions($this->getRequest()->getParams());
                $return_data = \Sl_Model_Factory::mapper($Obj)->save($Obj,true);
                
                \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('afterPost', array(
                    'model'     => $Obj,
                    'view'      => $this->view,
                    'request'   => $this->getRequest(),
                )));
                
                $forward_url = $this->getRequest()->getParam(\Sl_Form_Factory::AFTER_SAVE_URL_INPUT, '');

                if (!strlen($forward_url)) {

                    $forward_url = '/';

                    \Sl_Service_Acl::setContext($this->getRequest());

                    if (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                                        'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                        'module' => $this->_getModule(),
                                        'controller' => $this->getModelName(),
                                        'action' => 'list'
                                    )))) {
                        $forward_url = \Sl\Service\Helper::listUrl($Obj);
                    } elseif (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                                        'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                        'module' => $this->_getModule(),
                                        'controller' => $this->getModelName(),
                                        'action' => 'detailed'
                                    )))) {
                        $forward_url = $this->view->url(array(
                                    'module' => $this->_getModule(),
                                    'controller' => $this->getModelName(),
                                    'action' => 'detailed'
                                )) . '/id/' . $Obj->getId();
                    }
                }

                if ($this->view->is_iframe) {
                    $this->view->obj_id = $Obj->getId();
                    $this->view->obj_tostring = $Obj->__toString();
                    $this->view->obj_edit_url =  $this->view->url(array(
                                    'module' => $this->_getModule(),
                                    'controller' => $this->getModelName(),
                                    'action' => 'edit'
                                )) . '/id/' . $Obj->getId();
                                        $this->view->obj_id = $Obj->getId();
                    $this->view->module_name = $this->_getModule();    
                    $this->view->model_name = $this->getModelName();
                    $this->view->obj_data = $return_data->toArray();
                    $this->view->obj_tostring = $Obj->__toString();
                    
                    $this->_forward('closeiframe');
                } else {
                    $this->_redirect(str_replace('_id_',$Obj->getId(),$forward_url));
                }
            } else {
                //print_r($form->getMessages());
                $form->populate($this->getRequest()->getParams());
                $this->view->errors = $form->getMessages();
            }
        } else {
            $form->populate($this->getRequest()->getParams());
        }
        \Sl_Event_Manager::trigger(new \Sl\Event\Modelaction('after', array(
            'model'     => $Obj,
            'view'      => $this->view,
            'request'   => $this->getRequest(),
        )));
        $this->view->calc_script .= \Sl\Serializer\Serializer::getCalculatorsJS($Obj);

        $this->view->form = $form;
        \Sl_Event_Manager::trigger(new \Sl_Event_Action('afterCreateAction', array(
                    'model' => $Obj,
                    'view' => $this->view,
                )));
    }

    public function ajaxcreateAction() {

        $this->view->result = true;
        try {

            //   $this->_helper->viewRenderer->setNoController(true);
            //   $this->_helper->viewRenderer->setRender('main/edit');

            $Obj = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());

            $exclude_relation = $this->getRequest()->getParam('exclude_relation', false);
            
            if ($set_relation = $this->getRequest()->getParam('set_relation', false)){
                $relation = \Sl_Modulerelation_Manager::getRelations($Obj,$set_relation);
                if ($relation instanceof \Sl\Modulerelation\Modulerelation){
                    $dest_object = $relation -> getRelatedObject($Obj);
                    if ($relation_values=$this->getRequest()->getParam('relation_values', false)){
                    
                        $relation_values = (!is_array($relation_values))?explode(';',$relation_values):$relation_values;
                        $related_objects = array();
                        $mapper = \Sl_Model_Factory::mapper($dest_object);
                        foreach($relation_values as $id){
                            $related_objects[$id] = $mapper->find($id);
                            
                        }
                        $Obj->assignRelated($relation->getName(), $related_objects);
                    }
                }
                
            };
            
            
            $form = \Sl_Form_Factory::build($Obj, true, false, true, array($exclude_relation));


            $form->setAction($this->view->url(array('module' => $Obj->findModuleName(), 'controller' => $Obj->findModelName(), 'action' => $this->getRequest()->getActionName())));

            if ($this->getRequest()->isPost() && $this->getRequest()->getParam('ajax_action', false) > 0) {

                if ($form->isValid($this->getRequest()->getParams())) {
                    $Obj->setOptions($this->getRequest()->getParams());

                    $Obj = \Sl_Model_Factory::mapper($Obj)->save($Obj, true);
                    $this->view->result = array('id' => $Obj->getId(), 'string' => $Obj . '');
                    if ($options_string = $this->getRequest()->getParam('data-extended')) {
                        $options = explode(';', $options_string);
                        if (is_array($options) && count($options)) {
                            $this->view->extra = $this->_collectExtraObjectViewInfo($Obj, $options);
                        }
                    }
                } else {
                    $this->view->result = false;
                    $form->populate($this->getRequest()->getParams());
                    $this->view->description = $form->getMessages();
                }
            } else {
                $this->view->calc_script = \Sl\Serializer\Serializer::getCalculatorsJS($Obj);
                $view_name = $this->_getModule()->getDir() . '/View/' . $this->getModelName() . '/ajaxedit.phtml';
                if (file_exists($view_name)) {

                    $form->setDecorators(array(array('ViewScript', array('viewScript' => $this->getModelName() . '/ajaxedit.phtml'))));
                }

                $this->view->form = '' . $form;
            }
        } catch (Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage() . ($e->getPrevious() ? PHP_EOL . $e->getPrevious()->getMessage() : '');
        }
    }

    public function ajaxeditAction() {

        $this->view->result = true;

        $Obj = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())->findAllowExtended($this->getRequest()->getParam('id', 0));
//print_r($Obj->fetchRelated());
//die;
        try {

            //   $this->_helper->viewRenderer->setNoController(true);
            //   $this->_helper->viewRenderer->setRender('main/edit');


            if (!$Obj) {
                if (false === $this->getRequest()->getParam('id', false)) {
                    throw new \Sl_Exception_Model('Illegal ' . $this->getModelName() . ' id');
                }
            }

            $exclude_relation = $this->getRequest()->getParam('exclude_relation', false);

            $form = \Sl_Form_Factory::build($Obj, true, false, true, array($exclude_relation));


            $form->setAction($this->view->url(array('module' => $Obj->findModuleName(), 'controller' => $Obj->findModelName(), 'action' => $this->getRequest()->getActionName())));

            if ($this->getRequest()->isPost() && $this->getRequest()->getParam('ajax_action', false) > 0) {

                if ($form->isValid($this->getRequest()->getParams())) {
                    $Obj->setOptions($this->getRequest()->getParams());

                    \Sl_Model_Factory::mapper($Obj)->save($Obj);
                    $this->view->result = array('id' => $Obj->getId(), 'string' => $Obj . '');
                } else {

                    $form->populate($this->getRequest()->getParams());
                    $this->view->result['description'] = $form->getMessages();
                }
            } else {
                $this->view->calc_script .= \Sl\Serializer\Serializer::getCalculatorsJS($Obj);
                $this->view->form = '' . $form;
            }
        } catch (Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }

    public function ajaxdetailedAction() {
        $this->view->result = true;
        try {
            
            $Objs_array = array();
            $ext_data = array();
            $model = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());
            if ($this->getRequest()->isPost()  && $request_data_ids = $this->getRequest()->getParam('data_ids', false)) {
                if (is_array($request_data_ids) || count($request_data_ids)) {
                    $data_extended = $this->getRequest()->getParam('data-extended','').$this->getRequest()->getParam('extended', '');
                    $options = explode(';', $data_extended ? $data_extended : '');
                    if (!is_array($options)) {
                        $options = array();
                    }
                    
                    foreach ($request_data_ids as $data_id) {
                        if($data_id[0]){
                                $model = \Sl\Service\Helper::getModelByAlias($data_id[0]);
                        }
                        else{
                            $Objs = \Sl_Model_Factory::mapper($model)->find($data_id[1]);
                            $model = Sl\Service\Helper::getModelByExtend($Objs->getExtend());
                        }
                        $object = \Sl_Model_Factory::mapper($model)->find($data_id[1]);
                        $Objs_array[$object->getId()] = (string) $object;
                        //$Objs_a[$object->getId()] =  $object;
                        if (count($options)) {
                            $ext_data[$object->getId()] = $this->_collectExtraObjectViewInfo($object, $options);
                        }
                    }

                }
            }            
            elseif ($this->getRequest()->isPost() && $request_ids = $this->getRequest()->getParam('ids', false)) {
                
                if (is_array($request_ids) || count($request_ids)) {
                    if (!is_array($request_ids)) {
                        $request_ids = array($request_ids);
                    }
                    $Objs = \Sl_Model_Factory::mapper($model)
                            ->fetchAll('id in (' . implode(', ', $request_ids) . ')');
                    $data_extended = $this->getRequest()->getParam('data-extended','').$this->getRequest()->getParam('extended', '');
                    $options = explode(';', $data_extended ? $data_extended : '');
                    if (!is_array($options)) {
                        $options = array();
                    }                    
                    
                    foreach ($Objs as $object) {
                        $Objs_array[$object->getId()] = (string) $object;
                        //$Objs_a[$object->getId()] =  $object;
                        if (count($options)) {
                            $ext_data[$object->getId()] = $this->_collectExtraObjectViewInfo($object, $options);
                        }
                    }
                }
            }
            $this->view->objects = $Objs_array;
            //$this->view->ob = $Objs_a;
            $this->view->extra = $ext_data;
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }

    public function popuplistAction() {
        if (\Sl_Service_Settings::value('USE_NEW_LISTVIEW') == 1) {
            $this->getRequest()->setActionName('popups');
            return $this->popupsAction();
        } else {

            //$this->_helper->viewRenderer->setNoController(true);
            //$this->_helper->viewRenderer->setRender('main/popuplist');
            $columns = $this->getRequest()->getParam('request_fields', array());
            $selected = $this->getRequest()->getParam('selected', array());
            $filter_fields = $this->getRequest()->getParam('filter_fields', array());
            $returnfields = $this->getRequest()->getParam('returnfields', array());
            $calcs = $this->getRequest()->getParam('calcs', '');
            $selected = array_filter($selected);

            $customs = array();
            foreach ($this->getRequest()->getParams() as $k => $v) {
                if (preg_match('/^c_(.+)$/', $k)) {
                    $customs[$k] = $v;
                }
            }

            $calcs = explode(':', $calcs);

            $prepared_columns = array();
            $type = $this->getRequest()->getParam('type');
            if (count($columns)) {
                foreach ($columns as $column) {
                    $matches = array();
                    if (preg_match('/(.+)[-|:](.+)/', $column, $matches)) {
                        $prepared_columns[] = array($matches[1] => $matches[2]);
                    } else {
                        $prepared_columns[] = $column;
                    }
                }
            } else {
                $prepared_columns = \Sl\Model\Identity\Identity::GET_SIMPLE_NAME;
            }

            $identity = \Sl_Model_Factory::identity($this->getModelName(), $this->_getModule(), array(
                        'columns' => $prepared_columns,
                        'handling' => $this->getRequest()->getParam('handling', '0'),
            ));

            //$objects = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())->fetchAll();
            //$identity = \Sl_Model_Factory::mapper($identity)->fetchAllExtended($identity);

            $check_type = in_array($type, array(
                        \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
                        \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE,
                        \Sl_Modulerelation_Manager::RELATION_FILE_ONE,
                    )) ? 'radio' : 'checkbox';

            if ($type === 'none') {
                $check_type = false;
            }
            $identity->setIsList(false);
            $content = \Sl\Serializer\Serializer::getDTTemplate(\Sl_Model_Factory::object($identity), $identity, $check_type, $selected, $filter_fields, $calcs, $customs, true, false, $returnfields);
            $this->view->content = $content;
        }

    }

    public function nlistAction() {

        $this->view->headScript()->appendFile('/home/main/nlist.js');
        $this->view->headLink()->appendStylesheet('/home/main/nlist.css');
        $selected = $this->getRequest()->getParam('selected', array());
        
        
        $default_object = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());

        $filter_fields = $this->getRequest()->getParam('filter_fields', array());
        $is_iframe = $this->getRequest()->getParam('is_iframe');
        
       
        
        
        $group_actions = \Sl\Service\Groupactions::getGroupActionNames($default_object);
        $allowed_group_actions = array();
        
        if (is_array($group_actions) && count($group_actions)){
            
            
            foreach($group_actions as $action){
                $resource = \Sl_Service_Acl::joinResourceName(array(
                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                    'module' =>  $this->getRequest()->getParam('module'),
                    'controller' => $this->getRequest()->getParam('controller'),
                    'action' => $action
                ));
                
                if(\Sl_Service_Acl::isAllowed($resource, \Sl_Service_Acl::PRIVELEGE_ACCESS))
                $allowed_group_actions[] = $action;
            }
        }
        
       $content = \Sl\Serializer\Serializer::getDTTemplate($default_object, null, count($allowed_group_actions)?'checkbox':false, array(), $filter_fields, array(), array(), false, $is_iframe);
        $this->view->title = null;
        $this->view->subtitle = null;
        $this->view->controller = $this->getRequest()->getControllerName();
        $this->view->content = $content;
    }

    public function ajaxlistAction() {
        $this->view->result = true;

        $request_search = (bool) ($this->getRequest()->getParam('quick_search', false));
        $request_export = (bool) ($this->getRequest()->getParam('export', false));
        
        try {
            $options = array();
            if ($request_search) {
                $options['limit'] = 'limit';
                $options['filter_data'] = 'filter_fields';
                $fields = $this->getRequest()->getParam('filter_fields', array());

                $this->getRequest()->setParam('filter_fields', array_unique(array_merge($fields, array(
                                    'name-like-' . $this->getRequest()->getParam('name'),
                                ))));
            }
            try {
                $this->_doDataRequest($this->view, $this->getRequest(), $options);
            } catch (Exception $e) {
                throw $e;
            }
        } catch (Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }

    protected function _doDataRequest(\Sl_View &$view, \Zend_Controller_Request_Http $request, $opts = array(), $raw_data = false) {
        $options = array(
            'limit' => 'iDisplayLength',
            'offset' => 'iDisplayStart',
            'sort_column' => 'iSortCol_0',
            'sort_dir' => 'sSortDir_0',
            'searchable_prefix' => 'bSearchable_',
            'global_search' => 'sSearch',
            'local_search_prefix' => 'sSearch_',
            'sortable_prefix' => 'bSortable_',
            'filter_data' => 'filter_data',
            'request_data' => 'request_data',
            'check_type' => 'check_type',
            'selected_data' => 'selected_data',
            'only_active' => 'only_active',
            'calculators' => 'calculators',
            'archived' => 'archived',
        );
        
        \Sl\Service\Benchmark::save('in action');
        
        $options = array_merge($options, $opts);

        $request_params = array();

        $view->request_data = $request->getParams();

        $handling = $this->getRequest()->getParam('handling', null);

        $set_filters = $request->getParam($options['filter_data'], array());

        $prepared_data = array();

        $request_data = $request->getParam($options['request_data'], array());

        foreach ($request_data as $item) {
            $matches = array();
            if (preg_match('/(.+)[\.|-](.+)/', $item, $matches)) {
                $prepared_data[] = array($matches[1] => $matches[2]);
            } else {
                $prepared_data[] = $item;
            }
        }

        $object = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());
        $check_type = $request->getParam($options['check_type'], '');
        $select = $request->getParam($options['selected_data'], array());

        $is_export = (bool) ($this->getRequest()->getParam('export', false));
        
        $calcs = $this->getRequest()->getParam($options['calculators'], '');
        //$this->view->use_calcs = $calcs;
        \Sl\Service\Benchmark::save('before identity build');
        $identity = \Sl_Model_Factory::identity($object, null, array(
                    'columns' => count($prepared_data) ? $prepared_data : \Sl\Model\Identity\Identity::GET_SIMPLE_NAME,
                    'check_type' => $check_type,
                    'selected' => $select,
                    'handling' => $handling,
                    'use_calculator' => $calcs,
                    'config_type' => $is_export?\Sl\Model\Identity\Identity::OPTIONS_EXPORT:\Sl\Model\Identity\Identity::OPTIONS_LISTVIEW,
                ));
        \Sl\Service\Benchmark::save('after identity build');
        $this->view->identity = $identity;
        
        foreach ($request->getParam('custom_data') as $key => $value) {
            $m = array();
            if (preg_match('/^c_(.+)$/', $key, $m)) {
                $method = $object->buildMethodName($m[1], 'set');
                if (method_exists($identity, $method)) {
                    $identity->{$method}($value);
                }
            }
        }
        
        if( $object->checkExtend($object) && $object->extendTable() && !$object->isAllExtend()){
            $identity->justExtend($object->extendTable() ? $object->extendTable() : null);
        }
        elseif($object->isAllExtend())
        {
            $identity->justExtend(false);
        }
        $active = $request->getParam($options['active'], '1');

        if ($active !== '1') {
            $identity->justActive(false);
        }
        // Установка уровня отоьбражения архивных
        $identity->setArchived($request->getParam($options['archived'], '-1'));
        
        if ($request->getParam('use_or_search', false)) {
            $identity->setUseOrSearch(true);
        }

        $identity->limit($request->getParam($options['limit'], 10))
                ->offset($request->getParam($options['offset'], 0));
        //print_r($identity->getOffset());die;
        $sort_column = ($request->getParam($options['sort_column'], (3 /* + ($check_type?1:0) */)) - (2 + ($check_type?1:0) ));
        $sort_dir = $this->getRequest()->getParam($options['sort_dir'], 'asc');

        $this->view->sort_col = $sort_column;
        $this->view->sort_dir = $sort_dir;
        $sort_array = array_map(function($el) {
                    return $el['name'];
                }, $identity->getObjectFields(true, true));
        $this->view->sort_array = array_merge(array(0, 1, 2), array_map(function($el) {
                            return $el['name'];
                        }, $identity->getObjectFields(true, true, true)));

        foreach ($set_filters as $filter) {

            //list($name, $operator, $values) = explode('-', $filter);
            $matches = array();
            if (preg_match('/^([^-]+)-([^-]+)-(.+)/', $filter, $matches)) {
                array_shift($matches);
                $name = array_shift($matches);
                $operator = array_shift($matches);
                $values = array_shift($matches);
            } else {
                continue;
            }
            $matches = array();
            if (preg_match('/(.+):(.+)/', $name, $matches)) {
                $name = array($matches[1] => $matches[2]);
            }
            $use_or = false;
            if ($operator == 'useor') {
                $matches = array();
                $or_condition = preg_replace('/^([a-z]+)-([^-]+)-(.+)$/', '$3', $values);
                if (preg_match('/^([^-]+)-([^-]+)-(.+)/', $or_condition, $matches)) {
                    array_shift($matches);
                    $name2 = array_shift($matches);
                    $operator2 = array_shift($matches);
                    $values2 = array_shift($matches);
                    $matches = array();
                    if (preg_match('/(.+):(.+)/', $name2, $matches)) {
                        $name2 = array($matches[1] => $matches[2]);
                    }
                    $values2 = explode(',', $values2);

                    $operator = preg_replace('/^([a-z]+)-([^-]+)-(.+)$/', '$1', $values);
                    $values = preg_replace('/^([a-z]+)-([^-]+)-(.+)$/', '$2', $values);
                    $values = explode(',', $values);
                    $n_values = array(
                        'value' => $values,
                        'name2' => $name2,
                        'related' => key($name2),
                        'operator2' => $operator2,
                        'values2' => $values2,
                    );
                    $identity->field($name)->useor($n_values);
                    //$this->view->{'test2-' . $name2} = array($name2, $operator2, $values2);
                }
            } else {
                if (preg_match('/,/', $values)) {
                    $values = explode(',', $values);
                };
                //print_r(array($name, $operator, $values));
                $identity->field($name)->{$operator}($values);
                //$this->view->{'test-' . $name} = array($name, $operator, $values);
            }
        }

        $test = array();

        $sorted = array_map(function($el) {
                    return $el['name'];
                }, $identity->getCalculatedObjectFields(true, true, true));
        //print_r($sorted);die;
        //print_r($identity->getObjectFields(true, true, true));die;
        $test[] = "Sort column: " . $sort_column . ' (' . $sorted[$sort_column] . ')';
        foreach ($sorted as $k => $column) {

            //foreach ($identity->getObjectFields(true) as $k => $column) {
            if ($request->getParam($options['searchable_prefix'] . ($k + (2/* + ($check_type?1:0) */)), false) == 'true') {
                //$test[] = 'Checking '.$column.' ('.$k.')';
                $gSearch = $request->getParam($options['global_search'], '');
                $lSearch = $request->getParam($options['local_search_prefix'] . ($k + (2/* + ($check_type?1:0) */)), '');
                //$test[] = 'Local search: '.$lSearch;
                $matches = array();
                if (preg_match('/([-\.])/', $column, $matches)) {
                    //$test[] = array('column' => $column, 'matches'=>$matches);
                    $datas = explode($matches[1], $column);
                    $column = array($datas[0] => $datas[1]);
                    //$test[] = 'Column now: '.print_r($column, true);
                }
                $matches = array();
                //$test[] = 'Local search: '.$lSearch;
                if (preg_match('/^(.*)::(.*)$/', $lSearch, $matches)) {
                    if($matches[1] || $matches[2])
                    $identity->field($column)->between($matches[1], $matches[2]);
                } else {


                    if ($lSearch) {
                        if (preg_match('/^' . \Sl\Service\Lists::LISTS_SEARCH_KEY_PREFIX . '.+/', $lSearch)) {
                            $lSearch = preg_replace('/^' . \Sl\Service\Lists::LISTS_SEARCH_KEY_PREFIX . '/', '', $lSearch);
                            $identity->field($column)->eq($lSearch);
                        } else {
                            $identity->field($column)->like($lSearch);
                        }
                        //$test[] = array($column, $lSearch);
                        //$test[] = 'Search: identity->field('.$column.')->like('.$lSearch.')';
                    }
                }
                if ($gSearch != '') {
                    //$identity->field($column)->like($gSearch);
                }
            }
            //$test[] = 'Check sorting: col='.(is_array($column)?(key($column).'.'.current($column)):$column).'; k='.$k.'; param='.($options['sortable_prefix'] . ($k + (2/* + ($check_type?1:0)*/))).'; req_data='.($this->getRequest()->getParam($options['sortable_prefix'] . ($k + (2/* + ($check_type?1:0)*/)), false));
            // Сортировка регулируется на самой странице. Нет смысла в проверке этого условия - только путаница
            //if ($this->getRequest()->getParam($options['sortable_prefix'] . ($k + (3/* + ($check_type?1:0) */)), false) == 'true') {
                $test[] = 'Checking sort: ' . $k . ' == ' . $sort_column;
                if (($k + 1) == $sort_column) {
                    $test[] = 'identity->field(' . $column . ')->sort(' . $sort_dir . ')';
                    $identity->field($column)->sort($sort_dir);
                }
            //}
            $test[] = "";
        }
        //$this->view->eee = $test;
        //$this->view->ttt = $identity->getObjectFields(true, true, true);
        //$this->view->ttt_calc = $identity->getCalculatedObjectFields(true, true, true);
        $view->id_test = $identity->getComps(false);
        //$view->sort_test = $identity->getSort();

        $identity = \Sl_Model_Factory::mapper($identity)->fetchAllExtended($identity);
        
        \Sl\Service\Benchmark::save('after fetch data');
        
        $view->sql = $identity->getSqlSource();

        $view->iTotalRecords = $identity->getTotalCount();
        $view->iTotalDisplayRecords = $identity->getFiteredCount();
        $view->sEcho = $request->getParam('sEcho', 0);
        $data = $identity->getData($raw_data);
        
        $error = false;
        $last_counter = 0;
        foreach($data as $k=>$v) {
            if($last_counter && $last_counter != count($v)) {
                $error = true;
            }
            $last_counter = count($v);
        }
        if($error) {
            $cache = \Zend_Registry::get('cache')->getBackend();
            $cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('identity'));
            throw new \Exception('Something wrong with identity data. '.__METHOD__);
        }
        
        $view->aaData = $data;//$identity->getData();
        
        \Sl\Service\Benchmark::save('before render');
        
        $this->view->benchmark = \Sl\Service\Benchmark::get();
    }
    
    public function printAction() {
        $available_relations = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())
                    ->getAllowedRelations();
        
        $id = $this->getRequest()->getParam('id', 0);
        
            if (is_array($id) && count($id)){
                 $object = \Sl_Model_Factory::object($this->getModelName(), $this->_getModule());
                 $objects = \Sl_Model_Factory::mapper($object)->fetchAll('id in ('.implode(',',$id).')');
            } else {
                
                $object = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())
                    ->findExtended($id, $available_relations);
                $relations = $object->findFilledRelations(); 
                
                foreach ($relations as $key=>$value){
                        $rl = \Sl_Modulerelation_Manager::getRelations($object, $value);
                        if ($rl->getType() == \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER){
                        $items = $object->fetchRelated($value); 
                            foreach ($items as $obj){
                                $obj = \Sl_Model_Factory::mapper($obj)->findAllowExtended($obj->getId());
                                $item_owner[$value] =$items;
                            }
                        }        
                } 
            }   
              
        
        
    
        $printforms = \Sl_Model_Factory::mapper('printform', \Sl_Module_Manager::getInstance()->getModule('home'))
                ->fetchAllByNameType(\Sl\Printer\Manager::type($object));
        $form = null;
        switch (count($printforms)) {
            case 0:
                throw new \Exception($this->view->translate('There are no printforms for this object.') . __METHOD__);
                break;
            case 1:
                $form = current($printforms);
                break;
            default:
                $pfid = $this->getRequest()->getParam('pfid', 0);
                $form = \Sl_Model_Factory::mapper('printform', \Sl_Module_Manager::getInstance()->getModule('home'))
                        ->find($pfid);
                /* foreach($printforms as $pf) {
                  if($pf->getId() == $pfid) {
                  $form = $pf;
                  }
                  } */
                break;
        }
        if (!$form) {
            throw new \Exception($this->view->translate('Can\'t find needed subform.') . __METHOD__);
        }
        $printfile_relation = \Sl_Modulerelation_Manager::getRelations($form, 'printformfile');
        $form = \Sl_Model_Factory::mapper($form)->findRelation($form, $printfile_relation); 
        $printer = \Sl\Printer\Manager::getPrinter($form);  
        $printer->setCurrentObject($object,$item_owner);
        if (count($objects)) {$printer->setAdditionObjects(array('objects'=>$objects)); }
        \Sl_Event_Manager::trigger(new \Sl\Event\Printer ('beforePrintAction', array(
                    'model' => $object,
                    'printer' => $printer,
                    'printform' => $form,
                    )));
        
        $printer->printIt();
        die;
    }
    
     protected function _collectExtraObjectViewInfo(\Sl_Model_Abstract $model, array $options) {
        $extra = array();
        
     
        foreach ($options as $option) {

            $param = $option;
            if (preg_match('/^fields:(\S+)/', $option, $rez)) {
                $param = 'fields';
                $option = $rez[1];
            }

            switch ($param) {
                case 'url':
                    try {
                        $extra[$option] = \Sl\Service\Helper::modelEditViewUrl($model);
                    } catch (\Sl\Exception\Service $e) {
                        if ($e->getCode() == \Sl\Exception\Service::NO_ID) {
                            $extra[$option] = '#';
                        } else {
                            throw $e;
                        }
                    }
                    break;                
                case 'alias':
                    try {
                        $extra[$option] = \Sl\Service\Helper::getModelAlias($model);
                    } catch (\Sl\Exception\Service $e) {
                        if ($e->getCode() == \Sl\Exception\Service::NO_ID) {
                            $extra[$option] = '#';
                        } else {
                            throw $e;
                        }
                    }
                    break;
                case 'fields':
                    $fields = explode(',', $option);
                  
                    foreach ($fields as $val) {
                      
                        $field = explode('.', $val);
                        $field_val = array();
                        if (count($field) > 1) {
                            
                            $model = \Sl_Model_Factory::mapper($model)->findExtended($model->getId(), array($field[0]));
                            $rel_obj = $model->fetchRelated($field[0]);
                            
                            if($rel_obj){
                               foreach($rel_obj as $r_o)
                                     {
                           if(strtolower($r_o->findModelName())=='file') 
                                    $field_val[] =  '<a href="/file/detailed/id/'.$r_o->getId().'" target="_blank">' . $r_o->Lists($field[1]).'</a>';
                           else{
                               $field_val[] =  $r_o->Lists($field[1]);
                           }                                   
                            $extra['url'] = \Sl\Service\Helper::modelEditViewUrl($model);
                                //        $field_val[] =  $r_o->Lists($field[1]);
                                      }   
                               $extra[$val] =  implode(', ', $field_val);                            
                            }
                        } else {
                            $extra[$val] =  $model->Lists($val);
                        }
                        
                    }                   
                    break;
                default: $extra[$option] = $model->Lists($option);
            }
        }
        return $extra;
    }
/* deprecated:
    public function deleteAction() {
        
        try {
            $object = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())
                    ->find($this->getRequest()->getParam('id', 0));
          

            if (!$object) {
                throw new Exception('Can\'t find object in DB.');
            }
            \Sl_Model_Factory::mapper($object)->delete($object);
           
            if (\Sl_Service_Acl::isAllowed(\Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $this->_getModule(),
                            'controller' => $this->getModelName(),
                            'action' => 'list'
                        )))) {
            $forward_url = $this->view->url(array(
                'module' => $this->_getModule(),
                'controller' => $this->getModelName(),
                'action' => 'list',
                    ) );
            } else {
            $forward_url = $this->view->url(array(
                'module' => 'home',
                'controller' => 'main',
                'action' => 'home',
                    ));
        }
        
        if ($this->view->is_iframe) {
                $this->_forward('closeiframe');
        } else {
              $this->_redirect($forward_url);
        }
        
            
            
            
        } catch (Exception $e) {
           throw new Exception("Error Processing Delete ", 1, $e);
           
        }
    }
*/
    public function ajaxdeleteAction() {
        
        $this->view->result = true;
        $ids = $this->getRequest()->getParam('id', array());
        $ids = is_array($ids) ? $ids : array($ids);
        $data_ids = $this->getRequest()->getParam('data_ids', array());
        
        $cant = $this->view->translate('Невозможно удалить объект id: ');
        
        if(count($data_ids)){
            
            foreach ($data_ids as $data_id) {
                $p_alias = explode('.', $data_id[0]); 
                $id = $data_id[1];
                $alias = $data_id[0];
                $module = $p_alias[0];
                $controller = $p_alias[1];
                $action = $this->getRequest()->getParam('action');
                $resource = \Sl_Service_Acl::joinResourceName(array('type'=> \Sl_Service_Acl::RES_TYPE_MVC, 
                                                            'module' => $module,
                                                            'controller'=>$controller,
                                                            'action'=>$action));
                
                $model = \Sl\Service\Helper::getModelByAlias($alias);                                           
                $object = \Sl_Model_Factory::mapper($model)->find($id);
                /* $fh = fopen(APPLICATION_PATH.'/logs/ajaxdelete.log', 'a+'); */
                
                \Sl_Service_Acl::setContext($object);
                
                if (!\Sl_Service_Acl::isAllowed($resource)){
                    $errors_log[$id] = $cant . $object->__toString();
                    $errors_id[] = $id;
                    $this->view->result = false;
                    continue;
                }
                    
                if (!$object) {
                    $this->view->result = false;
                    $errors_log[$id] = 'Can\'t find object in DB.';
                    $errors_id[] = $id;
                }
                try {
                    \Sl_Model_Factory::mapper($object)->delete($object);
                } catch (Exception $e) {
                    $this->view->result = false;
                    $errors_log[$object->__toString()] = $cant . $object->__toString();
                    $errors_id[] = $object->getId();
                }
            }

            if ($this->view->result == false) {
                $this->view->description = $errors_log;
                $this->view->errIds = $errors_id;
            }  
        }
        elseif (count($ids)) {
                
            $module = $this->getRequest()->getParam('module');
            $controller = $this->getRequest()->getParam('controller');
            $action = $this->getRequest()->getParam('action');
            $resource = \Sl_Service_Acl::joinResourceName(array('type'=> \Sl_Service_Acl::RES_TYPE_MVC, 
                                                            'module' => $module,
                                                            'controller'=>$controller,
                                                            'action'=>$action));
            
            
            foreach ($ids as $id) {
                $object = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())
                        ->find($id);
                /* $fh = fopen(APPLICATION_PATH.'/logs/ajaxdelete.log', 'a+'); */
                
                \Sl_Service_Acl::setContext($object);
                
                if (!\Sl_Service_Acl::isAllowed($resource)){
                    $errors_log[$id] = $cant . $object->__toString();
                    $errors_id[] = $id;
                    $this->view->result = false;
                    continue;
                }
                    
                if (!$object) {
                    $this->view->result = false;
                    $errors_log[$id] = 'Can\'t find object in DB.';
                    $errors_id[] = $id;
                }
                try {
                    \Sl_Model_Factory::mapper($object)->delete($object);
                } catch (Exception $e) {
                    $this->view->result = false;
                    $errors_log[$object->__toString()] = $cant . $object->__toString();
                    $errors_id[] = $object->getId();
                }
            }

            if ($this->view->result == false) {
                $this->view->description = $errors_log;
                $this->view->errIds = $errors_id;
            }
        }
    }

    public function closeiframeAction() {
        
    }
    
    protected function _getDefaultIdBasedActions() {
        return array('edit', 'detailed', 'ajaxedit', 'ajaxdelete', 'log', 'ajaxlog');
    }
    
    public function exportAction() {
        $type = $this->getRequest()->getParam('type', 'xls');
        
        $method_name = '_'.$type.'export';
        
        if(method_exists($this, $method_name)) {
            return $this->$method_name();
        } else {
            throw new \Exception($this->view->translate('No such export method.'));
        }
    }
    
    public function logAction() {
        $this->view->headScript()->appendFile('/home/main/log.js');
        $this->view->headLink()->appendStylesheet('/home/main/log.css');
        
        $model = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())
                        ->find($this->getRequest()->getParam('id', 0));
        if(!$model) {
            throw new \Exception($this->view->translate('Can\'t show log'));
        }

        $this->view->title = null;
        $this->view->subtitle = null;
        
        $config = $model->describeFields();
        $module = \Sl_Module_Manager::getInstance()->getModule($model->findModuleName());
        if(isset($module->section('forms')->{'model_'.$model->findModelName().'_form'})) {
            $config = array_merge_recursive($config, $module->section('forms')->{'model_'.$model->findModelName().'_form'}->toArray());
        }
        
        $config = array_map(function($el){
            $el['label'] = is_array($el['label'])?current($el['label']):$el['label'];
            return $el;
        }, $config);
        $this->view->fields = $config;
        
        $this->view->ajax_url = \Sl\Service\Helper::ajaxLogUrl($model);
    }
    
    public function ajaxlogAction() {
        $this->view->result = true;
        try {
            $this->view->sEcho = $this->getRequest()->getParam('sEcho', '');
            $this->view->data = $this->getRequest()->getParams();
            
            $model = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())
                            ->find($this->getRequest()->getParam('id', 0));
            if(!$model) {
                throw new \Exception($this->view->translate('Can\'t show log'));
            }
            
            $search = array();
            if(($v = $this->getRequest()->getParam('sSearch_1', false)) && $this->getRequest()->getParam('bSearchable_1', false)) {
                $search['field_name'] = $v;
            }
            $sort_col = $this->getRequest()->getParam('iSortCol_0', 0);
            if($this->getRequest()->getParam('bSortable_'.$sort_col, 'false') == 'true') {
                switch($sort_col) {
                    case 0:
                        $order = 'timestamp';
                        break;
                    case 1:
                        $order = 'field_name';
                        break;
                    default:
                        $order = 'timestamp';
                        break;
                }
                $order .= ' '.$this->getRequest()->getParam('sSortDir_0', 'desc');
            } else {
                $order = 'timestamp desc';
            }
            
            $limit = $this->getRequest()->getParam('iDisplayLength', 10);
            $offset = $this->getRequest()->getParam('iDisplayStart', 0);
            
            $data = \Sl_Model_Factory::mapper('log', 'home')->fetchAllLogs($model, $search, $order, $limit, $offset);
            
            $this->view->iTotalRecords = $data['total'];
            $this->view->iTotalDisplayRecords = $data['filtered'];
            
            unset($data['total']);
            unset($data['filtered']);
            
            $config = $model->describeFields();
            $module = \Sl_Module_Manager::getInstance()->getModule($model->findModuleName());
            if(isset($module->section('forms')->{'model_'.$model->findModelName().'_form'})) {
                $config = array_merge_recursive($config, $module->section('forms')->{'model_'.$model->findModelName().'_form'}->toArray());
            }
            $config = array_map(function($el){ $el['label'] = is_array($el['label'])?current($el['label']):$el['label']; return $el; }, $config);
            
            $user_ids = array_unique(array_map(function($el){ return $el['user_id']; }, $data));
            $users = array();
            foreach($user_ids as $uid) {
                $u = \Sl_Model_Factory::mapper('user', 'auth')->find($uid);
                if($u) {
                    $users[$uid] = $u;
                }
            }
            
            foreach($data as $k=>$v) {
                if(preg_match('/-/', $v['field_name'])) {
                    list($relation_name, $related_id, $related_field) = explode('-', $v['field_name']);
                    $relation = \Sl_Modulerelation_Manager::getRelations($model, $relation_name);
                    if($relation) {
                        $related_model = $relation->getRelatedObject($model);
                        $related_config = $related_model->describeFields();
                        $related_module = \Sl_Module_Manager::getInstance()->getModule($related_model->findModuleName());
                        if(isset($related_module->section('forms')->{'model_'.$related_model->findModelName().'_form'})) {
                            $related_config = array_merge_recursive($related_config, $related_module->section('forms')->{'model_'.$related_model->findModelName().'_form'}->toArray());
                        }
                        $related_config = array_map(function($el){ $el['label'] = is_array($el['label'])?current($el['label']):$el['label']; return $el; }, $related_config);
                        $related_relation = \Sl_Modulerelation_Manager::getRelations($related_model, $related_field);
                        if($related_relation) {
                            $data[$k]['field_name'] = isset($related_config['modulerelation_'.$related_field]['label'])?$related_config['modulerelation_'.$related_field]['label']:$related_field;
                        } else {
                            $data[$k]['new_value'] = $related_model->Lists($related_field, is_null($v['new_value'])?'NULL':$v['new_value']);
                            $data[$k]['old_value'] = $related_model->Lists($related_field, is_null($v['old_value'])?'NULL':$v['old_value']);
                            $data[$k]['field_name'] = isset($related_config[$related_field]['label'])?$related_config[$related_field]['label']:$related_field;
                        }
                        $relation_name = isset($config['modulerelation_'.$relation_name]['label'])?$config['modulerelation_'.$relation_name]['label']:$relation_name;
                        $data[$k]['field_name'] = $relation_name.' - '.$data[$k]['field_name'];
                    } else {
                        $relation_name = isset($config[$relation_name]['label'])?$config[$relation_name]['label']:$relation_name;
                        $v['field_name'] = $data[$k]['field_name'] = $relation_name;
                    }
                } else {
                    if(\Sl_Modulerelation_Manager::getRelations($model, $v['field_name'])) {
                        $relation_name = $v['field_name'];
                        $relation = \Sl_Modulerelation_Manager::getRelations($model, $relation_name);
                        if($relation) {
                            $related = $relation->getRelatedObject($model);
                            if($related && ($related instanceof \Sl_Model_Abstract)) {
                                // Old
                                if($v['old_value']) {
                                    $related = \Sl_Model_Factory::mapper($related)->find($v['old_value']);
                                    if($related) {
                                        $data[$k]['old_value'] = $related.'';
                                    }
                                }
                                if($v['new_value']) {
                                    $related = \Sl_Model_Factory::mapper($related)->find($v['new_value']);
                                    if($related) {
                                        $data[$k]['new_value'] = $related.'';
                                    }
                                }
                            }
                        }
                        $data[$k]['field_name'] = isset($config['modulerelation_'.$v['field_name']]['label'])?$config['modulerelation_'.$v['field_name']]['label']:$v['field_name'];
                    } else {
                        $data[$k]['new_value'] = $model->Lists($v['field_name'], is_null($v['new_value'])?'NULL':$v['new_value']);
                        $data[$k]['old_value'] = $model->Lists($v['field_name'], is_null($v['old_value'])?'NULL':$v['old_value']);
                        $data[$k]['field_name'] = isset($config[$v['field_name']]['label'])?$config[$v['field_name']]['label']:$v['field_name'];
                    }
                }
                if(isset($users[$v['user_id']])) {
                    $data[$k]['user_id'] = $users[$v['user_id']].'';
                }
            }
            
            $this->view->aaData = array_map(function($el){ return array_values($el); }, $data);
            
        } catch(\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
    
    protected function _setExportEileName($modelname) {
     //  return('dasfadsfasdfasdfasd');
        $date_mask = null;
                $filters = $this->view->identity->getComps(true);
                foreach ($filters as $key => $filter) {
                    if ($filter['name'] == 'receive_date') {
                        foreach ($filter['value'] as $key => $value) {
                            if ($date_mask == null) {
                                $date_mask = $value;
                            } else {
                                $date_mask = $date_mask . '_' . $value;
                            }
                        }
                    }
                }
                if ($date_mask == null) {
                    $date = new DateTime;
                    $date_mask = $date->format("Y-m-d");
                }
        switch ($modelname) {
            case 'package':
                 $exportfilename = 'MZ-' . $date_mask;
                return $exportfilename;
                break;
            case 'finoperation':
                 $exportfilename = 'BL-' . $date_mask;
                return $exportfilename;
                break;
            default:
                $action = 'list';
                $module = $this->getRequest()->getParam('module');
               // $name = $module.'_'.$modelname.'_'.$action;
                $name = $this->view->translate($module.'_'.$modelname.'_'.$action);
                return \Sl_Service_Transliteration::translit($name, 'ru').$date_mask;
                //return $name.$date_mask;
                break;
        }
        /*    if ($modelname=='package'){
          $date_mask=null;
          $filters = $this->view->identity->getComps(true);
          foreach ($filters as $key => $filter) {
          if ($filter['name'] == 'receive_date') {
          foreach ($filter['value'] as $key => $value) {
          if ($date_mask == null) {
          $date_mask = $value;
          } else {
          $date_mask = $date_mask . '_' . $value;
          }
          }
          }
          }
          if ($date_mask == null){
          $date=new DateTime;
          $date_mask = $date->format("Y-m-d");

          } $exportfilename = 'MZ-'.$date_mask;
          return $exportfilename;
          }
          if ($modelname=='finoperation'){
          $exportfilename = 'BL';
          return $exportfilename;
          } */
    }
    
    protected function _nXlsExport() {
        set_time_limit(0);
        //error_reporting(E_ALL);
        $model = \Sl_Model_Factory::object($this->getRequest()->getControllerName(), $this->_getModule());
        // Данные о колонках
        $fields = array();
        foreach ($this->getRequest()->getParam('cols', array()) as $name => $data) {
            if (isset($data['roles'])) {
                $data['roles'] = explode(',', $data['roles']);
            }
            $fields[$name] = $data;
        }
        // Создаем набор на основании полученнх даннх 
        $fs = Fieldset\Factory::build($model, 'listview', $fields);

        // Определение фильтров
        // Конфигурация
        $filters = \Sl\Service\Config::read($model, 'filters')->toArray();
        $fieldsets = \Sl\Service\Config::read($model, 'fieldsets')->toArray();
        // Текущий фильтр.
        // Из заголовков таблицы
        $current_filter = $this->getRequest()->getParam('filters', false);
        $filter = $this->getRequest()->getParam('filter', 'default');
        if (false === array_search($filter, array_keys($filters))) {
            $filter = 'default';
        }

        // Определение набора колонок
        $cookie_fieldset = 'current-' . $model->findModuleName() . '_' . $model->findModelName() . '-fieldset';
        $fieldset_name = $this->getRequest()->getParam('fieldset', $this->getRequest()->getCookie($cookie_fieldset, 'default'));
        if (false === array_search($fieldset_name, array_keys($fieldsets))) {
            $fieldset_name = 'default';
        }

        // Строим фильтры
        $fs->addComps(array(
            FieldComp\Factory::build($filters[$filter]['filter'], $fs)
        ));
        if (false !== $current_filter) {
            $ext_filters = FieldComp\Factory::build(array('type' => 'multi', 'comps' => $this->_parseFilterData($current_filter)), $fs);
            $fs->addComps(array($ext_filters));
        }

        // Настраиваем набор данных
        $dataset = new \Sl\Model\Identity\Dataset\Xls();
        $dataset->addOptions(array(
            'order' => array(
                'field' => $fs->getFieldByIndex($this->getRequest()->getParam('iSortCol_0', 0)),
                'dir' => $this->getRequest()->getParam('sSortDir_0', 'desc'),
            ),
            'offset' => 0,
        ))->setFieldset($fs);
        // Получаем данные
        $dataset = \Sl_Model_Factory::mapper($model)->fetchDataset($dataset);
        
        $excel = new \PHPExcel();
        $ws = $excel->getActiveSheet();

        $headers = array();
        foreach ($fs->getFields('export') as $field) {
            if ($field->getVisible() && (!$field->hasRole('system'))) {
                $headers[$field->getName()] = $field->getLabel();
            }
        }
        
        $cur_col = 0;
        $cur_row = 1;

        foreach ($headers as $value) {
            $value = html_entity_decode($value, ENT_COMPAT | ENT_HTML401, 'UTF-8');
            $cell = $ws->getCellByColumnAndRow($cur_col, $cur_row)->setValue($value);
            $ws->getStyleByColumnAndRow($cur_col, $cur_row)->getFont()->setBold(true);
            $cur_col++;
        }
        $cur_row++;

        foreach ($dataset->getData() as $values) {
            $cur_row++;
            $cur_col = 0;
            foreach (array_keys($headers) as $name) {
                $cell = $ws->getCellByColumnAndRow($cur_col, $cur_row)->setValue($values[$name]);
                $cur_col++;
            }
        }
        
        header('Content-Description: File Transfer');
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=export.xls');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        $writer = new \PHPExcel_Writer_Excel2007($excel);
        echo $writer->save('php://output');
        die;
    }

    protected function _xlsexport() {
        if($this->getRequest()->getParam('cols', false)) {
            $this->_nXlsExport();
        }
        set_time_limit(300);\Zend_Registry::get('cache')->clean();
        if($this->getRequest()->getParam('page_only', false)) {
            // Добавляем условие по id in(....)
            // Возможно оно должно быть единственным, дабы не усложнять запрос
            $ids = $this->getRequest()->getParam('export_ids', array());
            $filters = $this->getRequest()->getParam('filter_data', array());
            $filters[] = 'id-in-'.implode(',', $ids);
            $this->getRequest()->setParam('filter_data', $filters);
        } else {
            // Убираем ограничение по кол-ву
            $this->getRequest()->setParam('iDisplayLength', '-1');
            $this->getRequest()->setParam('iDisplayStart', 0);
        }
        //$this->getRequest()->setParam('export', false);
        $this->_doDataRequest($this->view, $this->getRequest(), array(), true);
        $modelname = $this->getModelName();       
        //echo '<pre>'.print_r($modelname, true).'</pre>';die;
        while(ob_get_level()) {
            ob_end_clean();
        }
        try {
            $data = $this->view->aaData;
            $fields = $this->view->identity->getCalculatedObjectFields(true, true, true);
            //header('Content-type: text/plain');
            //echo "\r\n".$this->view->identity->getConfigOptionsType()."\r\n";
            //print_r(array_map(function($el){ return $el['label'].' ('.$el['name'].'): '.($el['visible']?1:0); }, $fields));die;
            $visibility_data = $this->getRequest()->getParam('visibility_data', array());
            $to_delete_columns = array(0, 1, 2);
            if($this->view->identity->getOption('check_type')) {
                $to_delete_columns[] = 3;
            }
            $to_delete_count = count($to_delete_columns);
            $headers = array();
            foreach($fields as $k=>$field) {
                if(!isset($visibility_data[$k + $to_delete_count]) || $visibility_data[$k + $to_delete_count] == '0') {
                    $to_delete_columns[] = $k + $to_delete_count;
                    continue;
                }
                $headers[] = ucfirst(strtolower(htmlspecialchars_decode($field['label'])));
            }
            if(count($to_delete_columns)) {
                foreach($data as $k=>$row) {
                    foreach($to_delete_columns as $del) {
                        unset($row[$del]);
                    }
                    $row = array_map(function($el){
                        $el = strip_tags($el);
                        return trim($el);
                    }, $row);
                    $data[$k] = array_values($row);
                }
            }

            while(ob_get_level()) {
                ob_end_clean();
            }
            
            array_unshift($data, $headers);
	    $fname = self::_setExportEileName($modelname);
            unset($this->view->aaData);
            unset($this->view->identity);
            if(class_exists('PHPExcel')) {
                $excel = new \PHPExcel();
                $ws = $excel->getActiveSheet();
                $headers = array_shift($data);
                
                $cur_col = 0;
                $cur_row = 1;
                
                foreach($headers as $value) {
                    $value = html_entity_decode($value, ENT_COMPAT | ENT_HTML401, 'UTF-8');
                    $cell = $ws->getCellByColumnAndRow($cur_col, $cur_row)->setValue($value);
                    $ws->getStyleByColumnAndRow($cur_col, $cur_row)->getFont()->setBold(true);
                    $cur_col++;
                }
                $cur_row++;
                
                foreach($data as $values) {
                    $cur_row++;
                    $cur_col = 0;
                    foreach($values as $value) {
                        $cell = $ws->getCellByColumnAndRow($cur_col, $cur_row)->setValue($value);
                        $cur_col++;
                    }
                }
                
                header('Content-Description: File Transfer');
                header('Content-Encoding: UTF-8');
                header('Content-type: text/csv; charset=UTF-8');
                header('Content-Disposition: attachment; filename='.$fname.'.xls');
               // header('Content-Disposition: attachment; filename=someshit.xls');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                
                $writer = new \PHPExcel_Writer_Excel2007($excel);
                echo $writer->save('php://output');
            } else {
                header('Content-Description: File Transfer');
                header('Content-Encoding: UTF-8');
                header('Content-type: text/csv; charset=UTF-8');
                header('Content-Disposition: attachment; filename=export.csv');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                $this->outputCSV($data);
            }
        } catch(\Exception $e) {
            print_r($e->getMessage());die;
        }
        die;
    }
    
    private function outputCSV($data) {
        $outstream = fopen("php://output", "w");
        function __outputCSV(&$vals, $key, $filehandler) {
            fputcsv($filehandler, array_map(function($el){ return iconv('UTF-8', 'cp1251', $el); }, $vals), ';'); // add parameters if you want
        }
        array_walk($data, "__outputCSV", $outstream);
        fclose($outstream);
    }
    public function ajaxselecteditemsAction()
            {
        
           $this->getRequest()->setParam('iDisplayLength', '-1');
           $this->getRequest()->setParam('iDisplayStart', 0);
        
            $this->_doDataRequest($this->view, $this->getRequest(), array(), true);
            $data =$this->view->aaData;
            if($data)
            {
                $this->view->rezult = true;
            }

            }

/* deprecate
    public function archiveAction() {
        $model = \Sl_Model_Factory::mapper($this->getRequest())
                        ->find($this->getRequest()->getParam('id', 0));
        if(!$model) {
            throw new \Exception('Не удалось определить объект');
        }
        
        $forward_to = $this->getRequest()->getParam(\Sl_Form_Factory::AFTER_SAVE_URL_INPUT, false);
        
        if(!$forward_to) {
            $forward_to = \Sl\Service\Helper::listUrl($model);
        }
        
        try {
            \Sl_Model_Factory::mapper($mode)->archive($model->setArchived(1));
            $this->_forward($forward_to);
        } catch (Exception $e) {
            \Sl\Module\Home\Service\Errors::addError(array('archive' => $e->getMessage()));
        }
    }
*/    
    public function ajaxarchiveAction() {
        $this->view->result = true;
        //error_reporting(E_ERROR);
        $ids = $this->getRequest()->getParam('id', array());
        $ids = is_array($ids)?$ids:array($ids);
        $cant = $this->view->translate('Не удалось изменить ');
        $data_ids = $this->getRequest()->getParam('data_ids', array());
        
        if(count($data_ids)){
  
            foreach ($data_ids as $data_id) {
                    $id = $data_id[1];
                    $alias = $data_id[0];
                    $model = \Sl\Service\Helper::getModelByAlias($alias); 
                    $model = \Sl_Model_Factory::mapper($model)->find($id);
                    if(!$model) {
                        throw new \Exception($this->view->translate('Не удалось определить объект'));
                    }
                    
                    if($this->getRequest()->getParam('toggle')) {
                        $model->setArchived((int) !$model->getArchived());
                    } else {
                        $model->setArchived((int) $this->getRequest()->getParam('set_archived', 1));
                    }
                    try{
                        $model = \Sl_Model_Factory::mapper($model)->archive($model, true);
                        if($this->getRequest()->getParam('return', false)) {
                            if (!is_array($this->view->model)){
                                $this->view->model = array();
                            }    
                            $this->view->model[$id] = $model->toArray();
                        }
                    } catch (\Exception $e) {
                        $this->view->result = false;
                        $errors_log[$model->__toString()] = $cant . $model->__toString().' '.$e->getMessage();
                        $errors_id[] = $model->getId();
                    }
                }
           if (!$this->view->result){
               $this->view->description = $errors_log;
               $this->view->errIds = $errors_id;
           }       
            
        }
        elseif(count($ids)){
        
                foreach($ids as $id){
                    $model = \Sl_Model_Factory::mapper($this->getModelName(), $this->_getModule())
                                ->find($id);
                    if(!$model) {
                        throw new \Exception($this->view->translate('Не удалось определить объект'));
                    }
                    
                    if($this->getRequest()->getParam('toggle')) {
                        $model->setArchived((int) !$model->getArchived());
                    } else {
                        $model->setArchived((int) $this->getRequest()->getParam('set_archived', 1));
                    }
                    try{
                        $model = \Sl_Model_Factory::mapper($model)->archive($model, true);
                        if($this->getRequest()->getParam('return', false)) {
                            if (!is_array($this->view->model)){
                                $this->view->model = array();
                            }    
                            $this->view->model[$id] = $model->toArray();
                        }
                    } catch (\Exception $e) {
                        $this->view->result = false;
                        $errors_log[$model->__toString()] = $cant . $model->__toString().' '.$e->getMessage();
                        $errors_id[] = $model->getId();
                    }
                }
           if (!$this->view->result){
               $this->view->description = $errors_log;
               $this->view->errIds = $errors_id;
           }
                
        }
        
    }
    /*
      try {
                    \Sl_Model_Factory::mapper($object)->delete($object);
                } catch (Exception $e) {
                    $this->view->result = false;
                    $errors_log[$object->__toString()] = $cant . $object->__toString();
                    $errors_id[] = $object->getId();
                }
            }

            if ($this->view->result == false) {
                $this->view->description = $errors_log;
                $this->view->errIds = $errors_id;
            } 
     * */
    public function groupprintAction() {
      //error_reporting(E_ALL);
        $this->view->result = true;
        $pfid = $this->getRequest()->getParam('pfid');
        $form = \Sl_Model_Factory::mapper('printform', \Sl_Module_Manager::getInstance()->getModule('home'))
                        ->find($pfid);
        $ids = $this->getRequest()->getParam('id', array());
        $module = $this->getRequest()->getParam('module');
        $model = $this->getRequest()->getParam('controller');
        $ids = is_array($ids) ? $ids : array($ids);
        
        $type = $form->getType();
        $object = \Sl_Model_Factory::mapper($model, $module)
                    ->findExtended(current($ids));
        if ($type == 'application/Html'){
        $template = $form->getData();
        $template_withno_newline = trim(preg_replace('/\s\s+/', ' ', $template));
        //var_dump(nl2br($template));die;
         if (preg_match_all('/<'.$model.'>(.+)<\/'.$model.'>/m', $template_withno_newline, $tags, PREG_SET_ORDER)) {
             $res = array();
             foreach ($tags as $tag){
                 $new_div = '';
                 preg_match_all('/%([-\.a-zA-Z:0-9_]*?)(\|.+?)?%/', $tag[1], $matches_variable, PREG_SET_ORDER); 
                 preg_match_all('/< *list *mrm *=(.+?)>/', $tag[1], $matches_mrm, PREG_SET_ORDER);
                 if (count($matches_variable)||count($matches_mrm)){
                     foreach ($ids as $id){ 
                     $row = $tag[1];
                     foreach ($matches_variable as $match_var){ 
                         $row = str_replace($match_var[0], '%extras:'.$id.'_'.$match_var[1].'%', $row);
                     }
                     foreach ($matches_mrm as $match_mrm){ 
                         $mrm_model = str_replace('\'', '', $match_mrm[1]);
                         $mrm_model = trim(str_replace('\"', '', $mrm_model));
                         $mrm_tag = str_replace($mrm_model, 'extras:'.$id.'_'.$mrm_model, $match_mrm[0]); 
                         $row = str_replace($match_mrm[0], $mrm_tag, $row);
                         //print_R($row); die;
                     }
                     $new_row = '<div>'.$row.'</div>';
                     $new_div = $new_div.$new_row;
                     $res[$tag[0]]=$new_div; 
                 }
                 
             }
           } 
           
           foreach ($res as $tag => $replasment){
               $template = str_replace($tag, $replasment, $template_withno_newline);
           }    
           $form->setData($template);
         //print_R($template);die;
           
         }
     
        };
        
        $printer = \Sl\Printer\Manager::getPrinter($form);  
        $printer->setCurrentObject($object);
        
        $data_array = array();
        $total_array = array();
        $addition_objects = array();
        if (count($ids)) {
            $available_relations = \Sl_Model_Factory::mapper($object)
                    ->getAllowedRelations();
            foreach ($ids as $id) {

                $a_object = \Sl_Model_Factory::mapper($model, $module)
                        ->findExtended($id, $available_relations);



                $raw_data = array();
                foreach ($a_object->toArray() as $key => $value) {
                    $key_name = $key;
                    $raw_data[$key_name] = array(
                        'name' => $key_name,
                        'value' => $value,
                    );
                }
                foreach ($a_object->fetchRelated() as $relation => $items) {
                    if (count($items)) {
                        foreach ($items as $item) {
                            $item_data = $item->toArray();
                            $values = array();
                            //if(isset($prefix)){$key_name=$prefix.':'.$key;}
                            foreach ($item_data as $key => $val) {
                                if (!isset($raw_data[$relation . '.' . $key])) {
                                    $raw_data[$relation . '.' . $key] = array(
                                        'name' => $relation . '.' . $key,
                                        'relation' => $relation,
                                        'value' => array(
                                            $val,
                                        ),
                                    );
                                } else {
                                    $raw_data[$relation . '.' . $key]['value'][] = $val;
                                }
                            }
                        }
                    }
                }
                $data = array();
                foreach ($raw_data as $key => $value) {
                    foreach ($value as $name => $field) {
                        $data[$id . '_' . $value['name']] = $value['value'];
                    }
                }
                
                
                /* єресь!!!!
                if ($object instanceof \Sl\Module\Logistic\Model\Package) {
                    $package_array = self::addPackageData($object, $id);
                  
                    
                } 
                
                if (count($package_array)){
                    $data = array_merge_recursive($data, $package_array);
                }*/
                $addition_objects[]=$a_object;
                $data_array = array_merge_recursive($data, $data_array);
            }
        }
        
        $printer->setAdditionObjects(array('objects'=>$addition_objects));
        $printer->addExtras($data_array);
        
        \Sl_Event_Manager::trigger(new \Sl\Event\Printer('beforeGroupPrintAction', array(
            'model' => $object,
            'printer' => $printer,
            'printform' => $form,
            'ids' => $ids
        )));
        
        $printer->printIt();
        die;
        
    }
    
    public function importAction() {
        $import_methods = array();
        foreach(get_class_methods($this) as $method) {
            $matches = array();
            if(preg_match('/^(.+)importAction$/', $method, $matches)) {
                $import_methods[$matches[1]] = $this->view->translate($method);
            }
        }
        $this->view->types = $import_methods;
        try {
            if(!count($import_methods)) {
                throw new \Exception('No import methods defined. '.__METHOD__);
            }
            $method = $this->getRequest()->getParam('type', key($import_methods));
            if(!array_key_exists($method, $import_methods)) {
                throw new \Exception('Wrong import type. '.__METHOD__);
            }
            $this->_forward($method.'import');
        } catch(\Exception $e) {
            \Sl\Module\Home\Service\Errors::addError($e->getMessage());
        }
    }

    public function filtersAction() {
        $fields = $this->getRequest()->getParam('filter_fields', array());
        $comps = array();    
        foreach($fields as $field){
            
            list($filed_name, $type, $value) = (explode('-', $field));
            $comps[]= array('field' => str_replace(':', '.', $filed_name), 'type' => $type, 'value' => $value);
          
            
        } 
        $this->view->comps = $comps; 
        
        $this->_clearTitles();
        $this->view->headScript()->appendFile('/js/libs/angular/angular.js');
        
        $model = \Sl_Model_Factory::object($this->getRequest()->getControllerName(), $this->_getModule());
        /*
        if($model->checkExtend($model) && $model->extendTable()){                       
                          $model = \Sl_Model_Factory::object($model->Extend());
        }
         * 
         */     
        //AuthSettings::clean($model, 'filters');
        //AuthSettings::write($model, 'state/fieldset', '_default');
        
        $this->view->model_alias = \Sl\Service\Helper::getModelAlias($model);
        
        // Фильтры
        $filters_config = Config::read($model, 'filters');
        
        try {
            $user_config = AuthSettings::read($model, 'filters');
            $filters_config->merge($user_config);
        } catch (\Exception $e) {
            // Нет настроек пользователя
        }
        
       
        // Наборы полей
        $fieldsets_config = Config::read($model, 'fieldsets');
        try {
            $user_config = AuthSettings::read($model, 'fieldsets');
            $fieldsets_config->merge($user_config);
        } catch (\Exception $e) {
            // Нет настроек пользователя
        }
        
         // Фильтруем popup-ы
        foreach($fieldsets_config as $k=>$v) {
            if($v->type && ($v->type === 'popup')) {
                unset($fieldsets_config->$k);
            }
        }
        
        //AuthSettings::clean($model, 'fieldsets');
        //print_r($fieldsets_config->toArray());die;
        
        // Фильтруем popup-ы
        foreach($fieldsets_config as $k=>$v) {
            if($v->type && ($v->type === 'popup')) {
                unset($fieldsets_config->$k);
            }
        }
       
        // Доступные поля
        $listview_config = Config::read($model, 'listview');
        try {
            // AuthSettings::clean($model, 'listview');
            $user_config = AuthSettings::read($model, 'listview');
            $listview_config->merge($user_config);
        } catch (\Exception $e) {
            // Нет настроек пользователя
        }
	
        // Определяем текущий набор колонок
        // Читаем из state/fieldset
        // Если там нет - берем _default и пишем в state/fieldset как current.
        // Чтобы смело менять и не парится
        $state = AuthSettings::read($model, 'state');
        if(!isset($state->fieldset) || !in_array($state->fieldset, array_keys($fieldsets_config->toArray()))) { // Первый раз на этой странице или какой-то непонятный набор
            if(!isset($fieldsets_config->current)) { // Нет копии настроек по-умолчанию
                $_default_config = $fieldsets_config->_default->toArray();
                $_default_config['name'] = 'current';
                $_default_config['label'] = 'По-умолчанию';
                $fieldsets_config->current = AuthSettings::write($model, 'fieldsets/current', $_default_config);
            }
            $state->fieldset = AuthSettings::write($model, 'state/fieldset', 'current');
            $cur_fieldset = '_default';
        } else {
            $cur_fieldset = $state->fieldset;
        }
        // Создаем набор
        $fieldset = Fieldset\Factory::build($model, 'listview');
	
        // Данные для передачи на view
        $fieldsets = $filters = array();
        
        // Наполняем данные о доступных наборах + напоняем текущий
        foreach($fieldsets_config as $name=>$data) {
            $fieldsets[$name] = array(
                'active' => ($name == $cur_fieldset),
                'name' => $name,
                'description' => $data->label,
            );
            if($name == $cur_fieldset) {
                foreach($data->fields->toArray() as $fname) {
                    if(!$fieldset->hasField($fname)) {
                        $fieldset->createField($fname, array(
                            'roles' => array('render', 'from', 'export')
                        ));
                    }
                }
                foreach($listview_config->toArray() as $fname=>$field_data) {
                    try {
                        if(!$fieldset->hasField($fname)) {
                            $fieldset->createField($fname, $field_data);
                        }
                    } catch (\Exception $e) {
                        // Не удалось добавить ....
                    }
                }
                foreach($data->fields as $fieldname) {
                    try {
                        if(!$fieldset->hasField($fieldname)) {
                            $fieldset->createField($fieldname, array(
                                'roles' => array('render', 'from', 'export')
                            ));
                        }
                    } catch (\Exception $e) {
                        // Не удалось добавить ....
                    }
                }
            }
        }
        
	// Даем возможность добавить поля в набор
        \Sl_Event_Manager::trigger(new \Sl\Event\Fieldset('prepare', array(
            'fieldset' => $fieldset,
        )));
        
        // Данные о наборах на view
        $this->view->fieldsets = $fieldsets;
        
        // Текущий фильтр
        if(!isset($state->filter)) {
            AuthSettings::write($model, 'state/filter', '_default');
            $cur_filter = '_default';
        } else {
            $cur_filter = $state->filter;
        }
        
        // Доступные фильтры
        foreach($filters_config as $name=>$data) {
            $data->active = ($name == $cur_filter);
            $filters[$name] = $data->toArray();
        }
        
        $this->view->order_data = AuthSettings::read($model, 'state/order')->toArray();
        
        // Данные о фильрах на view
        $this->view->filters = $filters;
        $this->view->fieldset = $fieldset;
        
    }
    
    public function ajaxfiltersAction() {
        
        $this->view->result = true;
        try {
            //error_reporting(E_ERROR);
            // Определили объект
            $model = \Sl_Model_Factory::object($this->getRequest()->getControllerName(), $this->_getModule());
            
            // Данные о колонках
            $fields = array();
            foreach($this->getRequest()->getParam('cols', array()) as $name=>$data) {
                if(isset($data['roles'])) {
                    $data['roles'] = explode(',', $data['roles']);
                }
                $fields[$name] = $data;
            }
            // Создаем набор на основании полученнх даннх 
            $fs = Fieldset\Factory::build($model, 'listview', $fields);
            
            // Определение фильтров
            // Конфигурация
            $filters_config = Config::read($model, 'filters');
            try {
                $user_config = AuthSettings::read($model, 'filters');
                $filters_config->merge($user_config);
            } catch (\Exception $e) {
                // Нет настроек пользователя
            }
            
            // Текущий фильтр.
            // Из заголовков таблицы
            $current_filter = $this->getRequest()->getParam('filters', false);
            $filter = $this->getRequest()->getParam('filter', null);
            
            if(!$filter) {
                throw new \Exception('No filter');
            }
            AuthSettings::write($model, 'state/filter', $filter);
            
            // Строим фильтры
            $fs->addComps(array(
                FieldComp\Factory::build($filters_config->$filter->filter->toArray(), $fs)
            ));
            $event = new \Sl\Event\Fieldset('prepareAjax', array(
                'fieldset' => $fs,
                'filters' => $current_filter,    
            ));
           \Sl_Event_Manager::trigger($event);

            $current_filter = $event->getOption('filters');
                        
            if(false !== $current_filter) {
                $ext_filters = FieldComp\Factory::build(array('type' => 'multi', 'comps' => $this->_parseFilterData($current_filter)), $fs);
                $fs->addComps(array($ext_filters));
            }
            $cust_filter = $this->getRequest()->getParam('comps', false);
            if(false !== $cust_filter) {
                $ext_filters = FieldComp\Factory::build(array('type' => 'multi', 'comps' => $cust_filter), $fs);
                $fs->addComps(array($ext_filters));
            }

            // Настраиваем набор данных
            $dataset = new \Sl\Model\Identity\Dataset\Datatables();
            $order_field = $fs->getFieldByIndex($this->getRequest()->getParam('iSortCol_0', 0));
            if(!$order_field || !$order_field->getSortable()) {
                // Ищем поле для сортировки
                $order_field = null;
                foreach($fs->getFields('render') as $field) {
                    if($order_field instanceof Field) continue;
                    if($field->getSortable()) {
                        $order_field = $field;
                    }
                }
                // Ни одно из видимых полей не сортируемое - берем id
                $order_field = $fs->getField('id');
                $this->getRequest()->setParam('sSortDir_0', null);
            }
            $order_dir = (string) $this->getRequest()->getParam('sSortDir_0', 'desc');
            AuthSettings::write($model, 'state/order/field', $order_field->getName());
            AuthSettings::write($model, 'state/order/dir', $order_dir);
            $dataset->addOptions(array(
                'popup' => $this->getRequest()->getParam('popup', 0),
                'order' => array(
                    'field' => $order_field,
                    'dir' => $order_dir,
                ),
                'limit' => $this->getRequest()->getParam('iDisplayLength', 10),
                'offset' => $this->getRequest()->getParam('iDisplayStart', 0),
            ))->setFieldset($fs);
            // Получаем данные
            $dataset = \Sl_Model_Factory::mapper($model)->fetchDataset($dataset);
            // Наполняем ответ
            $this->view->aaData = $dataset->getData();
            $this->view->sSql = $dataset->getOption('sql_source');
            $this->view->sEcho = $this->getRequest()->getParam('sEcho');
            $this->view->iTotalRecords = $dataset->getOption('total_count');
            $this->view->iTotalDisplayRecords = $dataset->getOption('filtered_count');
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
    
    /**
     * Преобразовывает информацию о текущем фильтре в обычный вид
     * 
     * @param array $filter_data
     * @return array
     */
    protected function _parseFilterData(array $filter_data) {
        $filters = array();
        foreach($filter_data as $type=>$data) {
            if(in_array($type, array('and', 'or'))) {
                $filters[] = array(
                    'type' => 'multi',
                    'comparison' => ($type == 'and')?FieldComp\Multi::COMPARISON_AND:FieldComp\Multi::COMPARISON_OR,
                    'comps' => $this->_parseFilterData($data),
                );
            } else {
                foreach($data as $field=>$value) {
                    if(false !== strpos($value, '::')) {
                        $value = explode('::', $value);
                    }
                    $filters[] = array(
                        'type' => $type,
                        'field' => $field,
                        'value' => $value,
                    );
                }
            }
        }
        return $filters;
    }
    
    /**
     * Очистка заголовков описания текущего action-а на страницы
     */
    protected function _clearTitles() {
        $this->view->title = null;
        $this->view->subtitle = null;
    }
    
    /**
     * Возвращает конфигурацию для listview
     * 
     * @param \Sl_Model_Abstract $model
     */
    protected function _getListviewArray(\Sl_Model_Abstract $model) {
        /**
         * @TODO: Переписать куда-то. Здесь этому не место. Но пока не решил куда ...
         */
        $section = \Sl_Module_Manager::getInstance()->getModule($model->findModuleName())->section('listview');
        if(!$section || ($section && !isset($section->{$model->findModelName()}))) {
            return array(
                'fields' => array(
                    'id',
                    'create',
                    'timestamp',
                    'active',
                ),
                'fieldsets' => array(
                    '_default' => array(
                        'fields' => array(
                            'id',
                            'active',
                            'create',
                        ),
                        'name' => '_default',
                    ),
                ),
                'filters' => array(
                    '_default' => array(
                        'type' => 'multi',
                        'comparison' => 1, // AND
                        'comps' => array(
                            '_system' => array(
                                'type' => 'multi',
                                'comparison' => 1, // AND
                                'comps' => array(
                                    array(
                                        'type' => 'eq',
                                        'field' => 'active',
                                        'value' => 1
                                    ),
                                ),
                            ),
                            '_user' => array(
                                'type' => 'multi',
                                'comparison' => 2, //OR,
                                'comps' => array(
                                    '_custom' => array(
                                        'type' => 'multi',
                                        'comparison' => 1, // AND
                                        'comps' => array(

                                        ),
                                    ),
                                    '_id' => array(
                                        'type' => 'in',
                                        'field' => 'id',
                                        'value' => array(),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            );
            /**
             * @TODO: Записать, чтобы не строить сл. раз.
             */
        } else {
            return $section->{$model->findModelName()}->toArray();
        }
    }
    
    public function popupsAction() { 
        //error_reporting(E_ERROR);
        //$this->_helper->viewRenderer->setRender('main/filters');
        $this->_helper->viewRenderer->setNoController(true);
        $this->_clearTitles();
        $this->view->headScript()->appendFile('/js/libs/angular/angular.js');
        $this->view->popup_view = $this->getRequest()->getParam('type', 0);
        
        $this->view->fields_to_return = array_diff(explode(',', $this->getRequest()->getParam('returnfields', '')), array(''));
        $model = \Sl_Model_Factory::object($this->getRequest()->getControllerName(), $this->_getModule());
        $this->view->model_alias = \Sl\Service\Helper::getModelAlias($model);
        
        $tmp_config = $this->_getListviewArray($model);
         
        // Фильтры
        try {
            $filters_config = Config::read($model, 'filters');
        } catch(\Exception $e) {
            $filters_config = Config::write($model, 'filters', $tmp_config['filters'])->toArray();
        }
        try {
            $user_config = AuthSettings::read($model, 'filters');
            $filters_config->merge($user_config);
        } catch (\Exception $e) {
            // Нет настроек пользователя
        }
      
        // Наборы полей
        try {
            $fieldsets_config = Config::read($model, 'fieldsets');
        } catch(\Exception $e) {
            $fieldsets_config = Config::write($model, 'fieldsets', $tmp_config['fieldsets'])->toArray();            
        }
        try {
            $user_config = AuthSettings::read($model, 'fieldsets');
            $fieldsets_config->merge($user_config);
        } catch (\Exception $e) {
            // Нет настроек пользователя
        }
      // print_R($fieldsets_config->toArray()); die;
          
        // Фильтруем не popup-ы
        foreach($fieldsets_config as $k=>$v) {
            if(!isset($v->type) || ($v->type !== 'popup')) {
                unset($fieldsets_config->$k);
            }
        }
        
        // Временная заглушка пока не устоялись новые конфиги
        if(!isset($fieldsets_config->_popup)) {
            try {
                $data = Config::read($model, 'fieldsets/_default')->toArray();
                $data = array_merge($data, array(
                    'name' => '_popup',
                    'type' => 'popup',
                ));
                Config::write($model, 'fieldsets/_popup', $data);
            } catch (\Exception $e) {
                $data = $this->_getListviewArray($model);
                Config::write($model, 'fieldsets/_default', $data['fieldsets']['_default']);
                $data['fieldsets']['_default']['name'] = '_popup';
                $data['fieldsets']['_default']['type'] = 'popup';
                Config::write($model, 'fieldsets/_popup', $data['fieldsets']['_default']);
                throw new \Exception('Update page please. '.__METHOD__);
            }
        }
        

        
        
        // Доступные поля
        $listview_config = Config::read($model, 'listview');
        try {
            $user_config = AuthSettings::read($model, 'listview');
            $listview_config->merge($user_config);
        } catch (\Exception $e) {
            // Нет настроек пользователя
        }
	
        // Определяем текущий набор колонок
        // Читаем из state/fieldset
        // Если там нет - берем _default и пишем в state/fieldset
       $state = AuthSettings::read($model, 'state');
        if(!isset($state->popup_fieldset)) {
            AuthSettings::write($model, 'state/popup_fieldset', '_popup');
            $cur_fieldset = '_popup';
        } else {
            $cur_fieldset = $state->popup_fieldset;
        }
        	
        // Создаем набор
        $fieldset = Fieldset\Factory::build($model, 'listview');
	
        // Данные для передачи на view
        $fieldsets = $filters = array();
        
        // Наполняем данные о доступных наборах + напоняем текущий
        foreach($fieldsets_config as $name=>$data) {
            $fieldsets[$name] = array(
                'active' => ($name == $cur_fieldset),
                'name' => $name,
                'description' => $data->label,
            );
            if($name == $cur_fieldset) {
                foreach($data->fields as $fieldname) {
                    try {
                        $fieldset->createField($fieldname, array(
                            'roles' => array('render', 'from', 'export')
                        ));
                    } catch (\Exception $e) {
                        // Не удалось добавить ....
                    }
                }
            }
        }
        
	// Даем возможность добавить поля в набор
        \Sl_Event_Manager::trigger(new \Sl\Event\Fieldset('prepare', array(
            'fieldset' => $fieldset,
        )));
        
        // Данные о наборах на view
        $this->view->fieldsets = $fieldsets;
        
        // Текущий фильтр
        if(!isset($state->filter)) {
            AuthSettings::write($model, 'state/filter', '_default');
            $cur_filter = '_default';
        } else {
            $cur_filter = $state->filter;
        }
        
        // Доступные фильтры
        foreach($filters_config as $name=>$data) {
            $data->active = ($name == $cur_filter);
            $filters[$name] = $data->toArray();
        }
        
        
                //$this->_helper->viewRenderer->setRender('main/' . $action);
        $this->view->selected = $this->getRequest()->getParam('selected', array());
        // Данные о фильрах на view
        $this->view->filters = $filters;
        $this->view->fieldset = $fieldset; 
       // $this->_helper->viewRenderer->setNoController(true);
       
         $this->_helper->viewRenderer->setRender('main/filters');
         $this->view->headScript()->appendFile('/home/main/filters.js');
         
      
    }
    
    public function ajaxautocompleteAction() {
        /*
        name:
        filter_fields[]:id-in-0
        quick_search:1
        handling:0
         */
        $this->view->result = true;
        try {
            $model = \Sl_Model_Factory::object($this->getRequest()->getControllerName(), $this->_getModule());
            if(!$model) {
                throw new \Exception('Can\'t determine model. '.__METHOD__);
            }
            $fieldset = Fieldset\Factory::build($model, 'listview', array(
                'name' => array(
                    'roles' => array('from'),
                ),
                'id' => array(
                    'roles' => array('from'),
                ),
            ));
            
            \Sl_Event_Manager::trigger(new \Sl\Event\Fieldset('prepareAjax', array(
                'fieldset' => $fieldset
            )));
            
            $filter_config = Config::read($model, 'filters/_default', Config::MERGE_FIELDS);
            $extra_filters = array(
                array(
                    'type' => 'like',
                    'field' => 'name',
                    'value' => (string) $this->getRequest()->getParam('name'),
                ),
            );
            foreach($this->getRequest()->getParam('filter_fields') as $item) {
                list($field, $type, $value) = explode('-', $item);
                if($field && $type && $value) {
                    $field = str_replace(':', '.', $field);
                    if(false !== strpos(',', $value)) {
                        $value = explode(',', $value);
                    }
                    $extra_filters[] = array(
                        'type' => $type,
                        'field' => $field,
                        'value' => $value,
                    );
                }
            }
            $fieldset->addComps(array(
                FieldComp\Factory::build($filter_config->filter->toArray(), $fieldset),
                FieldComp\Factory::build(array(
                    'type' => 'multi',
                    'comps' => $extra_filters,
                ), $fieldset),
            ));
            $dataset = new Sl\Model\Identity\Dataset\Autocomplete();
            $dataset->addOptions(array(
                'limit' => 10,
                'offset' => 0,
            ))->setFieldset($fieldset);
            
            $dataset = \Sl_Model_Factory::mapper($model)->fetchDataset($dataset);
            $this->view->aaData = $dataset->getData();
            $this->view->name_index = 'name';
            $this->view->value_index = 'id';
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }
    
}

?>
