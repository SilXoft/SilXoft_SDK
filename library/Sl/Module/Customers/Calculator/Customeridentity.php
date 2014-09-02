<?php
namespace Sl\Module\Customers\Calculator;

class Customeridentity extends \Sl\Calculator\Identitycalculator {
    
    protected $_model_name = 'Sl\\Module\\Logistic\\Model\\Customers\\Customer';
    protected $_required_fields = array(
        'is_dealer'
    );
    protected $_update_fields = array(
        'name',
    );
    
    public function calculate($Obj) {
        if(isset($Obj['is_dealer'])) {
            if($Obj['is_dealer']) {
                $Obj['name'] .= '<span class="htmlify icon-star-empty pull-right">&nbsp;</span>';
            }
        }
        return $Obj;
    }
}