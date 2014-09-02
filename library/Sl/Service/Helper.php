<?php
namespace Sl\Service;
use Sl\Exception\Service as Exception;

class Helper {

    const HASH_SALT = 'lakjfhadslkfhbasdjhfvbjhvbdj';
    const POPUP_LIST_ACTION = 'popuplist';
	const DUPLICATE_ACTION = 'duplicate';
    const VALIDATE_ACTION = 'ajaxvalidate';
	const LIST_ACTION = 'list';
	const POPUP_CREATE_ACTION = 'ajaxcreate';
	const POPUP_EDIT_ACTION = 'ajaxedit';
    const POPUP_AJAXLIST_ACTION = 'ajaxlist';
	const RETURN_TO_EDIT_ACTION = 'returntoedit';
    const TO_EDIT_ACTION = 'edit';
    const TO_DETAILED_ACTION = 'detailed';
    const GROUPPRINT_ACTION = 'groupprint';    
	const PRINT_ACTION = 'print';
    const CREATE_ACTION = 'create';
    const AJAX_CREATE_ACTION = 'ajaxcreate';
    //const DELETE_ACTION = 'delete';
    const AJAX_DELETE_ACTION = 'ajaxdelete';
    const AJAX_ARCHIVE_ACTION = 'ajaxarchive';
    const AJAX_SELECTED_ITEMS_ACTION = 'ajaxselecteditems';
    const EXPORT_LIST_ACTION = 'export';
    const LOG_ACTION = 'log';
    const AJAX_LOG_ACTION = 'ajaxlog';
    const EMAIL_ACTION = 'getemailpflist';
    const MODEL_ALIAS_SEPARATOR = '.';
    
    
    protected static $_view;
    
    protected static function getView() {
        if(!isset(self::$_view)) {
            self::$_view = new \Sl_View();
        }
        return self::$_view;
    }
    
    public static function hash($string) {
        return md5($string . self::HASH_SALT . $string);
    }
    
    public static function getModelAlias($model, $module = null) {
        if ($model instanceof \Sl_Model_Abstract){
                return strtolower(implode(self::MODEL_ALIAS_SEPARATOR,array($model->findModuleName(),$model->findModelName())));            
        } else {
            return strtolower(implode(self::MODEL_ALIAS_SEPARATOR,array($module,$model)));
        }
    }
 
    public static function getModelExtend($model) {

        $done = false;
        $i=0;
        do {           
            $parent_class = new \ReflectionClass($model);
            $parent_class_name = $parent_class->getParentClass()->getName();
            $extend[$i] = self::getModelAlias($model);
            
            if ( $parent_class_name == 'Sl_Model_Abstract' ) {
                    $done = true;
            }
            else{
                $model = \Sl_Model_Factory::object($parent_class_name);
            }            
            $i++;
            if($i==10) die(' getModelExtend 10');
        } while ($done == false);       

        return '|'.implode('|', array_reverse($extend)).'|';
    }  
    
        public static function getModelByExtend($extend) {

            if($extend){
                $alias = end(array_filter (explode('|', $extend) ));                
                return self::getModelByAlias($alias);
            }
                
    }  
    
    
    public static function getModelnameByAlias($alias) {
        if (strpos($alias,self::MODEL_ALIAS_SEPARATOR)){
            list($module,$model) = explode(self::MODEL_ALIAS_SEPARATOR,$alias);
            return $model;
        }
    }
    
    public static function getModuleByAlias($alias) {
        if (strpos($alias,self::MODEL_ALIAS_SEPARATOR)){
            list($module,$model) = explode(self::MODEL_ALIAS_SEPARATOR,$alias);
            return \Sl_Module_Manager::getInstance()->getModule($module);
        }
    }
    
    public static function getModulenameByAlias($alias) {
        if (strpos($alias,self::MODEL_ALIAS_SEPARATOR)){
            list($module,$model) = explode(self::MODEL_ALIAS_SEPARATOR,$alias);
            return $module;
        }
    }
    
    public static function getModelByAlias($alias, $module_name = null) {
        if(strpos($alias, self::MODEL_ALIAS_SEPARATOR)){
            list($module, $model) = explode(self::MODEL_ALIAS_SEPARATOR, $alias);
        } elseif(!is_null($module_name)) {
            list($module, $model) = array($module_name, $alias);
        } else {
            throw new \Exception('Unknown params combination. '.__METHOD__);
        }
        return \Sl_Model_Factory::object($model, $module);
    }
    
    public static function popupUrl($data, $create = false) {
        if ($data instanceof \Sl\Model\DbTable\DbTable) {
            $class_name = get_class($data);
            $array = explode('\\', $class_name);
            $controller = array_pop($array);
            unset($array[count($array) - 1]);
            unset($array[count($array) - 1]);
            $module = array_pop($array);

            return '/' . strtolower(implode('/', array($module, $controller, !$create?self::POPUP_LIST_ACTION:$create)));
        } elseif ($data instanceof \Sl_Model_Abstract) {
            $class_name = get_class($data);
            $array = explode('\\', $class_name);
            $controller = array_pop($array);
            unset($array[count($array) - 1]);
            $module = array_pop($array);
            return '/' . strtolower(implode('/', array($module, $controller, !$create?self::POPUP_LIST_ACTION:$create)));
        } else {
            throw new Exception('Can\'t build from such parameters. ' . __METHOD__);
        }
    }
    
	
	public static function ajaxSearchUrl($data) {
        if ($data instanceof \Sl\Model\DbTable\DbTable) {
            $class_name = get_class($data);
            $array = explode('\\', $class_name);
            $controller = array_pop($array);
            unset($array[count($array) - 1]);
            unset($array[count($array) - 1]);
            $module = array_pop($array);

            return '/' . strtolower(implode('/', array($module, $controller, self::POPUP_AJAXLIST_ACTION)));
        } elseif ($data instanceof \Sl_Model_Abstract) {
            $class_name = get_class($data);
            $array = explode('\\', $class_name);
            $controller = array_pop($array);
            unset($array[count($array) - 1]);
            $module = array_pop($array);
            
            return '/'.strtolower(implode('/', array($module, $controller, self::POPUP_AJAXLIST_ACTION)));
        } else {
            throw new Exception('Can\'t build from such parameters. ' . __METHOD__);
        }
    }
	
	public static function returnToEditUrl( \Sl_Model_Abstract $model) {
       		if ($id = $model->getId()){ 
	            $class_name = get_class($model);
	            $array = explode('\\', $class_name);
	            $controller = array_pop($array);
	            unset($array[count($array) - 1]);
	            $module = array_pop($array);
	            
	            return '/'.strtolower(implode('/', array($module, $controller, self::RETURN_TO_EDIT_ACTION,'id',$id)));
			}
    
    }
	public static function getToEditUrl( \Sl_Model_Abstract $model) {
       		if ($id = $model->getId()){ 
	            $class_name = get_class($model);
	            $array = explode('\\', $class_name);
	            $controller = array_pop($array);
	            unset($array[count($array) - 1]);
	            $module = array_pop($array);
	            
	            return '/'.strtolower(implode('/', array($module, $controller, self::TO_EDIT_ACTION,'id',$id)));
			}
    
    }    
	
    public static function ajaxValidateUrl( \Sl_Model_Abstract $model) {
             
                $class_name = get_class($model);
                $array = explode('\\', $class_name);
                $controller = array_pop($array);
                unset($array[count($array) - 1]);
                $module = array_pop($array);
                
                return '/'.strtolower(implode('/', array($module, $controller, self::VALIDATE_ACTION)));
            
    
    }
    
	
	public static function returnPrintUrl( \Sl_Model_Abstract $model, \Sl\Module\Home\Model\Printform $printform) {
       		 
            $class_name = get_class($model);
            $array = explode('\\', $class_name);
            $controller = array_pop($array);
            unset($array[count($array) - 1]);
            $module = array_pop($array);
            $array = array($module, $controller, self::PRINT_ACTION);
            
            if($printform) {
                $array = array_merge($array,array('pfid',$printform->getId()));
                //return '/'.strtolower(implode('/', array($module, $controller, self::PRINT_ACTION,'id',$id,'pfid',$printform->getId())));
            }
            if ($model->getId()){
                $array = array_merge($array, array('id',$model->getId()));
            }
            return '/'.strtolower(implode('/', $array));
		
    
    }
    public static function returnGroupPrintUrl( \Sl_Model_Abstract $model, \Sl\Module\Home\Model\Printform $printform) {
       		 
            $class_name = get_class($model);
            $array = explode('\\', $class_name);
            $controller = array_pop($array);
            unset($array[count($array) - 1]);
            $module = array_pop($array);
            $array = array($module, $controller, self::GROUPPRINT_ACTION);
            
            if($printform) {
                $array = array_merge($array,array('pfid',$printform->getId()));
                //return '/'.strtolower(implode('/', array($module, $controller, self::PRINT_ACTION,'id',$id,'pfid',$printform->getId())));
            }
            if ($model->getId()){
                $array = array_merge($array, array('id',$model->getId()));
            }
            return '/'.strtolower(implode('/', $array));
		
    
    }
    
	public static function returnEmailFileUrl( \Sl_Model_Abstract $model) {
       		if ($id = $model->getId()){ 
	            $class_name = get_class($model);
	            $array = explode('\\', $class_name);
	            $controller = array_pop($array);
	            unset($array[count($array) - 1]);
	            $module = array_pop($array);
	            return '/'.strtolower(implode('/', array('customers', 'main', self::EMAIL_ACTION,'id',$id,'mle',$module,'ml',$controller)));
			}
    
    }
	
	public static function duplicateUrl( \Sl_Model_Abstract $model) {
       		 if ($id = $model->getId()){	
	            $class_name = get_class($model);
	            $array = explode('\\', $class_name);
	            $controller = array_pop($array);
	            unset($array[count($array) - 1]);
	            $module = array_pop($array);
	            
	            return '/'.strtolower(implode('/', array($module, $controller,self::DUPLICATE_ACTION,'id',$id)));
			 }
    		
    }
	
	public static function listUrl( \Sl_Model_Abstract $model) {
       		 
	            $class_name = get_class($model);
	            $array = explode('\\', $class_name);
	            $controller = array_pop($array);
	            unset($array[count($array) - 1]);
	            $module = array_pop($array);
	            
	            return '/'.strtolower(implode('/', array($module, $controller)));
			
    
    }

    public static function selectedItemsListUrl(\Sl_Model_Abstract $model) {
	            $class_name = get_class($model);
	            $array = explode('\\', $class_name);
	            $controller = array_pop($array);
	            unset($array[count($array) - 1]);
	            $module = array_pop($array);
	            
	            return '/'.strtolower(implode('/', array($module, $controller))).'/'.self::AJAX_SELECTED_ITEMS_ACTION;
    }

        public static function exportListUrl(\Sl_Model_Abstract $model, $type = null) {
        $type = '';
        if(!is_null($type)) {
            $type = '/'.strval($type);
        }
        return self::listUrl($model).'/'.self::EXPORT_LIST_ACTION.$type;
    }
    public static function ajaxListUrl($data) {
        if ($data instanceof \Sl\Model\DbTable\DbTable) {
            $class_name = get_class($data);
            $array = explode('\\', $class_name);
            $controller = array_pop($array);
            unset($array[count($array) - 1]);
            unset($array[count($array) - 1]);
            $module = array_pop($array);

            return '/' . strtolower(implode('/', array($module, $controller, self::POPUP_AJAXLIST_ACTION)));
        } elseif ($data instanceof \Sl_Model_Abstract) {
            $class_name = get_class($data);
            $array = explode('\\', $class_name);
            $controller = array_pop($array);
            unset($array[count($array) - 1]);
            $module = array_pop($array);
            
            return '/'.strtolower(implode('/', array($module, $controller, self::POPUP_AJAXLIST_ACTION)));
        } else {
            throw new Exception('Can\'t build from such parameters. ' . __METHOD__);
        }
    }
    
    public static function logUrl(\Sl_Model_Abstract $model) {
        if(!$model->getId()) {
            throw new \Exception('Can\'t build url without object id');
        }
        return '/'.implode('/', array(
            $model->findModuleName(),
            $model->findModelName(),
            self::LOG_ACTION,
            'id',
            $model->getId()
        ));
    }
    
    public static function ajaxLogUrl(\Sl_Model_Abstract $model) {
        if(!$model->getId()) {
            throw new \Exception('Can\'t build url without object id');
        }
        return '/'.implode('/', array(
            $model->findModuleName(),
            $model->findModelName(),
            self::AJAX_LOG_ACTION,
            'id',
            $model->getId()
        ));
    }
    
    public static function ajaxFileCreate() {
        return '/'.strtolower(implode('/', array('home', 'file', self::AJAX_CREATE_ACTION)));
    }
    
    /**
     * 
     * @param \Sl_Model_Abstract $model Объект
     * @return string ссылка на редактирование/просмотр объекта или "#" если нет разрешения даже на просмотр
     * @throws \Sl\Exception\Service если у объекта нет ID
     */
    public static function modelEditViewUrl(\Sl_Model_Abstract $model, $set_new_id = false) {
        \Zend_Controller_Front::getInstance()->getRouter()->useRequestParametersAsGlobal(false);
        //$r = new \Zend_Controller_Router_Rewrite();
        
        
        
        $editable = \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $model->findModuleName(),
                            'controller' => $model->findModelName(),
                            'action' => 'edit'
                        ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                    )?1:0;
		$creatable = \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $model->findModuleName(),
                            'controller' => $model->findModelName(),
                            'action' => 'create'
                        ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                    )?1:0;
										
        $readable = \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $model->findModuleName(),
                            'controller' => $model->findModelName(),
                            'action' => self::TO_DETAILED_ACTION
                        ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                    )?1:0;
        /**
         * @todo Проверить $editable и венуть какую-то другую ссылку
         */
        if($model instanceof \Sl\Module\Home\Model\File) {
            $editable = false;
        }
		if(!$readable && !$editable) return '#';
		
		if(!$model->getId() && !$set_new_id) {
        	return self::getView()->url(array(
	            'module' => $model->findModuleName(),
	            'controller' => $model->findModelName(),
	            'action' => $creatable?'create':'#',
        	));	

        } elseif(!$model->getId() && $set_new_id){
            return self::getView()->url(array(
                'module' => $model->findModuleName(),
                'controller' => $model->findModelName(),
                'action' => $editable?'edit':'detailed',
                'id' => '_id_',
            )); 
        }else {
        	return self::getView()->url(array(
	            'module' => $model->findModuleName(),
	            'controller' => $model->findModelName(),
	            'action' => $editable?'edit':'detailed',
	            'id' => $model->getId(),
        	));	
        }
        
        
    }
    
    /* deprecated 
     * 
    */
    
    public static function deleteUrl(\Sl_Model_Abstract $model) {
       
        return self::ajaxdeleteUrl($model);
        
        
        /*     
        if(!$model->getId()) {
            return '#';
        }
        return self::getView()->url(array(
            'module' => $model->findModuleName(),
            'controller' => $model->findModelName(),
            'action' => self::DELETE_ACTION,
            'id' => $model->getId(),
        )); */
    }
    
    public static function ajaxdeleteUrl(\Sl_Model_Abstract $model) {
        if(!$model->getId()) {
            return '#';
        }
        return self::getView()->url(array(
            'module' => $model->findModuleName(),
            'controller' => $model->findModelName(),
            'action' => self::AJAX_DELETE_ACTION,
            'id' => $model->getId(),
        ));
    }
    
    public static function ajaxarchiveUrl(\Sl_Model_Abstract $model) {
        if(!$model->getId()) {
            return '#';
        }
        return self::getView()->url(array(
            'module' => $model->findModuleName(),
            'controller' => $model->findModelName(),
            'action' => self::AJAX_ARCHIVE_ACTION,
            'set_archived' => 1 - ((int) $model->getArchived()),
            'id' => $model->getId(),
        ));
    }
    
    public static function buildModelUrl(\Sl_Model_Abstract $model, $action, array $options = array()) {
        return self::getView()->url(array_merge(array(
            'module' => $model->findModuleName(),
            'controller' => $model->findModelName(),
            'action' => $action,
        ), $options));
    }
}

