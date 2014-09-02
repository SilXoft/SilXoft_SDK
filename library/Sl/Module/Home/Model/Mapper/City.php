<?php
namespace Sl\Module\Home\Model\Mapper;

class City extends \Sl_Model_Mapper_Abstract {
    
    protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\City';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\City';
    }
}

