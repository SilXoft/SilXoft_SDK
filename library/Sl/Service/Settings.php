<?php

class Sl_Service_Settings {

    protected static $_settings;

    const TYPE = 'type';
    const VALUE = 'value';

    protected static $_setting_model_class = '\Sl\Module\Home\Model\Settings';

    private static function settings($name, $request) {
        $name =  strtoupper($name);
        if (!isset(self::$_settings)) {
            self::$_settings = array();
            $object = \Sl_Model_Factory::object(self::$_setting_model_class);
            $result = Sl_Model_Factory::mapper($object)->fetchAll();
            foreach ($result as $key => $value) {
                self::$_settings[strtoupper($value->getName())] = $value;
            }
        }
        if (isset(self::$_settings[$name])) {

            switch ($request) {
                case self::VALUE:
                    return self::$_settings[$name]->getValue();
                case self::TYPE :
                    return self::$_settings[$name]->getType();
                default :
                    throw new Exception('Wrong parameter of "settings" method!');
            }
        } /*else {
            throw new Exception('No such settings in the system!');
        }*/
        return null;
    }

    public static function value($name, $default = null) {
        if(isset(self::$_settings) && !isset(self::$_settings[$name])) {
            return $default;
        }
        return self::settings($name, self::VALUE);
    }

    public static function type($name) {
        return self::settings($name, self::TYPE);
    }

}

?>
