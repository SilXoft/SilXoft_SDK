<?php
namespace Sl\Service;

class Lists {
	const LISTS_SEARCH_KEY_PREFIX = ':';
	const FINAL_STATUS_VALUE = 100;
	
	static $_lists = array(
		'statuses'=>array(
			0 => 'Новый',
			1 => 'Обрабатывается',
			100 => 'Принят',
			-1 => 'Отменен',
		),
		'openclose'=>array(
			0 => 'Скрыт',
			1 => 'Открыт',
			
		)
	);
	
	static $_lists_statuses = array(
			'final'=>array( 'statuses' => array(self::FINAL_STATUS_VALUE)),
			'canceled'=>array( 'statuses' => array(-1)),
			'processed'=>array('statuses' => array(1))
	);
	
	public static function setList($list_name, array $list){
		$list_name = self::buildListName($list_name);	
		if (!self::checkListName($list_name)){
			self::$_lists[$list_name]=$list;
		}
	}

	public static function setListStatuses(array $lists_statuses =array()){
		foreach (self::$_lists_statuses as $key_name => $set_values){
			if (isset($lists_statuses[$key_name]) && is_array($lists_statuses[$key_name])){
				foreach ($lists_statuses[$key_name] as $list_config_name => $values){
					$list_name=self::buildListName($list_config_name);
					if (is_array(self::getList($list_name)) && count(self::getList($list_name)) && !isset($set_values[$list_name]))self::$_lists_statuses[$key_name][$list_name] = $values;
				}
			}	
		}	
			
	}
	
	public static function getListStatuses($list_name){
		$list_name = self::buildListName($list_name);
		$action_array=array();
		foreach (self::$_lists_statuses as $key_name => $set_values){
			$action_array[$key_name] = isset($set_values[$list_name])?$set_values[$list_name]: array();
			
		}	
		return $action_array;
			
	}
	
	
	public static function checkListStatusValue($list_name, $value, $action){
		$list_statuses = self::getListStatuses($list_name);
		
		return(isset($list_statuses[$action]) && in_array($value,$list_statuses[$action]));
	}
	
	
	public static function getListStatusEditableValue($list_name){
		$list_statuses = self::getListStatuses($list_name);
		
		if (isset($list_statuses['processed'])) return $list_statuses['processed']; 
		
	}
	
	public static function getListStatusFinalValues($list_name){
		$list_statuses = self::getListStatuses($list_name);
		
		if (isset($list_statuses['final'])) return $list_statuses['final']; 
		
	}
	
	
	public static function getListStatusCancelValues($list_name){
		$list_statuses = self::getListStatuses($list_name);
		
		if (isset($list_statuses['canceled'])) return $list_statuses['canceled']; 
		
	}
	
	
	public static function getList($list_name) {
		
		$list_name=self::buildListName($list_name);
		
		return self::translate(self::$_lists[$list_name]);
		
	}
	
		
	public static function buildListName($list_name) {
		return strtolower(str_replace('_','',$list_name));
	}
	
	protected static function checkListName($list_name) {
		return isset(self::$_lists[self::buildListName($list_name)]);
	}
	
	public static function fetchListValue($list_name, $value){
		$list_array = self::getList($list_name);
		return isset($list_array[$value])?$list_array[$value]:'значение не установлено';  
	}
	
	
	protected static function translate($value){
		if (\Zend_Registry::isRegistered('Zend_Translate')) {
			$translator= \Zend_Registry::get('Zend_Translate');
			if (is_array($value)){
				$tr_value = array();
				foreach ($value as $key => $val){
					$tr_value[$key] = $translator->translate($val);
				}
				$value = $tr_value;
			} else {
				//$value = $translator->translate($value);
				return $value;
			}
		}
		return $value;
	}
	
	/**
	 * Вызов несуществующего метода
	 * @param string $name
	 * @param array $arguments
	 * @throws Sl_Exception_Model
	 */
	public function __call($name, $arguments) {
		throw new Sl_Exception_Model('No such method in ' . get_class($this));
	}
	
}
