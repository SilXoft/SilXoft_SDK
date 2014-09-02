<?php
namespace Sl\Module\Auth\Model;

class Resource extends \Sl_Model_Abstract {

	protected $_name;
	protected $_description = '';
	
	protected $type;
	protected $resource_array;
	
	public function setName($name) {
		$this -> _name = $name;
		return $this;
	}

	public function setDescription($description) {
		$this -> _description = $description;
		return $this;
	}

	public function getName() {
		return $this -> _name;
	}

	public function getDescription() {
		return $this -> _description;
	}
	
	public function fetchType() {
        try {
            $array = $this->fetchResourceArray();
        } catch (\Exception $e) {
            if(!preg_match('/split resource name/', $e->getMessage())) {
                throw $e;
            } else {
                return null;
            }
        }
		return $array['type'];
	}
	
	public function assignResourceArray() {
		$this->resource_array =  \Sl_Service_Acl::splitResourceName($this->getName());
		return $this;	
	}
	
	public function fetchResourceArray() {
		if (!is_array($this->resource_array)){
			$this->assignResourceArray();
		}  
		return $this->resource_array;	
	}
	
	protected function _getTranslated($name) {
		if ($translator = \Zend_Registry::get('Zend_Translate')) {
			$new_name='';
			$name_array = \Sl_Service_Acl::splitResourceName($name);
			switch($name_array['type']) {
				case \Sl_Service_Acl::RES_TYPE_MVC :
					$new_name = $translator -> translate('Страница') . ' ' . mb_strtoupper($name_array['controller'].'/'.$name_array['action']) . ' ('.$name_array['module'] . ')';
					break;
				case \Sl_Service_Acl::RES_TYPE_FIELD :
					$new_name = $translator -> translate('Форма') .' '. mb_strtoupper($name_array['name']) .' '. $translator -> translate('поле') . ' ' . mb_strtoupper($name_array['field']) . ' ('. $name_array['module']. ')' ;
					break;
				case \Sl_Service_Acl::RES_TYPE_OBJ :
						$new_name = $translator -> translate('Модель') . ' '. mb_strtoupper($name_array['name']).' '. $translator -> translate('Поле') . ' ' . mb_strtoupper($name_array['field']) . ' ('.$name_array['module'] .')' ;
					break;
				default :
					$new_name = $name;
					break;
			}
			return $new_name;
		} else
			return $name;

	}

}
