<?php
namespace Sl\Module\Home\Model\Mapper;

class Email extends \Sl_Model_Mapper_Abstract {
    
    protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\Email';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\Email';
    }
}

