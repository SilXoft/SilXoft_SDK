<?php
namespace Sl\Module\Home\Calculator;

class Uniquecustomeremail extends \Sl\Calculator\Uniquechecker  {
    	
     protected $_model_name ='Sl\Module\Home\Model\Email';
	 
	 protected $_required_fields=array(
			'mail',
			
	);
	 
}

