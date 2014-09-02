<?php
namespace Sl\Module\Auth\Listener;

use Sl_Service_Acl as AclService;

class Auth extends \Sl_Listener_Abstract implements \Sl_Listener_Bootstrap_Interface,
                                                    \Sl_Listener_Router_Interface,
                                                    \Sl_Listener_Model_Interface,
                                                    \Sl_Listener_Acl_Interface,
                                                    \Sl_Listener_View_Interface {
    protected $_current_request;
    protected $_loaded_permissions = array();
     
     
     /**
	 * Перенаправляем на страницу "/auth" если пользователь не авторизирован
	 */
	public function onRouteShutdown(\Sl_Event_Router $event) {
		$request = $event -> getOption('request');
        $this->_current_request = $request;
		if (!\Zend_Auth::getInstance() -> hasIdentity()) {
			if (!$request || ($request -> getModuleName() != 'auth')) {
				//\Zend_Controller_Action_HelperBroker::getStaticHelper('redirector') -> gotoUrl('/auth/main/form');
			}
		}
	}
	
	/**
	 * Переключаем шаблон ввида в зависимости от авторизации пользователя
	 */
	public function onAfterLayoutInit(\Sl_Event_Bootstrap $event) {
		if (!($layout = $event -> getLayout()))
			return;
        
		/*if (!\Zend_Auth::getInstance() -> hasIdentity()) {
			$layout -> setLayout('auth');
		}*/
		$layout -> setViewScriptPath($this -> getModule() -> getDir() . '/View');
	}

	public function onAfterInit(\Sl_Event_Router $event) {
		if (!($router = $event -> getRouter()))
			return;
		if (!$router -> hasRoute('logout')) {
			$route = new \Zend_Controller_Router_Route_Static('logout', array(
				'module' => 'auth',
				'controller' => 'main',
				'action' => 'logout',
			));

			$router -> addRoute('logout', $route);
		}
	}

	public function onBeforeInit(\Sl_Event_Router $event) {
	}

	public function onRouteStartup(\Sl_Event_Router $event) {
	}

	public function onSetRequest(\Sl_Event_Router $event) {
	}

	public function onSetResponse(\Sl_Event_Router $event) {
	}

	public function onDispatchLoopShutdown(\Sl_Event_Router $event) {
	}

	public function onDispatchLoopStartup(\Sl_Event_Router $event) {
	}

	public function onGetRequest(\Sl_Event_Router $event) {
	}

	public function onGetResponse(\Sl_Event_Router $event) {
	}

	public function onPostDispatch(\Sl_Event_Router $event) {
	}

	public function onPreDispatch(\Sl_Event_Router $event) {
	}

	public function onAfterRequestInit(\Sl_Event_Bootstrap $event) {
	}

	public function onAfterTranslationInit(\Sl_Event_Bootstrap $event) {
	}

	public function onAfterViewInit(\Sl_Event_Bootstrap $event) {
	}

	public function onBeforeLayoutInit(\Sl_Event_Bootstrap $event) {
	}

	public function onBeforeRequestInit(\Sl_Event_Bootstrap $event) {
	}

	public function onBeforeTranslationInit(\Sl_Event_Bootstrap $event) {
	}

	public function onBeforeViewInit(\Sl_Event_Bootstrap $event) {
	}

	public function onBeforeSave(\Sl_Event_Model $event) {

	}

	public function onAfterSave(\Sl_Event_Model $event) {
            $model = $event->getModel();
            if($model instanceof \Sl\Module\Auth\Model\User) {
                if($model->getId()) {
                    if(!$model->issetRelated('usersetting')) {
                        $model = \Sl_Model_Factory::mapper($model)->findRelation($model, 'usersetting');
	}
                    if(!count($model->fetchRelated('usersetting'))) {
                        $setting = \Sl_Model_Factory::object('setting', 'auth');
                        $setting->assignRelated('usersetting', array($model));
                        \Sl_Model_Factory::mapper($setting)->save($setting, false, false);
                    }
                }
            }
	}

	public function onBeforeAclCreate(\Sl_Event_Acl $event) {
	}

	/**
	 * Наповнення Acl
	 * 
	 */
	public function onAfterAclCreate(\Sl_Event_Acl $event) {
	    $roles = array();
            $oRoles = array();
            if (\Zend_Auth::getInstance()->hasIdentity()) {
                $full_user = \Zend_Auth::getInstance()->getIdentity();
               if (!$full_user-> issetRelated('userroles')){
                   $full_user = \Sl_Model_Factory::mapper($full_user)->findRelation($full_user, 'userroles');
                   \Zend_Auth::getInstance()->getStorage()->write($full_user);
               }
                \Sl_Service_Acl::setCurrentUser($full_user);
               
                $tmp_roles = $full_user->fetchRelated('userroles');
                if (is_array($tmp_roles)) {
                    usort($tmp_roles, function($a, $b) {
                        if ($a->getParent() == $b->getParent())
                            return 0;
                        return ($a->getParent() < $b->getParent()) ? 1 : -1;
                    });
                }
                
                foreach ($tmp_roles as $role) {
                    $roles[$role->getId()] = $role->getName();
                    $oRoles[] = $role;
                    if ($role->getParent()) {
                  //      \Sl_Service_Acl::acl()->addRole(new \Zend_Acl_Role($role->getName()), $role->getParent());
                    } else {
                  //      \Sl_Service_Acl::acl()->addRole(new \Zend_Acl_Role($role->getName()));
                    }
                }
            }

        if (!count($roles)) $roles[\Sl_Service_Acl::getDefaultRole()->getId()]=\Sl_Service_Acl::getDefaultRole();
        \Sl_Service_Acl::setCurrentRoles($oRoles);
        unset($oRoles);
        if(USE_AJAX) {
            return;
        }
        $role_ids = array_keys($roles);
        sort($role_ids);
        $cache_id = 'acl_roles|'.implode(':', $role_ids);
        
        $cache = \Zend_Registry::get('cache')->getBackend();
        /*@var $cache \Zend_Cache_Backend_Memcached*/
        if(!$cache->test($cache_id)) {
            $permissions = \Sl_Model_Factory::mapper('permission', $this->getModule()) -> fetchAllByRoles($roles);
            $cache->save(serialize($permissions), $cache_id, array('acl'));
        } else {
            $permissions = unserialize($cache->load($cache_id));
        }
        
		foreach($permissions as $array){
			if (!\Sl_Service_Acl::acl()->has(new \Zend_Acl_Resource($array->resource_name))) 
			
			\Sl_Service_Acl::acl() -> add(new \Zend_Acl_Resource($array->resource_name));
			
			
			$assertion = \Sl_Assertion_Factory::getAssertion($array->resource_name);
			
			if ($array->privilege == \Sl_Service_Acl::PRIVELEGE_ACCESS) {
				    
				\Sl_Service_Acl::acl() -> allow(null, $array->resource_name, null, $assertion);
                //Додавання зялежних action-ів
                if ($grouped = \Sl_Service_Acl::getGroupedResources($array->resource_name)){
                   
                    foreach ($grouped as $resource){
                    
                        if (!\Sl_Service_Acl::acl()->has(new \Zend_Acl_Resource($resource))) 
                            \Sl_Service_Acl::acl() -> add(new \Zend_Acl_Resource($resource));
                        $gr_assertion = \Sl_Assertion_Factory::getAssertion($resource);
                        \Sl_Service_Acl::acl() -> allow(null, $resource, null, $assertion);
                    
                    }
                    
                }
                
                
			} elseif($array->privilege == \Sl_Service_Acl::PRIVILEGE_DENY) {
				//\Sl_Service_Acl::acl() -> deny(null, $array->resource_name, $array->privilege);
			} elseif($array->privilege == \Sl_Service_Acl::PRIVELEGE_UPDATE) {
			 
                 
				\Sl_Service_Acl::acl() -> allow(null, $array->resource_name, \Sl_Service_Acl::PRIVELEGE_UPDATE, $assertion);
				\Sl_Service_Acl::acl() -> allow(null, $array->resource_name, \Sl_Service_Acl::PRIVELEGE_READ, $assertion);
			
				
			} else {
				\Sl_Service_Acl::acl() -> allow(null, $array->resource_name, $array->privilege, $assertion);
			}
		}
        
      
	}

    public function onAfterContent(\Sl_Event_View $event) {
        
    }
    
    public function onHeadScript(\Sl_Event_View $event) {
        $request = $this->_current_request;
        $current_action = $request->getParam('action',false);
             
        if ($current_action == 'list' && \Zend_Auth::getInstance() -> hasIdentity()) {
           
              //if ('list'==$request->getParam('action',false)) 
              {
                //Виведення js для перевірки даних про об'єкт на listview
                $res = \Sl_Service_Acl::joinResourceName(array(
                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                    'module' => 'auth',
                    'controller' => 'main',
                    'action' => 'ajaxmodeleditinformation',
                ));
                
                if(\Sl_Service_Acl::isAllowed($res)) {
                     
                    $event->getView()->headScript()->appendFile('/auth/main/ajaxmodeleditinformation.js');
                     
                }
                 
                 
                
              }
         } else {
             
         }
        
    }
    
    
    public function onBeforeContent(\Sl_Event_View $event) {
      
    }

    public function onBodyBegin(\Sl_Event_View $event) {
        
    }

    public function onBodyEnd(\Sl_Event_View $event) {
        
    }

    public function onContent(\Sl_Event_View $event) {
        $request = $event -> getOption('request');
		if (!\Zend_Auth::getInstance() -> hasIdentity()) {
			$res = \Sl_Service_Acl::joinResourceName(array(
                'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                'module' => 'auth',
                'controller' => 'main',
                'action' => 'form',
            ));
            if(\Sl_Service_Acl::isAllowed($res)) {
                
                echo $event->getView()->action('form', 'main', 'auth');
            }
		}
                
                
        $cur_user = \Zend_Auth::getInstance()->getIdentity();
        if($cur_user->getId() === \Sl_Service_Settings::value('GUEST_USER_ID', false)) {
            $res = \Sl_Service_Acl::joinResourceName(array(
                'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                'module' => 'auth',
                'controller' => 'main',
                'action' => 'form',
            ));
            if(\Sl_Service_Acl::isAllowed($res)) {
                echo $event->getView()->action('form', 'main', 'auth');
            }
        }
    }

    public function onFooter(\Sl_Event_View $event) {
        
    }

    public function onHeadLink(\Sl_Event_View $event) {
        
    }

    public function onHeadTitle(\Sl_Event_View $event) {
        
    }

    public function onHeader(\Sl_Event_View $event) {
        
    }

    public function onLogo(\Sl_Event_View $event) {
        
    }

    public function onNav(\Sl_Event_View $event) {
        
    }

    public function onPageOptions(\Sl_Event_View $event) {
        
    }

    public function onBeforePageHeader(\Sl_Event_View $event) {
        
    }

    public function onAppRun(\Sl_Event_Bootstrap $event) {
        
    }

    public function onIsAllowed(\Sl_Event_Acl $event) {
        // Работает только если AJAX
        if(USE_AJAX) {
            $resource = $event->getOption('resource');
            $res_data = AclService::splitResourceName($resource);
            
            $type = $res_data['type'];
            $module = $res_data['module'];
            $name = isset($res_data['controller'])?$res_data['controller']:$res_data['name'];
            
            $alias = $type.AclService::RES_TYPE_SEPARATOR.implode(AclService::RES_DATA_SEPARATOR, array($module, $name));
            if(!isset($this->_loaded_permissions[$alias])) {
                $roles = array_keys(\Zend_Auth::getInstance()->getIdentity()->fetchRelated('userroles'));
                
                $acl = AclService::acl();
                
                $roles = array_keys(\Zend_Auth::getInstance()->getIdentity()->fetchRelated('userroles'));
                
                $cache = \Zend_Registry::get('cache')->getBackend();
                /*@var $cache \Zend_Cache_Backend*/
                $cache_id = 'acl_part_'.$alias.':'.implode('-', $roles);
                if(!$cache->test($cache_id)) {
                    $permissions = \Sl_Model_Factory::mapper('permission', $this->getModule())
                                            ->fetchAllByNameRoles($alias, $roles);
                    $cache->save(serialize($permissions), $cache_id, array('acl'));
                } else {
                    $permissions = unserialize($cache->load($cache_id));
                }
                foreach($permissions as $array) {
                    if (!$acl->has($array->resource_name)) {
                        $acl->add(new \Zend_Acl_Resource($array->resource_name));
                    }

                    $assertion = \Sl_Assertion_Factory::getAssertion($array->resource_name);

                    if($array->privilege == AclService::PRIVELEGE_ACCESS) {
                        $acl->allow(null, $array->resource_name, null, $assertion);
                        // Додавання зялежних action-ів
                        if($grouped = AclService::getGroupedResources($array->resource_name)) {
                            foreach ($grouped as $resource) {
                                if(!$acl->has($resource)) {
                                    $acl->add(new \Zend_Acl_Resource($resource));
                                }
                                $gr_assertion = \Sl_Assertion_Factory::getAssertion($resource);
                                $acl->allow(null, $resource, null, $gr_assertion);
                            }
                        }
                    } elseif ($array->privilege == AclService::PRIVILEGE_DENY) {
                        
                    } elseif ($array->privilege == AclService::PRIVELEGE_UPDATE) {
                        $acl->allow(null, $array->resource_name, AclService::PRIVELEGE_UPDATE, $assertion);
                        $acl->allow(null, $array->resource_name, AclService::PRIVELEGE_READ, $assertion);
                    } else {
                        $acl->allow(null, $array->resource_name, $array->privilege, $assertion);
                    }
                }
                
                \Zend_Registry::set('Zend_Acl', $acl);
                // Переделать на какое-то подобие merge
                AclService::__readAcl();
                //echo (AclService::acl()->isAllowed(null, $resource)?1:0)."\r\n";
                /*****************************/
                $this->_loaded_permissions[$alias] = true;
            }
        }
    }

    public function onAfterSessionInit(\Sl_Event_Bootstrap $event) {
        
    }

}
