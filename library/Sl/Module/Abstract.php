<?php

/**
 * Абстракный модуль системы
 * 
 */
abstract class Sl_Module_Abstract extends Sl_Model_Abstract {

    /**
     * "Слушатели" событий системы
     * 
     * @var \Sl_Listener_Abstract[]
     */
	protected $_listeners;
    
    /**
     * Тип модуля. Используется мало
     * 
     * @var string 
     */
	protected $_type;
    
    /**
     * Папка модуля
     * 
     * @var string
     */
	protected $_dir;
    
    /**
     * Путь к файлу модуля
     * 
     * @var string
     */
	protected $_file;
	
    /**
     * Название секции конфига из которой грузятся связи
     */
	const MODULERELATION_CONFIG_SECTION = 'modulerelations';
	
    /**
     * Название секции конфига из которой грузятся связи
     */
    const NAVIGATION_CONFIG_SECTION = 'navigation_pages';
    
    /**
     * Конфигурация модуля
     * 
     * @var \Zend_Config
     */
	protected $_module_config;
 
    /**
     * Установка типа
     * 
     * @param string $type
     * @return \Sl_Module_Abstract
     */
	public function setType($type) {
		$this -> _type = $type;
		return $this;
	}

    /**
     * Установка папки
     * 
     * @param string $type
     * @return \Sl_Module_Abstract
     */
	public function setDir($dir) {
		$this -> _dir = $dir;
		return $this;
	}

    /**
     * Установка пути файла модуля
     * 
     * @param string $type
     * @return \Sl_Module_Abstract
     */
	public function setFile($file) {
		$this -> _file = $file;
		return $this;
	}

    /**
     * Возвращает тип
     * 
     * @param string $delimiter Разделитель
     * @return string
     */
	public function getType($delimiter = false) {
		return !$delimiter ? $this -> _type : str_replace('_', $delimiter, $this -> getType());

	}

    /**
     * Возвращает название модуля
     * 
     * @return string
     */
	public function getName() {
		$classname=get_class($this);
		if (strpos($classname,'_')!==false){	
			$array = explode('_', $classname);
		} else {
			$array = explode('\\', $classname);
		}
		
		array_pop($array);
		return strtolower(array_pop($array));
	}

    /**
     * Возвращает папку модуля
     * 
     * @return string
     */
	public function getDir() {
		return $this -> _dir;
	}

    /**
     * Возвращает путь к файлу модуля
     * 
     * @return string
     */
	public function getFile() {
		return $this -> _file;
	}

    /**
     * Возвращает мамссив "слушателей" событий
     * 
     * @return \Sl_Listener_Abstract[]
     */
	public function getListeners() {
        return array();
    }

    /**
     * Возвращает массив массивов связей и их настроек
     * 
     * @return array
     */
	public function getModulerelations() {
        if (!($config_relations = $this -> section(self::MODULERELATION_CONFIG_SECTION))) {
            $config_relations = $this -> _saveModuleConfig(array(), self::MODULERELATION_CONFIG_SECTION);
        };

        return array_merge($config_relations -> toArray(), array());

    }
	
    /**
     * Повертає пункти меню модуля
     * 
     * @return array
     */
    public function getMenuPages() {
        if (!($config_pages = $this -> section(self::NAVIGATION_CONFIG_SECTION))) {
            $config_pages = $this -> _saveModuleConfig(array(), self::NAVIGATION_CONFIG_SECTION);
        };

        return array_merge($config_pages -> toArray(), array());

    }
    
    
    /**
     * Возвращает массив массивов калькуляторов и их приоритетов
     * 
     * @return array
     */
	public function getCalculators() {
        return array();
    }
    
    /**
     * Возвращает массив текстовых/массивных представлений нужных ролей
     * 
     * @return array
     */
	public function getRequiredRoles() {
        return array();
    }
	
    /**
     * Возвращает строковое представление ресурса для этого модуля
     * 
     * @return string
     */
	public function getResourceId() {
		return Sl_Service_Acl::joinResourceName(array(
			'type' => Sl_Service_Acl::RES_TYPE_MODULE,
			'name' => strtolower(preg_replace('/Sl_Module_(.+)_Module/', '$1', get_class($this))),
		));
	}

    /**
     * Регистрирует "слушателей" в менеджере событий
     * 
     * @param \Sl_Module_Abstract $module
     */
	public function registerListeners(\Sl_Event_Manager $manager) { 
		foreach ($this->getListeners() as $listener) {
		    if ($listener instanceof \Sl_Listener_Abstract){
		        $manager -> register($listener);    
		    }elseif(is_array($listener) && $listener['listener'] instanceof \Sl_Listener_Abstract){
		        $manager -> register($listener['listener'], $listener['order']);
		    }
			
		}
        return $this;
	}

    /**
     * Возвращает секцию настроек модуля
     * 
     * @param string $section
     * @return \Zend_Config|null
     * 
     */
	public function section($section, $readonly = true) {
		return $this -> config(false, $readonly) -> $section;
	}
    
    /**
     * Возвращает слитый конфиг по конкретной секции
     * 
     * @param \Sl_Model_Abstract $model Модель
     * @param string $section секция конфига модуля
     * @return \Zend_Config Конфигурация
     */
    public function modelConfig(\Sl_Model_Abstract $model = null, $section = null, $return_empty = true, $readonly = false) {
        $readonly = (bool) $readonly;
        if(is_null($section)) { // Только настройки модели (аналог model::describeFields)
            $filename = $this->_getDir().'/configs/'.$model->findModelName().'.php';
            if(file_exists($filename)) {
                $config = new \Zend_Config(require $filename, $readonly);
                return $config->model;
            } else {
                return $return_empty?(new \Zend_Config(array(), $readonly)):null;
            }
        } elseif(is_null($model)) { // Только настройки модуля
            $config = $this->section($section, $readonly);
            if(!$config) {
                return $return_empty?(new \Zend_Config(array(), $readonly)):null;
            }
            return $config;
        } else { // Задана и секция и модель - сливаем
            $subsection = null;
            if(is_array($section)) {
                $subsection = current($section);
                $section = key($section);
            }
            $model_config = $this->modelConfig($model, null, true, true);
            $section_config = $this->section($section, true);
            if(isset($section_config->{$model->findModelName()})) {
                $section_config = $section_config->{$model->findModelName()};
            }
            if($subsection && isset($section_config->$subsection)) {
                $section_config = $section_config->$subsection;
            }
            if($section_config) {
                return $model_config->merge($section_config);
            }
            return $model_config;
        }
    }

    /**
     * Создает и загружает в систему файл настроек
     * 
     * @param string $key Не используется
     * @return \Zend_Config
     */
	protected function config($key = false, $readonly = true) {
		if (!isset($this -> _module_config)) {
			$filename = $this -> _getDir() . '/configs/module.php';
			if (!file_exists($filename)) {
				Sl\Service\Common::createDefaultConfig($filename);
			}
			$this -> _module_config = new Zend_Config(require $filename, $readonly);
		}
		return $this -> _module_config;
	}

    /**
     * Возвращает префикс модуля
     * 
     * @param string $delimiter Разделитель
     * @return string
     */
	public function prefix($delimiter = '_') {
		if (preg_match("/\\\/", get_class($this))) {
			// Namespave /
			$sep = '\\';
		} else {
			// Simple _
			$sep = '_';
		}
		$data = explode($sep, get_class($this));
		unset($data[count($data) - 1]);
		$name = array_pop($data);
		
		return $this -> getType($delimiter) . $delimiter . $name;
	}

    /**
     * Возвращает префикс модели
     * 
     * @return string
     */
	public function prefixModel() {
		return $this -> prefix('\\') . '\\Model';
	}

    /**
     * Возвращает название класса контроллера для соответствующей модели
     * 
     * @param string $controller_name Название модели
     * @return string
     */
	public function getControllerClassName($controller_name) {
		$controller_class_name = false;
		if (strlen($controller_name)) {
			$controller_class_name = '\\' . implode('\\', array(
				$this -> prefix('\\'),
				'Controller',
				ucfirst(strtolower($controller_name))
			));
		}
		return $controller_class_name;
	}

    /**
     * Возвращает название класа модели
     * 
     * @param string $model_name Название модели
     * @return string
     */
	public function getModelClassName($model_name) {
		$model_class_name = false;
		if (strlen($model_name)) {
			$model_class_name = '\\' . implode('\\', array(
				$this -> prefix('\\'),
				'Model',
				ucfirst(strtolower($model_name))
			));
		}
		return $model_class_name;
	}

    /**
     * Регистрация связей модуля
     * 
     * @return \Sl_Module_Abstract
     */
	public function registerModulerelations($rewrite = false) {
	    \Sl_Modulerelation_Manager::getInstance()->setModulerelations($this->getModulerelations(), $this, $rewrite);
        return $this; 
	}
	
	/**
     * Регистрация калькуляторов модуля
     * 
     * @return \Sl_Module_Abstract
     */
	public function registerCalculators() {
		$calculators = $this->getCalculators();
		if($calculators) {
			foreach($calculators as $calculator_array) {
				if ($calculator_array instanceof \Sl\Calculator\Calculator){
					\Sl_Calculator_Manager::setCalculator($calculator_array);
				}else {
					\Sl_Calculator_Manager::setCalculator($calculator_array['calculator'], isset($calculator_array['sort_order'])?$calculator_array['sort_order']:false);
				}
			}
		}
        return $this;
	}
	
    /**
     * Устанавливает списки в сервис списков, считываемые из конфигурации модуля
     * 
     * @return \Sl_Module_Abstract
     */
	public function setModuleLists(){
		$lists = $this->section('lists');
		if (isset($lists) && count($lists)){
			foreach ($lists as $list_name => $list){
				\Sl\Service\Lists::setList($list_name,$list->toArray());
			}
		}
		
		$lists_statuses = $this->section('lists_statuses');
		if (isset($lists_statuses) && count($lists_statuses)){
			\Sl\Service\Lists::setListStatuses($lists_statuses->toArray());
		}
        return $this;
	}
    
    /**
     * Регистрируем роли необходимые для функционирования модуля
     * 
     * @return \Sl_Module_Abstract
     */
    public function declareRequiredRoles() {
        $manager = \Sl_Module_Manager::getInstance();
        
        foreach($this->getRequiredRoles() as $role) {
            $parent = null;
            if(is_array($role)) {
                $parent = key($role);
                $role = $role[$parent];
            }
            $manager->registerRequiredRole($this, $role, $parent);
        }
        return $this;
    }
    
    /**
     * Возвращает массив принтеров модуля
     * 
     * @return \Sl\Printer\Printer[]
     */
    public function getPrinters() {
        return array();
    }
    
    /**
     * Генерирует listview секцию модуля
     * 
     * @param \Sl_Model_Abstract $model Модель, для которой генерируется конфиг
     * 
     * @return \Zend_Config
     */
    public function generateListViewOptions(\Sl_Model_Abstract $model = null) {
        if(is_null($model)) {
            // Строим весь конфиг
            $this->_saveModuleConfig(array(), 'listview_options');
            return $this->config();
        } else {
            $config = $this->section('listview_options');
            $data = $model->_config()->model->toArray();
            $new_data = array();
            $sort = 0;
            foreach($data as $field=>$options) {
                if(!in_array($field, array('id', 'create', 'active'))) {
                    $sort += 10;
                    $new_data[$field] = array(
                        'order' => $sort,
                        'label' => $options['label'],
                    );
                }
            }
            $config_data = $config->toArray();
            $config_data[$model->findModelName()] = $new_data;
            $this->_saveModuleConfig($config_data, 'listview_options');
            return $this->section('listview_options');
        }
    }
    
    /**
     * Генерирует duplicate секцию модуля
     * 
     * @param \Sl_Model_Abstract $model Модель, для которой генерируется конфиг
     * @return \Zend_Config
     */
    public function generateDuplicateOptions(\Sl_Model_Abstract $model = null) {
        if(is_null($model)) {
            // Строим весь конфиг
            $this->_saveModuleConfig(array(), 'duplicate');
            return $this->config();
        } else {
            $config = $this->section('duplicate');
            if(!$config) {
                $config = $this->generateDuplicateOptions()->duplicate;
            }
            $data = array();
            
            foreach($model->describeFields() as $field=>$options) {
                if(!in_array($field, array('id', 'create', 'active'))) {
                    $data[$field] = array(
                        'label' => $options['label'],
                    );
                }
            }
            
            foreach(\Sl_Modulerelation_Manager::getRelations($model) as $relation) {
                if(in_array($relation->getType(), array(
                    \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER,
                    \Sl_Modulerelation_Manager::RELATION_MODEL_ITEM,
                    \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE,
                    \Sl_Modulerelation_Manager::RELATION_ONE_TO_MANY,
                    \Sl_Modulerelation_Manager::RELATION_FILE_ONE,
                    \Sl_Modulerelation_Manager::RELATION_FILE_MANY
                ))) {
                    continue;
                }
                $data['modulerelation_'.$relation->getName()] = array(

                );
            }
            $config_data = $config->toArray();
            if(!isset($config_data[$model->findModelName()])) {
                $config_data[$model->findModelName()] = array();
            }
            $config_data[$model->findModelName()] = $data;
            $this->_saveModuleConfig($config_data, 'duplicate');
            return $this->section('duplicate')->{$model->findModelName()};
        }
    }
    
    /**
     * Генерирует detailed секцию модуля
     * 
     * Якщо немає на вході моделі - просто створює рубрику 'detailed' в module.php. Якщо модель є - шукає її конфіг. 
     * Якщо не знаходить - формує конфіг з конфігу module або model.
     * 
     * @param \Sl_Model_Abstract $model Модель, для которой генерируется конфиг
     * @return \Zend_Config
     */
    public function generateDetailedOptions(\Sl_Model_Abstract $model = null) {
        if (is_null($model)) {
            $this->_saveModuleConfig(array(), 'detailed');
            return $this->config();
        } else {
            $form_name = strtolower('model_' . $model->findModelName() . '_form');
            $module_config = \Sl_Module_Manager::getInstance()->getCustomConfig($model->findModuleName(), 'forms', $form_name);
            if (isset($module_config)) {
                $data = $module_config;
            } else {
                $data = $model->_config()->model;
            } 
            $data_array=array($model->findModelName()=>$data->toArray()); 
            $config_options = \Sl_Module_Manager::getInstance()
                                    ->getCustomConfig($model->findModuleName(),'detailed');
            $data_array = array_merge_recursive($config_options->toArray(), $data_array);
            $this->_saveModuleConfig($data_array, 'detailed');
            return $data;
        }
    }

    /**
     * Генерирует export секцию модуля
     * 
     * @param \Sl_Model_Abstract $model Модель, для которой генерируется конфиг
     * @return \Zend_Config
     */
    public function generateExportOptions(\Sl_Model_Abstract $model = null) {
        if(is_null($model)) {
            // Строим весь конфиг
            $this->_saveModuleConfig(array(), 'export_options');
            return $this->config();
        } else {
            $config = $this->section('export_options');
            $lv_options = $this->section('listview_options');
            
            $new_data = $lv_options->{$model->findModelName()}->toArray();
            
            $config_data = $config->toArray();
            $config_data[$model->findModelName()] = $new_data;
            $this->_saveModuleConfig($config_data, 'export_options');
            return $this->section('export_options');
        }
    }
    
    /**
     * Обновляет секцию связей конфига
     * 
     * @param array $relation_array Массив настроек связей
     * @return \Sl_Module_Abstract
     */
	public function updateModulerelationSection(array $relation_array){
		if (!($current_relations = $this->section(self::MODULERELATION_CONFIG_SECTION))){
			$current_relations = array();
		} else {
			$current_relations=$current_relations->toArray();
		};
		
		$current_relations = array_merge($current_relations, $relation_array);
		$this->_saveModuleConfig($current_relations,self::MODULERELATION_CONFIG_SECTION);
        return $this;
	}
	
    /**
     * Сохраняет информацию в конфигурации модуля
     * 
     * @param array $data Данные, которые необходимо сохранить
     * @param string $section Секция конфигурации в которой нужно сохранить данные
     * @return \Zend_Config
     */
    protected function _saveModuleConfig($data, $section) {
        $config = new Zend_Config(require $this ->_getDir().'/configs/module.php', true);
        
        $config->$section = $data;
        
        $config_writer = new Zend_Config_Writer_Array( array(
			'config' => $config,
			'filename' => $this ->_getDir().'/configs/module.php',
		));
		$config_writer -> write();
        $this->_module_config = null;
		return $config->$section;
    }
}
