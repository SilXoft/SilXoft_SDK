<?php
namespace Sl\Module\Home\Model\Mapper;

class Address extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\Address';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\Address';
    }
}