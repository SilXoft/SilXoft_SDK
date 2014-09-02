<?php

namespace Sl\Calculator;

abstract class Uniquechecker extends Calculator {
		

	
	
	public function calculate($Obj){
			
		if ($count  = \Sl_Model_Factory::mapper($Obj)->getCountLikeThis($Obj,$this->getCheckedFields())){
			$this->setWarnings(\Zend_Registry::get('Zend_Translate')->translate('уже есть в системе!'), $this->getCheckedFields());
		};
		
		

		return $this;	
	}
	
	public function getRequiredFields(){
		return array_merge(array('id'),parent::getRequiredFields());
	}
	
	public function getCheckedFields(){
		return parent::getRequiredFields();
	}
	
	public function getValues(){
		return array();
	}
	
	
}

