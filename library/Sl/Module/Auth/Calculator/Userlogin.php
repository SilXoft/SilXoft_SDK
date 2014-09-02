<?php

namespace Sl\Module\Auth\Calculator;

class Userlogin extends \Sl\Calculator\Calculator {

    protected $_model_name = 'Sl\\Module\\Auth\\Model\\User';
    protected $_required_fields = array(
        'email',
        'id',
        'login',
    );
    protected $_updated_fields = array(
        'login',
    );

    public function calculate($Obj) {
        /*@var $Obj \Sl\Module\Auth\Model\User*/
        if(!$Obj->getId()) {
            $Obj->setLogin($Obj->getEmail());
        }
        $this->fillModel($Obj);
        return $this;
    }
    
    public function isFieldChanged($name) {
        return (bool) count(array_diff(array_map(function($el) use($name) { return preg_match('#'.$name.'#', $el)?'1':''; }, $this->getChangedFields()), array('')));
    }
}

