<?php
namespace Sl\Module\Customers\Calculator;

class Uniquecustomername extends \Sl\Calculator\Uniquechecker  {
    	
     protected $_model_name ='Sl\\Module\\Customers\\Model\\Customer';
	 
	 protected $_required_fields=array(
			'name',
			
	);
	 
    
}

