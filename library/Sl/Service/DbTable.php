<?php
namespace Sl\Service;
use Sl\Exception\Service as Exception;

class DbTable {

    protected static $_db_tables = array();

    public static function get($string) {
        if(!isset(self::$_db_tables[$string])) {
        	
            if(class_exists($string)) {
                $table = new $string();
                
                if(!($table instanceof \Sl\Model\DbTable\DbTable)) {
                    throw new Exception('Wrong class type . ('.$string.') in '.__METHOD__);
                }
                self::$_db_tables[$string] = $table;
            } else {
            	  throw new Exception('Wrong class name. ('.$string.') in '.__METHOD__);
            }
        }
        return self::$_db_tables[$string];
    }
	public static function getAll(){
		return self::$_db_tables;
	}
}

