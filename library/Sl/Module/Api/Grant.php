<?php
namespace Sl\Module\Api;

abstract class Grant {
    
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
    
    protected static $_supported_request_types;
    
    const EC_INVALID_REQUEST            = 1;
    const EC_UNAUTHORIZED_CLIENT        = 2;
    const EC_ACCESS_DENIED              = 4;
    const EC_UNSUPPORTED_RESPONSE_TYPE  = 8;
    const EC_INVALID_SCOPE              = 16;
    const EC_SERVER_ERROR               = 32;
    const EC_TEMPORARILY_UNAVAILABLE    = 64;
    
    const EC_DEFAULT                    = 1024;
    
    const REQUEST_TYPE_AUTHORIZATION = 101;
    const REQUEST_TYPE_TOKEN = 102;
    const REQUEST_TYPE_DATA = 104;
    
    abstract public function processAuthorizeRequest(\Zend_Controller_Request_Abstract $request, \Sl_View $view);
    abstract public function processTokenRequest(\Zend_Controller_Request_Abstract $request, \Sl_View $view);
    abstract public function isValid(\Zend_Controller_Request_Abstract $request, $type);
    
    public function _redirect($url) {
        \Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')
                            ->gotoUrl($url);
    }
    
    protected function _hasHeaderAuth(\Zend_Controller_Request_Abstract $request) {
        return (bool) $request->getHeader('Authorization');
    }
    
    /**
     *
     * @param int $type Тип обрабатываемого запроса
     * @return bool Являеться ли тип запроса поддерживаемым даной реализацией
     */
    public static function checkContextType($type) {
        if(!isset(self::$_supported_request_types)) {
            self::$_supported_request_types = array();
            $cl = new \ReflectionClass(__CLASS__);
            foreach($cl->getConstants() as $name=>$value) {
                if(false !== strpos($name, 'REQUEST_TYPE_')) {
                    self::$_supported_request_types[] = $value;
                }
            }
        }
        return in_array($type, self::$_supported_request_types);
    }
    
}