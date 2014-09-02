<?php
namespace Sl\Service;

class Benchmark {
    
    protected static function _init() {
        \Zend_Registry::set('bm', array('init' => microtime(true)));
    }
    
    public static function save($message) {
        try {
            $bm = \Zend_Registry::get('bm');
        } catch(\Exception $e) {
            self::_init();
            $bm = array();
        }
        $bm[$message] = microtime(true);
        \Zend_Registry::set('bm', $bm);
    }
    
    public static function get() {
        $bm = \Zend_Registry::get('bm');
        
        $min = null;
        $last = null;
        $inc = 0;
        asort($bm);
        foreach($bm as $k=>$v) {
            if(is_null($min)) $min = $v;
            if(is_null($last)) $last = $v;
            $bm[($inc++).': '.$k] = sprintf('%.2f (delta: %.3f)', $v-$min, $last-$v);
            $last = $v;
            unset($bm[$k]);
        }
        return $bm;
    }
}