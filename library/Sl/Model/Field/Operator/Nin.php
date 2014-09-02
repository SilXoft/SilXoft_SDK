<?php
namespace Sl\Model\Field\Operator;

class Nin extends Operator {
    
    protected $_name = 'nin';
    
    protected function _buildWhereTemplate() {
        return 'nin(?)';
    }
}