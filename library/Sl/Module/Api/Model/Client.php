<?php

namespace Sl\Module\Api\Model;

class Client extends \Sl_Model_Abstract {

    protected $_name;
    protected $_secret;
    protected $_redirect_uri;
    protected $_loged = false;

    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    public function setSecret($secret) {
        $this->_secret = $secret;
        return $this;
    }

    public function setRedirectUri($redirect_uri) {
        $this->_redirect_uri = $redirect_uri;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }

    public function getSecret() {
        return $this->_secret;
    }

    public function getRedirectUri() {
        return $this->_redirect_uri;
    }

}
