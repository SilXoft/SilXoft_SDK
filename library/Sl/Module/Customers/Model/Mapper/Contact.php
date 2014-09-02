<?php
namespace Sl\Module\Customers\Model\Mapper;

class Contact extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Customers\Model\Contact';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Customers\Model\Table\Contact';
    }
}