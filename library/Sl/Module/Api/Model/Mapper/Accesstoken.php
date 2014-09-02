<?php
namespace Sl\Module\Api\Model\Mapper;

class Accesstoken extends \Sl_Model_Mapper_Abstract {
    protected function _getMappedDomainName() {
        return '\Sl\Module\Api\Model\Accesstoken';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Api\Model\Table\Accesstoken';
    }
    
    public function findByName($name) {
        $this->expireOld();
        $row = $this->_getDbTable()->fetchRow(array(
            'name = ?' => $name,
            'active = 1',
        ));
        if(!$row) {
            return null;
        }
        return $this->create($row);
    }
    
    public function expireOld() {
        return $this->_getDbTable()->expireOld();
    }
}