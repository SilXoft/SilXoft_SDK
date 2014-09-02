<?php
namespace Sl\Module\Auth\Controller;
class Admin extends \Sl_Controller_Action {
    
	public function listaclAction() {
		$resources = \Sl_Model_Factory::mapper ( 'resource', $this->_getModule () )->fetchAll ( null, 'name' );
		$this->view->resources = $resources;
		
		$roles = \Sl_Model_Factory::mapper ( 'role', $this->_getModule () )->fetchAll ();
		$this->view->roles = $roles;
		
		$permissoins = \Sl_Model_Factory::mapper ( 'permission', $this->_getModule () )->fetchAll ();
		$permissions_assoc = array ();
		
		foreach ( $permissoins as $permission ) {
			$permissions_assoc [$permission->getResourceId ()] [$permission->getRoleId ()] = $permission;
		}
		
        usort($roles, function($a, $b){
            if($a->getParent() == $b->getParent()) return 0;
            return ($a->getParent() < $b->getParent())?-1:1;
        });
        
        // Строим "всеролевой" ACL
        $acl = new \Sl\Acl\Acl();
        $acl->deny();
        foreach($roles as $role) {
            $parent = null;
            if($role->getParent()) {
                if($acl->hasRole($role->getParent())) {
                    $parent = $role->getParent();
                }
            }
            $acl->addRole($role->getName(), $parent);
            foreach($resources as $resource) {
                if(!$acl->has($resource->getName())) {
                    $acl->addResource($resource->getName());
                }
                if(isset($permissions_assoc[$resource->getId()][$role->getId()])) {
                    switch($permissions_assoc[$resource->getId()][$role->getId()]->getPrivilege()) {
                        case \Sl_Service_Acl::PRIVILEGE_DENY:
                            $acl->deny($role->getName(), $resource->getName());
                            break;
                        default:
                            $acl->allow($role->getName(), $resource->getName(), $permissions_assoc[$resource->getId()][$role->getId()]->getPrivilege());
                            break;
                    }
                }
            }
        }
        
        $this->view->mega_acl = $acl;
		$this->view->permissoins = $permissions_assoc;
	}
	public function ajaxcreatepermissionAction() {
		$this->view->result = true;
		try {
                        \Zend_Registry::get('cache')->getBackend()->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('acl'));
			$role_id = $this->getRequest ()->getParam ( 'role_id' );
			$resource_id = $this->getRequest ()->getParam ( 'resource_id' );
			$privilege = $this->getRequest ()->getParam ( 'value' );
			// die($role_id.' '.$resource_id);
			$permissions = \Sl_Model_Factory::mapper ( 'permission', $this->_getModule () )->fetchAllByRoleResource ( $role_id, $resource_id );
			$saved = array();
            if (count ( $permissions )) {
				foreach ( $permissions as $permission ) {
					$permission->setPrivilege ( $privilege );
					$permission->setActive ( 1 );
					
					$saved[] = \Sl_Model_Factory::mapper ($permission)->save ( $permission, true );
				}
			} else {
				$obj = \Sl_Model_Factory::object ( 'Permission', $this->_getModule () );
				$obj->setRoleId ( $role_id );
				$obj->setResourceId ( $resource_id );
				$obj->setPrivilege ( $privilege );
				
				$saved[] = \Sl_Model_Factory::mapper ($obj)->save ( $obj, true );
			}
            $this->view->saved = array_map(function($el) { return $el->toArray(); }, $saved);
		} catch ( Exception $e ) {
			$this->view->result = false;
			$this->view->description = $e->getMessage ();
		}
	}
	
	/**
	 * Удаление права
	 * 
	 */
	public function ajaxdeletepermissionAction() {
		$this->view->result = true;
		try {
                    \Zend_Registry::get('cache')->getBackend()->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('acl'));
			$role_id = $this->getRequest()->getParam('role_id');
			$resource_id = $this->getRequest()->getParam('resource_id');
			$permissions = \Sl_Model_Factory::mapper ('permission', $this->_getModule())->fetchAllByRoleResource($role_id, $resource_id);
			$saved = array();
            if (count ( $permissions )) {
				foreach ( $permissions as $permission ) {
					$saved[] = \Sl_Model_Factory::mapper($permission, $this->_getModule())
										->save($permission->setPrivilege(\Sl_Service_Acl::PRIVILEGE_DENY), true);
				}
			} else {
                $obj = \Sl_Model_Factory::object ( 'permission', $this->_getModule () );
				$obj->setRoleId ( $role_id );
				$obj->setResourceId ( $resource_id );
				$obj->setPrivilege (\Sl_Service_Acl::PRIVILEGE_DENY);
				
				$saved[] = \Sl_Model_Factory::mapper ($obj)->save ( $obj, true );
            }
            $this->view->saved = array_map(function($el) { return $el->toArray(); }, $saved);
		} catch (\Exception $e ) {
			$this->view->result = false;
			$this->view->description = $e->getMessage ();
		}
	}
	public function ajaxrebuildAction() {
		set_time_limit(0);
        $cache = \Zend_Registry::get('cache');
        /*@var $cache \Zend_Cache_Core*/
        $cache->getBackend()->clean();
        
        $this->view->result = true;
		
		try {
			
			$modules = \Sl_Module_Manager::getModules ();
			$modules_resources = array ();
			
			foreach ( $modules as $module_name => $module ) {
                            try{
			    //Перебудова конфіга зв'язків
                $module->registerModulerelations(true);
                
				// Створення ресурсів MVC:module|controller|action
				if (is_dir ( $module->getDir () . '/Controller' )) {
					$dh = opendir ( $module->getDir () . '/Controller' );
					if ($dh) {
						
						while ( false !== ($filename = readdir ( $dh )) ) {
							$matches = array ();
							if (preg_match ( '/(.+)\.php$/', $filename, $matches )) {
								$controller_name = strtolower ( $matches [1] );
								$controller_class_name = $module->getControllerClassName ( $controller_name );
                                if (class_exists ( $controller_class_name )) {
									foreach ( get_class_methods ( $controller_class_name ) as $method ) {
										$method_matches = array ();
                                        if (preg_match ( '/(.+)Action$/', $method, $method_matches )) {
											$modules_resources [] = \Sl_Service_Acl::joinResourceName ( array (
													'type' => \Sl_Service_Acl::RES_TYPE_MVC,
													'module' => $module_name,
													'controller' => $controller_name,
													'action' => $method_matches [1] 
											) );
										}
									}
								}
							}
						}
					}
				}
				
				// Створення ресурсів OBJ:module|name|field
				if (is_dir ( $module->getDir () . '/Model' )) {
					$dh = opendir ( $module->getDir () . '/Model' );
					if ($dh) {
						
						while ( false !== ($filename = readdir ( $dh )) ) {
							$matches = array ();
							if (preg_match ( '/(.+)\.php$/', $filename, $matches )) {
								$model_name = strtolower ( $matches [1] );
								$model_class_name = $module->getModelClassName ( $model_name );
								if (class_exists ( $model_class_name )) {
                                    $class = new \ReflectionClass($model_class_name);
                                    if($class->isAbstract()) {
                                        continue;
                                    }
                                    $model = new $model_class_name();
									// список властивостей
                                    foreach ( array_keys ( call_user_func ( array (
											$model,
											'toArray' 
									) ) ) as $var ) {
										
										$modules_resources [] = \Sl_Service_Acl::joinResourceName ( array (
												'type' => \Sl_Service_Acl::RES_TYPE_OBJ,
												'module' => $module_name,
												'name' => $model_name,
												'field' => $var 
										) );
									}
                                                                   try {
									// список зв'язків
									$relations = \Sl_Modulerelation_Manager::getRelations ( $model );
                                                                       // if(get_class($model) == 'Application\Module\Itftc\Model\Subscriber')
                                                             
			       
									foreach ( $relations as $relation ) {
										$modules_resources [] = \Sl_Service_Acl::joinResourceName ( array (
												'type' => \Sl_Service_Acl::RES_TYPE_OBJ,
												'module' => $module_name,
												'name' => $model_name,
												'field' => $relation->getName () 
										) );
									}
                                                                } catch ( \Exception $e ) {
                    
                                                                    $this->view->result = false;
                                                                    $this->view->description = 'rebuild config relation ajaxrebuild'.$e->getMessage ();
                                                                  }                                                                        
								}
							}
						}
					}
				}
				
				// Створення ресурсів FIELD:module|key|field
				$forms = $module->section ( 'forms' );
				
				if (is_object ( $forms ) && count ( $forms->toArray () )) {
                                    try{
					foreach ( $forms as $form_name => $fields ) {
						if (is_object ( $fields->fields ))
							foreach ( $fields->fields->toArray () as $field => $options ) {
								
								$modules_resources [] = \Sl_Service_Acl::joinResourceName ( array (
										'type' => \Sl_Service_Acl::RES_TYPE_FIELD,
										'module' => $module_name,
										'name' => $form_name,
										'field' => $field 
								) );
							}
					}
                                                     } catch ( \Exception $e ) {
                    
                            $this->view->result = false;
                            $this->view->description = 'rebuild form resource '.$e->getMessage ();
                            
                            
                  }     
                                        
				}
                                
                                                  } catch ( \Exception $e ) {
                    
                            $this->view->result = false;
                            $this->view->description = 'save resource 2 '.$e->getMessage ();
                            
                            
                  }
			}
             try {
			$resources = \Sl_Model_Factory::mapper ( 'resource', $this->_getModule () )->fetchAll('active in (0 , 1)');
			
			$to_disable_list = $resources;
			foreach ( $resources as $key => $record ) {
				while (false !== ($resource_number = array_search ( $record->getName(), $modules_resources ))) {
					unset ( $modules_resources [$resource_number] );
					unset ( $to_disable_list [$key] );
					if($record->fetchType() != \Sl_Service_Acl::RES_TYPE_CUSTOM) {
                        if (!$record->getActive()){
                            $record->setActive(1);
                            \Sl_Model_Factory::mapper ( $record )->save ( $record );
                        }
                    }
				}
			}
                  } catch ( \Exception $e ) {
                    
                            $this->view->result = false;
                            $this->view->description = 'save resource 4 '.$e->getMessage ();
                            
                            
                  } 
			if (count ( $to_disable_list )) {
                            
				foreach ( $to_disable_list as $key => $record ) {
                                    
					if($record->fetchType() != \Sl_Service_Acl::RES_TYPE_CUSTOM) {
                                            try{
                                                $record->setActive ( 0 );
                                                \Sl_Model_Factory::mapper ( $record )->save ( $record );
                                            
                                      } catch ( \Exception $e ) {
                    
                                        $this->view->result = false;
                                        //echo $e->getTraceAsstring();
                                        //die;
                                        $this->view->description = 'save record '.  get_class($record).'  '.$e->getMessage ();                                                       
                                       }
                                       }
                                            
				}
			}
			
			if (count ( $modules_resources )) {
                            
                            
				foreach ( $modules_resources as $record ) {
                                    try{
					$obj = \Sl_Model_Factory::object ( 'Resource', $this->_getModule () );					
					$obj->setName ( $record );
					\Sl_Model_Factory::mapper ( $obj )->save ( $obj );
                                        
                                        
                                    } catch ( \Exception $e ) {                    
                                        $this->view->result = false;
                                        $this->view->description = 'save resource '. get_class($obj) .' record '.$record.' '.$e->getMessage ();                            
                                    }
				}

			}
		} catch ( \Exception $e ) {
                    
			$this->view->result = false;
			$this->view->description = $e->getMessage ();
		}
	}
    
    public function permissionsAction() {
        $modules = \Sl_Module_Manager::getModules();
        $this->view->modules = $modules;
    }
    
    public function formsmainAction() {
        $this->view->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setRender('permissions-tabs');
        $module = $this->getRequest()->getParam('m', '');
        if(!$module) {
            throw new Exception('Such module "'.$module.'" not found. '.__METHOD__);
        }
        $type = \Sl_Service_Acl::RES_TYPE_FIELD;
        $this->_buildDataByTypeModule($type, $module);
    }
    
    public function customsmainAction() {
        $this->view->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setRender('permissions-tabs');
        $module = $this->getRequest()->getParam('m', '');
        if(!$module) {
            throw new Exception('Such module "'.$module.'" not found. '.__METHOD__);
        }
        $type = \Sl_Service_Acl::RES_TYPE_CUSTOM;
        $this->_buildDataByTypeModule($type, $module);
    }
    
    public function pagesmainAction() {
        $this->view->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setRender('permissions-tabs');
        $module = $this->getRequest()->getParam('m', '');
        if(!$module) {
            throw new Exception('Such module "'.$module.'" not found. '.__METHOD__);
        }
        $type = \Sl_Service_Acl::RES_TYPE_MVC;
        $this->_buildDataByTypeModule($type, $module);
    }
    
    public function modelsmainAction() {
        $this->view->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setRender('permissions-tabs');
        $module = $this->getRequest()->getParam('m', '');
        if(!$module) {
            throw new Exception('Such module "'.$module.'" not found. '.__METHOD__);
        }
        $type = \Sl_Service_Acl::RES_TYPE_OBJ;
        $this->_buildDataByTypeModule($type, $module);
    }
    
    protected function _buildDataByTypeModule($type, $module) {
        $roles = \Sl_Model_Factory::mapper('role', $this->_getModule())->fetchAll();
        $resources = \Sl_Model_Factory::mapper ('resource', $this->_getModule())->fetchAllByTypeModule($type, $module);
        
        $resources_array = array();
        
		$ps = \Sl_Model_Factory::mapper('permission', $this->_getModule())->fetchAll();
		$permissions = array ();
		
		foreach($ps as $permission) {
			$permissions[$permission->getResourceId()][$permission->getRoleId()] = $permission;
		}
        
        $this->view->layout()->disableLayout(true);
        $acl = new \Sl\Acl\Acl();
        $acl->deny();
        foreach($roles as $role) {
            $parent = null;
            if($role->getParent()) {
                if($acl->hasRole($role->getParent())) {
                    $parent = $role->getParent();
                }
            }
            $acl->addRole($role->getName(), $parent);
            foreach($resources as $resource) {
                if(!$acl->has($resource->getName())) {
                    $acl->addResource($resource->getName());
                }
                $res_array = \Sl_Service_Acl::splitResourceName($resource->getName());
                $resources_array[$res_array['name']?$res_array['name']:$res_array['controller']][$resource->getId()] = $resource;
                if(isset($permissions[$resource->getId()][$role->getId()])) {
                    switch($permissions[$resource->getId()][$role->getId()]->getPrivilege()) {
                        case \Sl_Service_Acl::PRIVILEGE_DENY:
                            $acl->deny($role->getName(), $resource->getName());
                            break;
                        default:
                            $acl->allow($role->getName(), $resource->getName(), $permissions[$resource->getId()][$role->getId()]->getPrivilege());
                            break;
                    }
                }
            }
        }
        
        $this->view->resources = $resources;
        $this->view->reses = $resources_array;
        $this->view->roles = $roles;
        $this->view->permissions = $permissions;
        $this->view->mega_acl = $acl;
        
    }
    
    /**
     * Добавляет всем пользователям системы настройки
     * Если их еще нет
     */
    public function applyusersettingsAction() {
        header('Content-type: text/plain; charset: utf-8');
        set_time_limit(0);
        echo "\r\n";
        try {
            $res_data_tpl = array(
                'type' => \Sl_Service_Acl::RES_TYPE_OBJ,
                'module' => 'auth',
                'name' => 'setting',
            );
            $needed_resources = array(
                \Sl_Service_Acl::joinResourceName(array_merge($res_data_tpl, array(
                    'field' => 'usersetting'
                ))),
                \Sl_Service_Acl::joinResourceName(array_merge($res_data_tpl, array(
                    'field' => 'listview'
                ))),
                \Sl_Service_Acl::joinResourceName(array_merge($res_data_tpl, array(
                    'field' => 'filters'
                ))),
                \Sl_Service_Acl::joinResourceName(array_merge($res_data_tpl, array(
                    'field' => 'fieldsets'
                ))),
                \Sl_Service_Acl::joinResourceName(array_merge($res_data_tpl, array(
                    'field' => 'state'
                ))),
            );
            foreach($needed_resources as $res) {
                if(!\Sl_Service_Acl::isAllowed($res, \Sl_Service_Acl::PRIVELEGE_UPDATE)) {
                    throw new \Exception('Need "'.$res.'" UPDATE permissions to do this. '.__METHOD__);
                }
            }
            $users = \Sl_Model_Factory::mapper('user', 'auth')->fetchAll();
            echo "Total user count: ".count($users)."\r\n";
            foreach($users as $k=>$user) {
                echo "Start processing user #".$user->getId()."\r\n";
                $user = \Sl_Model_Factory::mapper($user)->findRelation($user, 'usersetting');
                if(!$user->fetchOneRelated('usersetting')) {
                    $setting = \Sl_Model_Factory::object('setting', 'auth');
                    $setting->assignRelated('usersetting', array($user));
                    try {
                        echo "Saving usersettings for ".$user->getLogin().".....\r\n";
                        \Sl_Model_Factory::mapper($setting)->save($setting);
                    } catch(\Exception $e) {
                        echo "Save error: ".$e->getMessage()."\r\n";
                    }
                    unset($users[$k]); // Чистим лишнее
                } else {
                    echo "Settings already exists for this user\r\n";
                }
                echo "User #".$user->getId()." done\r\n";
            }
            echo 'Done';
        } catch (\Exception $e) {
            echo 'Error: '.$e->getMessage();
        }
        die;
    }
    
    public function postDispatch() {
        //parent::postDispatch();
    }
}