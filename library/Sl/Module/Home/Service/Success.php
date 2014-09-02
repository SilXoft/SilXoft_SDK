<?php
namespace Sl\Module\Home\Service;

class Success extends Storage {
    
    protected static $_key_name = 'success';
    
    public static function getMessages() {
        return self::_getAll();
    }
    
    public static function addMessage($message, $key = null) {
        self::_add($message, $key);
    }
    
    public static function addMessages(array $errors) {
        self::_addMany($errors);
    }
    
    public static function setMessages(array $errors) {
        self::_setMany($errors);
    }
    
    public static function clearMessages() {
        self::_clear();
    }
}