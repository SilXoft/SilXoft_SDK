<?php
namespace Sl\Module\Auth\Calculator;

class Uniqueuserlogin extends \Sl\Calculator\Uniquechecker  {
    	
     protected $_model_name ='Sl\Module\Auth\Model\User';
	 
	 protected $_required_fields=array(
			'login',
			
	);
	 
}

