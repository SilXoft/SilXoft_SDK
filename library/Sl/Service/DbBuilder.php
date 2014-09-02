<?php
namespace Sl\Service;
use Sl\Exception\Service as Exception;

/**
 * Класс выполняющий административные задачи с БД
 */
class DbBuilder {
    
    /**
     *
     * @var \Zend_Db_Adapter_Abstract
     */
    protected static $_adapter;
    
    const TYPE_INDEXED = 1;
    
    /**
     * Добавляет (если нет) индексы таблиц связей
     * 
     * @return \Sl\Service\Message\DbBuilder
     */
    public static function rebuidIndexes() {
        // Достаем все связи
        $message = new \Sl\Service\Message\DbBuilder();
        $relations_data = \Sl_Modulerelation_Manager::getRelations();
        $dbs = array();
        $errors = array();
        $result = array();
        // Вытаскиваем таблицы связей из "связей"
        foreach($relations_data as $relations) {
            foreach($relations as $name=>$modulerelation) {
                try {
                    $dbs[$name] = $modulerelation->getIntersectionDbTable();
                } catch(Exception $e) {
                    $message->addError($e->getMessage());
                }
            }
        }
        
        // Идем по базам
        foreach($dbs as $name=>$db) {
            // Достаем все индексы
            /*@var $db \Zend_Db_Table_Abstract*/
            $SQL = "SHOW INDEX FROM ".$db->info('name');
            $indexes = self::getAdapter()->fetchAll($SQL);
            $columns = $db->info(\Zend_Db_Table::METADATA);
            
            // Проверяем есть ли нужные
            $to_index = array();
            foreach($columns as $col_name=>$info) {
                if(preg_match('/^(.+)_id$/', $col_name)) {
                    if($info['DATA_TYPE'] == 'int') {
                        $to_index[$col_name] = 'mr_'.$db->info('name').'_index_'.$col_name;
                    }
                }
            }
            
            foreach($to_index as $key=>$column_name) {
                foreach($indexes as $index) {
                    if($index->Column_name == $key) {
                        unset($to_index[$key]);
                    }
                }
            }
            // Если нет, создаем
            foreach($to_index as $sh_name=>$column_name) {
                $SQL = 'CREATE INDEX '.$column_name.' ON '.$db->info('name').' ('.$sh_name.')';
                try {
                    self::getAdapter()->query($SQL);
                    $message->addItem('Index "'.$column_name.'" successully created.');
                } catch(Exception $e) {
                    $message->addError($e->getMessage());
                }
            }
        }
        return $message;
    }
    
    /**
     * 
     * @return \Zend_Db_Adapter_Abstract
     */
    public static function getAdapter() {
        if(!isset(self::$_adapter)) {
            self::setDefaultAdapter(\Zend_Db_Table::getDefaultAdapter());
        }
        return self::$_adapter;
    }
    
    public static function setDefaultAdapter(\Zend_Db_Adapter_Abstract $adapter) {
        self::$_adapter = $adapter;
    }
    
    public static function getDefaultAdapter() {
        return \Zend_Db_Table::getDefaultAdapter();
    }
}