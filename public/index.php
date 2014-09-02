<?php
header('Access-Control-Allow-Origin: *');
error_reporting(0);
error_reporting(E_ERROR);
//error_reporting(E_ALL);

/*if($_SERVER['REMOTE_ADDR'] != '93.75.191.45') {
    echo 'Sorry. Site is under construction. Please try again later.';
    die;
}*/

set_error_handler(function($type, $message, $file, $line){
    throw new \Exception($message, $type);
}, E_RECOVERABLE_ERROR);

/*register_shutdown_function(function(){
    $data = error_get_last();
    if($data['type'] & E_ERROR) {
        $cache = \Zend_Registry::get('cache');
        if($cache) {
            $cache->clean();
        }
        while(ob_get_level()) {
            ob_end_clean();
        }
        header('Location: /home');
        die;
    }
});*/

// Define path to application directory
define('APPLICATION_NAME', 'itftc');
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));

defined('USE_AJAX')
    || define('USE_AJAX', (bool) (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

// Добавляем свой автолоадер
Zend_Loader_Autoloader::getInstance()->pushAutoloader(new Sl_Autoloader());
// Инициализация фронт-контроллера
//Zend_Debug::dump(Zend_Controller_Front::getInstance()->getControllerDirectory());

Zend_Controller_Front::getInstance()
        ->setDispatcher(new Sl_Dispatcher())
        ->setControllerDirectory(Sl_Module_Manager::getControllerDirectory())
        ->setDefaultModule('home')
        ->setDefaultControllerName('main')
        ->setDefaultAction('list');

$application->bootstrap('cache');
Zend_Controller_Front::getInstance()->registerPlugin(new Sl_Plugin_Eventer());

// Для дебага. Вообще грузится должно из конфига
//Sl_Module_Manager::reloadModuleInformation();

$modules = Sl_Module_Manager::getInstance()->getModules();

// 50, 51 added Illya
Sl_Event_Manager::trigger(new Sl_Event_Bootstrap('beforeViewInit'));
$view = new \Sl_View(array('scriptPath' => LIBRARY_PATH . Sl_Module_Manager::SCRIPT_BASE_PATH));

\Sl\Service\Benchmark::save('before module foreach');

foreach($modules as $module) {
    if(!\Sl_Event_Manager::getCached()) {
        $module->registerListeners(Sl_Event_Manager::getInstance());
    }
    $module->setModuleLists();
    $view->addScriptPath(\Sl_Module_Manager::getViewDirectory($module->getName()));
    $view->addBasePath(\Sl_Module_Manager::getViewDirectory($module->getName()));
}

$application->bootstrap('log');

\Sl\Service\Benchmark::save('after module init');

$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
$viewRenderer->setView($view);
Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

Sl_Event_Manager::trigger(new Sl_Event_Bootstrap('appRun'));
//66 added illya
Sl_Event_Manager::trigger(new Sl_Event_Bootstrap('afterViewInit', array('view' => $view)));

$application->bootstrap('system');
$application->bootstrap('db');
$application->bootstrap('dbsession');
$application->bootstrap('session');

//$application->bootstrap('request');
//$application->bootstrap('router');
$application->bootstrap('translation');
//Sl_Modulerelation_Manager::getInstance();
//Sl_Calculator_Manager::getInstance();

\Sl\Service\Benchmark::save('before calcs and relations');

$counter = 0;
$counter2 = 0;
foreach($modules as $module) {
    $m_time = microtime(true);
    $module->registerModulerelations();
    $counter += (microtime(true)-$m_time);
    $m_time = microtime(true);
    $module->registerCalculators();
    $counter2 += (microtime(true)-$m_time);
    //\Sl\Printer\Manager::addPrinters($module->getPrinters());
}

\Sl\Service\Benchmark::save('rels: '.$counter.'; calcs: '.$counter2);
\Sl\Service\Benchmark::save('before required roles');

\Sl_Module_Manager::getInstance()->registerRequiredRoles();

\Sl\Service\Benchmark::save('before bootstrap acl');

$application->bootstrap('aclService');

$application->bootstrap('request');
$application->bootstrap('router');

\Sl\Service\Benchmark::save('after bootstrap acl');

// Устанавливаем переводчика фабрике форм
Sl_Form_Factory::setTranslator(Zend_Registry::get('Zend_Translate'));

$application->bootstrap('view');
$application->bootstrap('layout');
$application->bootstrap('mail');

\Sl\Service\Benchmark::save('before dispatch');

Zend_Controller_Front::getInstance()->dispatch();
