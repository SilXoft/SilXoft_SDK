<?php
namespace Sl\Module\Api\Grant;

class ClientCredentials extends \Sl\Module\Api\Grant {
    
    /**
     * 
     * Генерация и отдача access_token-а с проверкой всех принятых параметров
     * @param \Zend_Controller_Request_Abstract $request
     * @param \Sl_View $view
     * @throws \Exception
     */
    public function processAuthorizeRequest(\Zend_Controller_Request_Abstract $request, \Sl_View $view) {
        $grant_type = $request->getParam('grant_type', '');
        if(!$grant_type) {
            throw new \Exception('"grant_type" param is required', self::EC_INVALID_REQUEST);
        }
        if($grant_type !== 'client_credentials') {
            throw new \Exception('Unsupported flow.', self::EC_INVALID_REQUEST);
        }
        $scope = $request->getParam('scope', false);
        if(!$this->_hasHeaderAuth($request)) {
            throw new \Exception('No authorization given. ', self::EC_INVALID_REQUEST);
        }
        $auth_data = trim(str_replace('Basic', '', $request->getHeader('Authorization')));
        if(!$auth_data) {
            throw new \Exception('Unknown authorization type or params', self::EC_INVALID_REQUEST);
        }
        list($client_name, $client_secret) = explode(':', base64_decode($auth_data));
        $client = \Sl_Model_Factory::mapper('client', 'api')->findByName($client_name);
        if(!$client) {
            throw new \Exception('Wrong "client_id" param', self::EC_INVALID_REQUEST);
        }
        if($client->getSecret() !== $client_secret) {
            throw new \Exception('No client found with such key/secret pair', self::EC_UNAUTHORIZED_CLIENT);
        }
        try {
            // Удаляем все токены этого клиента, если они были
            $client = \Sl_Model_Factory::mapper($client)->find($client->getId());
            \Sl_Model_Factory::mapper($client)->save($client->assignRelated('accesstokenclient', array()));
            // Генерируем access_token
            $access_token = \Sl\Module\Api\Service\Generator::accesstoken();
            $access_token->assignRelated('accesstokenclient', array($client));
            $access_token = \Sl_Model_Factory::mapper($access_token)->save($access_token, true);
        } catch(\Exception $e) {
            throw new \Exception('Can\'t create access token ('.$e->getMessage().').', self::EC_SERVER_ERROR);
        }
        $view->access_token = $access_token->getName();
    }

    public function processTokenRequest(\Zend_Controller_Request_Abstract $request, \Sl_View $view) {
        throw new \Exception('Not implemented. ', self::EC_INVALID_REQUEST);
    }

    public function isValid(\Zend_Controller_Request_Abstract $request, $type) {
        if($type === self::REQUEST_TYPE_AUTHORIZATION) {
            return (bool) ($request->getParam('grant_type', '') === 'client_credentials');
        }
        return false;
    }

}