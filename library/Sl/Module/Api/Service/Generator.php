<?php
namespace Sl\Module\Api\Service;

class Generator {
    
    public static function client() {
        return \Sl_Model_Factory::object('client', 'api')
                    ->setName(self::generate(15))
                    ->setSecret(self::generate(30));
    }
    
    public static function authcode() {
        return \Sl_Model_Factory::object('authcode', 'api')
                    ->setName(self::generate(10))
                    ->setExpires(new \DateTime());
    }
    
    public static function accesstoken() {
        return \Sl_Model_Factory::object('accesstoken', 'api')
                    ->setName(self::generate('40'))
                    ->setExpires(new \DateTime());
    }
    
    public static function generate($length) {
        $code = '';
        $length = abs(intval($length));
        if(!$length) {
            $length = 1;
        }
        while(strlen($code) < $length) {
            $charcode = rand(30, 150);
            $upper = (bool) ($charcode%3 == 1);
            if(($charcode >= 48 && $charcode <= 57) || ($charcode >= 65 && $charcode <= 90) || ($charcode >= 97 && $charcode <= 122)) {
                $code .= $upper?strtoupper(chr($charcode)):chr($charcode);
            }
        }
        return $code;
    }
}