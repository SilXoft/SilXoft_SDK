<?php
namespace Sl\Module\Auth\Model\Mapper;

class Setting extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Auth\Model\Setting';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Auth\Model\Table\Setting';
    }
}