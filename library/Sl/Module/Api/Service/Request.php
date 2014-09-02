<?php
namespace Sl\Module\Api\Service;

class Request {
    
    protected $_flows = array(
        'authorization_code' => array(
            
        ),
    );
    
    public static function checkAuthRequest(\Zend_Controller_Request_Http $request) {
        
    }
    
    public static function checkFlow(\Zend_Controller_Request_Http $request, $request_type) {
        switch($request_type) {
            case 'authorization':
                
                break;
        }
    }
    
    public static function getHeaders() {
        if(function_exists('apache_request_headers')) {
            return apache_request_headers();
        } else {
            return self::_getHeaders();
        }
    }
    
    protected function _getHeaders() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }
}