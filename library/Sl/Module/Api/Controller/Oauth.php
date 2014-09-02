<?php
namespace Sl\Module\Api\Controller;

/**
 * Реализация OAuth2 протокола.
 * 
 * @todo Нужно бы переписать на исполдьзование сервисов и/или интерфейсов.
 * Потому как будет тяжело добавлять другие методы авторизации и т.д.
 * 
 */

use Sl_Service_Acl as AclService;
use Sl\Service\Config as Config;
use Application\Module\Itftc\Service\Sync;

use Sl\Module\Api\Grant\Manager as GrantManager;

class Oauth extends \Zend_Controller_Action {
    
    protected $_known_errors = array(
        1 => array(
            'code' => 'invalid_request',
            'desc' => 'The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed.',
        ),
        2 => array(
            'code' => 'unauthorized_client',
            'desc' => 'The client is not authorized to request an authorization code using this method.',
        ),
        4 => array(
            'code' => 'access_denied',
            'desc' => 'The resource owner or authorization server denied the request.',
        ),
        8 => array(
            'code' => 'unsupported_response_type',
            'desc' => 'The authorization server does not support obtaining an authorization code using this method.',
        ),
        16 => array(
            'code' => 'invalid_scope',
            'desc' => 'The requested scope is invalid, unknown, or malformed.',
        ),
        32 => array(
            'code' => 'server_error',
            'desc' => 'The authorization server encountered an unexpected condition that prevented it from fulfilling the request.',
        ),
        64 => array(
            'code' => 'temporarily_unavailable',
            'desc' => 'The authorization server is currently unable to handle the request due to a temporary overloading or maintenance of the server.',
        )
    );
    
    const EC_INVALID_REQUEST            = 1;
    const EC_UNAUTHORIZED_CLIENT        = 2;
    const EC_ACCESS_DENIED              = 4;
    const EC_UNSUPPORTED_RESPONSE_TYPE  = 8;
    const EC_INVALID_SCOPE              = 16;
    const EC_SERVER_ERROR               = 32;
    const EC_TEMPORARILY_UNAVAILABLE    = 64;
    
    const EC_DEFAULT                    = 1024;
    
    
    /**
     * 
     * @param \Zend_Controller_Request_Abstract $request
     * @param \Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs
     */
    public function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        parent::__construct($request, $response, $invokeArgs);
        // Включаем ajax-контекст для oauth-действий
        
        $this->_helper
                    ->ContextSwitch()
                    ->addActionContext('authorize', 'json')
                    ->addActionContext('token', 'json')
                    ->addActionContext('getdata', 'json')
                    ->addActionContext('postdata', 'json')
                    ->initContext('json');
        
        \Sl_Model_Factory::mapper('authcode', 'api')->expireOld();
        \Sl_Model_Factory::mapper('accesstoken', 'api')->expireOld();
        
        \Sl\Module\Api\Grant\Manager::addSupportedGrant(new \Sl\Module\Api\Grant\AuthorizationCode());
        \Sl\Module\Api\Grant\Manager::addSupportedGrant(new \Sl\Module\Api\Grant\ClientCredentials());
    }
    
    /**
     * 
     * Передача access_token-а
     * @throws \Exception
     */
    public function authorizeAction() {
        $processor = \Sl\Module\Api\Grant\Manager::getProcessor($this->getRequest(), \Sl\Module\Api\Grant::REQUEST_TYPE_AUTHORIZATION);
        if(!$processor) {
            throw new \Exception('Unsupported grant', \Sl\Module\Api\Grant::EC_UNSUPPORTED_RESPONSE_TYPE);
        }
        $processor->processAuthorizeRequest($this->getRequest(), $this->view);
    }
    
    public function tokenAction() {
        $processor = \Sl\Module\Api\Grant\Manager::getProcessor($this->getRequest(), \Sl\Module\Api\Grant::REQUEST_TYPE_TOKEN);
        if(!$processor) {
            throw new \Exception('Unsupported grant', \Sl\Module\Api\Grant::EC_UNSUPPORTED_RESPONSE_TYPE);
        }
        $processor->processTokenRequest($this->getRequest(), $this->view);
    }
    
    public function dispatch($action) {
        try {
            parent::dispatch($action);
        } catch(\Exception $e) {
            \Zend_Controller_Front::getInstance()->throwExceptions(false);
            $this->view->error = $this->humanizeError($e, true);
            $this->view->error_description = $this->humanizeError($e);
            if(!$this->_helper->ContextSwitch()->getCurrentContext() === 'json') {
                return $this->render();
            } else {
                echo json_encode($this->view->getVars());
                die;
            }
        }
    }
    
    public function testAction() {
        header('Content-type: text/plain; charset=utf-8');
        try {
            try {
                $ss = \Sl_Model_Factory::object('subscription', 'billing')->setId(1);
                print_r(\Sl_Model_Factory::mapper('tariff', 'itftc')->findLastBySubscription($ss));die;
            } catch(\Exception $e) {
                echo $e->getMessage();
                die('qweqwe');
            }
            $url = 'http://itftc:81/oauth2/authorize';
            $headers = array(
                'Content-type: application/x-www-form-urlencoded',
                'Authorization: Basic'.base64_encode('DwxGVtVMutw10NP:PAFINATG2xLTVOI9JUCiXq56l2tJ2t')
            );
            $data = array(
                'grant_type' => 'client_credentials',
                'scope' => 'test',
            );

            // Шлем-с
            $options = array(
                'http' => array(
                    'header' => implode("\r\n", $headers)."\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data),
                ),
            );
            $context = stream_context_create($options);

            $res = json_decode(file_get_contents($url, false, $context), true);
            $access_token = $res['access_token'];
            if(!$access_token) {
                die('Can\'t get access_token');
            }
            // Данные
            $url = 'http://itftc:81/apipost/itftc/service/create';
            $headers = array(
                'Content-type: application/x-www-form-urlencoded',
                'Authorization: Bearer '.$access_token
            );
            $data = array(
                'billing_service_id' => '1',
                'name' => 'Test',
                'price' => '10',
            );
            $options = array(
                'http' => array(
                    'header' => implode("\r\n", $headers)."\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data),
                ),
            );
            $context = stream_context_create($options);
            echo file_get_contents($url, false, $context)."\r\n";
            die;
        } catch (\Exception $e) {
            if($e->getCode() === Sync::EC_INVALID_RESPONCE) {
                die('Invalid responce');
            } else {
                echo $e->getMessage();
                die;
            }
        }
    }
    
    /**
     * Проверка может имеет ли пользователь спрашивать что-то
     * Установка прав в соответствии с ролями пользователя API
     * 
     * @throws \Exception
     */
    protected function _checkDataRequest() {
        // Достаем пользователя и авторизируем его, если он нормальный
        // Достаем access_token и проверяем его
        // @TODO Перенести куда-то подальше от контроллера ....
        $token_name = $this->getRequest()->getHeader('Authorization');
        if(!$token_name) {
            throw new \Exception('No authorization given', self::EC_INVALID_REQUEST);
        }
        if(false === strpos($token_name, 'Bearer')) {
            throw new \Exception('Authorization type not supported', self::EC_INVALID_REQUEST);
        }
        $token_name = str_replace('Bearer ', '', $token_name);
        if(!$token_name || (strlen($token_name) !== 40)) {
            throw new \Exception('Wrong authorization code', self::EC_ACCESS_DENIED);
        }
        $token = \Sl_Model_Factory::mapper('accesstoken', 'api')->findByName($token_name);
        if(!$token) {
            throw new \Exception('Wrong token value', self::EC_UNAUTHORIZED_CLIENT);
        }
        // Достаем клиента API
        $token = \Sl_Model_Factory::mapper($token)->findRelation($token, 'accesstokenclient');
        $client = $token->fetchOneRelated('accesstokenclient');
        if(!$client) {
            throw new \Exception('Can\'t find related API client for given token', self::EC_SERVER_ERROR);
        }
        // Достаем пользователя системы
        $client = \Sl_Model_Factory::mapper($client)->findRelation($client, 'apiclientuser');
        $user = $client->fetchOneRelated('apiclientuser');
        if(!$user) {
            throw new \Exception('Unexpected error when retrieve user of API client', self::EC_SERVER_ERROR);
        }
        
        // Теперь авторизируем его и даем права в соответствии с ролями
        // 
        // @TODO Костыль, пока не применим новую модель авторизации
        // или не решим проблему перестройки acl под другого пользователя
        \Zend_Registry::set('Zend_Acl', new \Sl\Acl\Acl());
        AclService::__readAcl();
        AclService::acl()->deny();
        
        $user = \Sl_Model_Factory::mapper($user)->findRelation($user, 'userroles');
        $permissions = \Sl_Model_Factory::mapper('permission', 'auth')->fetchAllByRoles($user->fetchRelated('userroles'));
        
        foreach($permissions as $permission) {
            if(!AclService::acl()->has($permission->resource_name)) {
                AclService::acl()->addResource($permission->resource_name);
            }
            if($permission->privilege == AclService::PRIVELEGE_ACCESS) {
                if($grouped = AclService::getGroupedResources($permission->resource_name)) {
                    foreach($grouped as $resource) {
                        if(!AclService::acl()->has($resource)) {
                            AclService::acl()->addResource($resource);
                        }
                        AclService::acl()->allow(null, $resource);
                    }
                }
                AclService::acl()->allow(null, $permission->resource_name, $permission->privilege);
            } elseif($permission->privilege == AclService::PRIVELEGE_READ) {
                AclService::acl()->allow(null, $permission->resource_name, \Sl_Service_Acl::PRIVELEGE_READ);
                AclService::acl()->allow(null, $permission->resource_name, \Sl_Service_Acl::PRIVELEGE_UPDATE);
            } else {
                AclService::acl()->allow(null, $permission->resource_name, $permission->privilege);
            }
        }
        // Пишем пользователя в хранилище дабы другие смогли пользоваться
        \Zend_Auth::getInstance()->getStorage()->write($user);
        \Sl_Event_Manager::trigger(new \Sl\Module\Api\Event\Api('checkRequest', array(
            'request' => $this->getRequest(),
        )));
    }
    
    public function getdataAction() {
        // Проверка доступности пользователю этой странички
        $this->_checkDataRequest();
        
        $module = $this->getRequest()->getParam('resmodule', '');
        $model = $this->getRequest()->getParam('rescontroller', '');
        $action = $this->getRequest()->getParam('resaction', '');
        
        // @TODO Серьезно подумать насчет использования Event-ов
        // Потому как иначе тут будет злостный if/else или switch для каждого проекта
        // Но пока нет времени
        
        if($module === 'home' && $model === 'describe') {
            // Описание API
            $endpoints = array();
            $models_data = \Sl_Module_Manager::getAvailableModels();
            foreach($models_data as $modulename=>$models) {
                foreach($models as $modelname) {
                    $endpoints[] = implode('/', array($modulename, $modelname));
                }
            }
            $this->view->data = array(
                'endpoints' => $endpoints,
            );
        } else {
            $resource = AclService::joinResourceName(array(
                'type' => AclService::RES_TYPE_MVC,
                'module' => $module,
                'controller' => $model,
                'action' => $action
            ));
            if(AclService::acl()->has($resource)) {
                if(!AclService::isAllowed($resource)) {
                    $this->view->data = array();
                    return;
                }
            }
            $oModel = \Sl_Model_Factory::object($model, $module);
            if(!$oModel || !($oModel instanceof \Sl_Model_Abstract)) {
                throw new \Exception('Can\'t build model from given params', self::EC_SERVER_ERROR);
            }
            switch($action) {
                case 'list':
                    $this->view->data = $this->_dataList($oModel);
                    break;
                case 'view':
                    $this->view->data = $this->_dataDetailed($oModel);
                    break;
                default:
                    throw new \Exception('Unknown action given', self::EC_INVALID_REQUEST);
            }
        }
    }
    
    protected function _dataDetailed(\Sl_Model_Abstract $model) {
        $model = \Sl_Model_Factory::mapper($model)->nFind($this->getRequest()->getParam('billing_user_id', 0), true, true, 'billing_user_id');
        if(!$model) {
            throw new \Exception('Can\'t find model with such id.', self::EC_INVALID_REQUEST);
        }
        return $model->toArray(true, true);
    }
    
    /**
     * Формирует данные в табличном виде
     * 
     * @param \Sl_Model_Abstract $model
     * @return array Данные
     */
    protected function _dataList(\Sl_Model_Abstract $model, \Zend_Controller_Request_Abstract $request = null) {
        if(is_null($request)) {
            $request = $this->getRequest();
        }
        $fs = \Sl\Model\Identity\Fieldset\Factory::build($model, 'listview');
        // Достаем поля, если они передавались в запросе
        $request_fields = $request->getParam('fields', '');
        $fields_sep = preg_replace('/.+(__|,).+/', '$1', $request_fields);
        $fields = array_diff(explode($fields_sep, $request_fields), array(''));
        
        if(!$fields || !count($fields)) {
            // Если поля никто не передавал - берем из конфига
            $fields = array();
            $fieldset_info = Config::read($model, 'fieldsets/_default', Config::MERGE_FIELDS)->toArray();
            $fields = $fieldset_info['fields'];
        }
        
        $fields = array_unique(array_merge($fields, array('id')));
        foreach($fields as $field) {
            $fs->createField($field, array(
                'roles' => array('from', 'render'),
            ));
        }
        $fs->addComps(array(\Sl\Model\Identity\Fieldset\Comparison\Factory::build(array(
            'type' => 'eq',
            'field' => 'active',
            'value' => '1'
        ), $fs)));
        
        $ds = new \Sl\Model\Identity\Dataset\Datatables();
        $ds->addOptions(array(
            'order' => array(
                'dir' => 'desc',
            ),
            'offset' => $this->getRequest()->getParam('offset', 0),
            'limit' => (int) $this->getRequest()->getParam('limit', 10),
        ))->setFieldset($fs);
        
        $ds = \Sl_Model_Factory::mapper($fs->getModel())->fetchDataset($ds);
        return $ds->getData();
    }
    
    /**
     * Обработка запроса от Биллинга 
     * направленного на изменение какого-то объекта
     * 
     * @throws \Exception
     */
    public function postdataAction() {
        $this->_checkDataRequest();

        $module = $this->getRequest()->getParam('resmodule', '');
        $model = $this->getRequest()->getParam('rescontroller', '');
        $action = $this->getRequest()->getParam('resaction', '');

        $log = \Zend_Registry::get('Zend_Log');
        $log->debug('API in', array('info' => print_r($this->getRequest()->getParams(), true)));

        $request = $this->_getCleanRequest();
        $params = array_merge($request->getParams(), array(
            'shared' => array(
                'context' => 'api',
                'type' => 'apipost',
            )
        ));

        switch($action) {
            case 'create':
                $oModel = \Sl_Model_Factory::object($model, $module);
                if(!$oModel) {
                    throw new \Exception('Can\'t build model with such params', self::EC_INVALID_REQUEST);
                }
                $oModel = \Sl\Service\Saver::create($oModel, $params);
                if($oModel && ($oModel instanceof \Sl_Model_Abstract)) {
                    $this->view->result = 'success';
                    $this->view->data = array(
                        'id' => $oModel->getId(),
                    );
                } else {
                    throw new \Exception(print_r(\Sl\Service\Saver::getLastError(), true), self::EC_SERVER_ERROR);
                }
                break;
            case 'update':
                $oModel = \Sl_Model_Factory::object($model, $module);
                if(!$oModel) {
                    throw new \Exception('Can\'t build model with such params', self::EC_INVALID_REQUEST);
                }
                $oModel = \Sl\Service\Saver::update($oModel, $params);
                if($oModel && ($oModel instanceof \Sl_Model_Abstract)) {
                    $this->view->result = 'success';
                    $this->view->data = array(
                        'id' => $oModel->getId(),
                    );
                } else {
                    throw new \Exception(print_r(\Sl\Service\Saver::getLastError(), true), self::EC_SERVER_ERROR);
                }
                break;
            default:
                $eFallback = new \Sl\Module\Api\Event\Api('fallbackPost', array(
                    'request' => $request,
                    'result' => false,
                    'data' => array(),
                ));
                \Sl_Event_Manager::trigger($eFallback);
                if($eFallback->getResult()) {
                    $this->view->result = 'success';
                    $this->view->data = $eFallback->getData();
                } else {
                    $message = 'Unknown action given';
                    if($eFallback->getErrorMessage()) {
                        $message = $eFallback->getErrorMessage();
                    }
                    throw new \Exception($message, self::EC_SERVER_ERROR);
                }
        }
    }
    
    /**
     * Предотвращение передачи неверных данных об исключении
     * 
     * @param \Exception $e
     * @param type $code_only
     * @return type
     */
    public function humanizeError(\Exception $e, $code = false) {
        $e_code = $e->getCode();
        $use_default = false;
        if($e_code & self::EC_DEFAULT) {
            $e_code &= ~self::EC_DEFAULT;
            $use_default = true;
        }
        if(!isset($this->_known_errors[$e_code])) {
            return $code?'unknown_error':'Unknown error when handling request. "'.$e->getMessage().'"';
        } else {
            return $code?$this->_known_errors[$e_code]['code']:($use_default?$this->_known_errors[$e_code]['desc']:$e->getMessage());
        }
    }
    
    /**
     * Возвращает объект запроса, очищенный от данных,
     * необходимых для роутинга на API-action-ы
     * 
     */
    protected function _getCleanRequest() {
        $request = clone $this->getRequest();
        $request->setParams(array(
            'module' => $request->getParam('resmodule', ''),
            'controller' => $request->getParam('rescontroller', ''),
            'action' => $request->getParam('resaction', ''),
        ))  ->setParams(array(
                'resmodule' => null,
                'rescontroller' => null,
                'resaction' => null,
        ))
            ->setActionName($request->getParam('action', ''))
            ->setControllerName($request->getParam('controller', ''))
            ->setModuleName($request->getParam('module', ''));
        return $request;
    }
    
}
