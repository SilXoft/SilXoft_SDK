<?php

class Dashboard_ResourceController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $tables = 'allow';
        $columns = array(
            'id' => 'INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (id)',
            'resources' => 'VARCHAR(50)',
            'privileges' => 'VARCHAR(50)',
        );
        $roles = Application_Model_Factory::mapper('role')->fetchAll();

        foreach ($roles as $role) {
            $columns[$role->getNickname()] = 'TINYINT(1)';
        }
        $table = new Ext_Db_Tables();
        $table->addTable($tables, $columns);
        echo 'уже';
        die;


        $front = $this->getFrontController();
        $acl = array();
        foreach ($front->getControllerDirectory() as $module => $path) {

            foreach (scandir($path) as $file) {

                if (strstr($file, "Controller.php") !== false) {

                    include_once $path . DIRECTORY_SEPARATOR . $file;

                    foreach (get_declared_classes() as $class) {

                        if (is_subclass_of($class, 'Zend_Controller_Action')) {

                            $controller = strtolower(substr($class, 0, strpos($class, "Controller")));
                            $actions = array();

                            foreach (get_class_methods($class) as $action) {

                                if (strstr($action, "Action") !== false) {
                                    $actions[] = $action;
                                }
                            }
                        }
                    }

                    $acl[$module][$controller] = $actions;
                }
            }
        }
        die(__METHOD__);
    }

    public function viewAction() {
        $resources = Sl_Model_Factory::mapper('resources')->fetchAll();
        $res_array = array();
        if($resources) {
            foreach($resources as $resource) {
                $data = Sl_Service_Acl::splitResourceName($resource->getName());
                $res_array[$data['type']][] = $resource;
            }
        }
        $this->view->resources = $res_array;
    }
    
    public function scanAction() {
        // Старые ресурсы
        $resources = Sl_Model_Factory::mapper('resources')->fetchAll();
        $old_res_names = array();
        $old_res_array = array();
        if($resources) {
            foreach($resources as $resource) {
                $old_res_names[$resource->getId()] = $resource;
                $old_res_names[$resource->getId()] = $resource->getName();
            }
        }
        
        // Ищем новые ресурсы
        $new_res = $this->_searchAllMvcRecources();
        $new_res = array_merge($new_res, $this->_searchAllModelResources());
        
        $to_add = array_diff($new_res, $old_res_names);
        $to_delete = array_diff($old_res_names, $new_res);
        
        $result = array();
        
        foreach($to_add as $name) {
            $res = Sl_Model_Factory::object('resources');
            $res->setActive(1);
            $res->setName($name);
            $res->setDescription('Новый');
            try {
                Sl_Model_Factory::mapper($res)->save($res);
                $result['added'][] = $res->getName();
            } catch(Sl_Exception_Model $e) {
                $result['errors'][] = 'Model error: '.$e->getMessage();
            } catch(Sl_Exception_Db $e) {
                $result['errors'][] = 'Db error: '.$e->getMessage();
            } catch(Exception $e) {
                $result['errors'][] = $e->getMessage();
            }
        }
        foreach($to_delete as $name) {
            foreach($old_res_names as $id=>$old_name) {
                if($old_name == $name) {
                    try {
                        Sl_Model_Factory::mapper($old_res_array[$id])->delete($old_res_array[$id]);
                        $result['deleted'][] = $res->getName();
                    } catch(Sl_Exception_Model $e) {
                        $result['errors'][] = 'Model error: '.$e->getMessage();
                    } catch(Sl_Exception_Db $e) {
                        $result['errors'][] = 'Db error: '.$e->getMessage();
                    } catch(Exception $e) {
                        $result['errors'][] = $e->getMessage();
                    }
                }
            }
        }
        $this->view->added = isset($result['added'])?$result['added']:null;
        $this->view->deleted = isset($result['deleted'])?$result['deleted']:null;
        $this->view->errors = isset($result['errors'])?$result['errors']:null;
    }
    
    protected function _searchAllMvcRecources() {
        $resources = array();
        if(Zend_Controller_Front::getInstance()->getControllerDirectory()) {
            foreach(Zend_Controller_Front::getInstance()->getControllerDirectory() as $module=>$path) {
                $dir = opendir($path);
                if($dir) {
                    while(false !== ($filename = readdir($dir))) {
                        if(preg_match('/Controller\.php$/', $filename)) {
                            $controller = strtolower(preg_replace('/^(.+)Controller\.php$/', '$1', $filename));
                            // Пытаемся создать класс
                            try {
                                $class_name = ucfirst($module).'_'.ucfirst($controller).'Controller';
                                
                                class_exists($class_name) || Zend_Loader_Autoloader::autoload($class_name);
                                
                                $methods = get_class_methods($class_name);
                                if($methods) {
                                    foreach($methods as $method) {
                                        if(preg_match('/^(.+)Action$/', $method)) {
                                            $resources[] = 'mvc:'.implode('|', array($module, $controller, $method));
                                        }
                                    }
                                }
                            } catch(Exception $e) {
                                throw new Sl_Exception_Controller('Error while searching MVC recources', 0, $e);
                            }
                        }
                    }
                    closedir($dir);
                }
            }
        }
        return $resources;
    }
    
    protected function _searchAllModelResources() {
        $resources = array();
        
        if(Zend_Controller_Front::getInstance()->getControllerDirectory()) {
            $modules = Zend_Controller_Front::getInstance()->getControllerDirectory();
            $modules['application'] = APPLICATION_PATH.'/models';
            foreach($modules as $module=>$path) {
                if(!is_dir($path."/../models")) continue;
                $dir = opendir($path."/../models");
                if($dir) {
                    while(false !== ($filename = readdir($dir))) {
                        if(preg_match("/(Abstract|Factory)/", $filename)) continue;
                        if(preg_match('/^[A-Z][a-z]+\.php$/', $filename)) {
                            $resources[] = Sl_Service_Acl::joinResourceName(array(
                                'type' => Sl_Service_Acl::RES_TYPE_MODEL,
                                'module' => $module,
                                'name' => strtolower(preg_replace('/^([A-Z][a-z]+)\.php$/', '$1', $filename)),
                            ));
                        }
                    }
                    closedir($dir);
                }
            }
        }
        
        return $resources;
    }
}

