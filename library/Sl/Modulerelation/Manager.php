<?php

class Sl_Modulerelation_Manager {

    protected static $_instance;

    /**
     *
     * @var \Sl\Modulerelation\Modulerelation[]
     */
    protected static $_relations = array();
    protected static $_config;
    protected static $_config_data = array();

    const CONFIG_PATH = '../application/configs/modulerelations.php';
    const CONFIG_BASE_KEY = 'config';
    const CONFIG_MODELS_KEY = 'models';
    const RELATION_ONE_TO_ONE = 11;
    const RELATION_ONE_TO_MANY = 12;
    const RELATION_MANY_TO_ONE = 21;
    const RELATION_MANY_TO_MANY = 22;
    const RELATION_ITEM_OWNER = 2;
    const RELATION_MODEL_ITEM = 20;
    const RELATION_FILE_ONE = 3;
    const RELATION_FILE_MANY = 4;
    const RELATION_FIELD_PREFIX = 'modulerelation';
    const RELATION_FIELD_SEPARATOR = '_';
    const SELFRELATION_PREFIX = 'reverse';

    protected function __construct() {
        try {
            $this->_config = self::getConfig()->toArray();
        } catch (\Exception $e) {
            throw new \Exception('Can not open modulerelations config file ' . self::CONFIG_PATH, $e);
        }
    }

    /**
     *
     * @return \Zend_Config
     */
    public static function getConfig() {
        try {
            self::$_config = new \Zend_Config(require self::CONFIG_PATH, true);
            return self::$_config;
        } catch (\Exception $e) {
            die('Can\'t read modulerelations config file');
        }
    }

    public static function setRelationExtend(\Sl_Model_Abstract $model) {

        $extend = $model->Extend();
        $rels = self::$_relations[$extend];
        
   //     print_r($rels);
     //   die;
        foreach ($rels as $modulerelation) {
            if (is_array($modulerelation))
                self::createModulerelationExtend($modulerelation, false, $model);
            if (is_object($modulerelation))
                self::setModulerelation($modulerelation, get_class($model));
        }
        
        if (self::$_relations[get_class($model)]) {
            $rels = self::$_relations[get_class($model)];
            foreach ($rels as $modulerelation) {
                if (is_array($modulerelation))
                    self::createModulerelation($modulerelation);
            }
        }
        
    }

    protected static function createModulerelationExtend(array $modulerelation, $return_models = false, $Obj) {


        $manager = self::getInstance();
        $related_models_keys = \Sl\Service\DbTable::get($modulerelation['db_table'])->findRelatedModelsKeys();

        $options = (isset($modulerelation['options']) && is_array($modulerelation['options'])) ? $modulerelation['options'] : array();

        $new_relation = new \Sl\Modulerelation\Modulerelation($modulerelation['type'], \Sl\Service\DbTable::get($modulerelation['db_table']), $options, isset($modulerelation['custom_configs']) ? (bool) $modulerelation['custom_configs'] : false);


        $model = array_shift($related_models_keys);
        $models = array($model);
        $manager->setModulerelation($new_relation, get_class($Obj));

        //$new_relation = $manager->invertRelation($new_relation);

        $model = array_shift($related_models_keys);
        $models[] = $model;
        $manager->setModulerelation($new_relation, get_class($Obj));

        if ($return_models) {
            return $models;
        }
    }

    /**
     * Вертає назви зв'язків по моделі
     * 
     * @param Sl_Model_Abstract $Obj - об'єкт моделі
     * @return \Sl\Modulerelation\Modulerelation[] масив назв зв'язків
     */
    public static function getRelations(\Sl_Model_Abstract $Obj = null, $rel_name = null) {
        if(is_null($Obj)) { // Вернуть все - ЗЛО. Не нужно так делать
            $rels = array();
            foreach(array_keys(self::$_config_data) as $alias) {
                $model = \Sl\Service\Helper::getModelByAlias($alias);
                $rels[gte_class($model)] = self::getRelations($model);
            }
            return $rels;
        }
        $alias = \Sl\Service\Helper::getModelAlias($Obj);
        if(is_null($rel_name)) {
            $rels = array();
            foreach(array_keys(self::$_config_data[$alias]) as $name) {
                $rels[$name] = self::getRelations($Obj, $name);
            }
            return $rels;
        } else {
            $rel_name = mb_strtolower($rel_name);
           // print_r(array(get_class($Obj), $alias, $rel_name, !isset(self::$_config_data[$alias][$rel_name])));
            if(!isset(self::$_config_data[$alias][$rel_name])) {
                return null;
            }
            if(!isset(self::$_config_data[$alias][$rel_name]['object'])) {
                self::$_config_data[$alias][$rel_name]['object'] = self::createModulerelation(self::$_config_data[$alias][$rel_name]['data']);
            }
            //print_r(self::$_config_data[$alias][$rel_name]);
            return self::$_config_data[$alias][$rel_name]['object'];
        }
    }

    /**
     * Вертає зв'язок між об'єктом і іншою моделлю
     * 
     * @param Sl_Model_Abstract $object - об'єкт або клас моделі
     * @param Sl_Model_Abstract $destination_object - об'єкт або клас моделі
     * @param array $type - встановленого типу
     * @param string $option - зі встановленим параметром
     * @return \Sl\Modulerelation\Modulerelation[] масив назв зв'язків
     */
    public static function getObjectsRelations($object, $destination_object, $type = array(), $option = null) {
        if (!is_array($type))
            $type = array();
        $object_class = $object instanceof \Sl_Model_Abstract ? get_class($object) : trim($object);
        $destination_object_class = $destination_object instanceof \Sl_Model_Abstract ? get_class($destination_object) : trim($destination_object);
        if (strlen($object_class) && strlen($destination_object_class) && isset(self::$_relations[$object_class]) && count(self::$_relations[$object_class])) {
            $relations_arr = null;
            $rels = self::$_relations[$object_class];
            foreach ($rels as $modulerelation) {
                if (is_array($modulerelation))
                    self::createModulerelation($modulerelation);
            }
            foreach (self::$_relations[$object_class] as $relation_name => $relation) {
                if ($relation->getRelatedObject($object_class) instanceof $destination_object_class) {
                    if ($option !== null && !$relation->getOption($option))
                        continue;
                    if (count($type) && !in_array($relation->getType(), $type))
                        continue;
                    if (!is_array($relations_arr))
                        $relations_arr = array();
                    $relations_arr[$relation_name] = $relation;
                }
            }

            return $relations_arr;
        }
    }

    protected static function _invertType($type) {
        if ($type == self::RELATION_ONE_TO_MANY)
            return self::RELATION_MANY_TO_ONE;
        if ($type == self::RELATION_MANY_TO_ONE)
            return self::RELATION_ONE_TO_MANY;
        if ($type == self::RELATION_ITEM_OWNER)
            return self::RELATION_MODEL_ITEM;
        if ($type == self::RELATION_MODEL_ITEM)
            return self::RELATION_ITEM_OWNER;
        return $type;
    }

    protected static function _buildInvertName(\Sl\Modulerelation\Modulerelation $relation) {
        $name = $relation->getName();
        if ($relation->isSelfRelation()) {
            if (strpos($name, self::SELFRELATION_PREFIX) === 0) {
                $name = substr($name, 1);
            } else {
                $name = self::SELFRELATION_PREFIX . $name;
            }
        }
        return $name;
    }

    public static function invertRelation(\Sl\Modulerelation\Modulerelation $relation) {
        $type = self::_invertType($relation->getType());
        $name = self::_buildInvertName($relation);
        $new_relation = clone $relation;
        $new_relation->setType($type)->setName($name);
        return $new_relation;
    }

    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function setModulerelation(\Sl\Modulerelation\Modulerelation $Modulerelation, $model_key) {

        $model_key = ($Modulerelation->isSelfRelation() && $model_key == self::SELFRELATION_PREFIX) ? $Modulerelation->getDbTable()->findRelatedModelsKeys($model_key) : $model_key;
        if (!isset(self::$_relations[$model_key]))
            self::$_relations[$model_key] = array();
        self::$_relations[$model_key][$Modulerelation->getName()] = $Modulerelation;

        /* 	
          $related_models_keys=$Modulerelation->getDbTable()->findRelatedModelsKeys();
          foreach($related_models_keys as $model_key){
          if (!isset(self::$_relations[$model_key]))self::$_relations[$model_key]=array();
          self::$_relations[$model_key][]=$Modulerelation;
          } */
    }

    public static function buildModulerelationName($classname) {

        $name_array = explode('\\', $classname);
        $name = array_pop($name_array);

        return strtolower($name);
    }

    protected static function createModulerelation(array $modulerelation, $return_models = false) {
        $manager = self::getInstance();
        $related_models_keys = \Sl\Service\DbTable::get($modulerelation['db_table'])->findRelatedModelsKeys();

        $options = (isset($modulerelation['options']) && is_array($modulerelation['options'])) ? $modulerelation['options'] : array();

        return new \Sl\Modulerelation\Modulerelation($modulerelation['type'], \Sl\Service\DbTable::get($modulerelation['db_table']), $options, isset($modulerelation['custom_configs']) ? (bool) $modulerelation['custom_configs'] : false);

        $model = array_shift($related_models_keys);
        $models = array($model);
        $manager->setModulerelation($new_relation, $model);

        $new_relation = $manager->invertRelation($new_relation);

        $model = array_shift($related_models_keys);
        $models[] = $model;
        $manager->setModulerelation($new_relation, $model);
        if ($return_models) {
            return $models;
        }
    }

    protected static function _tableClassToAlias($class) {
        if(!class_exists($class)) {
            $class = '\\'.$class;
        }
        return \Sl\Service\Helper::getModelAlias(\Sl_Model_Factory::object($class));
    }
    
    public function setModulerelations(array $modulerelations, \Sl_Module_Abstract $module, $rebuild_modulerelations_info = false) {
        $config = self::getConfig();
        $modulename = $module->getName();
        if($rebuild_modulerelations_info) {
            $config->$modulename = new \Zend_Config(array(), true);
        }
        if(!$config || !isset($config->$modulename)) {
            // Сначала создаем, а потом читаем
            $rels_data = array();
            foreach($modulerelations as $relation) {
                $table = \Sl\Service\DbTable::get($relation['db_table']);
                /*@var $table \Sl\Model\DbTable\DbTable*/
                if(!$table || !($table instanceof \Sl\Modulerelation\DbTable)) {
                    throw new \Exception('Wrong db_table param in relation (module: '.$modulename.'; '.print_r($relation, true).'). '.__METHOD__);
                }
                // Обратная связь
                $related_relation = $relation;
                $related_relation['type'] = self::_invertType($related_relation['type']);
                
                // Основное название связи
                $main_relation_name = self::buildModulerelationName(get_class($table));
                // По-умолчанию такое-же как и главной связи
                $related_relation_name = $main_relation_name;
                
                // Разбираем данные
                $reference = $table->findRefetenceArray();
                reset($reference);
                $main_model_data = array(
                    'alias' => self::_tableClassToAlias(key($reference)),
                    'data' => current($reference),
                );
                array_shift($reference);
                $related_model_data = array(
                    'data' => current($reference),
                );
                $selfrelated = (bool) (key($reference) === self::SELFRELATION_PREFIX);
                if($selfrelated) {
                    // Обработка связей "на себя"
                    $related_model_data['alias'] = $main_model_data['alias'];
                    $related_relation_name = self::SELFRELATION_PREFIX.$related_relation_name;
                } else {
                    $related_model_data['alias'] = self::_tableClassToAlias(key($reference));
                }
                unset($reference);
                
                @$rels_data[$main_model_data['alias']][$main_relation_name] = $relation;
                $children = \Sl_Model_Factory::mapper($main_model_data['alias'])->findDescendants();
                if(count($children)) {
                    foreach($children as $child) {
                        @$rels_data[\Sl\Service\Helper::getModelAlias($child)][$main_relation_name] = $relation;
                    }
                }
                @$rels_data[$related_model_data['alias']][$related_relation_name] = $related_relation;
                $children = \Sl_Model_Factory::mapper($related_model_data['alias'])->findDescendants();
                if(count($children)) {
                    foreach($children as $child) {
                        @$rels_data[\Sl\Service\Helper::getModelAlias($child)][$related_relation_name] = $relation;
                    }
                }
                }
            $config->$modulename = $rels_data;
            $writer = new \Zend_Config_Writer_Array(array('config' => $config));
            try {
                $writer->write(self::CONFIG_PATH);
                chmod(self::CONFIG_PATH, 0777);
            } catch (\Exception $e) {
                throw new \Exception('Can not save modularelations config: ' . $e->getMessage());
            }
        }
        foreach($config->$modulename->toArray() as $alias=>$relations) {
            foreach($relations as $name=>$relation) {
                if(isset(self::$_config_data[$alias][$name])) {
                    throw new \Exception('Duplicate !!!!!');
                }
                self::$_config_data[$alias][$name] = array(
                    'data' => $relation,
                    'object' => null,
                );
            }
        }
    }
    
    public static function test() {
        echo __METHOD__."\r\n";
        print_r(array(__METHOD__, self::$_relations, self::$_config_data));
        die;
    }
    
    protected static function _findChildren() {
        
    }

    /**
     * Ищем ограничивающую связь user с объектом, если такая есть
     * 
     * @param \Sl_Model_Abstract $object
     * @return \Sl\Modulerelation\Modulerelation
     */
    public static function findHandlingRelation(\Sl_Model_Abstract $object) {

        $relation = null;
        $user = \Zend_Auth::getInstance()->getIdentity();
        if ($user instanceof \Sl_Model_Abstract) {
            foreach (self::getRelations($user) as $rel) {
                if ($rel->getHandling()) {
                    if (get_class($object) == get_class($rel->getRelatedObject($user))) {
                        return $rel;
                    }
                }
            }
        }
        return $relation;
    }

    /**
     * Повертає назви всі керуючі зв'язків об'єкта
     * 
     * @param \Sl_Model_Abstract $object
     * @return array 
     */
    public static function findHandlingRelations(\Sl_Model_Abstract $object) {
        $relations = array();

        foreach (self::getRelations($object) as $rel) {
            if ($rel->getHandling()) {
                $relations[get_class($rel->getRelatedObject($object))] = $rel->getName();
            }
        }
        return $relations;
    }

    /**
     * Повертає назви всіх зв'язків з можливістю кастомізації конфігів
     * 
     * @param \Sl_Model_Abstract $object
     * @return array 
     */
    public static function findCustomConfigsRelations(\Sl_Model_Abstract $object) {
        $relations = array();

        foreach (self::getRelations($object) as $rel) {

            if ($rel->getCustomConfigs()) {


                $relations[get_class($rel->getRelatedObject($object))] = $rel->getName();
            }
        }
        return $relations;
    }

    public static function relationExists($relation_name, \Sl_Model_Abstract $model = null) {
        $result = false;
        if (!is_null($model)) {
            $result = isset(self::$_relations[get_class($model)][$relation_name]);
        } else {
            foreach (self::$_relations as $rels) {
                if ($result)
                    break;
                $result |= isset($rels[$relation_name]);
            }
        }
        return $result;
    }

}

?>
