<?php

class Sl_Service_Filter {
    
    /**
     * Фильтрует массив данных для передачи в модель
     * @param Sl_Model_Abstract $object
     * @param array $data
     * @return array
     */
    public static function filter(Sl_Model_Abstract $object, array $data) {
        $res = array();
        $keys = array_intersect_key($object->toArray(), $data);
        foreach($data as $k=>$v) {
            if(!array_key_exists($k, $keys)) {
                unset($data[$k]);
            }
        }
        return $data;
    }
}

?>
