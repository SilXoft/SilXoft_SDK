<?php
namespace Sl\Module\Home\Calculator;

class Uniquecityname extends \Sl\Calculator\Uniquechecker  {
    	
     protected $_model_name ='Sl\Module\Home\Model\City';
	 
	 protected $_required_fields=array(
			'name',
			
	);
	 
}

