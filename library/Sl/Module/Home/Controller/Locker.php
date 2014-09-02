<?php
namespace Sl\Module\Home\Controller;

class Locker extends \Sl_Controller_Action {

    const EC_SYSTEM_REDIRECT = 1000;
    const EC_ALREADY_EDITING = 1001;
    
    public function ajaxcheckresourceAction() {
        $this->view->result = true;
        try {
            if($resource = $this->getRequest()->getParam('resource', false)) {
                list($model_class, $model_id) = explode(':', $resource);
                $Obj = \Sl_Model_Factory::object($model_class);
                $Obj->setId($model_id);

                if (!\Sl_Model_Factory::mapper('Locker', $this->_getModule())->checkModel($Obj)) {
                    throw new \Exception($this->view->translate('Эту страницу редактирует другой пользователь!'), self::EC_ALREADY_EDITING);
                }
            } else {
                throw new \Sl_Exception_Model('Illegal resource', self::EC_SYSTEM_REDIRECT);
            }
        } catch(\Exception $e) {
            $this->view->result = false;
            $this->view->code = $e->getCode();
            $this->view->description = $e->getMessage();
        }
    }

    public function ajaxunlockresourceAction() {
        $this->view->result = true;
        try {
            if(!($resource = $this->getRequest()->getParam('resource', false))) {
                throw new \Exception($this->view->translate('Illegal resource'));
            }
            list($model_class, $model_id) = explode(':', $resource);
            $model = \Sl_Model_Factory::object($model_class);
            $model->setId($model_id);
            if (\Sl_Model_Factory::mapper('locker', 'home')->checkModel($model)) {
                \Sl_Model_Factory::mapper('locker', 'home')->unlockModel($model);
            }
        } catch(\Exception $e) {
            $this->view->result = false;
            $this->view->description = $e->getMessage();
        }
    }

}
