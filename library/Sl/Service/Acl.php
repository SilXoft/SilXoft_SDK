<?php

class Sl_Service_Acl {

    const RES_TYPE_MVC = 'mvc';
    const RES_TYPE_FIELD = 'field';
    const RES_TYPE_OBJ = 'obj';
    const RES_TYPE_CUSTOM = 'custom';

    const RES_TYPE_SEPARATOR = ':';
    const RES_DATA_SEPARATOR = '|';

    const ROLE_DEFAULT = 3;
    
    const PRIVELEGE_ACCESS = 1;
    
    const AUTORIZED_ALLOWED_RESOURCE = 'mvc:home|main|list';
    
    const PRIVELEGE_READ = 2;
    const PRIVELEGE_UPDATE = 3;
    const PRIVILEGE_DENY = 0;
    const ASSERTION_CONTEXT_DEFAULT_CLASS = '\Sl_Assertion_Context';
    const ASSERTION_CLASS_PREFIX = '\Sl\Assertion\Context\\';
    
    
    /**
     * Список доступа
     * 
     * @var \Sl\Acl\Acl
     */
    protected static $_acl;
    
    /**
     * групування action-ів
     * 
     * @var \Sl\Acl\Acl
     */
    protected static $_grouped_actions = array(
                                        'edit' => 'ajaxvalidate',
                                        'create' => 'ajaxvalidate',    
                                        'list' => 'ajaxlist',
                                        'filters' => 'ajaxautocomplete',
                                        'nlist' => 'ajaxlist',
                                        'popup' => 'ajaxlist',
                                        'log'=>'ajaxlog',
                               );
    
    protected static $_context;
    
    protected static $_temporary_context;
    
    
    /**
     * Роль по-умолчанию
     * 
     * @var \Sl_Model_Abstract
     */
    protected static $_default_role;
    
    /**
     * Текущий пользователь
     * 
     * @var \Sl_Model_Abstract
     */
    protected static $_current_user;
    
    /**
     * Массив ролей текущего пользователя
     * 
     * @var type 
     */
    protected static $_current_roles = array();

    public static function getCurrentUser() {
        return self::$_current_user;
    }
    
    public static function getCurrentRoles($ids_only = false) {
        if($ids_only) {
            return array_keys(self::$_current_roles);
        }
        return self::$_current_roles;
    }
    
    public static function setCurrentUser(\Sl_Model_Abstract $user) {
        self::$_current_user = $user;
    }
    
    public static function setCurrentRoles(array $roles) {
        self::$_current_roles = array();
        foreach($roles as $role) {
            if($role instanceof \Sl_Model_Abstract) {
                self::$_current_roles[$role->getId()] = $role;
            }
        }
    }
    
    /**
     * Преобразование из имени ресурса в данные
     * 
     * @param string $name
     * @return array
     * @throws Sl_Exception_Acl
     */
    public static function splitResourceName($name) {
        list($type, $data) = explode(self::RES_TYPE_SEPARATOR, $name);
        if (!in_array($type, self::getAllowedResourceTypes())) {
            throw new Sl_Exception_Acl('Can\'t split resource name');
        }
        $result = array('type' => $type, );
        switch($type) {
            case self::RES_TYPE_MVC :
                list($result['module'], $result['controller'], $result['action']) = explode(self::RES_DATA_SEPARATOR, $data);
                break;
            case self::RES_TYPE_FIELD :
            case self::RES_TYPE_OBJ :
                list($result['module'], $result['name'], $result['field']) = explode(self::RES_DATA_SEPARATOR, $data);
                break;
            case self::RES_TYPE_CUSTOM:
                list($result['module'], $result['name']) = explode(self::RES_DATA_SEPARATOR, $data);
                break;
            default :
                throw new Sl_Exception_Acl('Not implemented yet ...');
                break;
        }
        return $result;
    }
    
    
     /**
     * Пошук залежних Action-ів
     * 
     * @param string $action = null - action name
     * @return mixed
     */
    public static function getGroupedActions($action = null){
        if ($action == null){
            return self::$_grouped_actions;
        }  elseif(isset(self::$_grouped_actions[$action])) {
            return self::$_grouped_actions[$action];
        }  
        
        
    }
    
    
     /**
     * Побудова назв залежних ресурсів Action-ів
     * 
     * @param string $action = null - action name
     * @return array
     */
    public static function getGroupedResources($action){
        $action_arr = self::splitResourceName($action);
        if($action_arr['type'] == self::RES_TYPE_MVC && isset(self::$_grouped_actions[$action_arr['action']])) {
            $grouped_res = array();
            $group = self::$_grouped_actions[$action_arr['action']];
            if (is_array($group)){
                foreach ($group as $grouped_action_name){
                    $action_arr['action'] = $grouped_action_name;
                    $grouped_res[]=self::joinResourceName($action_arr);        
                }
            } else {
                $action_arr['action'] = $group;
                $grouped_res[]=self::joinResourceName($action_arr);
            }
            return $grouped_res;
        }  
        
        
    }
    
    protected static function _rebuildResource($resource){
        
    }
    
    
    public static function getContext(){
        return $context = self::_getTemporaryContext()?$context:self::$_context;
        
    }
    
    
    public static function getResourceType($resource_name){
        $resource_arr = self::splitResourceName($resource_name);
        return $resource_arr['type'];
    }
    
    
    
    public static function setContext($context, $type = null){
        
        $class_name = self::_buildContextClassName($type);
        self::$_context = new $class_name ($context, $type);
    }
    
    
    protected static function _getTemporaryContext(){
        $context = self::$_temporary_context;
        self::setTemporaryContext(null);    
        return $context;
    }
    
    static function setTemporaryContext($context){
        self::$_temporary_context = $context;   
    }
    
    /**
     * Возвращает имя ресурса на основании параметров
     * 
     * @param array $data Массив значений для создание идентификатора ресурса
     * @return string
     * @throws Sl_Exception_Acl
     */
    public static function joinResourceName(array $data) {
        if (!isset($data['type']) || !in_array($data['type'], self::getAllowedResourceTypes())) {
            throw new Sl_Exception_Acl('Can\'t join resource name with such params');
        }
        $name = '';
        switch($data['type']) {
            case self::RES_TYPE_MVC :
                $name = self::RES_TYPE_MVC . self::RES_TYPE_SEPARATOR . implode(self::RES_DATA_SEPARATOR, array(
                    $data['module'] ? $data['module'] : 'application',
                    $data['controller'],
                    $data['action']
                ));
                break;
            case self::RES_TYPE_FIELD :
            case self::RES_TYPE_OBJ :
                $name = $data['type'] . self::RES_TYPE_SEPARATOR . implode(self::RES_DATA_SEPARATOR, array(
                    $data['module'] ? $data['module'] : 'application',
                    $data['name'],
                    $data['field']
                ));
                break;
            case self::RES_TYPE_CUSTOM:
                $name = $data['type'].self::RES_TYPE_SEPARATOR.implode(self::RES_DATA_SEPARATOR, array(
                    $data['module'] ? $data['module'] : 'application',
                    $data['name'],
                ));
                break;
            default :
                throw new Sl_Exception_Acl('Not implemented yet ...');
                break;
        }
        return strtolower($name);
    }

    /**
     * Возвращает все доступные типы ресурсов
     * 
     * @return array
     */
    public static function getAllowedResourceTypes() {
        return array(
            self::RES_TYPE_FIELD,
            self::RES_TYPE_OBJ,
            self::RES_TYPE_MVC,
            self::RES_TYPE_CUSTOM,
        );
    }
    
    /**
     * Возвращает роль по-умолчанию
     * 
     * @return \Sl_Model_Abstract
     */
    public static function getDefaultRole() {
        return self::$_default_role;
    }
    
    /**
     * Устанавливает роль по-умолчанию
     * 
     * @param \Sl_Model_Abstract $role
     */
    public static function setDefaultRole(\Sl_Model_Abstract $role) {
        self::$_default_role = $role;
    }
    
    /**
     * Возвращает системный список доступа
     * 
     * @return \Sl\Acl\Acl
     */
    public static function acl() {
        if (!isset(self::$_acl)) {
            self::$_acl = new \Sl\Acl\Acl();
            \Sl_Event_Manager::trigger(new \Sl_Event_Acl('afterAclCreate', array('acl' => self::$_acl)));
            \Zend_Registry::set('Zend_Acl', self::$_acl);
        }
        return self::$_acl;
    }
    
    /**
     * Перечитывает acl из конфмга по ключу 'Zend_Acl'<br />
     * <b style="color: red;">ПЕРЕД ИСПОЛЬЗОВАНИЕ НУЖНО ХОРОШО ПОДУМАТЬ</b>
     * 
     */
    public static function __readAcl() {
        if(\Zend_Registry::isRegistered('Zend_Acl')) {
            self::$_acl = \Zend_Registry::get('Zend_Acl');
        }
    }

    protected static function _buildAcl() {
        
    }
    
    /**
     * Проверка разрешения на ресурс/привилегию
     * 
     * @param string $resource Ресурс
     * @param string $privilege Привилегия
     * @return boolean
     */
    public static function isAllowed ($resource, $privilege = null){
        //Перша сторінка всім досупна.
        if(is_array($resource) && $resource[0] instanceof \Sl_Model_Abstract) {
            $resource = $resource[0]->buildResourceName($resource[1]);
        }
        // Затичка для home/main/list - доступна всім залогіненим
        if (\Zend_Auth::getInstance()->hasIdentity() && $resource == self::AUTORIZED_ALLOWED_RESOURCE) return true;
        
        \Sl_Event_Manager::trigger(new \Sl_Event_Acl('isAllowed', array(
            'resource' => $resource,
            'acl' => self::acl(),
        )));
        
        return self::acl()->isAllowed(null, $resource, $privilege);
    }
    
    protected static function _buildContextClassName($type) {
        $type = trim(strval($type));
        if (strlen($type)){
            $class_name = self::ASSERTION_CLASS_PREFIX.ucfirst($type);
            if (class_exists($class_name)){
                return $class_name;
            }
        } 
        return self::ASSERTION_CONTEXT_DEFAULT_CLASS;
    }
    
}
