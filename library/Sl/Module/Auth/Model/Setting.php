<?php

namespace Sl\Module\Auth\Model;

class Setting extends \Sl_Model_Abstract {

    protected $_master_relation; 
protected $_form;

 
    protected $_state;
    protected $_listview;
    protected $_filters;
    protected $_fieldsets;
    protected $_loged = false;

    protected static $_decode_type;
    
    const DECODE_ARRAY = 1;
    const DECODE_CONFIG = 2;

    

	public function setForm ($form) {
		$this->_form = $form;
		return $this;
	}

	public function setState($state) {
        $this->_state = $this->_encode($state);
        return $this;
    }

    public function setMasterRelation($master_relation) {
        if($master_relation instanceof \Sl\Modulerelation\Modulerelation) {
            $master_relation = $master_relation->getName();
        }
        $this->_master_relation = $master_relation;
        return $this;
    }

    public function setListview($listview) {
        $this->_listview = $this->_encode($listview);
        return $this;
    }

    public function setFilters($filters) {
        $this->_filters = $this->_encode($filters);
        return $this;
    }

    public function setFieldsets($fieldsets) {
        $this->_fieldsets = $this->_encode($fieldsets);
        return $this;
    }

    public function getMasterRelation($as_object = false) {
        if($as_object) {
            return \Sl_Modulerelation_Manager::getRelations($this, $this->getMasterRelation());
        }
        return $this->_master_relation;
    }
    
    public function getState($as_array = false) {
        if($as_array) {
            return $this->_decode($this->getState());
        }
        return $this->_state;
    }

    public function getListview($as_array = false) {
        if($as_array) {
            return $this->_decode($this->getListview());
        }
        return $this->_listview;
    }

    public function getFilters($as_array = false) {
        if($as_array) {
            return $this->_decode($this->getFilters());
        }
        return $this->_filters;
    }

    public function getFieldsets($as_array = false) {
        if($as_array) {
            return $this->_decode($this->getFieldsets());
        }
        return $this->_fieldsets;
    }

    protected function _decode($string) {
        if(is_null($string)) {
            $string = $this->_encode(array());
        }
        switch(self::getDecodeType()) {
            case self::DECODE_CONFIG:
                return new \Sl\Config(json_decode($string, true), true);
            case self::DECODE_ARRAY:
            default:
                return json_decode($string, true);
        }
    }
    
    protected function _encode($data) {
        if(is_array($data)) {
            $data = json_encode($data);
        } elseif($data instanceof \Sl\Config) {
            $data = json_encode($data->toArray());
        }
        return $data;
    }
    
    public static function setDecodeType($type) {
        $old_type = self::getDecodeType();
        self::$_decode_type = $type;
        return $old_type;
    }
    
    public static function getDecodeType() {
        if(!isset(self::$_decode_type)) {
            self::$_decode_type = self::DECODE_ARRAY;
        }
        return self::$_decode_type;
    }
	public function getForm () {
		return $this->_form;
	}
}