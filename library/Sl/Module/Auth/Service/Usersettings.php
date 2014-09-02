<?php
namespace Sl\Module\Auth\Service;

use Sl\Module\Auth\Model\Setting as AuthSetting;

class Usersettings {
    
    const DATA_PATH_SEPARATOR = '/';
    const EC_NO_USER = 2001;
    
    public static function read(\Sl_Model_Abstract $model, $path, \Sl\Module\Auth\Model\User $user = null) {
        $path = self::_preparePath($path, $model);
        
        $method_part = array_shift($path);
        $method = \Sl_Model_Abstract::buildMethodName($method_part, 'get');
        $settings = self::getSettings($user);
        
        if(!method_exists($settings, $method)) {
            throw new \Exception('Wrong main part "'.$method_part.'". '.__METHOD__);
        }
        $data = $settings->$method(true);
        if(!$data) { // Секция еще не наполнялась
            $data = array();
        }
        foreach($path as $part) {
            if(!isset($data[$part])) {
                // @TODO Подумать на этим
                $data[$part] = array();
                //throw new \Exception('Wrong path. No data. '.__METHOD__);
            }
            $data = $data[$part];
        }
        if(is_array($data)) {
            return new \Sl\Config($data, true);
        } else {
            return (string) $data;
        }
    }
    
    public static function write(\Sl_Model_Abstract $model, $path, $data, \Sl\Module\Auth\Model\User $user = null) {
        $old_path = $path; // Чтобы знать что спрашивали
        $path = self::_preparePath($path, $model);
        
        $method_part = array_shift($path);
        $method = \Sl_Model_Abstract::buildMethodName($method_part, 'get');
        $set_method = \Sl_Model_Abstract::buildMethodName($method_part, 'set');
        
        $settings = self::getSettings($user);
        
        if(!is_array($data)) {
            if($data instanceof \Zend_Config) {
                $data = $data->toArray();
            } elseif(is_string($data)) {
                // Nothing to do
            } else {
                print_r(array_shift(debug_backtrace()));die;
                throw new \Exception('Wrong data param ('.gettype($data).'). '.__METHOD__);
            }
        }
        
        if(!method_exists($settings, $method) || !method_exists($settings, $set_method)) {
            throw new \Exception('Wrong main part "'.$method_part.'". '.__METHOD__);
        }
        // Читаем старые данные как массив
        $old_mode = AuthSetting::setDecodeType(AuthSetting::DECODE_CONFIG);
        $cur_data = $settings->$method(true);
        AuthSetting::setDecodeType($old_mode);
        
        // Приводим переданные данные в вид, пригодный для merge
        $path = array_reverse($path);
        foreach($path as $part) {
            $data = array($part => $data);
        }
        $old_path = func_get_arg(1);
        $cur_data->merge(new \Sl\Config($data, true));
        $settings->$set_method($cur_data);
        \Sl_Model_Factory::mapper($settings)->save($settings);
        return self::read($model, $old_path, self::_getUser($user));
    }
    
    public static function clean(\Sl_Model_Abstract $model, $path, \Sl\Module\Auth\Model\User $user = null) {
        $path = self::_preparePath($path, $model);
        
        $method_part = array_shift($path);
        $method = \Sl_Model_Abstract::buildMethodName($method_part, 'get');
        $set_method = \Sl_Model_Abstract::buildMethodName($method_part, 'set');
        
        $settings = self::getSettings($user);
        
        if(!method_exists($settings, $method) || !method_exists($settings, $set_method)) {
            throw new \Exception('Wrong main part "'.$method_part.'". '.__METHOD__);
        }
        // Читаем старые данные как массив
        $old_mode = AuthSetting::setDecodeType(AuthSetting::DECODE_ARRAY);
        $cur_data = $settings->$method(true);
        AuthSetting::setDecodeType($old_mode);
        
        // Проверка корректности пути
        $path_copy = $path;
        $cur_data_copy = $cur_data;
        while(count($path_copy)) {
            $part = array_shift($path_copy);
            if(!isset($cur_data_copy[$part])) {
                throw new \Exception('Wrong path. '.__METHOD__);
            }
            $cur_data_copy = $cur_data_copy[$part];
        }
        // Дерево в список
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($cur_data));
        $plain = array();
        foreach($iterator as $value) {
            $key = array();
            for($i = 0; $i <= $iterator->getDepth(); $i++) {
                $key[] = $iterator->getSubIterator($i)->key();
            }
            $plain[implode(':', $key)] = $value;
        }
        // Фильтр ненужного
        $path_str = implode(':', $path);
        foreach($plain as $k=>$v) {
            if(preg_match('/^'.$path_str.'.*$/', $k)) {
                unset($plain[$k]);
            }
        }
        // Список в дерево
        $rec = new \Sl\Config(array(), true);
        foreach($plain as $k=>$v) {
            $k_data = array_reverse(explode(':', $k));
            foreach($k_data as $i=>$iv) {
                $v = new \Sl\Config(array($iv => $v), true);
            }
            $rec->merge($v);
        }

        $settings->$set_method($rec);
        \Sl_Model_Factory::mapper($settings)->save($settings);
    }
    
    public static function getSettings(\Sl\Module\Auth\Model\User $user = null) {
        try {
            return self::_getUser($user)->fetchOneRelated('usersetting');
        } catch(\Exception $e) {
            if($e->getCode() == self::EC_NO_USER) {
                return new \Zend_Config(array(), true);
            } else {
                throw $e;
            }
        }
    }
    
    protected static function _getUser(\Sl\Module\Auth\Model\User $user = null) {
        if(is_null($user)) {
            $user = \Zend_Auth::getInstance()->getIdentity();
        }
        if(!$user || !($user instanceof \Sl_Model_Abstract)) {
            throw new \Exception('No user data. ', self::EC_NO_USER);
        }
        // @TODO Перенести в загрузку связи куда-то, чтобы все работало автоматом
        if(!$user->issetRelated('usersetting')) {
            $user = \Sl_Model_Factory::mapper($user)->findRelation($user, 'usersetting');
        }
        return $user;
    }
    
    protected static function _preparePath($path, \Sl_Model_Abstract $model) {
        if(!is_array($path)) {
            $path = explode(self::DATA_PATH_SEPARATOR, $path);
        }
        $main_part = array_shift($path);
        array_unshift($path, $model->findModelName());
        array_unshift($path, $model->findModuleName());
        array_unshift($path, $main_part);
        return $path;
    }
}
