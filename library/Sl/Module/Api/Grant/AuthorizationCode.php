<?php
namespace Sl\Module\Api\Grant;

class AuthorizationCode extends \Sl\Module\Api\Grant {
    
    public function processAuthorizeRequest(\Zend_Controller_Request_Abstract $request, \Sl_View $view) {

        $state = $request->getParam('state', false);
        if(false !== $state) {
            $view->state = $state;
        }
        $response_type = $request->getParam('response_type', '');
        if(!$response_type) {
            throw new \Exception('"response_type" param is required',self::EC_INVALID_REQUEST);
        }
        if($response_type !== 'code') {
            throw new \Exception('Unsupported flow', self::EC_UNSUPPORTED_RESPONSE_TYPE);
        }
        $client_name = $request->getParam('client_id', '');
        if(!$client_name) {
            throw new \Exception('"client_id" param is required', self::EC_INVALID_REQUEST);
        }
        $client = \Sl_Model_Factory::mapper('client', 'api')->findByName($client_name);
        if(!$client) {
            throw new \Exception('No client found with such "client_id"', self::EC_INVALID_REQUEST);
        }
        // Redirect URI is required cause in our form it required
        $redirect_uri = $request->getParam('redirect_uri', '');
        if(!$redirect_uri) {
            throw new \Exception('"redirect_uri" param is required.', self::EC_INVALID_REQUEST);
        }
        if($redirect_uri != $client->getRedirectUri()) {
            throw new \Exception('"redirect_uri" must match.', self::EC_INVALID_REQUEST);
        }
        $scope = $request->getParam('scope', false);
        // Генерируем Authcode
        try {
            $authcode = \Sl\Module\Api\Service\Generator::authcode();
            $authcode->assignRelated('authcodeclient', array($client));
            $authcode = \Sl_Model_Factory::mapper($authcode)->save($authcode, true);
        } catch(\Exception $e) {
            throw new \Exception('Can\'t create authorization_code ('.$e->getMessage().').', self::EC_SERVER_ERROR);
        }
        if(false === strpos('?', $redirect_uri)) {
            $redirect_uri .= '?';
        }
        if(false !== $state) {
            $redirect_uri .= '&state='.$state;
        }
        $redirect_uri .= '&code='.$authcode->getName();
        $this->_redirect($redirect_uri);
    }

    public function processTokenRequest(\Zend_Controller_Request_Abstract $request, \Sl_View $view) {
        $grant_type = $request->getParam('grant_type', '');
        if(!$grant_type) {
            throw new \Exception('"grant_type" param is required', self::EC_INVALID_REQUEST);
        }
        if($grant_type !== 'authorization_code') {
            throw new \Exception('Unsupported flow.', self::EC_INVALID_REQUEST);
        }
        if($this->_hasHeaderAuth($request)) {
            if($request->getParam('client_id', '')) {
                throw new \Exception('You should use only 1 auth method', self::EC_INVALID_REQUEST);
            }
            $auth_data = trim(str_replace('Basic', '', $request->getHeader('Authorization')));
            if($auth_data) {
                list($client_name, $client_secret) = explode(':', base64_decode($auth_data));
                $client = \Sl_Model_Factory::mapper('client', 'api')->findByName($client_name);
                if(!$client) {
                    throw new \Exception('Wrong "client_id" param', self::EC_INVALID_REQUEST);
                }
                if($client->getSecret() !== $client_secret) {
                    throw new \Exception('No client found with such key/secret pair', self::EC_UNAUTHORIZED_CLIENT);
                }
            }
        } else {
            $client_name = $request->getParam('client_id', '');
            if(!$client_name) {
                throw new \Exception('"client_id" param is required', self::EC_INVALID_REQUEST);
            }
            $client = \Sl_Model_Factory::mapper('client', 'api')->findByName($client_name);
            if(!$client) {
                throw new \Exception('Wrong "client_id" param', self::EC_INVALID_REQUEST);
            }
        }
        $code_value = $request->getParam('code', '');
        if(!$code_value) {
            throw new \Exception('"client_id" param is required', self::EC_INVALID_REQUEST);
        }
        $code = \Sl_Model_Factory::mapper('authcode', 'api')->findByName($code_value);
        if(!$code) {
            throw new \Exception('Unknown authorization code given', self::EC_ACCESS_DENIED);
        }
        $code = \Sl_Model_Factory::mapper($code)->findRelation($code, 'authcodeclient');
        $auth_client = $code->fetchOneRelated('authcodeclient');
        if(!$auth_client) {
            throw new \Excepotion('No user data assigned for this auth code', self::EC_INVALID_REQUEST);
        }
        if($auth_client->getId() !== $client->getId()) {
            throw new \Exception('Auth code doesn\'t exists for this user', self::EC_UNAUTHORIZED_CLIENT);
        }
        $redirect_uri = $request->getParam('redirect_uri', '');
        if(!$redirect_uri) {
            throw new \Exception('"redirect_uri" param is required', self::EC_INVALID_REQUEST);
        }
        if($redirect_uri !== $client->getRedirectUri()) {
            throw new \Exception('Redirect uri mismatch', self::EC_INVALID_REQUEST);
        }
        try {
            // Удаляем все токены этого клиента, если они были
            $client = \Sl_Model_Factory::mapper($client)->find($client->getId());
            \Sl_Model_Factory::mapper($client)->save($client->assignRelated('accesstokenclient', array()));
            
            $access_token = \Sl\Module\Api\Service\Generator::accesstoken();
            $access_token->assignRelated('accesstokenclient', array($client));
            $access_token = \Sl_Model_Factory::mapper($access_token)->save($access_token, true);
            // Удаляем код авторизации
            \Sl_Model_Factory::mapper($code)->delete($code);
        } catch(\Exception $e) {
            throw new \Exception('Can\'t create access token ('.$e->getMessage().').', self::EC_SERVER_ERROR);
        }
        if(false === strpos('?', $redirect_uri)) {
            $redirect_uri .= '?';
        }
        $view->access_token = $access_token->getName();
    }

    public function isValid(\Zend_Controller_Request_Abstract $request, $type) {
        if($type === self::REQUEST_TYPE_AUTHORIZATION) {
            return (bool) ($request->getParam('response_type', '') === 'code');
        } elseif($type === self::REQUEST_TYPE_TOKEN) {
            return (bool) ($request->getParam('grant_type', '') === 'authorization_code');
        }
        return false;
    }

}