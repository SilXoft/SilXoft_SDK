<?php

namespace Sl\Module\Home\Calculator;

class Settingsuppername extends \Sl\Calculator\Calculator {

    protected $_model_name = 'Sl\\Module\\Home\\Model\\Settings';
    protected $_required_fields = array(
                'name',
    );
    protected $_updated_fields = array(
                'name',
    );

    public function calculate($Obj) {
        
        $name = $Obj->getName();
        $name = strtoupper($name);
        $Obj->setName($name);
        $this->fillModel($Obj);
        return $this;
        
    }
    
}