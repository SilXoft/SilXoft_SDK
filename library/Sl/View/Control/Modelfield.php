<?php
namespace Sl\View\Control;

class Modelfield extends  \Sl\View\Control {
    
    protected $_id;
    protected $_field;
    protected $_priv_read;
    protected $_priv_update;
    
    public function setId($id) {
        $this->_id = $id;
        return $this;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function setField($field) {
        $this->_field = $field;
        return $this;
    }
    
    public function getField() {
        return $this->_field;
    }
    
    public function setPrivRead($priv) {
        $this->_priv_read = $priv;
        return $this;
    }
    
    public function setPrivUpdate($priv) {
        $this->_priv_update = $priv;
        return $this;
    }
    
    public function getPrivRead() {
        return $this->_priv_read;
    }
    
    public function getPrivUdpate() {
        return $this->_priv_update;
    }
    
    public function setPrivs($privs, $type = false) {
        if(is_array($privs)) {
            if(array_key_exists('read', $privs)) {
                $this->setPrivRead((bool) $privs['read']);
            } else {
                $this->setPrivRead((bool) array_shift($privs));
            }
            if(is_array($privs)) {
                if(array_key_exists('update', $privs)) {
                    $this->setPrivUpdate((bool) $privs['update']);
                } else {
                    $this->setPrivUpdate((bool) array_shift($privs));
                }
            }
        } else {
            switch($type) {
                case 'read':
                    $this->setPrivRead((bool) $privs);
                    break;
                case 'update':
                    $this->setPrivUpdate((bool) $privs);
                    break;
            }
        }
        return $this;
    }
}
