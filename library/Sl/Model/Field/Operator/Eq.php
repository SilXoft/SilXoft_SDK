<?php
namespace Sl\Model\Field\Operator;

class Eq extends Operator {
    
    protected $_name = 'eq';
    
    protected function _buildWhereTemplate() {
        return '= ?';
    }
}