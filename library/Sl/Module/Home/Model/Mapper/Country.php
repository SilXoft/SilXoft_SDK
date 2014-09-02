<?php
namespace Sl\Module\Home\Model\Mapper;

class Country extends \Sl_Model_Mapper_Abstract {
    
    protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\Country';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\Country';
    }
}

