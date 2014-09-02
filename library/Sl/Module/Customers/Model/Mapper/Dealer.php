<?php
namespace Sl\Module\Customers\Model\Mapper;

class Dealer extends \Sl_Model_Mapper_Abstract {
    
    protected function _getMappedDomainName() {
        return '\Sl\Module\Customers\Model\Dealer';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Customers\Model\Table\Dealer';
    }
}

