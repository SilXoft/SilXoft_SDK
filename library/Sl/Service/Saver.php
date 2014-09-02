<?php
namespace Sl\Service;

use Sl_Event_Manager as EManager;
use Sl\Event;

class Saver {
    
    protected static $_errors = array();
    
    public static function create(\Sl_Model_Abstract $model, array $params = array(), $events = true) {
        return self::_save($model, $params, false, $events);
    }
    
    public static function update(\Sl_Model_Abstract $model, array $params = array(), $events = true) {
        return self::_save($model, $params, true, $events);
    }
    
    protected static function _save(\Sl_Model_Abstract $model, $params, $check_existence = false, $events = true) {
        $shared = array();
        if(isset($params['shared'])) {
            $shared = $params['shared'];
            unset($params['shared']);
        }
        $form = null;
        try {
            // Чистим старые ошибки
            self::cleanErrors();
            
            // Инициализация данных
            $eInit = new Event\Saver('init', array(
                'model' => $model,
                'data' => $params,
                'form' => null,
                'shared' => $shared,
                'check_existence' => $check_existence,
            ));
            try {
                EManager::trigger($eInit);
            } catch(\Exception $e) {
                self::addError($e->getMessage(), $e->getTraceAsString());
            }
            
            if(!($eInit->getModel() instanceof $model)) {
                throw new \Exception('You can\'t change model in event');
            }
            
            $model = $eInit->getModel();
            $form = $eInit->getForm();
            $params = $eInit->getData();
            $shared = $eInit->getShared();
            
            // Если никто не создал форму - строим сами
            if(!$form || !($form instanceof \Sl\Form\Form)) {
                $form = \Sl_Form_Factory::build($model);
            }
            
            // Может кто-то захочет сделать что-то перед валидацией
            $eBeforeValidate = new Event\Saver('beforeValidate', array(
                'model' => $model,
                'data' => $params,
                'shared' => $shared,
                'form' => $form,
                'check_existence' => $check_existence,
            ));
            try {
                EManager::trigger($eBeforeValidate);
            } catch(\Exception $e) {
                self::addError($e->getMessage(), $e->getTraceAsString());
            }
            
            $form = $eBeforeValidate->getForm();
            $params = $eBeforeValidate->getData();
            $shared = $eBeforeValidate->getShared();
            // Валидация
            if($form->isValid($params)) {
                $model->setOptions($params);
                
                // Непосредственно перед сохранением
                // Почти тоже, что и Event в маппере, но тут есть shared-данные + параметры,
                // на базе которых строится объект
                $eBeforeSave = new Event\Saver('beforeSave', array(
                    'model' => $model,
                    'data' => $params,
                    'shared' => $shared,
                    'check_existence' => $check_existence,
                ));
                try {
                    EManager::trigger($eBeforeSave);
                } catch(\Exception $e) {
                    self::addError($e->getMessage(), $e->getTraceAsString());
                }
                
                $shared = $eBeforeSave->getShared();
                $params = $eBeforeSave->getData();
                if($check_existence && !$model->getId()) {
                    throw new \Exception('Can\'t update object without id');
                }
                
                try {
                    $model = \Sl_Model_Factory::mapper($model)->save($model, true, $events);
                } catch(\Exception $e) {
                    self::addError($e->getMessage(), $e->getTraceAsString());
                }
                $eAfterSave = new Event\Saver('afterSave', array(
                    'model' => $model,
                    'data' => $params,
                    'shared' => $eBeforeSave->getShared(),
                    'check_existence' => $check_existence,
                ));
                try {
                    EManager::trigger($eAfterSave);
                } catch(\Exception $e) {
                    self::addError($e->getMessage(), $e->getTraceAsString());
                }
                
                $shared = $eAfterSave->getShared();
                $params = $eAfterSave->getData();
            } else {
                $eError = new Event\Saver('error', array(
                    'model' => $model,
                    'data' => $params,
                    'shared' => $eBeforeValidate->getShared(),
                    'check_existence' => $check_existence,
                ));
                try {
                    EManager::trigger($eError);
                } catch(\Exception $e) {
                    self::addError($e->getMessage(), $e->getTraceAsString());
                }
                
                $shared = $eError->getShared();
                $params = $eError->getData();
            }
            
            $eFinishSave = new Event\Saver('finish', array(
                'model' => $model,
                'data' => $params,
                'shared' => $eBeforeSave->getShared(),
                'check_existence' => $check_existence,
            ));
            try {
                EManager::trigger($eFinishSave);
            } catch(\Exception $e) {
                self::addError($e->getMessage(), $e->getTraceAsString());
            }
            return $model;
        } catch (\Exception $e) {
            self::addError($e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }
    
    public static function cleanErrors() {
        self::$_errors = array();
    }
    
    public static function getErrors() {
        return self::$_errors;
    }
    
    public static function getLastError() {
        $last = end(self::getErrors());
        reset(self::$_errors);
        return $last;
    }
    
    public static function addError($message, $trace, $extras = '') {
        self::$_errors[] = array(
            'message' => $message,
            'trace' => $trace,
            'extras' => $extras,
        );
    }
    
}