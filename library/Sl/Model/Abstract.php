<?php

/**
 * Объект приложения
 */
abstract class Sl_Model_Abstract {

	protected $_id;
	protected $_active = 1;
        protected $_archived;
	protected $_config;
	protected $_config_path;
	protected $_related = array();
	protected $_lists = array();
	protected $_control_status;
        protected $_extend;
	protected $_loged = true;
        protected $_validators;
	protected $_all_extend = false;
	protected $_create;
        protected $_extend_table = null;
	
	/**
	 * Поле свое. Ни от кого не зависит
	 */
	const FIELD_TYPE_GET = 'get';

	/**
	 * Связь
	 */
	const FIELD_TYPE_FETCH = 'fetch';

    const FORMAT_TIMESTAMP = 'Y-m-d H:i:s';
    const FORMAT_DATE = 'Y-m-d';
	/**
	 * Виведення значення списку
	 * @param string $property
	 */
	public function Lists($field, $value = null) {
		if(is_null($value)) {
            if(method_exists($this, $this->buildMethodName($field,'get'))) {
                $value = $this->{$this->buildMethodName($field,'get')}();
            }
        }	
		if(isset($this->_lists[$field])) {
            return \Sl\Service\Lists::fetchListValue($this->_lists[$field], $value);
        } else {
            return $value;
        }
	}
        
        // возращает имя наследоваемого класса
        public function Extend()
            {
                $parent_class = new \ReflectionClass($this);
                $parent_class_name = $parent_class->getParentClass()->getName();            
                if ( $parent_class_name !='Sl_Model_Abstract' && $this->extendTable()) {
                        return $parent_class_name;
                }
               
            }
        // индикатор для наследованных таблиц
        public function extendTable()
            {   if($this->_extend_table)
                    return $this->_extend_table;
                else{
                  return   '|'.Sl\Service\Helper::getModelAlias($this).'|';
                }    
            }
                

	/**
	 * Виведення ассоційованого списку
	 * @param string $property
	 */
	public function ListsAssociations($property = false) {
		
		return $property? (isset($this -> _lists[$property]) ? $this -> _lists[$property] : false) : $this -> _lists;

	}
	public function getCreate($as_object = false) {
		if($as_object) {
            return DateTime::createFromFormat(self::FORMAT_TIMESTAMP, $this->getCreate());
        }
        return $this->_create;
	}
	
	public function setCreate ($date_create) {
        if($date_create instanceof DateTime) {
            $date_create = $date_create->format(self::FORMAT_TIMESTAMP);
        }
		$this->_create = $date_create;	
		return $this;
	}
        // проверка или наследывается модель от другой
        public static function checkExtend(\Sl_Model_Abstract $model) {
            $parent_class = new \ReflectionClass($model);
            $parent_class_name = $parent_class->getParentClass()->getName();
            
        if ( $parent_class_name !='Sl_Model_Abstract' &&  strlen($model->extendTable()) > 0 ) {
            return true;
        }
        return false;
        }	
	/**
	 * Виведення значення списку
	 * @param string $property
	 */
	public static function buildMethodName($property,$type='') {
        $name_array = array_map('ucfirst',explode('_',strtolower($property)));
		$method = $type.implode('', $name_array);
		return $method;
	}

	/**
	 * Вызов несуществующего метода
	 * @param string $name
	 * @param array $arguments
	 * @throws Sl_Exception_Model
	 */
	public function __call($name, $arguments) {
            
            //print_r(array_shift(debug_backtrace()));die;
		throw new Sl_Exception_Model('No such method  '.$name.' '.var_dump($arguments).' in ' . get_class($this));
	}

	/**
	 * Вызов несуществующего статического метода метода
	 * @param string $name
	 * @param array $arguments
	 * @throws Sl_Exception_Model
	 */
	public static function __callStatic($name, $arguments) {
		throw new Sl_Exception_Model('No such static method in ' . __CLASS__);
	}

	/**
	 * Создание из массива
	 */
	public function __construct(array $options = null) {
		$this -> _config_path = $this -> _getDir() . '/../configs/' . $this -> findModelName() . '.php';
		if (is_array($options)) {
			$this -> setOptions($options);
		}
	}

	/**
	 * "Магия" установки членов
	 * @param string $name
	 * @param string $value
	 * @throws Exception
	 */
	public function __set($name, $value) {
		$method = 'set' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Sl_Exception_Model('Invalid  property set' . $name);
		}
		$this -> $method($value);
	}

	/**
	 * "Магия" извлечения членов
	 * @param string $name
	 * @return string
	 * @throws Sl_Exception_Model
	 */
	public function __get($name) {

		$method = 'get' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			$method = 'get' . implode('', array_map('ucfirst', explode("_", $name)));
			if (!method_exists($this, $method)) {
				throw new Sl_Exception_Model('Invalid property get ' . $name);
			}
		}
		return $this -> $method();
	}

	/**
	 * Заполнение данными из массива
	 * @param array $options
	 * @return Sl_Model_Abstract
	 */
	public function setOptions(array $options) {
		
		$methods = get_class_methods($this);
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (preg_match('/^' . \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX . '_(.+)/', $key, $matches) && !preg_match('/_names$/', $key) && !preg_match('/_btn$/', $key)) {

				if (is_array($value)) {
					//якщо пов'язані сутності прийшли масивом
					$this -> assignRelated($matches[1], $value);
				} else {
					//якщо пов'язані сутності прийшли серіалізованими
					$values = explode(';', $value);
					$this -> assignRelated($matches[1], $values);
				}

			} elseif (in_array($method, $methods)) {
				$this -> $method($value);
			} else {
				$words = explode("_", $key);
				$words = array_map('ucfirst', $words);
				$method = 'set' . implode('', $words);
				if (in_array($method, $methods)) {
					$this -> $method($value);
				}
			}
		}
		return $this;
	}

    /**
     * Возвращает объект в виде массива
     * @return array
     */
    public function toArray($with_list_values = false, $with_relations = false) {
        $methods = get_class_methods($this);
        $getters = array();
        foreach ($methods as $k => $method) {
            if (preg_match('/^get/', $method)) {
                if ($method == 'getResourceId')
                    continue;
                $getters[] = $method;
            }
        }
        $result = array();
        foreach ($getters as $getter) {
            $name = explode('_', strtolower(preg_replace('/([A-Z])/', '_$1', $getter)));
            array_shift($name);
            $name = implode('_', $name);
            if ($with_list_values) {
                $result[$name] = $this->Lists($name);
            } else {
                $result[$name] = $this->$getter();
            }
        }
        if($with_relations) {
            foreach($this->_related as $relation_name=>$related) {
                if(!isset($result[$relation_name])) {
                    $result[$relation_name] = array();
                }
                foreach($related as $model) {
                    $result[$relation_name][] = $model->toArray($with_list_values, $with_relations);
                }
            }
        }
        return $result;
    }

    /**
	 * Возвращает сериализированный объект
	 * @return string
	 */
	public function toJson() {
		return json_encode($this -> toArray());
	}

	/**
	 * Установка идентификатора
	 * @param integer $id
	 * @return Sl_Model_Abstract
	 */
	public function setId($id) {
		$this -> _id = $id;
		return $this;
	}

	/**
	 * Установить состояние
	 * @param mixed $active
	 * @return Sl_Model_Abstract
	 */
	public function setActive($active) {
		$this -> _active = $active;
		return $this;
	}
    
   public function setExtend ($extend) {
		$this->_extend = $extend;
		return $this;
	}   
   public function getExtend () {
		return $this->_extend;
	}   
    /**
     * Установка статуса архивного
     * 
     * @param type $archived
     * @return \Sl_Model_Abstract
     */
    public function setArchived($archived) {
        $this->_archived = $archived;
        return $this;
    }

	public function getId() {
		return $this -> _id;
	}

	public function getActive() {
		return $this -> _active;
	}
    
    public function getArchived() {
        return $this->_archived;
    }

	public function describeFields($as_object = false, $cached = true) {
		if ($as_object) {
			return $this -> _config($cached);
		}
		if ($this -> _config($cached) -> model)	return $this -> _config($cached) -> model -> toArray();
	}

	public function describeField($name, $as_object = false) {
		if($name == 'id') return array();
        if ($as_object) {
			return $this -> _config() -> model -> $name;
		}
        if(!$this -> _config() -> model ->$name) {
            throw new Exception('No such field. ('.$name.')'.get_class($this).'::'.__FUNCTION__);
        }
        return $this -> _config() -> model ->$name -> toArray();
	}

	public function fillEmptyFieldInfo($new_fields = null) {
		// $data = array();
		
		$data = $this -> describeFields();
        $fields = array_keys($this->toArray());
        if (is_array($new_fields)) $fields= array_merge($fields, $new_fields); 
		foreach ( $fields as $name) {
			if (!isset($data[$name]))
				$data[$name] = array();
			if (!isset($data[$name]['label']))
				$data[$name]['label'] = strtoupper($name);
			if (!isset($data[$name]['type'])) {
				$data[$name]['type'] = $name == 'active' ? 'checkbox' : 'text';
			}

		}

		$this -> _saveConfig($data);

	}

	/**
	 * @return Zend_Config
	 */
	protected function _config($cache = true) {
		if (!file_exists($this -> _config_path)) {
			Sl\Service\Common::createDefaultConfig($this -> _config_path);
			$this -> _config = new Zend_Config(
			require $this -> _config_path);
			$this -> _saveConfig(array());
		}
		if (!isset($this -> _config) || !$cache) {
			$this -> _config = new Zend_Config(
			require $this -> _config_path);
		}

		return $this -> _config;
	}

	protected function _saveConfig(array $data) {

		if (!$this -> _config) {
			throw new Sl_Exception_Model('Nothing to save in ' . __METHOD__);
		}
		$config = new Zend_Config(
		require $this -> _config_path, true);
		$config -> model = $data;
        $config_writer = new Zend_Config_Writer_Array( array(
			'config' => $config,
			'filename' => $this -> _config_path,
		));
		$config_writer -> write();

	}

	public function changeConfigPath($path) {
		if (file_exists($path) && preg_match('/\.php$/', $path)) {
			$old_config = $this -> _config_path ? $this -> _config_path : null;
			$this -> _config_path = $path;
			return $old_config;
		} else {
			throw new Sl_Exception_Model('Coud\'t load config from: ' . $path);
		}
	}

	protected function _getDir() {
		$r = new ReflectionClass(get_class($this));
		return dirname($r -> getFileName());
	}

	public function __toString() {
		if (!method_exists($this, 'getName'))
			return $this -> getId() . '';
		else {
			return $this -> _getTranslated($this -> getName()).'';
		}
	}

	protected function _getTranslated($name) {
		return $name;
	}

	/**
	 * @param string $field - назва зв'язку
	 * @return array масив зв'язаних об'єктів
	 */

	public function fetchRelated($field = false) {
		
		if ($field) {
			$field = strtolower($field);
            //var_dump(array($this->_related, $this->_related[$field], $field));
			return isset($this -> _related[$field]) ? $this -> _related[$field] : array();
		} else {
			return $this -> _related;
		}

	}
    
    /**
     * @param string $field - назва зв'язку
     * @return \Sl_Model_Abstract перший з пов'язаних об'єктів або його id або null
     */

    public function fetchOneRelated($field) {
        
        if ($this->issetRelated($field)){
            $related = $this->fetchRelated($field);
            $first = current($related);
            return ($first instanceof \Sl_Model_Abstract?$first:current(array_keys($related)));
        }

    }
    
    
	/** Перевірити, чи встановлений зв'язок
	 * @param string $field - назва зв'язку
	 * @return bool
	 */
	public function issetRelated($field) {

		return isset($this -> _related[strtolower($field)]) && is_array($this -> _related[strtolower($field)]);

	}
	
	/** Знайти список заповнених зв'язків
	 * @return array
	 * */
	
	public function findFilledRelations() {

		return array_keys($this->_related);

	}
	
	public function assignRelated($field, array $objects) {
		$field = strtolower($field);
		$this -> _related[$field] = array();
		foreach ($objects as $key => $object) {
			if (is_object($object)) {
				if ($object -> getId()){
					$this -> _related[$field][$object -> getId()] = $object;
				} else {
					$this -> _related[$field][$key] = $object;
				}
				
			} elseif (is_array($object)) {
				$this -> _related[$field][$key] = $object;
			} elseif ($object) {
				$this -> _related[$field][$object] = $object;
			}
		}
		ksort($this -> _related[$field]);
		return $this;
	}

	public function findModuleName() {
		$array = explode('\\', get_class($this));
		unset($array[count($array) - 1]);
		unset($array[count($array) - 1]);
		return strtolower(array_pop($array));
	}

	public function findModelName() {
		$array = explode('\\', get_class($this));
		return strtolower(array_pop($array));
	}

	public function buildResourceName($property) {
		$property_name = $resource_name = false;
		$property_getter = implode('', array_map('ucfirst', explode('_', $property)));

		if (method_exists($this, 'get' . ucfirst($property_getter))) {
			$property_name = $property;
		} else {

			foreach (\Sl_Modulerelation_Manager::getInstance()->getRelations($this) as $relation) {
				if ($relation -> getName() == $property) {$property_name = $property;
					break;
				}
			}
		}
		if ($property_name) {

			$module_name = $this -> findModuleName();
			$module_name_array = explode('\\', get_class($this));
			$model_name = array_pop($module_name_array);
			$resource_name = \Sl_Service_Acl::joinResourceName(array(
				'type' => \Sl_Service_Acl::RES_TYPE_OBJ,
				'module' => $this -> findModuleName(),
				'name' => $model_name,
				'field' => $property_name
			));
		}
		return mb_strtolower($resource_name);

	}
	
	public function isEmpty() {
		
		$values = $this->toArray();	
		unset($values['active']);
		unset($values['create']);
		if (count(array_diff($values,array('')))) return false;
		
		$relations = $this->fetchRelated();
		foreach($relations as $relation => $relates){
			if (count($relates)) return false;
		}
		
		return true;
	}
	
	
	public function isValid() {
		
		
		return !$this->isEmpty();
	}
	

	public function findControlStatus(){
			
		if ($list_field = $this->_control_status){
			$method_name='get'.$this->buildMethodName($list_field);
			 
			$value = $this->$method_name();
			return $value;
		}	
		
	}
	
	public function findControlfield(){
			
		if ($list_field = $this->_control_status){
			
			return $list_field;
		}	
		
	}
	
	public function fetchControlStatusEditable(){
			
		if ($list_field = $this->_control_status){
			
			$method_name='set'.$this->buildMethodName($list_field);
			
			$value = \Sl\Service\Lists::getListStatusEditableValue($this->_lists[$list_field]); 
			
			if (count($value)){
				$this->$method_name(current($value));
			}
			
			
			
		}	
		
	}
	
	public function isFinal($property = null){
		if ($property && $list_name == $this->ListsAssociations($property) &&
			$final_statuses = \Sl\Service\Lists::getListStatusFinalValues($list_name) &&
			count($final_statuses)){
				
			$getMethod = $this->buildMethodName($property,self::FIELD_TYPE_GET);
			$value = $this->$getMethod();
			return in_array($value,$final_statuses);
			
		}	
		elseif ($value = $this->findControlStatus() ){
			if (\Sl\Service\Lists::checkListStatusValue($this->_lists[$this->_control_status],$value,'final')) return true;
		} 
		return false;	
		
	}
	
	public function fetchFinalStatusValues(){
	
		if ($list_field = $this->_control_status){
			$array =  \Sl\Service\Lists::getListStatusFinalValues($this->_lists[$list_field]);
			
			return $array;
		}
			 
		
	}
	
	public function isEditable() {
		
		if ($this->getId() && $value = $this->findControlStatus()){
			
			
			if (\Sl\Service\Lists::checkListStatusValue($this->_lists[$this->_control_status],$value,'canceled')	|| 
				  \Sl\Service\Lists::checkListStatusValue($this->_lists[$this->_control_status],$value,'final')) return false;
		} 
			return true;
		
		
	}
	

	
	public function isLoged(){
		return $this->_loged;
	}
	public function isAllExtend(){
		return $this->_all_extend;
	}        
	
	public function validators($field = null) {
        if(isset($this->_validators)) {
            if(!is_null($field)) {
                return isset($this->_validators[strval($field)])?$this->_validators[strval($field)]:array();
            } else {
                return $this->_validators;
            }
        } else {
            $fields = $this->_config()->model;
            $validator_groups = $this->_config()->validator_groups;
            $validators = array();
            foreach($fields as $name=>$data) {
                if($this->_config()->model->{$name}) {
                    if($this->_config()->model->{$name}->validators) {
                        $val_data = $this->_config()->model->{$name}->validators->toArray();
                        foreach($val_data as $k=>$v) {
                            $val_name = '';
                            $val_options = array();
                            if(is_string($v)) {
                                if(preg_match('/^gr_/', $v)) {
                                    // Група валидаторов
                                    $gr_vals = array();
                                    if($validator_groups && $validator_groups->$v) {
                                        $gr_data = $validator_groups->$v;
                                        if(is_string($gr_data)) {
                                            $gr_vals[] = $this->_buildValidatorObject($gr_data);
                                        } else {
                                            $gr_data = $gr_data->toArray();
                                            foreach($gr_data as $kk=>$vv) {
                                                $gr_name = $vv;
                                                $gr_options = array();
                                                if(is_array($vv)) {
                                                    $gr_name = $kk;
                                                    $gr_options = $vv;
                                                }
                                                $gr_vals[] = $this->_buildValidatorObject($gr_name, $gr_options);
                                            }
                                        }
                                    }
                                    $validators[$name] = $gr_vals;
                                    continue;
                                }
                                $val_name = $v;
                            } elseif(is_array($v)) {
                                $val_name = $k;
                                $val_options = $v;
                            }
                            $validator = $this->_buildValidatorObject($val_name, $val_options);
                            if($validator) {
                                $validators[$name][] = $validator;
                            }
                        }
                    }
                }
            }
            $this->_validators = $validators;
            return $this->validators($field);
        }
	    }
    
    protected function _buildValidatorObject($name, $options = array()) {
        try {
            return \Sl\Validate\Validate::factory($name, $options);
        } catch(Exception $e) {
            echo $e->getMessage()."\r\n";
            return null;
        }
    }
}
