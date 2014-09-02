<?php

namespace Sl\Module\Api\Model;

class Authcode extends \Sl_Model_Abstract {

    protected $_name;
    protected $_redirect_uri;
    protected $_expires;
    protected $_loged = false;

    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    public function setRedirectUri($redirect_uri) {
        $this->_redirect_uri = $redirect_uri;
        return $this;
    }

    public function setExpires($expires) {
        if($expires instanceof \DateTime) {
            $expires = $expires->format(self::FORMAT_TIMESTAMP);
        }
        if($expires === '0000-00-00 00:00:00') {
            $expires = null;
        }
        $this->_expires = $expires;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }

    public function getRedirectUri() {
        return $this->_redirect_uri;
    }

    public function getExpires($as_object = false) {
        if($as_object) {
            return \DateTime::createFromFormat(self::FORMAT_TIMESTAMP, $this->getExpires());
        }
        return $this->_expires;
    }

}
