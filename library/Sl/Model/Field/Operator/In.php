<?php
namespace Sl\Model\Field\Operator;

class In extends Operator {
    
    protected $_name = 'in';
    
    protected function _buildWhereTemplate() {
        return 'in(?)';
    }
}