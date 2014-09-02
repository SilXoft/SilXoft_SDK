<?php
namespace Sl\Module\Home\Model;

class Acnt extends \Sl_Model_Abstract implements \Sl\Model\Masterrelation  {

	protected $_name;
    protected $_master_relation;

	public function setName ($name) {
		$this->_name = $name;
		return $this;
	}

	public function getName () {
		return $this->_name;
	}

    public function setMasterRelation ($master_relation) {
        $this->_master_relation = $master_relation;
        return $this;
    }

    public function getMasterRelation () {
        return $this->_master_relation;
    }
    
    
    public function __toString () {
        if (!strlen($this->getName()) && $this->getMasterRelation()){
            $master_obj = current($this -> fetchRelated($this->getMasterRelation()));
            if ($master_obj instanceof \Sl_Model_Abstract){
                $title = implode('_',array('title',$master_obj->findModelName(),$master_obj->findModuleName()));
                $title = implode(' ',array(\Zend_Registry::get('Zend_Translate')->translate($title), $master_obj->__toString()));    
                return $title;
            }
        } else {
            return parent::__toString();
        }
    }
}