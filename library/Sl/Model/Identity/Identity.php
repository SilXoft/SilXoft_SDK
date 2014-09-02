<?php
namespace Sl\Model\Identity;
use Sl\Model\Field as Field;
use Sl\Exception\Identity as Exception;

/**
 * Класс для объяснения Mapper-у/DbTable-у что именно нужно вытащить
 * 
 */
abstract class Identity {
    
    protected $_is_list = true;
    
	/**
     *
     * @var Fieldscalculator
     */
     
	protected $_calculators = array();
    protected $_current_calculator;
	protected $_model_config_titles;
    /**
     *
     * @var Field
     */
    protected $_current_field = null;
    
    /**
     *
     * @var Identity
     */
    protected $_current_relation = null;
    
    const SEP_RELATED = '.';
    
    /**
     *
     * @var Field[]
     */
    protected $_fields = array();
    protected $_columns = array();
    protected $_and = null;
    protected $_enforce = array();
    protected $_limit = 10;
    protected $_offset = 0;
	
	protected $_just_active = true;
        protected $_just_extend=null;




        /**
     *
     * @var Identity[]
     */
    protected $_relations = array();
    
    /**
     *
     * @var \Sl\Model\DbTable\DbTable
     */
    protected $_table;
    
    /**
     *
     * @var \Sl\Model\DbTable\DbTable
     */
    protected $_inter_table;
    
    /**
     *
     * @var \Sl\Modulerelation\Modulerelation
     */
    protected $_modulerelation;
    
    /**
     *
     * @var \Sl_Model_Abstract
     */
    protected $_model;
    
    /**
     * искать ли управляющую связь
     * @var type 
     */
    protected $_handling;
    
    protected $_options = array(
        'enforce' => array(),
        'loadRelations' => true,
        'columns' => array(),
        'handling' => null,
    );
    
    /**
     *
     * @var integer[]
     */
    protected $_selected = array();
    
    /**
     *
     * @var boolean
     */
    protected $_available = true;
    
    /**
     * Строка запроса
     * 
     * @var string
     */
    protected $_sql;
    
    protected $_columns_all = array();
    
    protected $_tatal = 0;
    protected $_filtered = 0;
    protected $_raw_data = array();
    protected $_data = array();
    protected $_data_processed = false;
    
    protected $_use_or = false;
    
    protected $_config;
    
    protected $_translator;
    
    /**
     * Тип запроса относительно архивных записей
     * -1 - только не архивные
     * 1  - только архивные
     * 0  - все
     * 
     * @var string
     */
    protected $_archived;
    
    const GROUP_TPL = '%GROUP{name}%';
    const GET_SIMPLE_NAME = 'first';
    
    protected $_config_options;
    
    const OPTIONS_LISTVIEW = 'listview_options';
    const OPTIONS_EXPORT = 'export_options';
    
    public function __construct($field = null, array $options = array()) {
        $this->setOptions($options);
		
		if ($calculator = \Sl_Calculator_Manager::getCalculator($this)){
			$this->setCalculator($calculator);
		}
        
        $this->setConfigOptionsType(self::OPTIONS_LISTVIEW);
        if($this->getOption('config_type')) {
            $this->setConfigOptionsType($this->getOption('config_type'));
        }
        
        if($this->getOption('use_calculator')) {
            $this->setCurrentCalculator(preg_replace('/_/', "\\", array_shift($this->getOption('use_calculator'))));
        } else {
            $this->setCurrentCalculator(get_class($this->getCalculator()));
        }
		
        $this->_enforce = $this->getOption('enforce');
        if(!is_null($field)) {
            $this->field($field);
        }
        $this->_model = \Sl_Model_Factory::object($this);
        if($this->getOption('loadRelations')) {
            $this->loadRelations();
        }
        if($this->getOption('selected')) {
            $this->setSelected($this->getOption('selected'));
        }
        $this->_handling = $this->getOption('handling');
        $columns = $this->getOption('columns');
		
		
        if(is_array($columns)) {
            if(count($columns)) {
                $this->loadData($columns);
            } else {
                $this->loadData();
            }
        } else {
            if($columns == self::GET_SIMPLE_NAME) {
                $this->loadData(array($this->findNameColumn()));
            } else {
                throw new Exception('Wrong "columns" parameter set. '.__METHOD__);
            }
        }
		
		
		
    }
    
	
	
	public function justActive($just_active = null){
		if (is_null($just_active)){
			return $this->_just_active;
		} else {
			$this->_just_active = $just_active;
			return $this;
		}
	}
	public function justExtend($just_extend = null){
            
		if (is_null($just_extend)){
			return $this->_just_extend;
		} else {
			$this->_just_extend = $just_extend;
			return $this;
		}
	}
        
    public function setArchived($archived = null) {
        if(is_null($archived)) {
            return $this->_archived;
        } else {
            $this->_archived = $archived;
            return $this;
        }
    }
	
    public function setCurrentCalculator($calc) {
        if($this->getCalculator($calc)) {
            $this->_current_calculator = $calc;
        }
    }
    
	protected function setCalculator($calculator) {
		if(is_array($calculator)) {
            foreach($calculator as $calc) {
                if($calc instanceof \Sl_Model_Identity_Interface_Calculator) {
                    $this->addCalculator($calc);
                }
            }
        } elseif($calculator instanceof \Sl_Model_Identity_Interface_Calculator) {
            $this->addCalculator($calculator);
        }
	}
    
    public function cleanCalculators() {
        $this->_calculators = array();
    }
	
	/**
     * 
     * @return \Sl_Model_Identity_Interface_Calculator
     */
	public function getCalculator($calculator = false){
		if(false !== $calculator) {
            return isset($this->_calculators[$calculator])?$this->_calculators[$calculator]:null;
        } elseif($this->_current_calculator) {
            return $this->getCalculator($this->_current_calculator);
        } else {
            return current($this->_calculators);
        }
	}
	
    public function addCalculator(\Sl_Model_Identity_Interface_Calculator $calculator) {
        $this->_calculators[get_class($calculator)] = $calculator;
    }
	
	
    public function loadRelations() {
        $modulerelations = \Sl_Modulerelation_Manager::getRelations($this->_model);
        if($modulerelations) {
            foreach($modulerelations as $mr) {
                $relIdentity = \Sl_Model_Factory::identity($mr->getRelatedObject($this->_model), null, array('loadRelations'=>false))
                                    ->setModuleRelation($mr);
                $this->addRelatedIdentity($relIdentity);
            }
        }
    }
    
    public function setModuleRelation(\Sl\Modulerelation\Modulerelation $rel) {
        $this->_modulerelation = $rel;
        return $this;
    }
    
    public function getModuleRelation() {
        return $this->_modulerelation;
    }
    
    protected function cleanRelations() {
        $this->_relations = array();
        $this->_modulerelations = array();
        return $this;
    }
    
	/**
	 * Повертає оброблені калькулятором заголовки з getObjectFields
	 */
	
	public function getCalculatedObjectFields($extended = false, $as_object = false, $sorted = false, array $calculators = array()){
		$fields = $this->getObjectFields($extended, $as_object, $sorted);
		
		if ($calculator=$this->getCalculator()){
			$fields = $this->getObjectFields(true, true, true);
			
			$fields_copy = $fields_simple =array();
			
			foreach ($fields as $key => $value){
				$fields_simple[intval($key)*100] = is_array($value)?$value['name']:$value;  
				$fields_copy[intval($key)*100] = $value;
			}
			$new_fields = $calculator->getCalculatedColumns($fields_simple);	
			
			if ($as_object && is_array($new_fields)){
				
				foreach ($fields_copy as $key => $value){
					$new_key = array_search($value['name'],$new_fields);	
					if ($new_key!==false){
						$new_fields[$new_key] = $value;
					}
				}
				
				$copy_new_fields = $new_fields;
				foreach ($copy_new_fields as $key => $value){
					if (!is_array($value)){
						$new_fields[$key] = array(
                            'type'=>'text',
                            'name'=>$value,
                            'visible'=> $this->getFieldVisibility($value)!==null?$this->getFieldVisibility($value):1,
                            'calculate' => $this->getFieldCalculate($value),
                            'sortable' => false,
                            'label'=>$this->getFieldTitle($value),
                            'html'=>true
                        );
                    }
				}
			}
			
			$fields = array_values($new_fields);
			
			ksort($fields);
			foreach($fields as $k=>$v) {
                if(!isset($v['sort_order'])) {
                    $fields[$k]['sort_order'] = $k;
                }
            }
		}
		return $fields;
		
	}
	
    /**
     * Получить доступные колонки
     * 
     * @return string[]
     */
    public function getObjectFields($extended = false, $as_object = false, $sorted = false) {
        if(!$extended) {
        	return $this->_columns;
        } else {
            if($as_object) {
                $cache_id = APPLICATION_NAME.'_md5.'.('identity_'.array_pop(explode('\\', get_class($this))).'_fields_'.($sorted?'1':'0'));
                $cache = \Zend_Registry::get('cache')->getBackend();
                
                if($cache->test($cache_id)) {
                   
                    return unserialize($cache->load($cache_id));
                } else {
                    $current_relation = false;
                    $data = array();
                    $sort_base = 1000;
                    $data2 = array();
                    foreach($this->_columns_all as $key=>$colname) {
                        $rel_changed = false;
                        $matches = array();
                        $identity = $this;
                        $rel_title = false;
                        $relation_name='';
                        if(preg_match('/^(.+)\.(.+)$/', $colname, $matches)) {
                            $relation_name = $matches[1];	
                            $identity = $this->getRelation($matches[1]);
                            $rel_title = $identity->getTitle($this->_model, $this);
                            $name = $matches[2];
                        } else {
                            $name = $colname;
                        }
                        if(!$identity->isAvailable()) continue;
                        try {
                            $rel_name = $identity->getName();
                        } catch(Exception $e) {
                            $rel_name = 'base';
                        }
                        if($current_relation != $rel_name) {
                            $current_relation = $rel_name;
                            $rel_changed = true;
                        } else {
                            $rel_changed = false;
                        }

                        $model = \Sl_Model_Factory::object($identity);

                        if($this->isFieldVisible($colname)) {

                            if(false !== ($order = $this->getFieldSort($colname))) {
                                $new_key = $order;
                                $data2[$order] = $colname;
                            } else {
                                $new_key = ($key + $sort_base);
                                $data2[($key + $sort_base)] = $colname;
                            }
                        } else {
                            $new_key = ($key + $sort_base);
                            $data2[($key + $sort_base)] = $colname;
                        }

                        $data[$new_key] = $model->describeField($name);
                        if ($config_title = $this->getFieldTitle($colname)){
                            $data[$new_key]['label'] = $config_title; 
                        }

                        if (isset($config_options->{$colname}) && isset($config_options->{$colname}->label)){
                            $data[$new_key]['label']=$config_options->{$colname}->label;
                        }

                        if($relation_name) {
                            $data[$new_key]['rel_columns'] = 1;
                            $data[$new_key]['rel_name'] = $rel_title;
                            $data[$new_key]['relation'] = $relation_name;
                            $data[$new_key]['is_first'] = 1;//$rel_changed;
                        }
                        $data[$new_key]['name'] = $colname;
                        $data[$new_key]['sortable'] = (int) $this->isFieldSortable($colname);
                        $data[$new_key]['searchable'] = (int) $this->isFieldSearchable($colname);
                        $data[$new_key]['visible'] = (int) $this->isFieldVisible($colname);
                        $data[$new_key]['hidable'] = (int) $this->isFieldHidable($colname);
                        $data[$new_key]['calculate'] = $this->getFieldCalculate($colname);
                        
                        
                        $data[$new_key]['type'] = $this->getFieldType($colname);
                        $data[$new_key]['class'] = $this->getFieldClass($colname);
                        if($this->getIsList()) {
                            $data[$new_key]['list_hidden'] = (int) $this->isFieldListHidden($colname);
                        }
                        if($this->isFieldSearchable($colname)) {
                            $options = $identity->getSelectValues($name);
                            if (count($options)){
                                $data[$new_key]['select'] = (int) $this->isFieldSelect($colname);
                                $data[$new_key]['options'] = $options;								
                            }
                        }
                    }
                    if($sorted) {
                        ksort($data);
                    }
                    $data = array_values($data);
                    foreach($data as $k=>$v) {
                        $data[$k]['sort_order'] = $k;
                    }
                    
                    $cache->save(serialize($data), $cache_id, array('identity'));
                    return $data;
                }
            }
            return $this->_columns_all;
        }
    }
    
    
	
	 public function getSelectValues($colname) {
	 	$object = \Sl_Model_Factory::object($this);
		
		$list_name = $object->ListsAssociations($colname);
		if (!is_array($list_name)){
			$list = \Sl\Service\Lists::getList($list_name);
			$list_updated = array();
			foreach ($list as $value => $name){
				$list_updated[\Sl\Service\Lists::LISTS_SEARCH_KEY_PREFIX.$value] = $name;
			}
		}  
		return is_array($list_updated)?array(null=>'All')+$list_updated:array();
		
    }
	
	
    /**
     * Проверка допустимо ли поле
     * 
     * @param type $name
     * @throws Exception
     */
    public function enforceField($name) {
        if(!in_array($name, $this->_enforce) && !empty($this->_enforce)) {
            $forcelist = implode(', ', $this->_enforce);
            throw new Exception('Поле недопустимо. ('.$forcelist.') '.__METHOD__);
        }
    }
    
    /**
     * Пустой ли объект
     * 
     * @return type
     */
    public function isVoid() {
        return empty($this->_fields);
    }
    
    /**
     * Усианавливает текущее поле
     * 
     * @param type $name
     * @return \Sl\Model\Identity\Identity
     * @throws Exception Поле должно быть доступно. Текущее поле должно быть заполнено.
     */
    public function field($name) {
        if(is_array($name)) {
            reset($name);
            $relation_name = key($name);
            $name = $name[$relation_name];
            if($this->getRelation($relation_name)) {
                return $this->getRelation($relation_name)->field($name);
            } else {
                throw new Exception('No such relation for this identity ("'.$relation_name.'"). '.__METHOD__);
            }
        }
        
        if(!$this->isVoid() && $this->getCurrentField()->isIncomplete()) {
            throw new Exception('Неполное поле. "'.$name.'" '.__METHOD__);
        }
        
        $this->enforceField($name);
        
        if(!isset($this->_fields[$name])) {
        	
            $this->_fields[$name] = new Field($name);
        }
		
        $this->setCurrentField($this->_fields[$name]);
        return $this;
    }
    
    /**
     * Устанавливаем доступные колонки
     * 
     * @param array $columns
     * @return \Sl\Model\Identity\Identity
     */
    public function columns(array $columns) {
        foreach($columns as $column) {
            if(is_array($column)) {
                if($column['type'] == \Sl_Model_Abstract::FIELD_TYPE_GET) {
                    $this->_columns[] = $column['key'];
                }
            } else {
                $this->_columns[] = $column;
            }
        }
        return $this;
    }
    
    /**
     * Устанавливает размер выборки
     * 
     * @param integer $limit
     * @return \Sl\Model\Identity\Identity
     */
    public function limit($limit) {
        $this->_limit = (int) $limit;
        return $this;
    }
    
	
	/**
     * Взвращает размер выборки
     * 
     * 
     * @return int $limit
     */
    public function getLimit() {
        return $this->_limit;
    }
  
  	/**
     * Взвращает смещение выборки
     * 
     * 
     * @return int $offset
     */
    public function getOffset() {
        return $this->_offset;
    }  
	
    /**
     * Устанавливает смещение
     * 
     * @param integer $offset
     * @return \Sl\Model\Identity\Identity
     */
    public function offset($offset) {
        $this->_offset = intval($offset);
        return $this;
    }
    
    /**
     * Добавление оператора сравнения к текущему полю
     * 
     * @param mixed $symbol
     * @param mixed $value
     * @return \Sl\Model\Identity\Identity
     * @throws Exception ПОле должно быть определено
     */
    protected function operator($symbol, $value) {
        if($this->isVoid()) {
            throw new Exception('Поле не определено. '.__METHOD__);
        }
        if($this->_current_relation) {
            $this->_current_relation->operator($symbol, $value);
        } else {
            $this->_current_field->addTest($symbol, $value);
        }
        return $this;
    }
    
    public function setAnd($and) {
        $this->_and = $and;
        return $this;
    }
    
    public function getAnd() {
        return $this->_and;
    }
    
    /* Операции сравнения
     *********************************************/
    
    public function useor($value) {
        return $this->operator('useor', $value);
    }
    
    /**
     * Операция сравнения "="
     * 
     * @param mixed $value
     * @return \Sl\Model\Identity\Identity
     */
    public function eq($value) {
        return $this->operator('=', $value);
    }
    
    /**
     * Операция сравнения ">"
     * 
     * @param mixed $value
     * @return \Sl\Model\Identity\Identity
     */
    public function gt($value) {
        return $this->operator('>', $value);
    }
    
    /**
     * Операция сравнения "<"
     * 
     * @param mixed $value
     * @return \Sl\Model\Identity\Identity
     */
    public function lt($value) {
        return $this->operator('<', $value);
    }
    
	    
    /**
     * Операция сравнения ">="
     * 
     * @param mixed $value
     * @return \Sl\Model\Identity\Identity
     */
    public function gte($value) {
        return $this->operator('>=', $value);
    }
    
    /**
     * Операция сравнения "<="
     * 
     * @param mixed $value
     * @return \Sl\Model\Identity\Identity
     */
    public function lte($value) {
        return $this->operator('<=', $value);
    }
    
	
    /**
     * Операция сравнения "LIKE"
     * 
     * @param mixed $value
     * @return \Sl\Model\Identity\Identity
     */
    public function like($value) {
        return $this->operator('like', '%'.$value.'%');
    }
    
    /**
     * Операция сравнения IN
     * 
     * @param integer|string[] $vals
     * @return \Sl\Model\Identity\Identity
     */
    public function in($vals) {
    	 if (!is_array($vals)) $vals = array($vals);	
        return $this->operator('in', $vals);
    }
    
    /**
     * Операция сравнения NOT IN
     * 
     * @param integer|string[] $vals
     * @return \Sl\Model\Identity\Identity
     */
    public function nin($vals) {
        if (!is_array($vals)) $vals = array($vals);	
        return $this->operator('nin', $vals);
    }
    
    public function between($min, $max) {
        return $this->operator('between', array($min, $max));
    }
    
    public function isnull($val) {
        return $this->operator('isnull', $val);
    }
    
    /**
     * Массив сравнений
     * 
     * @param bool $one_level Вернуть 1 уровень. По-умолчанию true
     * @return array
     */
    public function getComps($one_level = true) {
        $comps = array();
        foreach($this->_fields as $field) {
            $comps = array_merge($comps, $field->getComps());
        }
        if(!$one_level) {
            if($this->getRelations()) {
                foreach($this->getRelations() as $rel) {
                    $comps += $rel->getComps();
					
                }
            }
        }
        return $comps;
    }
    
    /**
     * Добавляет связь
     * 
     * @param \Sl\Model\Identity\Identity $identity Подчиненная сущность
     * @return \Sl\Model\Identity\Identity
     */
    public function addRelatedIdentity(Identity $identity) {
        $this->_relations[$identity->getName()] = $identity;
        return $this;
    }
    
    /**
     * Удаляет связь
     * 
     * @param \Sl\Model\Identity\Identity $identity подчиненная сущность
     * @return \Sl\Model\Identity\Identity
     * @throws Exception
     */
    public function removeRelation($identity) {
        if(is_string($identity)) {
            unset($this->_relations[$identity]);
        } elseif($identity instanceof Identity) {
            unset($this->_relations[$identity->getName()]);
        } else {
            throw new Exception('Wrong parameter type. '.__METHOD__);
        }
        return $this;
    }
    
    /**
     * Возвращает связи
     * 
     * @return \Sl\Model\Identity\Identity[]
     */
    public function getRelations() {
        return $this->_relations;
    }
    
    /**
     * Возвращает связь по имени
     * 
     * @param string $name
     * @return \Sl\Model\Identity\Identity
     */
    public function getRelation($name) {
        return isset($this->_relations[$name])?$this->_relations[$name]:null;
    }
    
    /**
     * Возвращает таблицу сущности
     * 
     * @return \Sl\Model\DbTable\DbTable
     */
    public function getTable() {
        if(!isset($this->_table)) {
            $this->_table = \Sl_Model_Factory::dbTable($this->_model);
        }
        return $this->_table;
    }
    
    /**
     * Возвращает связующую таблицу сущности
     * 
     * @return \Sl\Model\DbTable\DbTable
     * @throws Exception Если не установленя связь
     */
    public function getInterTable() {
        if(!isset($this->_modulerelation)) {
            throw new Exception('Modulerelation not set for this identity. '.__METHOD__);
        }
        return $this->_modulerelation->getIntersectionDbTable();
    }
    
    /**
     * Возвращает тип связи
     * 
     * @return integer
     * @throws Exception Если не установленя связь
     */
    public function getType() {
        if(!isset($this->_modulerelation)) {
            throw new Exception('Modulerelation not set for this identity. '.__METHOD__);
        }
        return $this->_modulerelation->getType();
    }
    
    /**
     * Возвращает имя связи
     * 
     * @return string
     * @throws Exception Если не установленя связь
     */
    public function getName() {
        if(!isset($this->_modulerelation)) {
            throw new Exception('Modulerelation not set for this identity. '.__METHOD__);
        }
        return $this->_modulerelation->getName();
    }
    
    /**
     * Возвращает заголовок связи
     * 
     * @return string
     * @throws Exception Если не установленя связь
     */
    public function getTitle(\Sl_Model_Abstract $object = null, \Sl\Model\Identity\Identity $identity = null) {
        if(!isset($this->_modulerelation)) {
            throw new Exception('Modulerelation not set for this identity. '.__METHOD__);
        }
        if($identity && $identity->getFieldTitle($this->getName())) {
            return $identity->getFieldTitle($this->getName());
        }
        return $this->_modulerelation->getTitle($object);
    }
    
	public function getModelConfig(){
		if (!$this->_model_config_titles){
			$model = \Sl_Model_Factory::object($this);
			$config_options = \Sl_Module_Manager::getInstance() -> getModule($model -> findModuleName()) -> section('titles') -> {$model->findModelName()};
			if ($config_options instanceof \Zend_Config){
				$this->_model_config_titles = $config_options;
			} else {
				$this->_model_config_titles = true;
			}
			
		} 
		if($this->_model_config_titles instanceof \Zend_Config){
			return $this->_model_config_titles;
		}
	}
    
    public function _config() {
        if(is_null($this->_config)) {
            $model = \Sl_Model_Factory::object($this);
			/*$config_options = \Sl_Module_Manager::getInstance()
                                    ->getModule($model -> findModuleName())
                                    ->section($this->getConfigOptionsType());*/
            $config_options = \Sl_Module_Manager::getInstance()
                                    ->getCustomConfig($model->findModuleName(),$this->getConfigOptionsType());
            if($this->getRelations()) {
                //print_r($config_options->toArray());die;
            }
            if($this->getConfigOptionsType() == self::OPTIONS_LISTVIEW) { // Опции списка
                if(!$config_options) {
                    $config_options = \Sl_Module_Manager::getInstance()
                                        ->getModule($model -> findModuleName())
                                        ->generateListViewOptions();
                }
                if(!$config_options->{$model->findModelName()}) {
                    $config_options = \Sl_Module_Manager::getInstance()
                                        ->getModule($model -> findModuleName())
                                        ->generateListViewOptions($model);
                }
            } elseif($this->getConfigOptionsType() == self::OPTIONS_EXPORT) { // Опции для экспорта
                if(!$config_options) {
                    $config_options = \Sl_Module_Manager::getInstance()
                                        ->getModule($model -> findModuleName())
                                        ->generateExportOptions();
                }
                if(!$config_options->{$model->findModelName()}) {
                    $config_options = \Sl_Module_Manager::getInstance()
                                        ->getModule($model -> findModuleName())
                                        ->generateExportOptions($model);
                }
            }
            $this->_config = $config_options->{$model->findModelName()}->toArray();
        }
        return $this->_config;
    }
	
    public function getConfigOptionsType() {
        return $this->_config_options;
    }
    
    public function setConfigOptionsType($type) {
        switch($type) {
            case self::OPTIONS_LISTVIEW:
            case self::OPTIONS_EXPORT:
                $this->_config_options = $type;
                $this->_config = null;
                break;
            default:
                throw new \Exception('No such optoins type. '.__METHOD__);
                break;
        }
    }
    
     /**
     * Возвращает тип калькуляции поля в колонке
     * 
     * @return string
     * 
     */
    public function getFieldCalculate($field) {
        /*$config = $this->getModelConfig();
        
        //var_dump($config->{$field}->label);
        //echo $field.''.PHP_EOL;
        if ($config && isset($config->{$field}) && isset($config->{$field}->label) ){
            return $config->{$field}->label;
        }*/
        $config = $this->_config();
        if(isset($config[$field])) {
            return isset($config[$field]['calculate'])?$config[$field]['calculate']:false;
        }
        return false;
    }
    
     /**
     * Возвращает заголовок поля
     * 
     * @return string
     * 
     */
    public function getFieldTitle($field) {
    	/*$config = $this->getModelConfig();
    	
		//var_dump($config->{$field}->label);
		//echo $field.''.PHP_EOL;
        if ($config && isset($config->{$field}) && isset($config->{$field}->label) ){
        	return $config->{$field}->label;
        }*/
        $config = $this->_config();
        if(isset($config[$field])) {
            return isset($config[$field]['label'])?$config[$field]['label']:false;
        }
        return false;
    }
    
    /**
     * Возвращает сортировку поля
     * 
     * @return string
     * 
     */
    public function getFieldSort($field) {
    	$config = $this->_config();
        if(isset($config[$field])) {
            return isset($config[$field]['order'])?$config[$field]['order']:false;
        }
        return false;
    }
    
     public function getFieldType($field) {
        $config = $this->_config();
        if(isset($config[$field])) {
            return isset($config[$field]['type'])?$config[$field]['type']:false;
        }
        return false;
    }
    
    public function getFieldClass($field) {
        $config = $this->_config();
        if(isset($config[$field])) {
            return isset($config[$field]['class'])?$config[$field]['class']:false;
        }
        return false;
    }
    
    public function isFieldVisible($field) {
        $config = $this->_config();
        if(isset($config[$field])) {
            if(isset($config[$field]['visible'])) {
                return (bool) $config[$field]['visible'];
            }
            return true;
        }
        return false;
    }
    
    public function isFieldListHidden($field) {
        $config = $this->_config();
        if(isset($config[$field])) {
            if(isset($config[$field]['list_hidden'])) {
                return (bool) $config[$field]['list_hidden'];
            }
            return false;
        }
        return false;
    }
    
    public function isFieldSearchable($field) {
        $config = $this->_config();
        if(isset($config[$field])) {
            if(isset($config[$field]['searchable'])) {
                return (bool) $config[$field]['searchable'];
            }
            return false;
        }
        return false;
    }
    
    public function isFieldSortable($field) {
        $config = $this->_config();
        if(isset($config[$field])) {
            if(isset($config[$field]['sortable'])) {
                return (bool) $config[$field]['sortable'];
            }
            return false;
        }
        return false;
    }
    
    public function isFieldSelect($field) {
        $config = $this->_config();
        if(isset($config[$field])) {
            if(isset($config[$field]['select'])) {
                return (bool) $config[$field]['select'];
            }
            return false;
        }
        return false;
    }
    
    public function isFieldHidable($field) {
        $config = $this->_config();
        if(isset($config[$field])) {
            if(isset($config[$field]['hidable'])) {
                return (bool) $config[$field]['hidable'];
            }
            return false;
        }
        return false;
    }
    
	 /**
     * Возвращает видимость поля
     * 
     * @return string
     * 
     */
    public function getFieldVisibility($field) {
    	$config = $this->getModelConfig();
    	
		//var_dump($config->{$field}->label);
		//echo $field.''.PHP_EOL;
        if ($config && isset($config->{$field}) && isset($config->{$field}->visible) ){
        	
        	return $config->{$field}->visible;
        }	
        
    }
	
    
    public function isHandling() {
        if(!isset($this->_modulerelation)) {
            throw new Exception('Modulerelation not set for this identity. '.__METHOD__);
        }
        return $this->_modulerelation->getHandling();
    }
    
    public function findHandling() {
        return $this->_handling;
    }
    
    public function setIsList($is_list) {
        $this->_is_list = $is_list;
        return $this;
    }
    
    public function getIsList() {
        return $this->_is_list;
    }
    
    /**
     * 
     * @return \Sl_Model_Abstract
     * @throws Exception
     */
    public function getHandledModel() {
        if(!isset($this->_modulerelation)) {
            throw new Exception('Modulerelation not set for this identity. '.__METHOD__);
        }
        return $this->_modulerelation->getRelatedObject(\Sl_Model_Factory::object($this));
    }
    
    /**
     * Устанавливает текущую сущность
     * 
     * @param \Sl\Model\Identity\Identity $rel
     * @return \Sl\Model\Identity\Identity
     */
    public function setCurrentRelation(Identity $rel) {
        $this->_current_relation = $rel;
        return $this;
    }
    
    /**
     * Устанавливает текущее поле
     * 
     * @param Field $field
     */
    public function setCurrentField(Field $field) {
        $this->_current_field = $field;
        return $this;
    }
    
    /**
     * Возвращает текущую сущность
     * 
     * @return \Sl\Model\Identity\Identity
     */
    public function getCurrentRelation() {
        return $this->_current_relation;
    }
    
    /**
     * Возвращает текущее поле
     * 
     * @return Field
     */
    public function getCurrentField() {
        return $this->_current_field;
    }
    
    /**
     * Возвращает поле, по которому установленна сортировка
     * 
     * @return Field|null
     */
    public function getSort() {
        foreach($this->_fields as $field) {
            if($field->isSorted()) {
                return $field;
            }
        }
        return null;
    }
    
    /**
     * Устанавливает сортировку
     * 
     * @param string $dir Направление
     * @return \Sl\Model\Identity\Identity
     * @throws Exception Если поле не определено
     */
    public function sort($dir = 'asc') {
        if($this->isVoid()) {
            throw new Exception('Поле не определено. '.__METHOD__);
        }
        if($this->getCurrentRelation()) {
            $this->getCurrentRelation()->sort($dir);
        } else {
            $this->getCurrentField()->sort($dir);
        }
        return $this;
    }
    
    /* Опции
     **********************************************************/
    
    /**
     * Установка опций
     * 
     * @param array $options
     * @return \Sl\Model\Identity\Identity
     */
    public function setOptions(array $options) {
        foreach($options as $key=>$value) {
            $this->setOption($key, $value);
        }
        return $this;
    }
    
    /**
     * Установка опции
     * 
     * @param string $key
     * @param mixed $value
     * @return \Sl\Model\Identity\Identity
     */
    public function setOption($key, $value) {
        $this->_options[$key] = $value;
        return $this;
    }
    
    /**
     * Возвращает все опции
     * 
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }
    
    /**
     * Возвращает опцию
     * 
     * @param string $key
     * @return mixed|null
     */
    public function getOption($key) {
        return isset($this->_options[$key])?$this->_options[$key]:null;
    }
    
    /**
     * Готовит массив колонок для связи
     * 
     * @param string $dep_name Имя связи
     * @return array
     * @throws Exception
     */
    public function prepareColumnsArray($dep_name) {
        $dependent = $this->getRelation($dep_name);
        if(!$dependent) {
            throw new Exception('No such relation found. '.__METHOD__);
        }
        $cols = $dependent->getObjectFields();
        //print_r($cols);
        $prepared_cols = array();
        if($dependent->isAvailable()) {
            if(in_array($dependent->getType(), array(
                \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE,
                \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
                //\Sl_Modulerelation_Manager::RELATION_ITEM_OWNER,
            ))) {
                foreach($cols as $col) {
                    $prepared_cols[$dependent->getName().'.'.$col] = $col.'';
                }
				if (!in_array('id',$cols)){
					
					$prepared_cols[$dependent->getName().'.id'] = 'id';
				
				}
            } else {
                if(in_array('name',$cols)) {
                    $prepared_cols[$dependent->getName().'.name'] = self::GROUP_TPL;
                } else {
                    if($cols[0]) {
                        $prepared_cols[$dependent->getName().'.'.$cols[0]] = preg_replace('/(\{)name(\})/', '{'.$cols[0].'}', self::GROUP_TPL);
                    } else {
                       
                    }
				}
				if (count($cols)) $prepared_cols[$dependent->getName().'.id'] = preg_replace('/(\{)name(\})/', '{id}', self::GROUP_TPL);
            }
        }
		
        return $prepared_cols;
    }
    
    public function getTotalCount() {
        return $this->_total;
    }
    
    public function setTotalCount($count) {
        $this->_total = intval($count);
    }
    
    public function getFiteredCount() {
        return $this->_filtered;
    }
    
    public function setFilteredCount($count) {
        $this->_filtered = intval($count);
    }
    
    public function setRawData(array $data = array()) {
        	
        $this->_raw_data = $data;
        $this->setProcessed(false);
    }
    
    public function setSqlSource($sql) {
        $this->_sql = $sql;
        return $this;
    }
    
    public function getSqlSource() {
        return $this->_sql;
    }
    
    protected function process($raw_data = false) {
    	$cache = \Zend_Registry::get('cache')->getBackend();
        //$cache->clean();
        $cache_string = 'identity_'.array_pop(explode('\\', get_class($this))).'_process_item';
        foreach($this->_raw_data as $key=>$item) {
            $id = $item['id'];
            $cache_id = $cache_string.'_'.$id.'_'.md5(implode('', $item));
            if(!$cache->test($cache_id)) {
                $this->_data[$key] = $this->_processItem($item, $this->isSelected($id), $raw_data);
                if($id != $this->_data[$key][0]) {
                    $this->_data[$key][0] .= ':'.$id;
                }
                $cache->save(serialize($this->_data[$key]), $cache_id, array('identity'));
            } else {
                $this->_data[$key] = unserialize($cache->load($cache_id));
            }
        }
		
        $this->setProcessed(true);
    }
    
    protected function setProcessed($processed) {
        $this->_data_processed = $processed;
    }
    
    public function isDataProcessed() {
        return $this->_data_processed;
    }
    
    public function getData($raw_data = false) {
        if(!$this->isDataProcessed()) {
            $this->process($raw_data);
        }
        return $this->_data;
    }
    
    protected function _processItem($data, $selected = false, $raw_data = false) {
        $cache = \Zend_Registry::get('cache')->getBackend();
        $cach_id = md5('iden');
        $values = array();	
		$calculated_columns = array();
		if ($calculator=$this->getCalculator()){
			$data = $calculator->calculateValues($data);
			$calculated_columns = array_keys($calculator->getAggregatedFields());
    	}
		$fields = $this->getCalculatedObjectFields(true, true, true);
		//print_r($fields);
        foreach($fields as  $field) {
        	$key = $field['name'];
            //echo $key."\r\n";
            if(!array_key_exists($key, $data)) continue;
            $value = $data[$key];
            
            if(!in_array($key, $this->_columns_all) && !in_array($key,$calculated_columns)) {
                unset($data[$key]);
                continue;
            }
			
            $matches = array();
            if(preg_match('/^(.+)\.(.+)$/', $key, $matches)) {
                $model = \Sl_Model_Factory::object($this->getRelation($matches[1]));
                $name = $matches[2];
            } else {
                $model = \Sl_Model_Factory::object($this);
                $name = $key;
            }
            
            if($this->getConfigOptionsType() == self::OPTIONS_EXPORT) {
                $field['html'] = true;
            }
            $values[] = \Sl\Serializer\Serializer::getDtFieldTemplate($model->setArchived($data['archived']), $name, $value, $calculated_columns, $field, $raw_data);
        }
        //print_r($values);
        if($check_type = $this->getOption('check_type')) {
            switch($check_type) {
                case 'radio':
                    $check = '<input type="radio" name="test[]" '.($selected?' checked="checked" ':'').' />';
                    break;
                case 'checkbox':
                    $check = '<input type="checkbox" name="test[]" '.($selected?' checked="checked" ':'').' />';
                    break;
                default:
                    $check = '';
                    break;
            }
            array_unshift($values, $check);
        }
		
		
		
		$model = \Sl_Model_Factory::object($this);
        
		$controller = $calculator && $calculator->getRowController()?$calculator->getRowController():$model->findModelName();
		$module = $calculator&&$calculator->getRowModule()?$calculator->getRowModule():$model->findModuleName();
		
		array_unshift($values, $controller.'.'.$module);
		
		
        $editable = \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $module,
                            'controller' => $controller,
                            'action' => 'edit'
                        ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                    )?1:0;
                    
        if (!$editable){
            $editable = \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $module,
                            'controller' => $controller,
                            'action' => 'detailed'
                        ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                    )?0:null;
        }                        
     //  /*  -- архівування та видалення виведені в групові дії
     /*   $can_delete = \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $module,
                            'controller' => $controller,
                            'action' => \Sl\Service\Helper::AJAX_DELETE_ACTION
                        ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                    )?1:0;
      */ 
        $can_archive = \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $model->findModuleName(),
                            'controller' => $model->findModelName(),
                            'action' => \Sl\Service\Helper::AJAX_ARCHIVE_ACTION
                        ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                    )?1:0;
        array_unshift($values, $editable);
        array_unshift($values, $data['id']);
        /*if(0&&$can_archive) {
            $values[] = \Sl\Serializer\Serializer::getDtArchiveFieldTemplate($model->setId($data['id'])->setArchived($data['archived']));
        }*/
        /*
        if( $can_delete) {
            $values[] = \Sl\Serializer\Serializer::getDtDeleteFieldTemplate($model->setId($data['id']));
        }   
        */        
		//print_r($values);
        return $values;
    }
    
    public function isAvailable() {
     
        return $this->_available;
    
    }
    
    public function setAvailable($available) {
        $this->_available = $available;
    }
    
    public function filterFieldsArray(array $fields = array()) {
        $clear_fields = array();
		
		
        if($this->_modulerelation) {
            $name = $this->getName();
            if(count($fields) == 0) {
                $fields = $this->_loadFieldsData();
            }
            foreach($fields as $k=>$field) {
                if(is_array($field)) {
                    $rel_name = key($field);
                    $colname = $field[$rel_name];
                    if($rel_name == $name) {
                        if(false !== array_search($colname, $this->_loadFieldsData())) {
                            $clear_fields[] = $colname;
                        }
                    }
                } else {
                    $matches = array();
                    if(preg_match('/^'.$name.'([\.|-|\:])(.+)$/', $field, $matches)) {
                        $col_name = $matches[2];
                        if(false === array_search($col_name, $this->_loadFieldsData())) {
                            unset($fields[$k]);
                            continue;
                        } else {
                            //$fields[$k] = $col_name;
                            $clear_fields[] = $colname;
                        }
                    } else {
                        unset($fields[$k]);
                        continue;
                    }
                }
            }
        } else {
            foreach($fields as $k=>$field) {
                if(is_array($field)) {
                    unset($fields[$k]);
                    continue;
                } else {
                    if(preg_match('/[\.|-]/', $field)) {
                        unset($fields[$k]);
                        continue;
                    } else {
                        if(false !== array_search($field, $this->_loadFieldsData())) {
                            $clear_fields[] = $field;
                        }
                    }
                }
            }
        }
		
		
        return $clear_fields;
    }
    
	/** Завантажує дані
	 * 
	 */
	
    public function loadData(array $raw_fields = array()) {
    	//print_r($raw_fields);
        if(count($raw_fields)) {
        	//print_r($raw_fields);
            if ($calculator = $this->getCalculator()){
				$raw_fields = $calculator->getRequestColumns($raw_fields);
			}	
            //if($this->getRelations()) print_r($raw_fields);
			$fields = $this->filterFieldsArray($raw_fields);	
    		//if($this->getRelations()) print_r($fields);
            $obj_fields = $this->_loadFieldsData($fields/*, (count($fields) == 0)*/);
            //if($this->getRelations()) print_r($obj_fields);die;
            $fields = $obj_fields;
        } else {
            $fields = $this->_loadFieldsData(array_keys($this->_config()));
        }
        
        $fields_all = $fields;
        
        \Sl_Service_Acl::setContext($this->_model);
        
        if($this->getRelations()) {
            foreach($this->getRelations() as $relation) {
                // Проверяем доступна ли нам связь
                $priv_read = \Sl_Service_Acl::isAllowed(array(
                        $this->_model,
                        $relation->getName()
                            ), \Sl_Service_Acl::PRIVELEGE_READ);

                $priv_edit = \Sl_Service_Acl::isAllowed(array(
                        $this->_model,
                        $relation->getName()
                            ), \Sl_Service_Acl::PRIVELEGE_UPDATE);
                
                if($priv_read || $priv_edit) {
                    // Если связь работает, то выставляем в нее поля. Все, если ничего нет. Определенные, если можем
                    if(count($raw_fields)) {
                        //echo $relation->getName().': '.count($raw_fields)."\r\n";
                        $rel_fields = $relation->filterFieldsArray($raw_fields);
						//echo $relation->getName().': '.print_r(array($rel_fields), true)."\r\n";
                        if(count($rel_fields)) {
                            //if($relation->getName() == 'packagefinoperation') print_r(array('begin', $relation->getObjectFields(), $rel_fields));
                            //$relation->loadData($rel_fields);
                            //if($relation->getName() == 'packagefinoperation') print_r(array('end', $relation->getObjectFields()));
                            foreach($rel_fields as $f) {
                                $fields_all[] = $relation->getName().'.'.$f;
                            }
							if (!in_array('id',$rel_fields)) $fields_all[] = $relation->getName().'.id';
							
                        } else {
                            $relation->setAvailable(false);
                        }
                    } else {
                        // Напоняем всем, чем можем
                        $relation->loadData();
                        if(count($relation->getObjectFields())) {
                            $related_list_options = array();
                            foreach($this->_config() as $k=>$v) {
                                $matches = array();
                                if(preg_match('/^'.($relation->getName()).'\.(.+)$/', $k, $matches)) {
                                    $related_list_options[] = $matches[1];
                                }
                            }
                            if(count($related_list_options) > 1) {
                                foreach($related_list_options as $option) {
                                    $fields_all[] = $relation->getName().'.'.$option;
                                }
                            } elseif(false !== array_search('name', $relation->getObjectFields())) {
                                $fields_all[] = $relation->getName().'.name';
                            } else {
                                $fs = $relation->getObjectFields();
                                $fields_all[] = $relation->getName().'.'.current($fs);
							}
							$fields_all[] = $relation->getName().'.id';
                            $relation->setAvailable(true);
                        } else {
                            $relation->setAvailable(false);
                        }
                    }
                } else {
                    $relation->setAvailable(false);
                }
            }
        }

        ksort($fields);
        ksort($fields_all);
        /*if($this->getRelations()) {
            print_r(array($raw_fields, $fields_all));die;
        }*/
        
        /*if(false === array_search('id', $fields)) {
            $fields[] = 'id';
            $fields_all[] = 'id';
        }*/
        //print_r(array_values($fields_all));
        $this->_columns = array_values($fields);
        $this->_columns_all = array_values($fields_all);
		
        return $this;
    }
    
    /**
     * Загружает и фильтрует данные о текущей модели
     * 
     * @return string[]
     */
    public function _loadFieldsData(array $need_fields = array(), $ignore_empty = false) {
        $fields = array();
        $sort_order = 1;
        //print_r($need_fields);die;
        
        if($ignore_empty) return $need_fields;
        
        $list_options = \Sl_Module_Manager::getInstance()
                            ->getModule($this->_model->findModuleName())
                            ->section('titles')
                            ->${$this->_model->findModelName()};
                            
        $model_described_fields = $this->_model->describeFields();
		
		\Sl_Service_Acl::setContext($this->_model);
         
        foreach ($model_described_fields as $key => $value) {
            //if($key == 'id') continue;
            if(count($need_fields) && !in_array($key, $need_fields)) continue;
            $priv_read = \Sl_Service_Acl::isAllowed(array(
                        $this->_model,
                        $key
                            ), \Sl_Service_Acl::PRIVELEGE_READ);

            $priv_edit = \Sl_Service_Acl::isAllowed(array(
                        $this->_model,
                        $key
                            ), \Sl_Service_Acl::PRIVELEGE_UPDATE);
            
            if (($priv_read || $priv_edit) && ($key != 'active')) {
                if ((isset($list_options->$key) && isset($list_options->$key->sort_order)) || isset($value['sort_order'])) {
                    $sort_order = $list_options->$key->sort_order ? $list_options->$key->sort_order : $value['sort_order'];
                }
                while (isset($fields[$sort_order])) {
                    $sort_order += 100;
                }
                $fields[$sort_order] = $key;
            } elseif($key == 'id'){
            	$fields[] = 'id';
            }
        }

        //$fields[0] = 'id';

        ksort($fields);
		//die;
        return $fields;
    }
    
    public function findNameColumn() {
        if(false /** @TODO: проверить нет ли чего в конфигах */) {
            
        } elseif(false !== array_search('name', $this->_loadFieldsData())) {
            return 'name';
        } elseif(true) {
            $fs = $this->_loadFieldsData();
            unset($fs['id']);
            return current($fs);
        } else {
            return 'id';
        }
    }
    
    public function setSelected($selected) {
        $this->_selected = $selected;
        return $this;
    }
    
    public function getSelected() {
        return $this->_selected;
    }
    
    public function isSelected($id) {
        return (false !== array_search($id, $this->getSelected()));
    }
    
    public function setUseOrSearch($use_or = true) {
        $this->_use_or = $use_or;
        return $this;
    }
    
    public function getOrSearch() {
        return $this->_use_or;
    }
    
    public function isRequired(Identity $relation) {
        foreach($this->getCalculatedObjectFields(true, true, true) as $item) {
            if(preg_match('/^'.$relation->getName().'\.(.+)$/', $item['name'])) {
                return true;
            }
        }
        
        if($relation->getComps()) {
            return true;
        }
        /*
        if ($relation->isHandling()){
        	$master_relation = \Sl_Modulerelation_Manager::findHandlingRelation(\Sl_Model_Factory::object($relation));
            if($master_relation) {
                // Ищем ограничения
                $restrictions = $master_relation->getRestrictions();
                if($restrictions){
                    return true;                    	
                }
            }
        }
        */
		if($this->getCalculator()) {
            foreach($this->getCalculator()->getRequestColumns($this->getObjectFields(true)) as $item) {
                if(preg_match('/^'.$relation->getName().'\.(.+)$/', $item)) {
                    return true;
                }
            }
        } else {
            foreach($this->getCalculatedObjectFields(true, true, true) as $item) {
                if(preg_match('/^'.$relation->getName().'\.(.+)$/', $item['name'])) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function setTranslator(\Zend_Translate $translate) {
        $this->_translator = $translate;
        return $this;
    }
    
    public function getTranslator() {
        if(!isset($this->_translator)) {
            $this->_translator = \Zend_Registry::get('Zend_Translate');
        }
        return $this->_translator;
    }
}
