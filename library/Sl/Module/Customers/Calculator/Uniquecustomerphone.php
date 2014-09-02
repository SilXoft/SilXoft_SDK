<?php
namespace Sl\Module\Customers\Calculator;

class Uniquecustomerphone extends \Sl\Calculator\Uniquechecker  {
    	
     protected $_model_name ='Sl\Module\Home\Model\Phone';
	 
	 protected $_required_fields=array(
			'phone',
			
	);
	 
}

