<?php
namespace Sl\Module\Customers\Model\Mapper;

class Lead extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Customers\Model\Lead';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Customers\Model\Table\Lead';
    }
    
    public function findByEmail($email) {
        $rowset = $this->_getDbTable()->findByEmail($email);
        if(!$rowset) return null;
        return $this->create($rowset);
    }
}