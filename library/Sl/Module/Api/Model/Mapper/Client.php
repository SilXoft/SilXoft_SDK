<?php
namespace Sl\Module\Api\Model\Mapper;

class Client extends \Sl_Model_Mapper_Abstract {
    
    protected function _getMappedDomainName() {
        return '\Sl\Module\Api\Model\Client';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Api\Model\Table\Client';
    }
    
    public function findByName($name) {
        $row = $this->_getDbTable()->fetchRow(array(
            'name = ?' => $name,
            'active = 1',
        ));
        if(!$row) {
            return null;
        }
        return $this->create($row);
    }
}