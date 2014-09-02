<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * Инициализация основных параметров приложения
     */
    protected function _initRequest() {
        Sl_Event_Manager::trigger(new Sl_Event_Bootstrap('beforeRequestInit'));

        // Меняем стандартный префикс "Фабрики"
        Sl_Model_Factory::setPrefix('Application_Model');

        Sl_Event_Manager::trigger(new Sl_Event_Bootstrap('afterRequestInit'));
    }

    /**
     * Инициализация роутера
     */
    public function _initRouter() {
        Sl_Event_Manager::trigger(new Sl_Event_Router('beforeInit'));

        $router = new \Sl_Router();
        Zend_Controller_Front::getInstance()->setRouter($router);

        Sl_Event_Manager::trigger(new Sl_Event_Router('afterInit', array('router' => $router)));
    }

    public function _initDb() {
        $options = $this -> getOptions();
        $db = Zend_Db::factory($options['resources']['db']['adapter'], $options['resources']['db']['params']);

        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Db_Table::getDefaultAdapter() -> setFetchMode(Zend_Db::FETCH_OBJ);
    }


    public function _initDbsession(){
        $options = $this -> getOptions();    
        Zend_Session::setSaveHandler(new Zend_Session_SaveHandler_DbTable($options['resources']['session']['db']));
    }
    
    /**
     * Инициализация переводчика
     * @param Zend_Config $config конфигурация
     */
    protected function _initTranslation(Zend_Config $config = null) {
        Sl_Event_Manager::trigger(new Sl_Event_Bootstrap('beforeTranslationInit'));
        if (!$config) {
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'translation');
        }

        $translate = new Zend_Translate( array(
            'adapter' => 'array',
            'content' => $config -> content,
            'locale' => 'ru',
            'scan' => Zend_Translate::LOCALE_FILENAME
        ));

        if ($config -> logger -> enabled) {
            $log_writer = new Zend_Log_Writer_Stream($config -> logger -> filename);
            $log = new Zend_Log($log_writer);

            $translate -> getAdapter() -> setOptions(array(
                'log' => $log,
                'logUntranslated' => true,
            ));
        }

        $translate -> getAdapter() -> setLocale($config -> locale -> default);
        $translate->getAdapter()->setLocale('ru');
        Zend_Registry::set('Zend_Translate', $translate);
        Sl_Event_Manager::trigger(new Sl_Event_Bootstrap('afterTranslationInit', array('translation' => $translate)));
    }

    /**
     * Инициализация View
     */
    public function _initView() {
        
    }

    /**
     * Инициализация Layout
     */
    public function _initLayout() {
        Sl_Event_Manager::trigger(new Sl_Event_Bootstrap('beforeLayoutInit'));
        $layout = Zend_Layout::startMvc() -> setLayout('main') -> setViewScriptPath(LIBRARY_PATH . Sl_Module_Manager::SCRIPT_BASE_PATH);
        Sl_Event_Manager::trigger(new Sl_Event_Bootstrap('afterLayoutInit', array('layout' => $layout)));
    }
	
    /**
     * Инициализация Acl
     */
    public function _initAclService() {
        Sl_Service_Acl::acl();
    }
	
    public function _initMail() {
        $transport = new \Zend_Mail_Transport_Smtp('mail.cargo80.com', array(
            'auth' => 'login',
            'username' => 'info@cargo80.com',
            'password' => '8c3a3QAi',
            //'ssl' => 'tls',
        ));
        \Zend_Mail::setDefaultTransport($transport);
        \Zend_Mail::setDefaultFrom('info@cargo80.com');
    }
    
    public function _initSystem(\Zend_Config $config = null) {
	umask(0);return;
	// @TODO: Выяснить почему не работает umask
        if(!$config) {
            try {
                $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'system');
            } catch(\Exception $e) {
                $config = null;
            }
        }
        if(!$config) {
            return;
        }
        
        $umask = 0;
        if(isset($config->system) && isset($config->system->umask)) {
		$umask = $config->system->umask;
        }
        umask($umask);
    }
    
    public function _initSession() {
        $session = new Zend_Session_Namespace('Zend_Auth');
        $session->setExpirationSeconds(60*50);
        \Sl_Event_Manager::trigger(new \Sl_Event_Bootstrap('afterSessionInit'));
    }
    
    public function _initCache() {
        $backend_cache = 'File';
        if(class_exists('Memcache') && false) {
            try {
                $memcached = new \Zend_Cache_Backend_Memcached();
                // Проверка существования сервера. Zend иногда поражает .....
                $memcached->getFillingPercentage();
                // Обертка для поддержки, хоть и ограниченной, теглв
                $backend_cache = new \Dklab_Cache_Backend_TagEmuWrapper($memcached);
            } catch(\Exception $e) {
                // Коль не получилось с тэгами, то и не нужно
                $backend_cache = 'File';
            }
        }
        $cache = \Zend_Cache::factory('Core', $backend_cache);
        $cache -> clean();
        if(!$cache) {
            die('Can\'t initialize cache.');
        }
        \Zend_Registry::set('cache', $cache);
        $cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('identity'));
        $options = $this->getOption('cache');
        if(isset($options['always_clean']) && ((bool) $options['always_clean'])) {
            $cache->clean();
        }
        // @TODO Зло в чистом виде. Но пока не придумад как сделать правильно
        if(isset($_GET['force_cache_clean'])) {
            $cache->clean();
        }
    }
    
    public function _initLog() {
        $options = array(
            'timestampFormat' => 'Y-m-d H:i:s',
            array(
                'writerName'   => 'Stream',
                'writerParams' => array(
                    'stream'   => '/tmp/application.log',
                ),
                'formatterName' => 'Simple',
                'formatterParams' => array(
                    'format'   => '%timestamp%: %message% -- %info%'."\r\n",
                ),
                'filterName'   => 'Priority',
                'filterParams' => array(
                    'priority' => Zend_Log::ERR,
                ),
            ),
        );
        $eBefore = new \Sl\Event\Log('beforeInit', array(
            'options' => $options,
        ));
        
        \Sl_Event_Manager::trigger($eBefore);
        
        $log = \Zend_Log::factory($eBefore->getOption('options'));
        
        \Sl_Event_Manager::trigger(new \Sl\Event\Log('afterInit'));
        
        \Zend_Registry::set('Zend_Log', $log);
    }
}
