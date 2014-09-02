<?php

class Application_Service_Acl extends Sl_Service_Acl {

    const ADMIN = 1;
    const USER = 2;
    const GUEST = 3;

    protected static $_acl;

    public static function getRole() {
        if(Zend_Auth::getInstance()->hasIdentity()) {
            switch(Zend_Auth::getInstance()->getIdentity()->role_id) {
                case self::ADMIN:
                    return 'admin';
                case self::USER:
                    return 'user';
                case self::GUEST:
                    return 'guest';
                default:
                    return 'guest';
            }
        } else {
            return 'guest';
        }
    }

    public static function setAcl(Zend_Acl $acl) {
        self::$_acl = $acl;
    }

    /**
     *
     * @return Zend_Acl
     */
    public static function getAcl() {
        return self::$_acl;
    }
}

?>
