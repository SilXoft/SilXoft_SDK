<?php
namespace Sl\Model\DbTable;

use Sl\Model\Identity\Fieldset\Filter as FieldFilter;

use Sl\Model\Identity\Field;
use Sl\Model\Identity\Fieldset;

abstract class DbTable extends \Zend_Db_Table_Abstract {
    
    protected $_joined_names = array();
    protected $_primaries = array();
    
    protected $_model;
    protected $_module;
    
    const GROUPED_SEPARATOR = ', ';
    
    /**
     * Constructor.
     *
     * Supported params for $config are:
     * - db              = user-supplied instance of database connector,
     *                     or key name of registry instance.
     * - name            = table name.
     * - primary         = string or array of primary key(s).
     * - rowClass        = row class name.
     * - rowsetClass     = rowset class name.
     * - referenceMap    = array structure to declare relationship
     *                     to parent tables.
     * - dependentTables = array of child tables.
     * - metadataCache   = cache for information from adapter describeTable().
     *
     * @param  mixed $config Array of user-specified config options, or just the Db Adapter.
     * @return void
     */
    public function __construct($config = array())
    {
        if (strlen($this->_name)){
            $this->_cleanJoinNames();
        }    
        parent::__construct($config);
    }
    
	/** Повертає кількість елементів з заданими фільтрами
	 * 
	 */
	public function getCountByFields(array $where, $id = null ){
		
		$select = $this->select();
        
        $select ->from($this, array(new \Zend_Db_Expr("COUNT(id) AS count")));
        
		foreach ($where as $field => $value){
			$select->where($field,$value);
		}
        
		
		$select->where('active = ?',1);
		
		
		if ($id){
			$select->where('id <> ?',$id);
			
		}
		
        return $this->getAdapter()->fetchOne($select, array(), \Zend_Db::FETCH_ASSOC);
	}
	
    /**
     * Вибирає дочірні елементи від self-relation
     * 
     * @param  \Sl_Model_Abstract $object - object
     * @param  \Sl\Modulerelation\Modulerelation $relation - self-relation
     * @param  (bool) $count_children - count of children
     * @return $rows
     */
    public function fetchByParent(\Sl_Model_Abstract $object, \Sl\Modulerelation\Modulerelation $relation, $count_children = false) {
        $select = $this->getAdapter()->select();
        $select -> from ($this->_name, array('*'));
        
        $mainName = $depName = get_class($this);
        $interTable = $relation->getDbTable();
        $dest_class=get_class($object);
        
        $depTable = $relation->getDependedTable($dest_class);
        
        $references = $relation->findSortedReferences($dest_class);
        $reference = array_shift($references);
        $interName = $this->_getUniqueJoinName($interTable->info('name'));
        $depName = $this->_getUniqueJoinName($depTable->info('name'), $relation);
        $select->joinLeft(array($interName=>$interTable->info('name')), $depTable->info('name').'.'.$reference['refColums'].' = '.$interName.'.'.$reference['columns'], array());
        
        //$reference = $interTable->getReference(get_class($depTable));
        $reference = array_shift($references);
        
        $select->joinLeft(array($depName=>$depTable->info('name')), $depName.'.'.$reference['refColums'].' = '.$interName.'.'.$reference['columns'], array());
        
        if ($object->getId()){
            $select->where($depName.'.'.$reference['refColums'].' = ? ',$object->getId());    
        } else {
            $select->where($depName.'.'.$reference['refColums'].' is null');
        }
        
        $references = array_reverse($relation->findSortedReferences($dest_class));
        $reference = array_shift($references);
        
        if ($count_children){
            $reverse_relation = \Sl_Modulerelation_Manager::invertRelation($relation);
            $interName2 = $this->_getUniqueJoinName($interTable->info('name'));
            $depName2 = $this->_getUniqueJoinName($depTable->info('name'), $reverse_relation);
            $select->joinLeft(array($interName2=>$interTable->info('name')), $depTable->info('name').'.'.$reference['refColums'].' = '.$interName2.'.'.$reference['columns'], array());
            
            $reference = array_shift($references);
            
            $select->joinLeft(array($depName2=>$depTable->info('name')), $depName2.'.'.$reference['refColums'].' = '.$interName2.'.'.$reference['columns'], array('COUNT('.$depName2.'.id) as children'));
            $select -> group($this->_name.'.'.(is_array($this->_primary)?current($this->_primary):$this->_primary));
        }                

        $this->_cleanJoinNames();
        //echo $select; die($object->getId());
        //return  $this->getAdapter()->fetchAll($select);
        return  (array) $this->getAdapter()->fetchAll($select, array(), \Zend_Db::FETCH_ASSOC);
        
        
    }
    
	
    /**
     * Заполняет "Идентити" информацией из БД
     * 
     * @param \Sl\Model\Identity\Identity $identity
     * @return \Sl\Model\Identity\Identity
     */
    public function fetchAllExtended(\Sl\Model\Identity\Identity $identity) {
        $select = $this->getAdapter()->select();
        \Sl\Service\Benchmark::save('**************************');
        \Sl\Service\Benchmark::save('start build query');
        $this->_buidFrom($select, $identity);

	\Sl\Service\Benchmark::save('after from query');
        $primary_key = $this->_getIdentityPrimaryKey($identity);
        

        
        $this->_buildJoins($select, $identity);
        \Sl\Service\Benchmark::save('after joins query');
        $select_count = clone $select;

        $this->_buildWhere($select, $identity);
        
        \Sl\Service\Benchmark::save('after where query');
        
        \Sl_Event_Manager::trigger(new \Sl\Event\Table('beforeQuery', array('query' => $select, 'model' => \Sl_Model_Factory::object($identity))));
        
        \Sl\Service\Benchmark::save('after event trigger query');
        
        $select_filtered = clone $select;

        $this->_buildOrders($select, $identity);
        
        \Sl\Service\Benchmark::save('after order query');
        
        $this->_rebuildCountWhere($select_count, $identity);
        \Sl\Service\Benchmark::save('after rebild count query');
        
        $this->_rebuildCountWhere($select_filtered, $identity);
        \Sl\Service\Benchmark::save('after rebild filter query');
        
        $select -> group($identity->getTable()->info('name').'.'.$primary_key);
	$select -> limit($identity->getLimit(),$identity->getOffset());
	
        \Sl\Service\Benchmark::save('just before query');
        $result = (array) $this->getAdapter()->fetchAll($select, array(), \Zend_Db::FETCH_ASSOC);
        \Sl\Service\Benchmark::save('just after query');
        \Sl\Service\Benchmark::save('**********************');
        
        $identity->setSqlSource($select.'');
        $identity->setRawData($result);
        $identity->setTotalCount($this->getAdapter()->fetchOne($select_count));
        $identity->setFilteredCount($this->getAdapter()->fetchOne($select_filtered));
        
        $this->_cleanJoinNames();
        return $identity;
    }
    
    /**
     * Наполняет объект запроса "FROM"-данными
     * 
     * @param \Zend_Db_Select $select запрос
     * @param \Sl\Model\Identity\Identity $identity
     */
    protected function _buidFrom(\Zend_Db_Select &$select, \Sl\Model\Identity\Identity $identity) {
        $cols = $identity->getObjectFields();
        if (!in_array('id',$cols)) array_unshift($cols, 'id');
        if (!in_array('archived',$cols)) $cols[] = 'archived';
        $select ->from($identity->getTable()->info('name'), $cols); 
    }
    
    /**
     * Наполняет объект запроса "JOIN"-ами
     * 
     * @param \Zend_Db_Select $select запрос
     * @param \Sl\Model\Identity\Identity $identity
     */
    protected function _buildJoins(\Zend_Db_Select &$select, \Sl\Model\Identity\Identity $mainIdentity) {
        
        	
        foreach($mainIdentity->getRelations() as $rel) {
            //if ($rel->isAvailable()) 
            //echo $rel->getName().': '.($mainIdentity->isRequired($rel)?'required':'not required')."\r\n";
            if($mainIdentity->isRequired($rel)) {
                $this->_buildJoin($select, $mainIdentity, $rel);
            }
        }
		$this->_cleanJoinNames();
		
    }
    
    /**
     * Добавляет к объекту запроса связь
     * 
     * @param \Zend_Db_Select $select запрос
     * @param \Sl\Model\Identity\Identity $identity
     */
    protected function _buildJoin(\Zend_Db_Select &$select, \Sl\Model\Identity\Identity $mainIdentity, \Sl\Model\Identity\Identity $dependentIdentity) {
            
        
            
        $mainTable = $mainIdentity->getTable();
        
        $interTable = $dependentIdentity->getInterTable();
        $depTable = $dependentIdentity->getTable();
       
        //$reference = $interTable->getReference(get_class($mainTable));
        $relation = $dependentIdentity->getModuleRelation();
        
        $references = $relation->findSortedReferences(get_class(\Sl_Model_Factory::object($mainIdentity)));
        //print_R(array($references,get_class($main), $reference));
        
        $reference = array_shift($references);
        
        $interName = $this->_getUniqueJoinName($interTable->info('name'));
        $depName = $this->_getUniqueJoinName($depTable->info('name'), $relation);
		 
        $select->joinLeft(array($interName=>$interTable->info('name')), $mainTable->info('name').'.'.$reference['refColums'].' = '.$interName.'.'.$reference['columns'], array());
        
        //$reference = $interTable->getReference(get_class($depTable));
        $reference = array_shift($references);
        
        $prepared_cols = $mainIdentity->prepareColumnsArray($dependentIdentity->getName());
		
		
        foreach($prepared_cols as &$v) {
            $v = preg_replace('/%GROUP\{(.+?)\}%/', 'GROUP_CONCAT(DISTINCT('.$depName.'.$1) SEPARATOR ", " ) ', $v);
        }
        
        if(!$mainIdentity->isRequired($dependentIdentity)) {
            $prepared_cols = array();
        }
        //print_r(array($select.'',$interName, array($depName=>$depTable->info('name')), $depName.'.'.$reference['refColums'].' = '.$interName.'.'.$reference['columns'].' AND '.$depName.'.active = 1', $prepared_cols));
        $select->joinLeft(array($depName=>$depTable->info('name')), $depName.'.'.$reference['refColums'].' = '.$interName.'.'.$reference['columns'].' AND '.$depName.'.active = 1', $prepared_cols);
		
		
    }
    
    /**
     * Наполняет объект запроса "ORDER"-данными
     * 
     * @param \Zend_Db_Select $select запрос
     * @param \Sl\Model\Identity\Identity $identity
     */
    protected function _buildOrders(\Zend_Db_Select &$select, \Sl\Model\Identity\Identity $mainIdentity) {
        $sorted = false;
        if($mainIdentity->getSort()) {
            $sorted = true;
            $select->order($mainIdentity->getTable()->info('name').'.'.$mainIdentity->getSort()->getSortString());
        }
        
        foreach($mainIdentity->getRelations() as $rel) {
            if($rel->getSort()) {
                $interTable = $rel->getInterTable();
	        $depTable = $rel->getTable();
	        
	        $interName = $this->_getUniqueJoinName($interTable->info('name'));
	        $depName = $this->_getUniqueJoinName($depTable->info('name'), $rel->getModuleRelation());
                
                $sorted = true;
                $select->order($depName.'.'.$rel->getSort()->getSortString());
            }
        }
  
        if(!$sorted) {
            $select->order($mainIdentity->getTable()->info('name').'.id desc');
        }
    }
    
    /**
     * Наполняет объект запроса "WHERE"-данными
     * 
     * @param \Zend_Db_Select $select запрос
     * @param \Sl\Model\Identity\Identity $identity
     */
    protected function _buildWhere(\Zend_Db_Select &$select, \Sl\Model\Identity\Identity $mainIdentity) {
    	    
            
    	$where_method = 'where';
        if(true === $mainIdentity->getOrSearch()) {
            $where_method = 'orWhere';
        }
        $user = \Zend_Auth::getInstance()->getIdentity();
        
        foreach($mainIdentity->getRelations() as $rel) {
           
		    $interTable = $rel->getInterTable();
	        $depTable = $rel->getTable();
	        
	        $interName = $this->_getUniqueJoinName($interTable->info('name'));
	        $depName = $this->_getUniqueJoinName($depTable->info('name'), $rel->getModuleRelation());
            
            foreach($rel->getComps() as $comp) {
                $cust_where_method = $where_method;
              
                if($comp['operator'] == 'in') {
                    $extra = '';
                    if(false !== ($ind = array_search('null', $comp['value']))) {
                        $extra = $depName.'.'.$comp['name'].' IS NULL ';
                        unset($comp['value'][$ind]);
                        
                    }
                    if(count($comp['value'])) {
                        $select->$cust_where_method($depName.'.'.$comp['name'].' in(?)'.(strlen($extra)?' OR '.$extra:''), $comp['value']);    
                     } else {
                        $select->$cust_where_method($extra); 
                     }
                    
                } elseif($comp['operator'] == 'nin') {
                    $extra = '';
                    if(false !== ($ind = array_search('null', $comp['value']))) {
                        $extra = $depName.'.'.$comp['name'].' NOT IS NULL ';
                        unset($comp['value'][$ind]);
                    }
                    
                    if(count($comp['value'])) {
                       $select->$cust_where_method($depName.'.'.$comp['name'].' not in(?)'.(strlen($extra)?' OR '.$extra:''), $comp['value']);    
                     } else {
                        $select->$cust_where_method($extra); 
                     }
                    
                    
                } elseif($comp['operator'] == 'isnull') {
                    if($comp['value']) {
                        $comp_str = 'IS NULL';
                    } else {
                        $comp_str = 'IS NOT NULL';
                    }
                    $select->$cust_where_method($depName.'.'.$comp['name'].' '.$comp_str);
                } elseif($comp['operator'] == 'between') {
                    if($comp['value'][0] && $comp['value'][1]) {
                        $select->$cust_where_method('CAST('.$rel->getTable()->info('name').'.'.$comp['name'].' AS DATE) between \''.$comp['value'][0].'\' and \''.$comp['value'][1].'\'');
                    } elseif($comp['value'][0]) {
                        $select->$cust_where_method($rel->getTable()->info('name').'.'.$comp['name'].' > ?', $comp['value'][0]);
                    } elseif($comp['value'][1]) {
                        $select->$cust_where_method($rel->getTable()->info('name').'.'.$comp['name'].' < ?', $comp['value'][1]);
                    }
                } elseif($comp['operator'] == 'useor') {
                    $select->$cust_where_method($rel->getTable()->info('name').'.'.$comp['name'].' in (?)', $comp['value']);
                    $tmp_rel = $mainIdentity->getRelation($comp['related']);
                    if($tmp_rel) {
                        $select->orWhere($tmp_rel->getTable()->info('name').'.'.current($comp['name2']).' in (?)', $comp['value2']);
                    }
                } else {
                    $collate = '';
                    if(is_string($comp['value'])) {
                        $collate = 'COLLATE utf8_unicode_ci';
                    }
                    
                    $dep_name = $this->_getUniqueJoinName($rel->getTable()->info('name'), $rel->getModuleRelation());
                    $select->$cust_where_method($dep_name.'.'.$comp['name'].' '.$comp['operator'].' ? '.$collate, $comp['value']);
                }
            }
			
			

        }
        
        $this->_cleanJoinNames();
		
        foreach($mainIdentity->getComps() as $comp) {
            if($comp['operator'] == 'in') {
                $select->$where_method($mainIdentity->getTable()->info('name').'.'.$comp['name'].' in(?)', $comp['value']);
            } elseif($comp['operator'] == 'nin') {
                $select->$where_method($mainIdentity->getTable()->info('name').'.'.$comp['name'].' not in(?)', $comp['value']);
            } elseif($comp['operator'] == 'isnull') {
                if($comp['value']) {
                    $comp_str = 'IS NULL';
                } else {
                    $comp_str = 'IS NOT NULL';
                }
                $select->$where_method($mainIdentity->getTable()->info('name').'.'.$comp['name'].' '.$comp_str);
            } elseif($comp['operator'] == 'between') {
                if($comp['value'][0] && $comp['value'][1]) {
                    $select->$where_method('CAST('.$mainIdentity->getTable()->info('name').'.'.$comp['name'].' AS date) between \''.$comp['value'][0].'\' and \''.$comp['value'][1].'\'');
                } elseif($comp['value'][0]) {
                    $select->$where_method($mainIdentity->getTable()->info('name').'.'.$comp['name'].' > ?', $comp['value'][0]);
                } elseif($comp['value'][1]) {
                    $select->$where_method($mainIdentity->getTable()->info('name').'.'.$comp['name'].' < ?', $comp['value'][1]);
                }
            } else {
                $collate = '';
                if(is_string($comp['value'])) {
                    $collate = 'COLLATE utf8_unicode_ci';
                }
                $select->$where_method($mainIdentity->getTable()->info('name').'.'.$comp['name'].' '.$comp['operator'].' ? '.$collate, $comp['value']);
            }
        }
		
		if ($mainIdentity->justActive()){
				
			$subquery = $select->getPart(\Zend_Db_Select::WHERE);
			
			
			$select ->reset(\Zend_Db_Select::WHERE);
			
			$select ->where($mainIdentity->getTable()->info('name').'.active = 1');
			if (count($subquery)) $select ->where(implode(' ',$subquery));	
		}
                
                if ( $mainIdentity->justExtend() || is_null($mainIdentity->justExtend() )){

                        $subquery = $select->getPart(\Zend_Db_Select::WHERE);						
			$select ->reset(\Zend_Db_Select::WHERE);			
                        if(is_null($mainIdentity->justExtend() )){
			$select ->where($mainIdentity->getTable()->info('name').'.extend IS NULL or '.$mainIdentity->getTable()->info('name').'.extend =""');                        
                        }                        
                        elseif($mainIdentity->justExtend()) $select ->where($mainIdentity->getTable()->info('name').'.extend = "'.$mainIdentity->justExtend().'"');                        
			
                        if (count($subquery)) $select ->where(implode(' ',$subquery));	
		}                
		
        if($archived = $mainIdentity->setArchived()) {
            switch($archived) {
                case '1':
                    $select->where($mainIdentity->getTable()->info('name').'.archived = 1');
                    break;
                case '-1':
                default:
                    $select->where($mainIdentity->getTable()->info('name').'.archived <> 1 OR '.$mainIdentity->getTable()->info('name').'.archived IS NULL');
                    break;
            }
        }

    }
    
    /**
     * Возвращает PRIMARY KEY основной таблицы "Идентити"
     * 
     * @param \Sl\Model\Identity\Identity $identity
     * @return string PRIMARY KEY
     */
    protected function _getIdentityPrimaryKey(\Sl\Model\Identity\Identity $identity) {
        $table_name = $identity->getTable()->info('name');
        if(!isset($this->_primaries[$table_name])) {
            $keys = $identity->getTable()->info(\Zend_Db_Table::PRIMARY);
            return $this->_primaries[$table_name] = array_shift($keys);
        }
        return $this->_primaries[$table_name];
    }
    
    /**
     * Переопределение "FROM" условия для выборки кол-ва записей
     * 
     * @param \Zend_Db_Select $select
     * @param \Sl\Model\Identity\Identity $identity
     */
    protected function _rebuildCountWhere(\Zend_Db_Select &$select, \Sl\Model\Identity\Identity $identity) {
        $select ->reset(\Zend_Db_Select::COLUMNS)
                ->columns(array('c'=>'count(distinct('.$identity->getTable()->info('name').'.'.$this->_getIdentityPrimaryKey($identity).'))'))
				->where ($identity->getTable()->info('name').'.active = 1');
    }
    
    /**
     * Формирует уникальные имена для таблиц связей
     * 
     * @param string $name Название таблицы в БД
     * @return string уникальное имя
     */
    protected function _getUniqueJoinName($name, \Sl\Modulerelation\Modulerelation $relation = null) {
        if(!is_null($relation)) {
            $name = $name.$relation->getName();
            if(!array_key_exists($name, $this->_joined_names)) {
                $this->_joined_names[$name] = $relation->getName();
            }
            return $this->_joined_names[$name];
        } else {
            if(array_key_exists($name, $this->_joined_names)) {
                $new_name = $name;
                $matches = array();
                if(preg_match('/(.+)_(\d+)$/', $this->_joined_names[$name], $matches)) {
                    $new_name = $matches[1].'_'.(++$matches[2]);
                } else {
                    $new_name = $name.'_2';
                }
                return $this->_joined_names[$name] = $new_name;
            }
        }
        return $this->_joined_names[$name] = $name;
    }
    
    /**
     * Очистка данных об уникальных именах таблиц связей
     */
    protected function _cleanJoinNames() {
        
        $this->_joined_names = strlen($this->_name)? array($this->_name=>$this->_name): array();
        
    }
    
    protected function _buildInnerJoin(\Zend_Db_Select $select, \Sl_Model_Abstract $main, \Sl\Modulerelation\Modulerelation $mainRelation, $params = array(), $join_table = false) {
        
        $mainTable = $mainRelation->getDependedTable(get_class($main));
        $interTable = $mainRelation->getDbTable();
        $dest_class=get_class(\Sl_Model_Factory::object($mainTable));
        $depTable = $mainRelation->getDependedTable($dest_class);
        //$reference = $interTable->getReference(get_class($mainTable));
        $references = $mainRelation->findSortedReferences($dest_class);
        //print_R(array($references,get_class($main), $reference));
        $reference = array_shift($references);
        
        $interName = $this->_getUniqueJoinName($interTable->info('name'));
        $depName = $this->_getUniqueJoinName($depTable->info('name'), $mainRelation);
        
        $mainTableName = $mainTable->info('name');
        
        $join_table = !$join_table?key($select->getPart(\Zend_Db_Select::FROM)):strtolower($join_table);
        
        if($mainTableName != $join_table) {
            $mainTableName = $join_table;
           
        }
        
        $select->join(array($interName=>$interTable->info('name')), $mainTableName.'.'.$reference['refColums'].' = '.$interName.'.'.$reference['columns'], array());
        
        //$reference = $interTable->getReference(get_class($depTable));
        $reference = array_shift($references);
        
        $select->join(array($depName=>$depTable->info('name')), $depName.'.'.$reference['refColums'].' = '.$interName.'.'.$reference['columns'], $params);
        //echo $select;
        return $select;
    }
    
    public function fetchListValues($fieldname) {
        $select = $this->getAdapter()->select();
        
        $select ->from($this->_name, array('k' => 'distinct('.$fieldname.')', 'v' => $fieldname));
        
        return $this->getAdapter()->fetchCol($select);
    }
    
    public function findRelated($object_id, \Sl\Modulerelation\Modulerelation $relation, $active = true) {
        $select = $this->getAdapter()->select();
        

        $target_object = \Sl_Model_Factory::object($this);
        $dest_object = $relation->getRelatedObject($target_object);
    
        
        $mainTable = $relation->getDependedTable(get_class($dest_object));
        $interTable = $relation->getDbTable();
        $depTable = $relation->getDependedTable(get_class($target_object));
 
        $references = $relation->findSortedReferences(get_class($target_object));
        $select ->from($this->_name, array());
         /* if($object_id==3){            
           
            echo $select;
            var_dump(get_class($mainTable));
            var_dump(get_class($interTable));
            var_dump(get_class($depTable));
           die;
        }*/    
        $reference = $interTable->getReference(get_class($mainTable));
        $reference = array_shift($references);

        $interName = $this->_getUniqueJoinName($interTable->info('name'));
        $depName = $this->_getUniqueJoinName($depTable->info('name'));

        
        $select->join(array($interName=>$interTable->info('name')), $mainTable->info('name').'.'.$reference['refColums'].' = '.$interName.'.'.$reference['columns'], array());


        //$reference = $interTable->getReference(get_class($depTable));
        $reference = array_shift($references);
        
        $select->join(array($depName=>$depTable->info('name')), $depName.'.'.$reference['refColums'].' = '.$interName.'.'.$reference['columns'], array('*'));

        $select ->where($this->_name.'.id = ?', $object_id);
       //   if($object_id==3 ){            
       //    var_dump(get_class($dest_object));
        //echo $select;
           //die;
     //   }   
               
        if ($active){
           
            $select ->where($depName.'.active > 0');
        }
        $this->_cleanJoinNames();
        //echo $select; //die('123');
        return  $this->getAdapter()->fetchAll($select);
    }
    
    public function countRelated($model_id, \Sl\Modulerelation\Modulerelation $relation) {
        $base_model = \Sl_Model_Factory::object($this);
        
        $select = $relation->getDbTable()->getAdapter()->select();
        
        $base_reference = $relation->findRefetenceArray(get_class($base_model));
        $dep_reference = $relation->findRefetenceArray(get_class($relation->getRelatedObject($base_model)));
        
        $select ->from(array('bt' => $relation->getDbTable()->info(\Zend_Db_Table::NAME)), array('c' => 'count(bt.'.$dep_reference['columns'].')'))
                ->join(array('dt' => $relation->getDependedTable(get_class($base_model))->info(\Zend_Db_Table::NAME)), 'dt.id = bt.'.$dep_reference['columns'])
                ->where('bt.'.$base_reference['columns'].' = ?', $model_id)
                ->where('dt.active = 1')
                ;
        //echo "\r\n".$select."\r\n";die;
        return $relation->getDbTable()->getAdapter()->fetchOne($select);
    }
    
    /**
     * 
     * @param \Sl\Model\Identity\Fieldset $fieldset
     * @return \Sl\Model\Identity\Dataset
     */
    public function fetchDataset(\Sl\Model\Identity\Dataset $dataset) {
        //$dataset = \Sl\Model\Identity\Dataset\Factory::build();
        //$dataset->setFieldset($fieldset);
        $fieldset = $dataset->getFieldset();
        
        $select = $this->getAdapter()->select();
        
        $select = $this->_nBuildFrom($select, $fieldset);
        
        $select = $this->_nBuildJoins($select, $fieldset);
        
        $select_count = clone $select;
        
        $select = $this->_nBuildWhere($select, $fieldset);
        
        $select_filtered = clone $select;
        
        $select = $this->_nBuildGroup($select, $fieldset);
        $this->_nRebuildCountWhere($select_count, $fieldset);
        $this->_nRebuildCountWhere($select_filtered, $fieldset);
        
        $select = $this->_nBuildLimit($select, $dataset);
        $select = $this->_nBuildOrder($select, $dataset);
        //echo $select;die;
        $dataset->addOption('sql_source', $select.'');
        try {
            $dataset->setData($this->getAdapter()->fetchAll($select, array(), \Zend_Db::FETCH_ASSOC));
        } catch(\Exception $e) {
            print_r(array(
                $e->getMessage(),
                $select.'',
            ));die;
        }
        $dataset->addOptions(array(
            'total_count' => $this->getAdapter()->fetchOne($select_count),
            'filtered_count' => $this->getAdapter()->fetchOne($select_filtered),
        ));
        return $dataset;
    }
    
    protected function _nBuildFrom(\Zend_Db_Select $select, \Sl\Model\Identity\Fieldset $fieldset) {
        // Видимые и (связанные или свои)
        $aFields = array();
        foreach($fieldset->getFields('from') as $field) {
            if(!$field->isRelated()) {
                // Поле модели. Просто транслируем
                $aFields[] = $field->getName();
            } else {
                // Связанное поле
                if($grouped = $field->getOption('grouped')) {
                    $separator = self::GROUPED_SEPARATOR;
                    $distinct = true;
                    if(is_array($grouped)) {
                        if(isset($grouped['separator'])) {
                            $separator = $grouped['separator'];
                        }
                        if(isset($grouped['distinct'])) {
                            $distinct = (bool) $grouped['distinct'];
                        }
                    }
                    $alias = self::buildDestinationAlias($field);
                    if($distinct) {
                        $alias = 'DISTINCT('.$alias.')';
                    }
                    $aFields[$field->getName()] = 'GROUP_CONCAT('.$alias.' SEPARATOR "'.$separator.'")';
                } else {
                    $aFields[$field->getName()] = self::buildDestinationAlias($field);
                }
            }
        }
        return $select->from(array($this->_name), $aFields); 
    }
    
    protected function _nBuildJoins(\Zend_Db_Select $select, \Sl\Model\Identity\Fieldset $fieldset) {
        // Строим только те, что нужны
        $field_aliases = array();
        foreach($fieldset->getFields() as $field) {
            if($field->hasRole('from') || $field->hasRole('compare')) {
                if(false === array_search($field->relationAlias(), $field_aliases)) {
                    $field_aliases[] = $field->relationAlias();
                    $select = $this->_nBuildJoin($select, $field);
                }
            }
        }
        return $select; 
    }
    
    protected function _nBuildJoin(\Zend_Db_Select $select, \Sl\Model\Identity\Field $field) {
        if(!$field->isRelated()) {
            return $select;
        }
        $sAlias = $field->relationAlias();
        
        $baseModel = $field->getModel();
        foreach(\Sl\Service\Alias::describeAlias($sAlias, $field->getModel()) as $relname) {
 
            $relation = \Sl_Modulerelation_Manager::getRelations($baseModel, $relname);
            $relatedModel = $relation->getRelatedObject($baseModel);
            // Тут можно строить запрос
            $prefix = self::aliasPrefix($sAlias);
            //*********************************************
            $mainReferenceData = $relation->findRefetenceArray(get_class($baseModel));
            $relatedReferenceData = $relation->findRefetenceArray(get_class($relatedModel));
            // Информация о таблице связей
            $interTableName = $relation->getIntersectionDbTable()->info('name');
            $interTableAlias = $prefix.$interTableName;
            $interTableNameArray = array($interTableAlias => $interTableName);
            $interTableBaseColumnAlias = $mainReferenceData['columns'];
            $interTableDestColumnAlias = $relatedReferenceData['columns'];
            // Информация о базовой таблице
            $baseTableName = $relation->getDependedTable(get_class($relatedModel))->info('name');
            if(!($field->getModel() instanceof $baseModel)) {
                $baseTableName = $prefix.$baseTableName;
            }
            $baseTableInterColumnAlias = $mainReferenceData['refColums'];
            // Информация о зависимой таблице
            $destTableName = $relation->getDependedTable(get_class($baseModel))->info('name');
            $destTableAlias = $prefix.$destTableName;
            $destTableNameArray = array($destTableAlias => $destTableName);
            $destTableInterColumnAlias = $relatedReferenceData['refColums'];
            // Вяжем со служебной таблицей
            $select->joinLeft($interTableNameArray, implode(' = ', array(
                $baseTableName.'.'.$baseTableInterColumnAlias,
                key($interTableNameArray).'.'.$interTableBaseColumnAlias
            )), array(''));
            // Вяжем зависимую к служебной
            $select->joinLeft($destTableNameArray, implode(' = ', array(
                key($interTableNameArray).'.'.$interTableDestColumnAlias,
                key($destTableNameArray).'.'.$destTableInterColumnAlias
            )), array(''));
            //**********************************************
            $baseModel = $relation->getRelatedObject($baseModel);
        }
        return $select;
    }
    
    protected function _nBuildWhere(\Zend_Db_Select $select, \Sl\Model\Identity\Fieldset $fieldset) {
        if(!$fieldset->getComp()->getEmpty()) {
            $select->where((string) $fieldset->getComp());
        }
        return $select;
    }
    
    protected function _nBuildLimit(\Zend_Db_Select $select, \Sl\Model\Identity\Dataset $dataset) {
        return $select->limit($dataset->getOption('limit'), $dataset->getOption('offset'));
    }
    
    protected function _nBuildOrder(\Zend_Db_Select $select, \Sl\Model\Identity\Dataset $dataset) {
        $order_data = $dataset->getOption('order');
        $dir = isset($order_data['dir'])?$order_data['dir']:'desc';
        
        try {
            $field = $order_data['field'];
            if(!($field instanceof Field)) {
                throw new \Exception('');
            }
            if(!$field || !$field->hasRole('from')) {
                $field = current($dataset->getFieldset()->getFields('from'));
            }
            if($field->isRelated()) {
                $select->order(self::buildDestinationAlias($field).' '.$dir);
            } else {
                $select->order($this->info('name').'.'.$field->getName().' '.$dir);
            }
        } catch (\Exception $e) {
            $select->order($this->info('name').'.id '.$dir);
        }
        return $select;
    }
    
    protected function _nBuildGroup(\Zend_Db_Select $select, \Sl\Model\Identity\Fieldset $fieldset) {
        return $select->group($this->info('name').'.'.$fieldset->getField('id')->getName());
    }
    
    /**
     * Переопределение "FROM" условия для выборки кол-ва записей
     * 
     * @param \Zend_Db_Select $select
     * @param \Sl\Model\Identity\Identity $identity
     */
    protected function _nRebuildCountWhere(\Zend_Db_Select &$select, \Sl\Model\Identity\Fieldset $fieldset) {
        $select ->reset(\Zend_Db_Select::COLUMNS)
                ->columns(array('c'=>'count(distinct('.$this->info('name').'.id))'))
				->where($this->info('name').'.active = 1');
    }
    
    public static function buildDestinationAlias(Field $field) {
        if(!$field->isRelated()) {
            return \Sl_Model_Factory::dbTable($field->getModel())->info('name').'.'.$field->cleanName();
        }
        $data = \Sl\Service\Alias::describeAlias($field->relationAlias(), $field->getModel(), true);
        $last = array_pop($data);
        list($module, $model) = explode('.', $last['dest']);
        if(!$model || !$module) {
            throw new \Exception('Can\'t determine destination table. '.__METHOD__);
        }
        return self::aliasPrefix($field->relationAlias()).\Sl_Model_Factory::dbTable($model, $module)->info('name').'.'.$field->cleanName();
    }
    
    public function getBaseModel() {
        if(!isset($this->_model)) {
            $this->_model = \Sl_Model_Factory::object($this);
        }
        return $this->_model;
    }
    
    public function getModule() {
        if(!isset($this->_module)) {
            return \Sl_Module_Manager::getInstance()->getModule($this->getBaseModel()->findModuleName());
        }
        return $this->_module;
    }
    
    public static function cleanAlias($alias) {
        return str_replace('.', '', $alias);
    }
    
    public static function aliasPrefix($alias) {
        return self::cleanAlias($alias).'__';
    }
    
    public function fetchDistinctValues($fieldname) {
        $select = $this->getAdapter()->select();
        
        $select ->from($this->_name, array(new \Zend_Db_Expr('DISTINCT('.$fieldname.') AS '.$fieldname)))
                ->where('active <> 0')
                ->where('extend like ?', '%|'.\Sl\Service\Helper::getModelAlias(\Sl_Model_Factory::model($this)).'|%')
                ;
        return $this->getAdapter()->fetchCol($select);
    }
}

