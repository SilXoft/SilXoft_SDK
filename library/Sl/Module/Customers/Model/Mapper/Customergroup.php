<?php
namespace Sl\Module\Customers\Model\Mapper;

class Customergroup extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Customers\Model\Customergroup';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Customers\Model\Table\Customergroup';
    }
}