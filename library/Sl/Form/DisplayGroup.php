<?php
namespace Sl\Form;

class DisplayGroup extends \Zend_Form_DisplayGroup {
    
    public function setLabel($label) {
		$this -> setAttrib('label', strval($label));
	}

	public function getLabel() {
		return $this -> getAttrib('label');
	}
    
    public function isRequired() {
		return false;
	}

	public function setRequired() {
		return;
	}
}