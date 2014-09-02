<?php
namespace Sl\Module\Home\Model;

class Email extends \Sl_Model_Abstract {
    
    protected $_mail;    

	
	
    public function setMail($mail) {
        $this->_mail = $mail;
        return $this;
    }
    
    public function getMail() {
        return $this->_mail;
    }
    
    public function __toString() {
        return $this->_mail;
    }

}

