<?php
namespace Sl\Module\Home\Service;

class Storage {
    
    protected static $_storage;
    protected static $_key_name;
    
    public static function setStorage($storage) {
        self::$_storage = $storage;
    }
    
    /**
     * 
     * @return \Zend_Session_Namespace
     */
    public static function getStorage() {
        if(!isset(self::$_storage)) {
            self::$_storage = new \Zend_Session_Namespace(__CLASS__);
        }
        return self::$_storage;
    }
    
    protected static function _getAll() {
        $data = self::getStorage()->{self::_keyName()};
        return $data?$data:array();
    }
    
    protected static function _add($message, $key = null) {
        self::_write($message, $key);
    }
    
    protected static function _addMany(array $data) {
        foreach($data as $key=>$item) {
            self::_add($item, $key);
        }
    }

    protected static function _clear() {
        self::getStorage()->{self::_keyName()} = array();
    }

    protected static function _setMany(array $data) {
        self::_clear();
        self::_addMany($data);
    }
    
    protected static function _write($message, $key = null) {
        $data = self::_getAll();
        if(!is_null($key)) {
            $data[$key] = $message;
        } else {
            $data[] = $message;
        }
        self::getStorage()->{self::_keyName()} = $data;
    }
    
    protected static function _keyName() {
        return static::$_key_name?static::$_key_name:'base';
    }
}