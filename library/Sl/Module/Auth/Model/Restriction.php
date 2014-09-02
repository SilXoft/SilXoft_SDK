<?php
namespace Sl\Module\Auth\Model;

class Restriction extends \Sl_Model_Abstract {

	protected $_name;
    protected $_main_object;
    protected $_rules;
    protected $_type;
    protected $_null_include;
    
    protected $_lists = array(
        'type' => 'auth_restrictions_type',
        'null_include' => 'auth_restrictions_nullinclude',
    );
    
    const STATUS_STRICT = 1;
    const STATUS_FREE = 0;
    
    const NULL_NO = 0;
    const NULL_YES = 1;
    
    const RULES_SEP = ':';
    
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}
    
    public function setRules($rules) {
        if(is_array($rules)) {
            $rules = implode(self::RULES_SEP, $rules);
        }
        $this->_rules = $rules;
        return $this;
    }
    
    public function setType($type) {
        $this->_type = $type;
        return $this;
    }
    
    public function setNullInclude($null_include) {
        $this->_null_include = $null_include;
        return $this;
    }
    
    /**
     * Базовый объект для просчета
     * 
     * @param type $object
     * @return \Sl\Module\Auth\Model\Restriction
     */
    public function setMainObject($object) {
        if($object instanceof \Sl_Model_Abstract) {
            $object = \Sl\Module\Auth\Service\Restrictions::object2String($object);
        }
        $this->_main_object = $object;
        return $this;
    }
    
    public function getName() {
		return $this->_name;
	}
    
    public function getRules($pretty = false) {
        if($pretty) {
            return explode(self::RULES_SEP, $this->getRules());
        }
        return $this->_rules;
    }
    
    public function getType() {
        return $this->_type;
    }
    
    public function getNullInclude() {
        return $this->_null_include;
    }
    
    /**
     * Возвращает базовую модель
     * 
     * @param type $pretty если true вернет объект|null
     * @return \Sl_Model_Abstract|null
     */
    public function getMainObject($pretty = false) {
        if($pretty) {
            return \Sl\Module\Auth\Service\Restrictions::string2Object($this->getMainObject());
        }
        return $this->_main_object;
    }
    
    /**
     * 
     * @param type $pretty
     * @return \Sl\Modulerelation\Modulerelation|null
     * @throws \Exception
     */
    public function fetchMainRelation($pretty = false) {
        if(!$this->getRules() || (count($this->getRules(true)) < 1)) {
            return null;
        } else {
            if(!$pretty) {
                return array_shift($this->getRules(true));
            } else {
                if($this->getMainObject(true)) {
                    return \Sl_Modulerelation_Manager::getRelations($this->getMainObject(true), $this->fetchMainRelation());
                } else {
                    throw new \Exception('Relation exists but can\'t build main onject.');
                }
            }
        }
    }
    
    
}