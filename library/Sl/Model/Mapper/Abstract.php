<?php

/**
 * Абстрактный менеджер объектов
 *
 * Умеет делать основные операции с объектами приложения
 */
abstract class Sl_Model_Mapper_Abstract {

    protected $_dbTable;
    protected $_mappedDomainName;
    protected $_mappedRealName;
    protected $_models = array();
    protected $_rowsets = array();
    protected $_mandatory_fields = array('master_relation', 'last_update');
    protected $_custom_mandatory_fields = array();
   
    public function __construct(){
        $this->_mandatory_fields = array_merge($this->_mandatory_fields, $this->_custom_mandatory_fields);
    }
   
   
     /**
          
     * Зберігає \Zend_Db_Table_Row в колекцію "роусетів".і
     * @param  $row
     * @return $obj
     * 
     */ 
    protected function saveCollection($row) {
        
		
		//$this -> _rowsets[$row['id']] = $row;
		if ($row instanceof Zend_Db_Table_Row){
		    $row = $row->toArray();
		}elseif(is_object($row)){
		    $row = (array)$row;
		}
		$obj = $this -> _createInstance($row);
		$this -> _models[$obj -> getId()] = $obj;
        //print_r($this -> _models);
        return $obj;
    
     
    }
	
	public function fetchByParent($id, $relation_name, $count_children = false){
            
        $object = \Sl_Model_Factory::object($this->_getDbTable());
        $relation = \Sl_Modulerelation_Manager::getRelations($object, $relation_name);
        if (!($relation instanceof \Sl\Modulerelation\Modulerelation)) 
            throw new \Exception('Relation '.$relation_name.' is not exist');
            
        if(!$relation->isSelfRelation())
            throw new \Exception('Relation '.$relation_name.' is not self-relation');
        $object = $id > 0 ? $this->find($id):\Sl_Model_Factory::object($this);
        $rows = $this->_getDbTable()->fetchByParent($object, $relation, $count_children);       
        
        $result = array();
        foreach ($rows as $row ){
            $obj = $this->create($row);  
            if ($count_children){
                $row= (array) $row;
                $result[] = array('object' => $obj, 'children' => $row['children']);
            } else {
               $result[] = $obj; 
            }
        }
        return $result;
    }

       /**
        * Дублює вхідний об'єкт, повертає новий, перевизначаючи зв'язки, які не можна дублювати.
        * @param \Sl_Model_Abstract $object
        * @return $new_onj
        * 
        */
	public function duplicate(\Sl_Model_Abstract $object) {
		$new_obj = \Sl_Model_Factory::object($object);
        
        $config = \Sl_Module_Manager::getInstance()->getCustomConfig($new_obj->findModuleName(), 'duplicate', $new_obj->findModelName());
        
        if(!$config) {
            $config = \Sl_Module_Manager::getInstance()->getModule($new_obj->findModuleName())->generateDuplicateOptions($new_obj);
        }
        
        foreach($config->toArray() as $field=>$value) {
            $matches = array();
            if(preg_match('/^modulerelation_(.+)$/', $field, $matches)) {
                if($object->issetRelated($matches[1])) {
                    $new_obj->assignRelated($matches[1], $object->fetchRelated($matches[1]));
                }
            } else {
                $set_method_name = \Sl_Model_Abstract::buildMethodName($field,'set');
                $get_method_name = \Sl_Model_Abstract::buildMethodName($field,'get');
                if(method_exists($new_obj, $set_method_name)) {
                    $new_obj->$set_method_name($object->$get_method_name());
                }
            }
        }
        
		$new_obj = $this->save($new_obj, true);
        return $new_obj;
        
    }
	
	
	
    /**
     * Повертає список назв доступних зв'язків об'єкта
     * @return array()
     * 
     */
	public function getAllowedRelations(){
		$Obj = \Sl_Model_Factory::object($this);
		$relations = \Sl_Modulerelation_Manager::getRelations($Obj);
		$allowed_relations  = array();
        
        \Sl_Service_Acl::setContext($Obj);
        
		foreach ($relations as $name => $relation){
			if ( \Sl_Service_Acl::isAllowed(array(
	                        $Obj,
	                        $relation->getName()
	                            ), \Sl_Service_Acl::PRIVELEGE_UPDATE) || 
	             \Sl_Service_Acl::isAllowed(array(
	                        $Obj,
	                        $relation->getName()
	                            ), \Sl_Service_Acl::PRIVELEGE_READ)) {
	        	$allowed_relations[] = $relation->getName();                    	
	        }
	                            	
		}
		return $allowed_relations; 
	}
	/**
         * Зберігає об'єкт і всі його зв'язки
         * @param \Sl_Model_Abstract $object
         * @param $return
         * @param $events
         * @param $set_context
         * @return
         * @throws Exception
         * Зберігає об'єкт і всі його зв'язки 
         */
    public function save(\Sl_Model_Abstract $object, $return = false, $events = true, $set_context = true){
		$Locker = \Sl_Model_Factory::object('\Sl\Module\Home\Model\Locker');
        
                
                //Перевірка, чи не зайнятий об'єкт іншим користувачем
		//echo $object.'<br>';
		
		if ($lock = \Sl_Model_Factory::mapper($Locker)->checkModel($object)){
			if ($object->getId() > 0) {
	        	$this->eraseCollection($object->getId());
			}	
			$filled_relations = $object->findFilledRelations();	
			
            //$object_before_update = $object->getId() > 0 ? $this->findExtended($object->getId(),$filled_relations):\Sl_Model_Factory::object($object);
        	
            $object_before_update = $this->findExtended($object->getId(),$filled_relations);
            if ($set_context) \Sl_Service_Acl::setContext($object_before_update);
            if(!$object_before_update) {
                $object_before_update = \Sl_Model_Factory::object($object);
            }
            
			
			        
	        if ($events) \Sl_Event_Manager::trigger(new \Sl_Event_Model('beforeSave', array('model' => $object, 'model_before_update'=>$object_before_update)));
			
            $filled_relations = $object->findFilledRelations();
            
			$data = $data_copy = $object -> toArray();
			
			$data_before_update = $object_before_update->toArray();
	        
	        //Перевірка прав редагування
			
	        foreach ($data_copy as $field => $value) {
	            $priv_edit = \Sl_Service_Acl::isAllowed(array(
	                        $object,
	                        $field
	                            ), \Sl_Service_Acl::PRIVELEGE_UPDATE);
	
	             if (!($priv_edit || $this->_isFieldMandatory($field))  
                    //якщо дані не змінювались
                    || $data[$field] === $data_before_update[$field]) {
                    
                    unset($data[$field]);
                }
                try {
                    if($field_data = $object->describeField($field)) {
                        if(isset($field_data['is_null']) && $field_data['is_null']) {
                            if($value === '') {
                                $data[$field] = null;
                            }
                        }
                    }
                } catch(\Exception $e) {
                    
                }
	        }
            
			//active переноситься незалежно від прав
			$data['active'] = $data_copy['active'];
                        $data['extend'] = '';

                        $data['extend'] = \Sl\Service\Helper::getModelExtend($object);                          
                        
                        //$data['extend'] = $data_copy['extend'];
                        // Вдруг NULL. Не зависит от прав на это поле.
			$data['archived'] = (int) $object->getArchived();
	        if (!($id = $object->getId())) {
	        	
	            unset($data['id']);
                if(!isset($data['create']) || !$data['create']) {
                    $data['create'] = date('Y-m-d H:i:s');
                }
				
                //print_r(array($data_before_update, $data_copy, $data));die;
                try {
	            $id = $this->_getDbTable()->insert($data);
	            $object->setId($id);
                    } catch ( \Exception $e){
			throw new \Exception("insert error: ".$e->getMessage(), 1);	
						
                    }                    
                    
	        } else {
	        	
	            unset($data['id']);
	            if (count($data)){
	            	
	                	//print_r(array($data_before_update, $data_copy, $data));die;
					
					try {
						$this->_getDbTable()->update($data, array('id = ?' => $id));
					} catch ( \Exception $e){
						throw new \Exception("Update error: ".$e->getMessage(), 1);	
						
					}			
					
				}
			
	        }
			
			foreach ($data as $field =>$value){
				\Sl\Service\Loger::Log($object, $field,$data_before_update[$field], $value);
			}
			
	
	        $relations = \Sl_Modulerelation_Manager::getRelations($object);
           // echo $object.' relations <br>';
            foreach ($relations as $relation) {
	        	
	        	if (!in_array($relation->getName(),$filled_relations)) {
	        			
	        		continue;
				}
	            
	            if ($object->issetRelated($relation->getName()) && \Sl_Service_Acl::isAllowed(array(
	                        $object,
	                        $relation->getName()
	                            ), \Sl_Service_Acl::PRIVELEGE_UPDATE)) {
					//echo $object.' relation '.$relation->getName().'  before save <br>';	                            	
	                $this->saveRelations($object, $relation);
                   // echo $object.' after relation '.$relation->getName().' relations save <br>';   
	            }
                //Контекст міг помінятися під час збереження Item
                if ($set_context) \Sl_Service_Acl::setContext($object_before_update);                
	        }
			//if ($object instanceof \Sl\Module\Logistic\Model\Itemservice){print_r($this -> findExtended($id, false, false)); die;}	 
			//echo $object.' before find <br>';
			$after_save_obj = $this -> find($id, false, false);
			
			
			
			
			//echo $object.' before afterSave <br>';
	        if ($events) \Sl_Event_Manager::trigger(new \Sl_Event_Model('afterSave', array(
	                    'model' => $after_save_obj,
	                    'model_before_update'=>$object_before_update,
	                )));
			//echo $object.' after afterSave <br>';
			
			\Sl_Model_Factory::mapper($Locker)->unlockModel($after_save_obj);
			
			
	        if ($return) {
	        	$after_save_obj = $this -> find($id, false, false);
	            return $after_save_obj;
	        }
		} else {
			
			throw new Exception("Object ".get_class($object)." is locked", 1);
			
		}
		
      
    }

    /**
     * Возвращает имя класса объекта уровня приложения
     */
    abstract protected function _getMappedDomainName();

    /**
     * Возвращает имя класса объекта уровня хранения данных
     */
    abstract protected function _getMappedRealName();

    /**
     * Создает экземпляр объекта уровня приложения
     * @param array $data
     * @return \Sl_Model_Abstract
     */
   
    protected function _createInstance(array $data) {
        $name = $this->_getMappedDomainName();
        $instance = new $name();
        $instance->setOptions($data);
        if ($instance instanceof \Sl\Model\Masterrelation){
            $relation_name = $instance->getMasterRelation();
            if(  $relation_name  && !$instance->issetRelated($relation_name)) {
                
                
                $relation = \Sl_Modulerelation_Manager::getRelations($instance,$relation_name);
                //if (!$relation->isSelfRelation()) 
                $instance = $this->findRelation($instance,$relation);
                
            }
        }
        return $instance;
    }
     /*
    protected function _createInstance(array $data) {
        $name = $this->_getMappedDomainName();
        $instance = new $name();
        $instance->setOptions($data);
        return $instance;
    }*/
    
    
    
    /**
     * Устанавливает адаптер БД
     * @param \Zend_Db_Table_Abstract $dbTable
     * @return \Sl_Model_Mapper_Abstract
     * @throws \Sl_Exception_Db
     */
    protected function _setDbTable(\Zend_Db_Table_Abstract $dbTable) {
        if (!$dbTable instanceof \Zend_Db_Table_Abstract) {
            throw new Sl_Exception_Db('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * Возвращает установленный адаптер БД
     * @return \Zend_Db_Table_Abstract
     */
    protected function _getDbTable() {
		if (!$this -> _dbTable) {
			$this -> _dbTable = \Sl\Service\DbTable::get($this -> _getMappedRealName());
        }
		return $this -> _dbTable;
    }

    /**
     * Удаление объекта
     * @param \Sl_Model_Abstract $object
     * @throws \Sl_Exception_Db
     */
    public function delete(\Sl_Model_Abstract $object, $with_items = true) {
		if (!$object -> getId()) {
			throw new Sl_Exception_Model("Нельзя удалять объект без указания Id");
		}
		if($with_items) {
            $item_relations = \Sl_Modulerelation_Manager::getRelations($object);
            foreach($item_relations as $k=>$rel) {
                if($rel->getType() != \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER) {
                    unset($item_relations[$k]);
                }
            }
            $object = $this->findExtended($object->getId(), array_map(function($el){ return $el->getName(); }, $item_relations));
            foreach($item_relations as $rel) {
                //if($rel->getType() == \Sl_Modulerelation_Manager::RELATION_MODEL_ITEM) {
                    $objs = $object->fetchRelated($rel->getName());
                    foreach($objs as $o) {
                        \Sl_Model_Factory::mapper($o)->delete($o, false);
                    }
                //}
            }
        }
		$this -> save($object->setActive(0));
	}

	/**
	 * Полное удаление объекта
	 * @param \Sl_Model_Abstract $object
	 * @throws \Sl_Exception_Db
	 */
	public function force_delete(\Sl_Model_Abstract $object) {
		
        if (!$object->getId()) {
            throw new Sl_Exception_Model("Нельзя удалять объект без указания Id");
        }
		
		$object -> setActive(null);
		
		$this -> save($object);
		
        $this -> _getDbTable() -> delete('id='.$object->getId());

    }
	/**
         * Видаляє об'єкт з коллекції "роусетів" по його id.
         * @param $object_id
         * 
         */
	protected function eraseCollection($object_id = false){
		if ($object_id){
			//unset($this -> _rowsets[$object_id]);
			unset($this -> _models[$object_id]);
			
		}else {
			//$this -> _rowsets=array();
			$this -> _models=array();
		}
		
	} 
	
    /**
     * Поиск объекта по первичному ключу
     * @param  $object_id
     * @param  $visible
     * @param  $cashe
     * @return null
     * 
     */
	public function find($object_id, $visible = true, $cashe = true) {
           // print_r($this -> _models[$object_id]);
	 //   echo '<br>find '.get_class($this).'  '.$object_id.'<br>';

		if ($cashe && isset($this -> _models[$object_id]) && is_object($this -> _models[$object_id]) 
                        && !$this -> _models[$object_id]->checkExtend($this -> _models[$object_id])
                        )
			return $this -> _models[$object_id];
     //   echo 'before get row<br>';
               
              
		$row = $this -> _getDbTable() ->find($object_id) -> current();
      //  echo 'before get row<br>';
        if (!$row) {
           // echo 'before erase collection<br>';
        	$this->eraseCollection($object_id);
           // echo 'after erase collection<br>';
			//unset($this -> _rowsets[$object_id]);
            return null;
        }
        //$object = $this -> _createInstance($row -> toArray());
       // echo 'before save collection<br>';
        $object = $this->saveCollection($row);       

        if( \Sl\Service\Helper::getModelExtend($object) !== $object->getExtend() ){                       
            return null;
        }

        if ($visible && !$object->getActive()) {
            return null;
        }
        //if(!method_exists($model, 'getExtend') && !is_null( $object->getExtend() )){
        //    return null;
       // }
        
//die('888');
        return $object;
    }
    
    
    /**
     * Вытаскивает все объекты по датам
     * @return Sl_Model_Abstract[]
     */
    public function fetchByDateField(\DateTime $date_start, \DateTime $date_end, $field_name = 'create', $visible = true) {
        $default_object = \Sl_Model_Factory::object($this);
        
        if (!method_exists($default_object, \Sl_Model_Abstract::buildMethodName($field_name,'get')))
                throw new \Exception("Field {$field_name} is not exist in ".get_class($default_object).' '.__METHOD__, 1);
                
        
        
        
        $where = array($field_name .' >= ? ' => $date_start->format('Y-m-d'), $field_name .' <= ? ' => $date_end->format('Y-m-d'));
        if ($visible){
            $where[] = ' active > 0';
        } 
     
        $rowset = $this->_getDbTable()->fetchAll($where,$field_name);
        if (count($rowset) == 0)
            return array();

        $objects = array();
        foreach ($rowset as $row)
            $objects[] = $this->saveCollection($row);
        /*
        if (count($relations)) {
            foreach ($objects as $key => $object) {
                $objects[$key] = $this->findExtended($object->getId(), $relations);
            }
        }*/

        return $objects;
    }
    
    
    /**
     * Вытаскивает все объекты
     * @return Sl_Model_Abstract[]
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null, $relations = array()) {

        if (is_null($where) || !$where || trim($where) === '') {
            $where = array('active = 1');
        } elseif (is_array($where)) {
            $noedit = false;
            foreach ($where as $key => $value) {
                if (preg_match('/active/', $key . $value)) {
                    $noedit = true;
                    break;
                }
            }
            if (!$noedit) {
                $where[] = 'active = 1';
            }
        } elseif (is_string($where) && !preg_match('/active/', $where)) {
            $where = array(
                $where,
                'active = 1'
            );
			
        }
		
		
        $rowset = $this->_getDbTable()->fetchAll($where, $order, $count, $offset);
        if (count($rowset) == 0)
            return array();

        $objects = array();
        foreach ($rowset as $row)
            $objects[] = $this->saveCollection($row);

        if (count($relations)) {
            foreach ($objects as $key => $object) {
                $objects[$key] = $this->findExtended($object->getId(), $relations);
            }
        }

        return $objects;
    }
    /**
     * Повертає масив колонок таблиці 'DATA_TYPE'
     * @return array()
     *
     */
    public function getTableColumns() {
        $data = $this->_getDbTable()->info(\Zend_Db_Table::METADATA);
        $result = array();
        if ($data) {
            foreach ($data as $key => $item) {
                $result[$key] = $item['DATA_TYPE'];
            }
        }
        return $result;
    }

	public function buildExtra(\Sl_Model_Abstract $object) {
		/*$extra = array();
		 foreach($object->fetchRelations() as $relation){
		 $method='fetch'.$relation;
		 $extra[$relation] = $object->$method;
		 }
		 return $extra;*/
	}

     /**
      * На вході отримує об'єкт або його id і масив зв'язків
      * Повертає об'єкт із пов'язанми по цим зв'язкам об'єктами(якщо такі є), або просто об'єкт
      * @param \Sl_Model_Abstract $object
      * @param  $get_relations
      * @return $obj 
      * 
      */ 
     public function findExtended($object, $get_relations = true) {
           
        
        if ($object instanceof \Sl_Model_Abstract) {
            $object_id = $object->getId();
        } else {
            $object_id = $object;
        }
       // $Row = $this->_getDbTable()->find($object_id)->current();
        
        $Obj = $this->find($object_id);


        if ($Obj) {
           // $Row = $this->_rowsets[$object_id];

            $relations = \Sl_Modulerelation_Manager::getRelations($Obj);
            $class_name = get_class($Obj);

            foreach ($relations as $relation) {
                $relation_name = strtolower($relation->getName());
                if (is_array($get_relations) && !in_array($relation_name, $get_relations))
                    continue;


                $Obj = $this->findRelation($Obj, $relation);
            }
        }

        return $Obj;
    }
	
	/**
         * На вході отримує об'єкт або його id та опціонально масив зв'язків
         * Повертає об'єкт із наповненими доступними зв'язками з усіх можливих або з масиву, що прийшов на вхід
         * @param $object_id
         * @param $get_relations
         * @return $obj
         * 
         */ 
	 public function findAllowExtended($object, $get_relations = true) {
        if ($object instanceof \Sl_Model_Abstract) {
            $object_id = $object->getId();
        } else {
            $object_id = $object;
        }
        $allowed_relations = $this->getAllowedRelations();
        $relations = is_array($get_relations) ? array_intersect($allowed_relations, $get_relations) : $allowed_relations;
        return $this->findExtended($object_id, $relations);
    }
	/**
         * На вході отримує об'єкт і об'єкт типу \Sl\Modulerelation\Modulerelation або його назвою.
         * Повертає об'єкт із пов'язаними по визначеному зв'язку об'єктами. 
         * @param \Sl_Model_Abstract $Obj
         * @param  $value
         * @return \Sl_Model_Abstract
         * @throws \Exception
         * 
         */
	  public function findRelation(\Sl_Model_Abstract $Obj, $value) {
        if (($relation = $value)  instanceof \Sl\Modulerelation\Modulerelation || 
            ($relation = \Sl_Modulerelation_Manager::getRelations($Obj, $value)) instanceof \Sl\Modulerelation\Modulerelation) {
            
            
            //$relation = \Sl_Modulerelation_Manager::getRelations($Obj, $value);
            if ($Obj->getId()) {
                $class_name = get_class($Obj);
                $rows = $this -> _getDbTable() -> findRelated($Obj->getId(),$relation);
                $related_objects = \Sl_Model_Factory::mapper($relation->getDependedTable($class_name))->create($rows);
                $related_objects = is_array($related_objects) ? $related_objects : (is_null($related_objects) ? array() : array($related_objects));
                $Obj->assignRelated($relation->getName(), $related_objects);
            }
           
        } 
            
        return $Obj;
        
    }
    
    /**
     * Проверяет наличие данных по связи и загружает, если они не загружены
     * 
     * @param \Sl_Model_Abstract $model
     * @param mixed $relation
     * @return \Sl_Model_Abstract
     */
    public function checkRelation(\Sl_Model_Abstract $model, $relation) {
        if(is_array($relation)) {
            foreach($relation as $rel) {
                $model = $this->findRelation($model, $rel);
            }
            return $model;
        } else{
            return $this->findRelation($model, $relation);
        }
    }
    /**
     * На вході отримує об'єкт і зв'язок. 
     * Зберігає в базі об'єкт із доступними по зв'язку об'єктами. 
     * @param \Sl_Model_Abstract $Obj
     * @param \Sl\Modulerelation\Modulerelation $relation
     * @return 
     * 
     */
    public function saveRelations(\Sl_Model_Abstract $Obj, \Sl\Modulerelation\Modulerelation $relation) {
	
        \Sl_Service_Acl::setContext($Obj);
        
        $reference_array = $relation->findRefetenceArray();
        $class_name = get_class($Obj);
      
        $relation_keys =array_keys($relation->findRefetenceArray());
        
       if($Obj->checkExtend($Obj) && !in_array(get_class($Obj), $relation_keys)){
           $class_name = $Obj->Extend();         
        }
        
        //print_r($reference_array);
        
        $current_reference_array = $reference_array[$class_name];
        unset($reference_array[$class_name]);
        $target_reference_array = array_shift($reference_array);
        
        $method = 'get' . ucfirst($current_reference_array['refColums']);

        $rowset = $relation->getDbTable()->fetchAll($current_reference_array['columns'] . '=' . $Obj->$method());
        
        $new_relations = $Obj->getActive()?$Obj->fetchRelated($relation->getName()):array();
               
		$target_class=$relation->getDbTable() -> findRelatedModelsKeys($class_name);

		//Прохід по існуючим зв'язкам (із БД)
		/*if ($Obj instanceof \Sl\Module\Logistic\Model\Itemservice){
				print_r($relation->getName());
				print_r($relation->getDbTable());	
				print_r($rowset->toArray()); 
                                die;
			
			}
			*/
        foreach ($rowset->toArray() as $row) {
			//Якщо такий є,	
            if (isset($new_relations[$row[$target_reference_array['columns']]])) {
				//Якщо це підпорядкований об'єкт, оновити його	
				if ($relation->getType()==\Sl_Modulerelation_Manager::RELATION_ITEM_OWNER){
						
					$rel_object=\Sl_Model_Factory::mapper($relation->getRelatedObject($Obj))->findExtended($row[$target_reference_array['columns']]);
					
					//Якщо стоїть ознака видалення
					if (is_array($new_relations) 
						&& is_array($new_relations[$row[$target_reference_array['columns']]]) 
						&& isset($new_relations[$row[$target_reference_array['columns']]]['delete']) 
						&& $new_relations[$row[$target_reference_array['columns']]]['delete'] == 1){
						//Видалити підпорядкований об'єкт	
						\Sl_Model_Factory::mapper($rel_object,\Sl_Module_Manager::getInstance()->getModule($rel_object->findModuleName()))->force_delete($rel_object);
						//Видалити зв'язок
						$relation -> getDbTable() -> delete(array(
							$target_reference_array['columns'] . '=?' => $row[$target_reference_array['columns']],
							$current_reference_array['columns'] . '=?' => $Obj -> $method()
							));
						} else {
							//Видалити поле з прапорцем видалення	
							if (is_array($new_relations) && is_array($new_relations[$row[$target_reference_array['columns']]]) && isset($new_relations[$row[$target_reference_array['columns']]]['delete'])) unset($new_relations[$row[$target_reference_array['columns']]]['delete']);
							//заповнити об'єкт
							if (is_array($new_relations[$row[$target_reference_array['columns']]])){
								$rel_object->setOptions($new_relations[$row[$target_reference_array['columns']]]);
							} elseif (is_object($new_relations[$row[$target_reference_array['columns']]])) {
								$rel_object = $new_relations[$row[$target_reference_array['columns']]];
							}
						
							//зберегти об'єкт
							\Sl_Model_Factory::mapper($rel_object,\Sl_Module_Manager::getInstance()->getModule($rel_object->findModuleName()))->save($rel_object);
					}
					
				}	
				
				//видалити із масива новостворюваних зв'язків
                unset($new_relations[$row[$target_reference_array['columns']]]);
            } else {
                $relation->getDbTable()->delete(array(
                    $target_reference_array['columns'] . '=?' => $row[$target_reference_array['columns']],
                    $current_reference_array['columns'] . '=?' => $Obj->$method()
                ));
				
				\Sl\Service\Loger::Log($Obj, $relation->getName(),$row[$target_reference_array['columns']], NULL);
            }
        };
        
        if (count($new_relations)) {
			//array_pop($new_relations);
              //if($relation->getName() == 'partnerpartnertariffitem') { print_r($new_relations); }           
            foreach ($new_relations as $key => $values) {
                // якщо об'єкт не створений
                $assigned_relations = array();
				if (is_object($values)){
					$assigned_relations =$values->fetchRelated();	
					$values=$values->toArray();
					
				}
				$target_id=$key;
                
				if ($relation->getType()==\Sl_Modulerelation_Manager::RELATION_ITEM_OWNER){
					
					
					if (preg_match('/^new/',$target_id)){
						if (isset($values['delete']) && $values['delete'] >0) continue;
						
                	}
                                         //if (!is_array($values)) {echo $key.' '.$relation->getName(). ' '. get_class($Obj);var_dump($values); die;}
                                      					
                                         $rel_object=$relation->getRelatedObject($Obj)->setOptions($values);
					
					//Наповнення підпорядкованого об'єкту зв'язками із форми
					if (count($assigned_relations)){
						foreach ($assigned_relations as $rel_name => $rel_values){
							if (is_array($rel_values))	$rel_object->assignRelated($rel_name, $rel_values);
							
						}
						
					}
                    
					if (!$rel_object->isValid()) continue;
                    
					if ($Obj->getId() && \Sl_Service_Acl::isAllowed(array(
											                        $rel_object,
											                        $relation->getName()
	                            									), \Sl_Service_Acl::PRIVELEGE_UPDATE))
	                 {
								//Прив'язка до батьківської моделі. Якщо не встановлювати, на подіях beforeSave і afterSave не буде прив'язаний власник  
						$rel_object->assignRelated($relation->getName(), array($Obj->getId()=>$Obj->getId()));
						$rel_object = \Sl_Model_Factory::mapper($rel_object)->save($rel_object,true);
					} else {
                                            
						$rel_object = \Sl_Model_Factory::mapper($rel_object)->save($rel_object,true);
						$target_id = $rel_object->getId();
						$relation -> getDbTable() -> insert(array(
                                                    $target_reference_array['columns'] => $target_id,
                                                    $current_reference_array['columns'] => $Obj -> $method()
                                                    )); 
					}
					
				} 
				//Перевірка типу зв'язку. Якщо  1-2 або 1-1, то видалити наявні зв'язки об'єкта
                
				else {
					if(in_array($relation->getType(), array(Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE,Sl_Modulerelation_Manager::RELATION_ONE_TO_MANY)))  {
						// Нужно бы еще проверить не забрали ли мы его у кого-то
                        $target_object = \Sl_Model_Factory::mapper($relation->getRelatedObject($Obj))->findExtended($target_id, array($relation->getName()));
                        $cur_related = $target_object->fetchRelated($relation->getName());
                        if(count($cur_related)) {
                            foreach($cur_related as $cur) {
                                if($cur->getId() && $cur->getId() != $target_id) {
                                    \Sl\Module\Home\Service\Errors::addError('Can\'t reassign "'.get_class($target_object).'" to "'.get_class($Obj).'"', 'Can\'t save relations');
                                    return;
                                    //throw new \Exception('Can\'t reassign "'.get_class($target_object).'" to "'.get_class($Obj).'"');
                                }
                            }
                        }
                        $relation->getDbTable()->delete(array(
                            $target_reference_array['columns'] . '=?' => $target_id,
                        ));
					}
					
					$relation -> getDbTable() -> insert(array(
	                    $target_reference_array['columns'] => $target_id,
						$current_reference_array['columns'] => $Obj -> $method()
	                ));
					
					
				}
				\Sl\Service\Loger::Log($Obj, $relation->getName(), NULL,$target_id);
            }

        }

    }
	/** Створення об'єкта із роусета
	 * 
	 * @return \Sl_Model_Abstract 
	 * */
    public function create($data) {
            
        if (is_array($data) && count($data)) {
            if(is_array(current($data))) {
                throw new Exception('not set creation from array in mapper');
            } elseif(is_object(current($data))){
                
                foreach ($data as $row){
                    $array[] = $this->saveCollection((array)$row);
                }   
                return $array;
            } else {
                return $this->_createInstance($data);    
            }
            
        } elseif ($data instanceof \Zend_Db_Table_Rowset) {
            $array = array();
            foreach ($data as $row){
                $array[] = $this->saveCollection($row);
                
            }    
            return $array;
        } elseif ($data instanceof \Zend_Db_Table_Row) {
            return $this->saveCollection($data);
        }
    }
    
	/** Створення нового об'єкта з id
	 * 
	 * @return \Sl_Model_Abstract 
	 */
    public function createNewObject() {
    	
        $data= array();
	    $data['create'] = date('Y-m-d H:i:s');
		$data['active'] = 1;
	    $id = $this->_getDbTable()->insert($data);
		$Obj = \Sl_Model_Factory::object($this);
		$Obj = $this->prepareNewObject($Obj) -> setId($id);
	    return $Obj;        
    }
	
	
    /**
     * Достаем все объекты с сортировкой поиском и т.д. и т.п. ....
     * 
     * @return \Sl\Model\Identity\Identity заполненная сущность
     */
    public function fetchAllExtended(\Sl\Model\Identity\Identity $identity) {
    	 
        return $this->_getDbTable()->fetchAllExtended($identity);
    }
    
    public function fetchDataset(\Sl\Model\Identity\Dataset $dataset) {
        return $this    ->_getDbTable()
                        ->fetchDataset($dataset);
    }
	
	/**
	 * Підготовка чистого об'єкта: наповнення зв'язаними об'єктами із identity
	 * @param \Sl_Model_Abstract $Obj - Новий об'єкт
	 * @return \Sl_Model_Abstract $Obj - Наповнений об'єкт
	 */
	public function prepareNewObject(\Sl_Model_Abstract $Obj){

            $model_described_fields = $Obj->describeFields(true);
           
           foreach ($model_described_fields as $key => $value) 
               {
                        $fields =$value->toArray();
                        
                        foreach($fields as $field_name => $field)
                            {
                                if($field['default_value'])
                                    {
                                        $Obj->{$Obj->buildMethodName($field_name, 'set')}($field['default_value']) ;
                                    }
                            }
               }
            
	    //TODO: переробити під restrictions
	    
	    /*
		$current_user = \Zend_Auth::getInstance() -> getIdentity();	
		
		if ($current_user->getId() && !$Obj->getId()) {
			// Якщо є поточний користувач і для наповненн прийшов новий об'єкт
				
			$handling_relations = \Sl_Modulerelation_Manager::findHandlingRelations($Obj);
			$handling_user_relations = \Sl_Modulerelation_Manager::findHandlingRelations($current_user);
			
			//підставляння поточного юзера в керуючий зв'язок
			
			if (isset($handling_relations[get_class($current_user)])){
				$Obj->assignRelated($handling_relations[get_class($current_user)], array($current_user->getId()=>$current_user));
			}
			
			
			//наповнюємо керуючі зв'язки із юзера
			foreach ($handling_user_relations as  $related_obj_class => $relation_name){
				if (isset($handling_relations[$related_obj_class])){
					$relations = $current_user->fetchRelated($relation_name);
					if (count($relations) == 1){
						$Obj->assignRelated($handling_relations[$related_obj_class], $relations);
					}	
				}

			}
		}
         * */
		return $Obj;
	}
	
	
	/** Повертає кількість невидалених елементів із такими самимим полями 
	 * @param \Sl_Model_Abstract - об'єкт 
	 * @param array  - масив полів
	 * @return int 
	 */
	public function getCountLikeThis(\Sl_Model_Abstract $Obj, array $fields){
		$fieldfilters = array();
		foreach ($fields as $field_name){
			$method = $Obj->buildMethodName($field_name,'get');
			if ($method && $value=$Obj->$method()){
				$fieldfilters[$field_name.' = ?'] = $value;
			}
		}
		
		
		
		return count($fieldfilters)? $this->_getDbTable()-> getCountByFields($fieldfilters, $Obj->getId()):0;
	}
    
        /**
         * Проксирует на save
         * 
         * @deprecated since version 0.7.6
         * 
         * @see save()
         */
        public function archive(\Sl_Model_Abstract $model, $return = false, $events = true, $set_context = true) {
            return $this->save($model, $return, $events, $set_context);
        }

        public function countRelated($model, $relation) {
            if(($model instanceof \Sl_Model_Abstract) && $model->getId()) {
                // Все в порядке
            } elseif(intval($model)) {
                // Дали Id - Строим подобие объекта
                $model = \Sl_Model_Factory::object($this)->setId(intval($model));
            } else {
                throw new \Exception('Wrong params "model" in '.__METHOD__);
            }
        
            if(is_string($relation)) {
                // Пытаемс постоить из строки
                if(!($relation = \Sl_Modulerelation_Manager::getRelations($model, $relation))) {
                    throw new \Exception('Wrong params "relation" in '.__METHOD__);
                }
            } elseif($relation instanceof \Sl\Modulerelation\Modulerelation) {
                // Все нормально
            } else {
                throw new \Exception('Wrong params "relation" in '.__METHOD__);
            }
        
            return $this->_getDbTable()->countRelated($model->getId(), $relation);
    }

      
    
    /** перевіряє, чи є поле важливим - для збереження попри дозволи acl 
     * @param string $field - назва поля 
     
     * @return bool 
     */
    protected function _isFieldMandatory($field){
        return in_array($field, $this->_mandatory_fields);
    }
    
    public function fetchDistinctValues($fieldname) {
        return (array) $this->_getDbTable()->fetchDistinctValues($fieldname);
    }
    
    public function findDescendants($as_object = true) {
        $descendants = array();
        $cur_model = $this->_model();
        foreach(\Sl_Module_Manager::getAvailableModels() as $modulename=>$models) {
            foreach($models as $modelname) {
                $tmp_model = \Sl_Model_Factory::object($modelname, $modulename);
                if($tmp_model && ($tmp_model instanceof $cur_model) && (get_class($tmp_model) != get_class($cur_model))) {
                    $descendants[] = clone $tmp_model;
                }
                unset($tmp_model);
            }
        }
        if(!$as_object) {
            return array_map('get_class', $descendants);
        }
        return $descendants;
    }
    
   public function findParents($as_object = true) {

        $object = \Sl_Model_Factory::object($this);
        $parent_class = new \ReflectionClass($object);
        $parent_class_name = $parent_class->getParentClass()->getName();
        if ($parent_class_name == 'Sl_Model_Abstract') {
            return array();
        } else {
            $parents = array();
            $done = false;
            $i = 0;
            do {
                $parent_obj = \Sl_Model_Factory::object($parent_class_name);
                $parent_class = new \ReflectionClass($parent_obj);
                $parent_class_name = $parent_class->getParentClass()->getName();

                $parents[] = $parent_obj;

                if ($parent_class_name == 'Sl_Model_Abstract') {
                    $done = true;
                } else {
                    $parent_obj = \Sl_Model_Factory::object($parent_class_name);
                }
                $i++;
                if ($i == 10)
                    die(' findParents 10');
            } while ($done == false);

            if (!$as_object) {
                return array_map('get_class', $parents);
            }
            return $parents;
        }
    }

    public function nFind($value, $visible = true, $cache = true, $field = 'id') {
        if($field === 'id') {
            return $this->find($value, $visible, $cache);
        }
        try {
            $method_name = \Sl_Model_Abstract::buildMethodName($field, 'get');
            if(!method_exists($this->_model(), $method_name)) {
                throw new \Exception('No such field in model. '.__METHOD__);
            }
            $fieldset = Sl\Model\Identity\Fieldset\Factory::build($this->_model(), 'listview');
            $fields = array_keys(\Sl\Service\Config::read($this->_model())->toArray());
            if(!in_array('id', $fields)) {
                $fields[] = 'id';
            }
            foreach($fields as $fieldname) {
                $fieldset->createField($fieldname, array(
                    'roles' => array(
                        'from',
                    ),
                ));
            }
            $comps = array(
                'type' => 'multi',
                'comps' => array(
                    array(
                        'field' => $field,
                        'type' => 'eq',
                        'value' => $value,
                    ),
                ),
            );
            if($visible) {
                $comps['comps'][] = array(
                    'field' => 'active',
                    'type' => 'eq',
                    'value' => '1',
                );
            }
            $fieldset->addComps(array(\Sl\Model\Identity\Fieldset\Comparison\Factory::build($comps, $fieldset)));
            $ds = new \Sl\Model\Identity\Dataset\Simple();
            $ds->setOptions(array(
                'order' => array(
                    'field' => $fieldset->getField('id'),
                    'dir' => 'desc',
                ),
                'offset' => 0,
                'limit' => 1,
            ))->setFieldset($fieldset);
            $ds = $this->fetchDataset($ds);
            $data = $ds->getData();
            switch(count($data)) {
                case 0:
                    return null;
                case 1:
                    return $this->create(current($data));
                default:
                    throw new \Exception('More than 1 instance found. '.__METHOD__);
            }
        } catch(\Exception $e) {
            echo $e->getMessage();
            die;
        }
    }
    
    protected function _model() {
        return \Sl_Model_Factory::object($this);
    }
}

?>
