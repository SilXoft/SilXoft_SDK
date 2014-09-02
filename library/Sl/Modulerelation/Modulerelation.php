<?php

namespace Sl\Modulerelation;

class Modulerelation {

    protected $_type;
    protected $_name;
    protected $_db_table;
    protected $_referenceMap;
    protected $_dependentTables;
    protected $_handling;
    protected $_custom_configs;
    protected $_options = array();

    /**
     * 
     * @param string $classname
     * @return \Zend_Db_Table_Abstract
     */
    public function getDependedTable($classname) {
      /*  if ($this->isSelfInverted()){
               $classname = array_shift(array_diff($this->getReferenceMap(), array($classname)));
         }
        */
        $db_table = $this->getDbTable()->findDependedTable($classname); 
        return $return = \Sl\Service\DbTable::get($db_table); 
    }

    /**
     *  
     * @return \Zend_Db_Table_Abstract
     */
    public function getIntersectionDbTable() {

        return $this->_db_table;
    }

    public function __construct($type, \Sl\Modulerelation\DbTable $db_table,  $vars = array(), $is_customized_config=false) {
        $avialable_relations = array(
            \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE,
            \Sl_Modulerelation_Manager::RELATION_ONE_TO_MANY,
            \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
            \Sl_Modulerelation_Manager::RELATION_MANY_TO_MANY,
            \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER,
            \Sl_Modulerelation_Manager::RELATION_MODEL_ITEM,
            \Sl_Modulerelation_Manager::RELATION_FILE_ONE,
            \Sl_Modulerelation_Manager::RELATION_FILE_MANY,
        );
        $type = intval($type);
        if (!in_array($type, $avialable_relations)) {
            throw new \Sl_Exception_Modulerelation('Error when determine relation type: type illegal ('.$type.').');
        }
        if (strpos($db_table->getName(),\Sl_Modulerelation_Manager::SELFRELATION_PREFIX)===0) {
            throw new \Sl_Exception_Modulerelation('Error when determine relation name: name illegal ('.$db_table->getName().').');
        }
        
        $this->setType($type)->setOptions($vars)->setCustomConfigs($is_customized_config)->setDbTable($db_table)->setReferenceMap($db_table->findRelatedModelsKeys());
    }

    /**
     * Устанавливает тип связи. ПО типу определяется метод для построения View
     * @param int $type
     * @return Sl_Modulerelation_Abstract
     */
    public function setType($type) {
        $this->_type = $type;
        return $this;
    }
    
    
    /**
     *  Перевірка, чи зв'язок пов'язує однакові об'єкти
     * @return bool
     */
    
    public function isSelfRelation(){
        $models = $this->getReferenceMap();
        
        return (in_array(\Sl_Modulerelation_Manager::SELFRELATION_PREFIX,$models));    
        
    }
    
    /**
     * Возвращает тип связи
     * @return int
     */
    public function getType() {
        return $this->_type;
    }
    
     /**
     * Встановлює додаткові властивості зв'язку
     * @param array $options
     * @return Sl_Modulerelation_Abstract
     */
    public function setOptions(array $options = array()) {
        
        $this->_options = $options; 
        return $this;
    }
    
    /**
     * повертає властивість зв'язку
     * @param string $name
     * @return void
     */
    public function getOption($name='') {
        $name=trim($name);  
        if (strlen($name) && isset($this->_options[$name])) return $this->_options[$name];
    }
    
    public function getOptions() {
        return $this->_options;
    }
    
    /**
     * Устанавливает массив-карту связи.
     * @param array $type
     * @return Sl_Modulerelation_Abstract
     */
    public function setReferenceMap($reference_map) {
        $this->_referenceMap = $reference_map;
        return $this;
    }
    
    public function findRefetenceArray($classname = null) {
            
        if ($classname) {
            if ($this->isSelfInverted()){
               $classname = array_shift(array_diff($this->getReferenceMap(), array($classname)));
            }
        
            return $this->getDbTable()->findRefetenceArray($classname);            
        
        } else {
            
            $reference_map = $this->getDbTable()->findRefetenceArray($classname);
            
            if ($this->isSelfInverted()){
                   
               $first_key = array_shift(array_keys($reference_map));
               $temp_val = $reference_map[$first_key]; 
               $reference_map[$first_key] = $reference_map[\Sl_Modulerelation_Manager::SELFRELATION_PREFIX];
               $reference_map[\Sl_Modulerelation_Manager::SELFRELATION_PREFIX] = $temp_val;
            }
            
            return $reference_map;
        }
    }
    /**
     * Повертає впорядкований масив зв'язку відносно вказаного класу. Для зв'язків на себе - з урахуванням інверсії. 
     */
     
    public function findSortedReferences($classname){
        

        $reference_map = $this-> getDbTable()->findRefetenceArray();   
        
        $model = \Sl_Model_Factory::object($classname);
        if ($model->checkExtend($model)){
            $cName = $classname;
            $classname=$model->Extend();
            if (!isset($reference_map[$classname])){
                 $classname = $cName;
            }
        }

        if (!isset($reference_map[$classname])) throw new \Sl_Exception_Modulerelation('Error when determine relation map: class illegal ('.$classname.').');
        
        if ($this->isSelfRelation()){
            if ($this->isSelfInverted()){
                $reference_map = array_reverse($reference_map);
            }
        } else {
            //if ($classname == 'Sl\Module\Customers\Model\Customer' || $classname == 'Sl\Module\Customers\Model\Dealer'){print_r(array($classname,$reference_map));}
            if (current(array_keys($reference_map))!=$classname) $reference_map = array_reverse($reference_map);
            //if ($classname == 'Sl\Module\Customers\Model\Customer' || $classname == 'Sl\Module\Customers\Model\Dealer'){print_r($reference_map);}
        }
        return $reference_map;
    }
    
    /**
     * Возвращает массив-карту связи.
     * @return array
     */
    public function getReferenceMap() {
        return is_array($this->_referenceMap) ? $this->_referenceMap : array();
    }

    /**
     * Установка db_table реализации связи
     * @param string $options
     * @return \Sl_Modulerelation_Abstract
     */
    public function setDbTable($db_table) {
        $this->_db_table = $db_table;
        return $this;
    }

    /**
     * Возвращает название класса db_table связи
     * @return \Zend_Db_Table
     */
    public function getDbTable() {
        return $this->_db_table;
    }

    /**
     * Возвращает название связи (по умолчанию берет из DbTable)
     * @return string
     */
    public function getName() {
        if (null == $this->_name){
           $this->_setDbTableName(); 
        }
        return $this->_name;
    }
    
    /**
     * Устанавливает название связи из tb_table
     * @return this
     */
    
    protected function _setDbTableName(){
        $this->_name = $this->_db_table->getName();
        return $this;
    }
    
    /**
     * Устанавливает название связи
     * @return this
     */
    public function setName($name = null) {
        if (null == $name){
            $this->_setDbTableName(); 
        } else {
            $this->_name = $name;
        }
        
        return $this;
    }
    
    public function getRelatedObject($object) {
        $object_class= $object instanceof \Sl_Model_Abstract?get_class($object):trim($object);
        if ($this->isSelfRelation() && $this->isSelfInverted()){
                
             
            $object_class=\Sl_Modulerelation_Manager::SELFRELATION_PREFIX;
        }  
        return \Sl_Model_Factory::object($this->getDependedTable($object_class));
    }
    
    public function isSelfInverted(){
        return (strpos($this->getName(),\Sl_Modulerelation_Manager::SELFRELATION_PREFIX) === 0);
    }
    
    public function getTitle(\Sl_Model_Abstract $object = null) {
        $title = strtoupper($this->getName());
        if(!is_null($object)) {
             $key = 'modulerelation_'.$this->getName();
             $list_options = \Sl_Module_Manager::getInstance()
                                ->getModule($object->findModuleName())
                                ->section('titles')
                                ->{$object->findModelName()};
             if (isset($list_options->$key) && isset($list_options->$key->label)) {
                $title = $list_options->$key->label;
            }
        }
        return $title;   
    }
    
    public function setHandling($is_handling) {
        $this->_handling = $is_handling;
        return $this;
    }
    
    public function getHandling() {
        return $this->_handling;
    }
    
    public function setCustomConfigs($is_customized_configs) {
        $this->_custom_configs = $is_customized_configs;
        return $this;
    }
    
    public function getCustomConfigs() {
        return $this->_custom_configs;
    }
    
    
    public function getRestrictions() {
        if(!$this->getHandling()) {
            throw new \Exception('Эта связь не управляющая. Метод не может быть запрошен.');
        }
        $user = \Zend_Auth::getInstance()->getIdentity();
        if(!$user->fetchRelated($this->getName())) {
            //echo "Name: ".$this->getName()."\r\n\r\n";
            //print_r($user);die;
        }
        return $user->fetchRelated($this->getName());
    }
}

