<?php
namespace Sl\Module\Home\Model\Mapper;

class Settings extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\Settings';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\Settings';
    }
}