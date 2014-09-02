<?php
namespace Sl\Service;

class Loger {
		
	const LOG_TABLE_ENDING = '_log';
	const LOG_MODEL_CLASS = "Sl\Module\Home\Model\Log";
	protected static $user_id = null;
	protected static $create_log_query = 'CREATE TABLE IF NOT EXISTS [TABLE_NAME]  (
									`id` int(11) NOT NULL AUTO_INCREMENT,
									`object_id` INT NOT NULL ,
									`active` tinyint(4) NOT NULL DEFAULT \'1\',
									`create` timestamp NULL DEFAULT NULL,
									`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
									`field_name` VARCHAR( 100 ) NOT NULL ,
									`old_value` TEXT  NULL ,
									`new_value` TEXT  NULL ,
									`action` VARCHAR( 20 ) NOT NULL ,
									`user_id` INT NOT NULL,
									INDEX ( `object_id` ),
									PRIMARY KEY (`id`)
									) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
	
	
	public static function createLogTable(\Sl_Model_Abstract $object){
		if (get_class($object) != self::LOG_MODEL_CLASS && $object->isLoged()){
			
			$db_table_log_name=self::getLogTableName($object);
			$db_table=\Sl_Model_Factory::dbTable($object);
			
			$db_adapter=$db_table->getAdapter();
			$db_adapter->query(str_replace('[TABLE_NAME]',$db_table_log_name,self::$create_log_query));
		}
	}
		
    
    public static function getCurrentUserId(){
        if (self::$user_id === null){
            $current_user = \Zend_Auth::getInstance()->getIdentity();
            if (\Sl_Service_Settings::value(SYSTEM_USER)) self::setCurrentUserId(\Sl_Service_Settings::value(SYSTEM_USER));
            elseif (is_object($current_user)) self::setCurrentUserId($current_user->getId());
            else self::setCurrentUserId(0);
             
        }
        
        return self::$user_id;
    }
     
    public static function setCurrentUserId( $user_id  = null ){
        self::$user_id = $user_id;
    }    
	public static function Log(\Sl_Model_Abstract $object,$field_name, $old_value, $new_value, $action='', $no_userId = false){
		if ($object->isLoged() && ($new_value != $old_value)){
			
			$log_object = \Sl_Model_Factory::object(self::LOG_MODEL_CLASS);
			$log_object -> setObjectId($object->getId());
			$log_object -> setFieldName($field_name);
			$log_object -> setOldValue($old_value);
			$log_object -> setNewValue($new_value);
			$log_object -> setAction($action);
			$log_object -> setUserId(self::getCurrentUserId());
			\Sl_Model_Factory::mapper($log_object)->save($object, $log_object, $no_userId);
		} 
		
		$relations = \Sl_Modulerelation_Manager::getRelations($object);
        
		
		
        foreach ($relations as $relation) {
        	
            if ($relation->getType() == \Sl_Modulerelation_Manager::RELATION_MODEL_ITEM){
            	$related = $object->fetchRelated($relation->getName());
				
				//Якщо не заповнений зв'язок
				if (!count($related)){
					$object=\Sl_Model_Factory::mapper($object)->findRelation($object,$relation);
					$related = $object->fetchRelated($relation->getName());
					$object->assignRelated($relation->getName(),array());
				}
				
	            if (count($related) == 1){
	            	
	            	$parent_object = array_shift($related);
				
					if ($parent_object instanceof \Sl_Model_Abstract) {
						
	                	self::Log($parent_object, implode('-',array($relation->getName(),$object->getId(),$field_name)), $old_value, $new_value,$action);
					}
	            }
			}
        }
		
		
	}  
	
	public static function getLogTableName(\Sl_Model_Abstract $object){
		$db_table=\Sl_Model_Factory::dbTable($object);
		$db_table_name=$db_table->info('name');
		$db_table_log_name=$db_table_name.self::LOG_TABLE_ENDING;
		return $db_table_log_name;
	}  
	
    
    public static function getLog(\Sl_Model_Abstract $object, $limit=1000, $order='id DESC', $field=''){
        if ($object->isLoged()){
            
            $log_object = \Sl_Model_Factory::object(self::LOG_MODEL_CLASS);
            
            $field=trim($field);
            $where = strlen($field)?' field_name like "'.$field.'" ':'';
            
            // TODO: реалізувати fetchAll   \Sl_Model_Factory::mapper($log_object)->fetchAll($where, $log_object);
        }
    }
    
    public static function getObjectFieldsLog(\Sl_Model_Abstract $model, $position = 1, array $fields = array('user_id', 'timestamp')) {
        if($model->isLoged()) {
            $log = \Sl_Model_Factory::object(self::LOG_MODEL_CLASS);
            return \Sl_Model_Factory::mapper($log)->getObjectFieldLog($model, $fields, $position);
        } else {
            return null;
        }
        
    }
}
