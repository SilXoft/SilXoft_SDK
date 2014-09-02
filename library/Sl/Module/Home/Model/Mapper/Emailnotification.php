<?php
namespace Sl\Module\Home\Model\Mapper;

class Emailnotification extends \Sl\Module\Home\Model\Mapper\Notification {

	protected function _getMappedDomainName() {
		return '\Sl\Module\Home\Model\Emailnotification';
	}

	protected function _getMappedRealName() {
		return '\Sl\Module\Home\Model\Table\Emailnotification';
	}

}