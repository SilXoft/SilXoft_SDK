<?php

class Application_Service_Common {

    protected static $_salt = 'dblashdgkjhagsdkhgadfkghsdgkfjs';

    const EXCERPT_LENGTH = 40;
    const EXCERPT_WORDS = 10;

    public static function hash($string) {
        return md5($string.self::$_salt);
    }

    /**
     * Создает обратимый хэш
     * @param string $string сторка для хэширования
     * @return string хэш, который можно восстановить
     */
    public static function encrypt($string) {
        $key = self::$_salt;
        for($i = 0; $i < strlen($string); $i++)
            for($j = 0; $j < strlen($key); $j++)
                $string[$i] = $string[$i] ^ $key[$j];
        return self::_str2hex($string);
    }

    /**
     * Восстанавливает значение по хэшу
     * @param string $string хэш
     * @return string декодированная строка
     */
    public static function decrypt($string) {
        $key = self::$_salt;
        $string = self::_hex2str($string);
        for($i = 0; $i < strlen($string); $i++)
            for($j = 0; $j < strlen($key); $j++)
                $string[$i] = $key[$j] ^ $string[$i];
        return $string;
    }

    /**
     * For internal usage only
     * @param string $str
     * @return string
     */
    private static function _str2hex($str) {
        $chrs = unpack('C*', $str);
        return vsprintf(str_repeat('%02x', count($chrs)), $chrs);
    }

    /**
     * For internal usage only
     * @param string $str
     * @return string
     */
    private static function _hex2str($str) {
        if(strlen($str) % 2 > 0) return '';
        if(!preg_match('/^[a-f0-9]+$/', $str)) return '';
        preg_match_all('/.{2}/', $str, $chrs);
        $chrs = array_map('hexdec', $chrs[0]);
        array_unshift($chrs, 'C*');
        return call_user_func_array('pack', $chrs);
    }

    public static function excerpt($text, $words = null, $length = null) {
        $words = $words?$words:self::EXCERPT_WORDS;
        $length = $length?$length:self::EXCERPT_LENGTH;
        if(mb_strlen($text, 'UTF-8') > $length) {
            $text = mb_substr($text, 0, $length);
        }
        return $text;
    }

    public static function getTransports() {
        return array(
            'Aвтотранспорт',
            'Залiзничний',
            'Водний'
        );
    }

    public static function getTerritories() {
        return array(
            'Україна',
           ' Україна, Білорусія',
            'Україна, Росія',
            'Україна, Молдова'
            );
    }

    public static function getCurrencies() {
        return array(
            'Гривня',
            'Долар США',
            'Евро'
        );
    }
    public static function getTypeContracts() {
        return array(
            '18',
            '19',
            '21'
        );
    }
    public static function getTypeContract() {
        return array(
            '18 отв с демер',
            '19 отв конт',
            '21 контейнера'
        );
    }

    public static function getPeriod() {
        return array(
            '1 місяць',
            '2 місяці',
            '3 місяці'
        );
    }
}

?>
