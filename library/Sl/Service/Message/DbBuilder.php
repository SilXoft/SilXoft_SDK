<?php
namespace Sl\Service\Message;
use Sl\Exception\Service as Exception;

class DbBuilder extends Message {
    
    public function setData($data) {
        if(!is_array($data)) {
            throw new Exception();
        }
        foreach($data as $key=>$item) {
            $this->addItem($item, $key);
        }
    }
    
    public function addItem($data, $key = false) {
        if(false !== $key) {
            $this->_data[$key] = $data;
        } else {
            $this->_data[] = $data;
        }
        return $this;
    }
}