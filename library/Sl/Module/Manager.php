<?php

class Sl_Module_Manager {

	protected static $_instance;
	protected static $_directories;
	protected static $_config_path;
	protected static $_config;
	protected $_modules;
    
    /**
     *
     * @var \Sl\Module\Auth\Model\Role[]
     */
    protected $_roles;

	const SCRIPT_BASE_PATH = '/Sl/Module/Home/View/';
	const CUSTOM_CONFIGS_ROOT = 'custom_configs';
	public function __construct() {;
	}

	/**
	 *
	 * @return Sl_Module_Manager
	 */
	public static function getInstance() {
		if (!isset(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function reloadModuleInformation() {
		$dirs = self::getModulesDirectories();
		//print_r($dirs);
		$modules = array();

		foreach ($dirs as $type => $dir) {
			try {
				$dh = opendir($dir);
				if ($dh) {

					while (false !== ($filename = readdir($dh))) {
						if (preg_match('/^[A-Z][a-z\d]+$/', $filename)) {
							$mainFile = $dir . '/' . $filename . '/Module.php';
							if (file_exists($mainFile)) {
								$modules[strtolower($filename)] = array('file' => $dir . '/' . $filename . '/Module.php', 'type' => $type, 'dir' => $dir . '/' . $filename, );

							}
						}
					}
				} else {
					throw new Exception(implode('; ', error_get_last()));
				}
			} catch(Exception $e) {
				throw new Exception($e -> getMessage());
			}
		}
		$old_config = self::getConfig();
		$modules_duple = $modules;
		$to_update = false;

		foreach ($old_config as $module_name => $options) {
			if (isset($modules_duple[$module_name]) && !count(array_diff($modules_duple[$module_name] , $options->toArray()))) {
				unset($modules_duple[$module_name]);
			} else {
				$to_update = true;
				break;
			}
		}
	
		if ($to_update || count($modules_duple)) {
			foreach ($old_config as $module_name => $options) {unset($old_config->$module_name);}
					
			foreach($modules as $name=>$data) {$old_config->$name = $data;}	
			
			$writer = new Zend_Config_Writer_Array( array('config' => $old_config));

			try {
				$writer -> write(self::getConfigPath());
			} catch(Exception $e) {
				die($e -> getMessage());
			}
		}

	}

    /**
     * Достаем все модули
     * 
     * @param boolean $only_active
     * @return Sl_Module_Abstract[]
     */
	protected function _getModules($only_active = true) {
		if (!isset($this -> _modules)) {
			$this -> _reloadModules($only_active);
		}
		return $this -> _modules;
	}

    protected function _reloadModules($only_active) {
		$config = self::getConfig();
		$modules = array();
        
		foreach ($config as $m_name => $options) {
			try {
				$modules[$m_name] = $this -> _loadModule($m_name, $options);
			} catch(Sl_Exception_Module $e) {
				// Can't load module
				continue;
			}
		}
		$this -> _modules = $modules;
	}

    /**
     * Загружаем и отдаем модуль
     * 
     * @param string $name
     * @param array $options
     * @return Sl_Module_Abstract
     * @throws Sl_Exception_Module
     */
	protected function _loadModule($name, $options) {
		$class_name = ucfirst($name) . '_Module';
        
		try {
			if (class_exists($class_name)) {
			    
				$class = new $class_name();
				return $class -> setOptions($options -> toArray());
			} else {
				throw new Sl_Exception_Module('Can\'t load module: ' . $name);
			}
		} catch(Sl_Exception_Module $e) {
			$class_name = '\\';
			$class_name .= preg_replace('/[_]/', '\\', $options -> type) . '\\';
			$class_name .= ucfirst($name) . '\\';
			$class_name .= 'Module';
           // echo $class_name.'<br>';
			if (class_exists($class_name)) {
				$class = new $class_name();
				return $class -> setOptions($options -> toArray());
			} else {
				
				throw new Sl_Exception_Module('Can\'t load module: ' . $class_name);
                
			}
		}
	}

    /**
     * Статическая обертка для _getModules
     * 
     * @param boolean $only_active ТОлько активные
     * @return Sl_Module_Abstract[] массив модулей приложения
     */
	public static function getModules($only_active = true) {
		return self::getInstance() -> _getModules($only_active);
	}
    
    public static function getAvailableModels($module_name = null) {
        $models = array();
        if(!is_null($module_name)) {
            $modules = array(self::getInstance()->getModule($module_name));
        } else {
            $modules = self::getModules();
        }
        foreach($modules as $module_name=>$module ) {
            if(is_dir($module->getDir().'/Model')) {
                $dh = opendir($module->getDir().'/Model');
                if($dh) {
                    while(false !== ($filename = readdir($dh))) {
                        $matches = array ();
                        if (preg_match ( '/(.+)\.php$/', $filename, $matches )) {
                            $model_name = strtolower($matches[1]);
                            $models[$module_name][] = $model_name;
                        }
                    }
                }
            }
        }
        return $models;
    }

	/**
	 *
	 * @return Zend_Config
	 */
	public static function getConfig() {
		try {
			self::$_config = new Zend_Config(require self::getConfigPath(), true);
			return self::$_config;
		} catch(Exception $e) {
			die('Can\'t read modules config file');
		}
	}

	public static function getConfigPath() {
		if (!isset(self::$_config_path)) {
			self::$_config_path = APPLICATION_PATH . '/configs/modules.php';
		}
		return self::$_config_path;
	}

	public static function setConfigPath($path) {
		if (file_exists($path) && preg_match('/\.php$/', $path)) {
			self::$_config_path = $path;
		} else {
			throw new Exception('Not an ini-file: ' . $path);
		}
	}

	public static function addModulesDirectories(array $dirs) {
		foreach ($dirs as $type => $dir) {
			try {
				self::addModulesDirectory($type, $dir);
			} catch(Exception $e) {
				continue;
			}
		}
	}

	public static function addModulesDirectory($type, $dir) {
		if (is_dir($dir) && ($type = strval($type))) {
			if (!in_array($type, array('library', 'custom'))) {
				self::$_directories[$type] = $dir;
			} else {
				throw new Sl_Exception_Module('Can\'t override "' . $type . '" type: ' . $dir);
			}
		} else {
			throw new Sl_Exception_Module('Not a directory: ' . $dir);
		}
	}

	public static function getModulesDirectories() {
		if (!isset(self::$_directories)) {
			//self::$_directories = array('Sl_Module' => APPLICATION_PATH . '/../library/Sl/Module', 'Application_Module' => APPLICATION_PATH . '/Module', );
			self::$_directories = array('Sl_Module' => '../library/Sl/Module', 'Application_Module' => '../application/Module', );
		}
		return self::$_directories;
	}
	/** 
	 * @param  string $module
     * @return str|array directory
	 */
	public static function getControllerDirectory($module = null) {
		$dirs = array();
		if ($module) {
			return self::getInstance() -> getModule($module) -> getDir();
		} else {
			foreach (self::getModules() as $name => $module) {
				$dirs[$name] = $module -> getDir();
			}
			return $dirs;
		}
	}

    /**
     * 
     * @param string $module Название модуля
     * @return type
     */
	public static function getViewDirectory($module = null) {
		return self::getControllerDirectory($module) . '/View';
	}

	public static function getModuleViewPath($name) {
		try {
			throw new Exception('TODO ' . __METHOD__);
		} catch(Exception $e) {
			return self::getScriptBasePath();
		}
	}

	public static function getScriptBasePath() {
		return APPLICATION_PATH . '/../library' . self::SCRIPT_BASE_PATH;
	}

    /**
     * Возвращает модуль по имени
     * 
     * @param string $name
     * @return Sl_Module_Abstract
     * @throws Sl_Exception_Module
     */
	public function getModule($name) {
		
		if (!isset($this -> _modules[$name])) {
			
			throw new Sl_Exception_Module('Module "' . $name . '" not loaded');
		}
		return $this -> _modules[$name];
	}
    
	public static function getStaticContentPath(Sl_Module_Abstract $module){
		
		$path=self::getControllerDirectory($module->getName()).'/static';
		return $path;
	}
    
    public function registerRequiredRoles() {
        $this->getRoles();
        foreach($this->getModules() as $module) {
            foreach($module->getRequiredRoles() as $role) {
                $parent = null;
                if(is_array($role)) {
                    $parent = key($role);
                    $role = current($role);
                }
                $this->registerRequiredRole($module, $role, $parent);
            }
        }
        $this->verifyRoles();
    }
    
    protected function registerRequiredRole(\Sl_Module_Abstract $module, $role, $parent = null) {
        $role_name = $module->getName().':'.$role;
        if($role && $role_name) {
            $n_role = \Sl_Model_Factory::object('role', $this->getModule('auth'));
            $n_role->setName($role_name);
            if($parent) {
                /** 
                 * @TODO: Как будет реализована связь роли к роли можно будет раскомментировать.
                 * Пока же это обычное поле в БД
                 */
                $n_role->setParent($parent);
                if(!$this->findRole($parent)) {
                    $this->_roles[$parent] = \Sl_Model_Factory::object($n_role)->setName($parent);
                }
            }
            $this->_roles[$role_name] = $n_role;
        }
    }
    
    protected function verifyRoles() {
        foreach($this->getRoles() as $name=>$role) {
            if(!$role->getId() && $name) {
                if($exist_role = \Sl_Model_Factory::mapper($role)->findByName($name)) {
                    $this->_roles[$name] = $exist_role;
                } else {
                    $this->_roles[$name] = \Sl_Model_Factory::mapper($role)->save($role, true);
                }
            }
        }
    }
    
    protected function getRoles() {
        if(!isset($this->_roles)) {
            if($this->getModule('auth')) {
                $rs = \Sl_Model_Factory::mapper('role', $this->getModule('auth'))
                                ->fetchAll();
                $roles = array();
                if($rs) {
                    foreach($rs as $role) {
                        /*@var $role \Sl\Module\Auth\Model\Role*/
                        $roles[$role->getName()] = $role;
                    }
                }
                $this->_roles = $roles;
            } else {
                $this->_roles = array();
            }
        }
        return $this->_roles;
    }
    
    protected function findRole($name) {
        $this->getRoles(); // На всякий случай. Если никто еще не заполнял
        return isset($this->_roles[$name])?$this->_roles[$name]:null;
    }
	
	public function getCustomConfig($module_name, $section, $subsection = null, $debug = false){
        if(is_null($subsection)) {
            $config = $this->getModule($module_name)->section($section);
            
            if ($user = \Zend_Auth::getInstance()->getIdentity()){
                foreach ($user -> findFilledRelations() as $relation_name){
                    $relation = \Sl_Modulerelation_Manager::getRelations($user, $relation_name);	
                    if ( 
                        $relation instanceof \Sl\Modulerelation\Modulerelation &&
                        $relation->getCustomConfigs()
                    ) {
                        foreach ($user -> fetchRelated($relation->getName()) as $rel_obj_id => $related_object){
                            if ($custom_config = $this->getModule($module_name)->section(self::CUSTOM_CONFIGS_ROOT)
                                                                               ->$relation_name
                                                                               ->$rel_obj_id
                                                                               ->$section){
                                $config->merge($custom_config); 
                            }
                        }
                    }
                } 
            }
        } else { 
            if(!isset($this->getModule($module_name)->section($section)->$subsection)) {
                return null;
            }
            $config = $this->getModule($module_name)->section($section)->$subsection;
            //var_dump($config->readOnly());

            if ($customer = \Zend_Auth::getInstance()->getIdentity()){

                foreach ($customer -> findFilledRelations() as $relation_name){
                    $relation = \Sl_Modulerelation_Manager::getRelations($customer,$relation_name);	

                    if ( 
                        $relation instanceof \Sl\Modulerelation\Modulerelation &&
                        $relation->getCustomConfigs()){

                            foreach ($customer -> fetchRelated($relation->getName()) as $rel_obj_id => $related_object){
                                if ($custom_config = @$this->getModule($module_name)->section(self::CUSTOM_CONFIGS_ROOT)
                                                                                   ->$relation_name
                                                                                   ->$rel_obj_id
                                                                                   ->$section
                                                                                   ->$subsection){


                                    $config->merge($custom_config); 
                                } else {

                                }
                            }
                        }
                } 
            }
        }
        if($config) {
            //$config->setReadOnly();
        }
		return $config;
	}
        
        public static function find($modulename) {
            return self::getInstance()->getModule($modulename);
        }
	
}
