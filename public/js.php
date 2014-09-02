<?php
/**
 * Перенаправление ссылок стилей в соответствующую папку
 * 
 * @TODO Перенастроить сервер для обработки этих запросов без участия PHP
 */
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));

// Формируем нужный путь
$name = '/Module/'.ucfirst($_REQUEST['module']).'/static/js/'.$_REQUEST['controller'].'/'.$_REQUEST['action'].'.js';
header('Content-type: text/javascript; charset=utf-8');

if(file_exists(LIBRARY_PATH.'/Sl'.$name)) { // Ищем в модулях библиотеке
    echo file_get_contents(LIBRARY_PATH.'/Sl'.$name);
} elseif(file_exists(APPLICATION_PATH.$name)) { // Ищем в модулях приложения
    echo file_get_contents(APPLICATION_PATH.$name);
} else {
    header("HTTP/1.0 404 Not Found");
}
die;