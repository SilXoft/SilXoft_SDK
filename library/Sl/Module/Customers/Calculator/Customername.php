<?php
namespace Sl\Module\Customers\Calculator;

class Customername extends \Sl\Calculator\Calculator {

    protected $_model_name = 'Sl\\Module\\Customers\\Model\\Customer';

    protected $_required_fields = array(
        'first_name',
        'middle_name',
        'last_name',
        'modulerelation_customeridentifiercustomer'
    );
    protected $_updated_fields = array('name', );

    public function calculate($Obj) {

        $first_name = trim($Obj -> getFirstName());
        $middle_name = trim($Obj -> getMiddleName());
        $last_name = trim($Obj -> getLastName());
        $relation = \Sl_Modulerelation_Manager::getRelations($Obj, 'customeridentifiercustomer');
        $customeridentifier_obj = $relation -> getRelatedObject($Obj);
        $customeridentifier = '';
        
        if ($relation instanceof \Sl\Modulerelation\Modulerelation) {
            if (!$Obj -> issetRelated('customeridentifiercustomer')) {
                $Obj = \Sl_Model_Factory::mapper($Obj) -> findRelation($Obj, $relation);
            }
            $identifier_arr = $Obj -> fetchRelated('customeridentifiercustomer');
            if (count($identifier_arr)) {
                $identifier = current($identifier_arr);

                if (!is_object($identifier)) {
                    $id = current(array_keys($identifier_arr));
                    
                    if ($id > 0) {
                        $identifier = \Sl_Model_Factory::mapper($customeridentifier_obj) -> find($id);

                    }

                }
                
                
                $customeridentifier = $identifier->getName();
                
            }

        }
        //$customeridentifier = trim($Obj->getLastName());
        
        $name = implode(' ', array(
           $customeridentifier, 
            $last_name,
            $first_name,
            $middle_name
        ));

        $Obj -> setName(trim($name));

        $this -> fillModel($Obj);

        return $this;

    }

}
