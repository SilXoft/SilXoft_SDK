<?php
namespace Sl\Model\Field\Operator;

class Lt extends Operator {
    
    protected $_name = 'lt';
    
    protected function _buildWhereTemplate() {
        return '< ?';
    }
}