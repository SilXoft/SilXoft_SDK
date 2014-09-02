<?php
namespace Sl\Module\Customers\Model\Mapper;

class Customersource extends \Sl_Model_Mapper_Abstract {
    
    protected function _getMappedDomainName() {
        return '\Sl\Module\Customers\Model\Customersource';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Customers\Model\Table\Customersource';
    }
}

