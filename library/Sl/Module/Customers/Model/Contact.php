<?php
namespace Sl\Module\Customers\Model;

class Contact extends \Sl_Model_Abstract {

	protected $_name;
	protected $_post;
	protected $_description;
        protected $_brief_description;

        public function setName ($name) {
		$this->_name = $name;
		return $this;
	}
        public function setBriefDescription ($brief_description) {
		$this->_brief_description = $brief_description;
		return $this;
	}        
	public function setPost ($post) {
		$this->_post = $post;
		return $this;
	}
	public function setDescription ($description) {
		$this->_description = $description;
		return $this;
	}

	public function getName () {
		return $this->_name;
	}
        public function getBriefDescription () {
		 return $this->_brief_description;
		
	}                
	public function getPost () {
		return $this->_post;
	}
	public function getDescription () {
		return $this->_description;
	}

	public function __toString() {
		
              //  if (method_exists($this, 'getBriefDescription') and strlen($this->getBriefDescription())>0)
	//		return $this -> getBriefDescription() . '';
                if (!method_exists($this, 'getName'))
			return $this -> getId() . '';
		else {
			return $this -> _getTranslated($this -> getName()).'';
		}
	}

}