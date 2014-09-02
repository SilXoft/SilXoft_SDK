<?php
namespace Sl\Module\Home\Service;

class Errors extends Storage {
    
    protected static $_key_name = 'errors';
    
    public static function getErrors() {
        return self::_getAll();
    }
    
    public static function addError($message, $key = null) {
        self::_add($message, $key);
    }
    
    public static function addErrors(array $errors) {
        self::_addMany($errors);
    }
    
    public static function setErrors(array $errors) {
        self::_setMany($errors);
    }
    
    public static function clearErrors() {
        self::_clear();
    }
}