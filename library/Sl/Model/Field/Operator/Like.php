<?php
namespace Sl\Model\Field\Operator;

class Like extends Operator {
    
    protected $_name = 'like';
    
    protected function _buildWhereTemplate() {
        return 'like(?)';
    }
}