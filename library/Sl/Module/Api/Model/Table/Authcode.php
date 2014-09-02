<?php

namespace Sl\Module\Api\Model\Table;

class Authcode extends \Sl\Model\DbTable\DbTable {

    protected $_name = 'api_authcodes';
    protected $_primary = 'id';
    
    public function expireOld() {
        $date = new \DateTime();
        $date->modify('- 10 minutes');
        return $this->getAdapter()->update($this->_name, array(
            'active' => '0'
        ), array(
            'expires < ?' => $date->format('Y-m-d H:i:s'),
        ));
    }

}
