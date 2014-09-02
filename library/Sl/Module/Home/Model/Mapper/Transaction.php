<?php
namespace Sl\Module\Home\Model\Mapper;

class Transaction extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\Transaction';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\Transaction';
    }
}