<?php
namespace Sl\Module\Home\Model\Mapper;

class Emaildetails extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\Emaildetails';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\Emaildetails';
    }
}