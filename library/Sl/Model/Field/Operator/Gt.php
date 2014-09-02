<?php
namespace Sl\Model\Field\Operator;

class Gt extends Operator {
    
    protected $_name = 'gt';
    
    protected function _buildWhereTemplate() {
        return '> ?';
    }
}