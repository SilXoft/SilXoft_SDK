<?php
namespace Sl\Module\Auth\Controller;
use Sl\Service as Service;
use Sl\Module\Auth as Auth;

class Main extends \Sl_Controller_Action {
    
    
    public function init() {
        parent::init();
        if(!preg_match('/^ajax(.+)$/', $this->getRequest()->getActionName())) {
            \Zend_Layout::getMvcInstance()->setLayout('auth');
        }
    }
  
    
    public function logoutAction() {
        \Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }
    
    public function ajaxmodeleditinformationAction() {
        $this->_helper->layout()->disableLayout();
        $this->view->result = true;
        $id = $this->getRequest()->getParam('id', 0);
        $model_name = $this->getRequest()->getParam('model_name', false);
        $module_name = $this->getRequest()->getParam('module_name', false);

        if ($id && $model_name && $module_name) {
            try {
                $module = \Sl_Module_Manager::getInstance()->getModule($module_name);
                $Obj = \Sl_Model_Factory::object($model_name, $module);

                if ($Obj->isLoged()) {
                    $this->view->data = array();
                    $Obj = \Sl_Model_Factory::mapper($Obj)->find($id);

                    $home_module = \Sl_Module_Manager::getInstance()->getModule('home');
                    $Locker = \Sl_Model_Factory::object('locker', $home_module);
                    $user_id = \Sl_Model_Factory::mapper($Locker)->getEditorId($Obj);

                    $create_data = \Sl\Service\Loger::getObjectFieldsLog($Obj);
                    $last_modified_data = \Sl\Service\Loger::getObjectFieldsLog($Obj, -1);

                    $user_tpl = \Zend_Auth::getInstance()->getIdentity();

                    $create_user = \Sl_Model_Factory::mapper($user_tpl)->find($create_data['user_id']);
                    $create_date = \DateTime::createFromFormat('Y-m-d H:i:s', $create_data['timestamp']);

                    $mod_user = \Sl_Model_Factory::mapper($user_tpl)->find($last_modified_data['user_id']);
                    $mod_date = \DateTime::createFromFormat('Y-m-d H:i:s', $last_modified_data['timestamp']);

                    $lock_user = \Sl_Model_Factory::mapper($user_tpl)->find($user_id);

                    $this->view->html = $this->view->partial('partials/createmodifiedinfo.phtml', array(
                        'cuser' => $create_user,
                        'muser' => $mod_user,
                        'luser' => $lock_user,
                        'cdate' => $create_date,
                        'mdate' => $mod_date,
                    ));
                    $this->view->data = array();
                }
            } catch (Exception $e) {
                $this->view->result = false;
                $this->view->description = $e->getMessage();
            }
        }
    }
    
    public function ajaxmodelseditinformationAction() {
        $this->_helper->layout()->disableLayout();
        $this->view->result = true;
        
        try {
            $ids = $this->getRequest()->getParam('ids', array());
            $model_name = $this->getRequest()->getParam('model_name', false);
            $module_name = $this->getRequest()->getParam('module_name', false);
            
            $module = \Sl_Module_Manager::getInstance()->getModule($module_name);
            $Obj = \Sl_Model_Factory::object($model_name, $module);
            
            if(!$Obj) {
                throw new \Exception('Wrong model data. '.__METHOD__);
            }
            if(!$Obj->isLoged()) {
                throw new \Exception('Such model is not logged. '.__METHOD__);
            }
            $tooltips = array();
            foreach($ids as $id) {
                $Obj = \Sl_Model_Factory::mapper($Obj)->find($id);

                $home_module = \Sl_Module_Manager::getInstance()->getModule('home');
                $Locker = \Sl_Model_Factory::object('locker', $home_module);
                $user_id = \Sl_Model_Factory::mapper($Locker)->getEditorId($Obj);

                $create_data = \Sl\Service\Loger::getObjectFieldsLog($Obj);
                $last_modified_data = \Sl\Service\Loger::getObjectFieldsLog($Obj, -1);

                $user_tpl = \Zend_Auth::getInstance()->getIdentity();

                $create_user = \Sl_Model_Factory::mapper($user_tpl)->find($create_data['user_id']);
                $create_date = \DateTime::createFromFormat('Y-m-d H:i:s', $create_data['timestamp']);

                $mod_user = \Sl_Model_Factory::mapper($user_tpl)->find($last_modified_data['user_id']);
                $mod_date = \DateTime::createFromFormat('Y-m-d H:i:s', $last_modified_data['timestamp']);

                $lock_user = \Sl_Model_Factory::mapper($user_tpl)->find($user_id);

                $tooltips[$id] = $this->view->partial('partials/createmodifiedinfo.phtml', array(
                    'cuser' => $create_user,
                    'muser' => $mod_user,
                    'luser' => $lock_user,
                    'cdate' => $create_date,
                    'mdate' => $mod_date,
                ));
            }
            $this->view->tooltips = $tooltips;
        } catch(\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
        
        

        if ($id && $model_name && $module_name) {
            try {
                $module = \Sl_Module_Manager::getInstance()->getModule($module_name);
                $Obj = \Sl_Model_Factory::object($model_name, $module);

                if ($Obj->isLoged()) {
                    $this->view->data = array();
                    $Obj = \Sl_Model_Factory::mapper($Obj)->find($id);

                    $home_module = \Sl_Module_Manager::getInstance()->getModule('home');
                    $Locker = \Sl_Model_Factory::object('locker', $home_module);
                    $user_id = \Sl_Model_Factory::mapper($Locker)->getEditorId($Obj);

                    $create_data = \Sl\Service\Loger::getObjectFieldsLog($Obj);
                    $last_modified_data = \Sl\Service\Loger::getObjectFieldsLog($Obj, -1);

                    $user_tpl = \Zend_Auth::getInstance()->getIdentity();

                    $create_user = \Sl_Model_Factory::mapper($user_tpl)->find($create_data['user_id']);
                    $create_date = \DateTime::createFromFormat('Y-m-d H:i:s', $create_data['timestamp']);

                    $mod_user = \Sl_Model_Factory::mapper($user_tpl)->find($last_modified_data['user_id']);
                    $mod_date = \DateTime::createFromFormat('Y-m-d H:i:s', $last_modified_data['timestamp']);

                    $lock_user = \Sl_Model_Factory::mapper($user_tpl)->find($user_id);

                    $this->view->html = $this->view->partial('partials/createmodifiedinfo.phtml', array(
                        'cuser' => $create_user,
                        'muser' => $mod_user,
                        'luser' => $lock_user,
                        'cdate' => $create_date,
                        'mdate' => $mod_date,
                    ));
                    $this->view->data = array();
                }
            } catch (Exception $e) {
                $this->view->result = false;
                $this->view->description = $e->getMessage();
            }
        }
    }
    
    public function formAction() {
        $form = \Sl_Form_Factory::build(array($this->_getModule(), 'auth_form'));
        $restore_password = \Sl_Form_Factory::build(array($this->_getModule(), 'restore_password'), TRUE);
        $this->view->errors = array();
        $this->view->restore_password = $restore_password;
        $this->view->form = $form;
        
        \Sl_Event_Manager::trigger(new Auth\Event('afterFormsCreation', array('request' => $this->getRequest()->getParams(), 'view' => $this->view)));
                
        $referer_check = \Sl_Service_Settings::value('REFERER_VALIDATE');
        if($referer_check && !$this->getRequest()->isPost()) {
            if(!isset($_SERVER['HTTP_REFERER']) || !preg_match('#^'.str_replace('.', '\.', $referer_check).'/#', $_SERVER['HTTP_REFERER'])) {
                $this->_redirect($referer_check);
            }
        }//print_r('fff');die;
        
        if ($this->getRequest()->getParam('check', false)) {
            //if ($restore_password->isValid($this->getRequest()->getParams())) {
                $login = $this->getRequest()->getParam('login', false);
                $user = \Sl_Model_Factory::mapper('user', $this->_getModule())->findByLogin($login);
                $email = $user->getEmail();
                $newPassword = substr((md5(mktime() . 'Illya is a great hero!')), 0, rand(5, 8));
                for ($i = 0; $i < 3; $i++) {
                    $key = rand(0, strlen($newPassword));
                    $newPassword[$key] = strtoupper($newPassword[$key]);
                }
                $newPassword = trim($newPassword);
                if (($id = $user->getId())&&($email)){
                    $mail = new \Zend_Mail('UTF-8');
                    $mail->addTo($email);
                    $mail->addBcc('s.kachan@silencatech.com');
                    $tpl = $this->view->partial('partials/email.phtml', array(
                        'name' => $user->getName(),
                        'login' => $user->getLogin(),
                        'server' => $_SERVER['SERVER_NAME'],
                        'password' => $newPassword,
                    ));
                    
                    $mail->setSubject($this->view->translate('Восстановление пароля'));
                    
                    $mail->setBodyHtml($tpl);
                    $mail->send();
                    $newPassword = \Sl\Service\Helper::hash($newPassword);
                    \Sl_Model_Factory::mapper('user', $this->_getModule())->newPasswordUpdate($user, $newPassword);
                   
                    $this->view->errors = array_merge($this->view->errors,array($this->view->translate('Уведомление') => $this->view->translate('Новый пароль отослан на Вашу почту.')));
                } else {
                    $this->view->errors = array_merge($this->view->errors,array($this->view->translate('Ошибка') => $this->view->translate('Логин не найден.')));
                }
                
            //} else {
              //  $restore_password->populate($this->getRequest()->getParams());
                //$this->view->errors = $restore_password->getMessages();
                //$this->view->is_changepass = True;
           // }
        } else {
            if ($this->getRequest()->isPost()) { 
                if ($form->isValid($this->getRequest()->getParams())) {
                     
                    try {
                        
                        \Sl_Event_Manager::trigger(new Auth\Event('beforeAuthenticate', array('request' => $this->getRequest()->getParams(), 'view' => $this->view)));
                        
                        
                        if ($id = \Sl\Module\Auth\Service\Adapter::authenticate($this->getRequest()->getParam('login', false), $this->getRequest()->getParam('password', ''))) {
                            \Sl_Event_Manager::trigger(new Auth\Event('afterAuthenticate', array('request' => $this->getRequest()->getParams(), 'view' => $this->view)));
                            

                            $handling_relations = array('userroles'); /* \Sl_Modulerelation_Manager::findHandlingRelations(\Sl_Model_Factory::object('user', $this->_getModule())); */
                            $custom_configs_relations = \Sl_Modulerelation_Manager::findCustomConfigsRelations(\Sl_Model_Factory::object('user', $this->_getModule()));
                            $user = \Sl_Model_Factory::mapper('user', $this->_getModule())->findExtended($id->id, $handling_relations + $custom_configs_relations);
                            \Zend_Auth::getInstance()->getStorage()->write($user);
                            $this->_redirect('/home');
                        } else {
                           // throw new \Exception('Can’t login with such login|password');
                            \Sl_Event_Manager::trigger(new Auth\Event('errorAuthenticate', array('request' => $this->getRequest()->getParams(),'view' => $this->view)));
                            
                            $this->view->errors = array_merge($this->view->errors,array($this->view->translate('Ошибка') => $this->view->translate('Неверный логин или пароль.')));
                        }   
                    } catch (\Sl_Exception_Acl $e) {
                       // echo 'acl: '.$e->getMessage();die;
                    } catch (\Exception $e) {
                        //echo "stand: ".$e->getMessage();die;
                        $this->view->errors = array_merge($this->view->errors,array('auth' => $e->getMessage()));
                        $form->populate($this->getRequest()->getParams());
                    }
                    
                } else { 
                    $form->populate($this->getRequest()->getParams());
                    $this->view->errors = array_merge($this->view->errors,$form->getMessages());
                }
            }
        }
    }
    
    /**
     * Восстановление пароля пользователя
     */
    public function ajaxpassrecoveryAction() {
        $this->view->result = true;
        try {
            $user = \Sl_Model_Factory::mapper('user', $this->_getModule())->find($this->getRequest()->getParam('id', 0));
            if(!$user) {
                throw new \Exception('Can\'t determine user. '.__METHOD__);
            }
            $this->_passwordRecovery($user);
        } catch (\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
            $this->view->code = $e->getCode();
        }
    }
    
    protected function _passwordRecovery(\Sl\Module\Auth\Model\User $user) {
        $email = $user->getEmail();
        
        $newPassword = substr((md5(mktime() . 'Illya is a great hero!')), 0, rand(5, 8));
        for ($i = 0; $i < 3; $i++) {
            $key = rand(0, strlen($newPassword));
            $newPassword[$key] = strtoupper($newPassword[$key]);
        }
        $newPassword = trim($newPassword);
        
        if ($user->getId() && $email) {
            $mail = new \Zend_Mail('UTF-8');
            $mail->addTo($email);

            $tpl = $this->view->partial('partials/email.phtml', array(
                'name' => $user->getName(),
                'login' => $user->getLogin(),
                'server' => \Sl_Service_Settings::value('BASE_URL', $this->getRequest()->getServer('SERVER_NAME', '')),
                'password' => $newPassword,
            ));

            $mail->setSubject($this->view->translate('Восстановление пароля'));
            $mail->setBodyHtml($tpl);
            $mail->send();
            
            $newPassword = \Sl\Service\Helper::hash($newPassword);
            
            \Sl_Model_Factory::mapper($user)->newPasswordUpdate($user, $newPassword);
        }
    }
}