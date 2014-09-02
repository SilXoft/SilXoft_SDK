<?php

class Sl_Event_Manager {
    
    protected static $_instance;
    protected $_listeners_raw = array();
    protected $_listeners = array();
    protected static $_cached = false;
    
    protected function __construct() {
        
    }
    
    /**
     * 
     * @return \Sl_Event_Manager
     */
    public static function getInstance($rebuild_cache = false) {
        if(!self::$_instance) {
            $cache = \Zend_Registry::get('cache');
            /*$var $cache \Zend_Cache*/
           
            $cache_id = APPLICATION_NAME.'_module_manager';
            if(true || $rebuild_cache || !$cache->getBackend()->test($cache_id)) {
                self::_setCached(false);
                self::$_instance = new self();
            } else {
                self::_setCached();
                self::$_instance = unserialize($cache->getBackend()->load($cache_id));
            }
        }
        return self::$_instance;
    }
    
    protected static function _setCached($cached = true) {
        self::$_cached = $cached;
    }
    
    public static function getCached() {
        // Инициализация
        \Sl_Event_Manager::getInstance();
        return self::$_cached;
    }
    
    public function register(\Sl_Listener_Abstract $listener, $order = null) {
        $key = $this->_key($listener);
        
        $this->_listeners_raw[$key] = $listener;
        
        $events = $listener->getSupportedEvents();
        foreach($events as $event) {
            if(!isset($this->_listeners[$event])) {
                $this->_listeners[$event] = array();
            }
            if(is_null($order)) {
                $this->_listeners[$event][] = $key;
            } else {
                $order = intval($order);
                if(array_key_exists($order, $this->_listeners[$event])) {
                    array_splice($this->_listeners[$event], $order, 0, $key);
                } else {
                    $this->_listeners[$event][$order] = $key;
                }
            }
            ksort($this->_listeners[$event]);
        }
        if(!self::getCached()) {
            $cache = \Zend_Registry::get('cache');
            /*$var $cache \Zend_Cache*/
            $cache_id = APPLICATION_NAME.'_module_manager';
            $cache->getBackend()->save(serialize(self::getInstance()), $cache_id, array('event_manager'));
        }
    }
    
    protected function _key(\Sl_Listener_Abstract $listener) {
        return md5(get_class($listener));
    }
    
    public function triggerEvent(Sl_Event_Abstract $event) {
        foreach($this->getListeners($event->getSuperType()) as $listener) {
            $methodname = 'on'.ucfirst($event->getType());
            if(method_exists($listener, $methodname)) {
                try {
                    $listener->$methodname($event);
                } catch (\Exception $e) {
                    // Что-то пошло не так. Нужно бы обработать
                }
            }
        }
    }
    
    public static function trigger(Sl_Event_Abstract $event) {
        return self::getInstance()->triggerEvent($event);
    }
    
    public function getListeners($event_type = null) {
        $data = array();
        if(is_null($event_type)) {
            $data = $this->_listeners_raw;
        } elseif(is_string($event_type)) {
            if(isset($this->_listeners[$event_type])) {
                foreach($this->_listeners[$event_type] as $key) {
                    $data[$key] = $this->_listeners_raw[$key];
                }
            }
        } else {
            throw new \Exception('Wrong event type. '.__METHOD__.', '.__LINE__);
        }
        return $data;
    }
}

?>
