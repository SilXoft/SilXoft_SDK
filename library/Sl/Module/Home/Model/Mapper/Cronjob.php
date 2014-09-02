<?php
namespace Sl\Module\Home\Model\Mapper;

class Cronjob extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\Cronjob';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\Cronjob';
    }
}