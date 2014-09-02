<?php
namespace Sl\Module\Auth\Service;

class Restrictions {
    
    protected static $_instance;
    
    protected $_restrictions;
    protected $_restrictions_objects;
    
    const MAIN_OBJECT_SEP = '.';
    
    public static function restrictions(\Sl_Model_Abstract $model, \Sl\Modulerelation\Modulerelation $target = null) {
        return self::getInstance()->getRestrictions($model, $target);
    }
    
    protected function __construct() {
        $cur_user = \Zend_Auth::getInstance()->getIdentity();
        
        $roles = $cur_user->fetchRelated('userroles');
        $restrictions = \Sl_Model_Factory::mapper('restriction', \Sl_Module_Manager::getInstance()->getModule('auth'))
                                    ->fetchAllByRoles($roles);
        foreach($restrictions as $restriction) {
            $this->addRestriction($restriction);
        }
    }
    
    public function addRestriction(\Sl\Module\Auth\Model\Restriction $restriction) {
        $rules = $restriction->getRules(true);
        if(!count($rules)) {
            throw new \Exception('Wrong restrictions object. '.__METHOD__);
        }
        $main_relation = $restriction->fetchMainRelation(true);
        if(!$main_relation) {
            throw new \Exception('Can\'t find main relation. '.__METHOD__);
        }
        $restricted_object = $main_relation->getRelatedObject($restriction->getMainObject(true));
        $last_relation = null;
        $last_related = $restricted_object;
        $relations = array();
        array_shift($rules);
        foreach($rules as $rule) {
            $last_relation = \Sl_Modulerelation_Manager::getRelations($last_related, $rule);
            if(!$last_relation) {
                throw new \Exception('Wrong relation "'.$rule.'" for "'.get_class($last_related).'". '.__METHOD__);
            }
            $last_related = $last_relation->getRelatedObject($last_related);
            if(!$last_related) {
                throw new \Exception('Wrong related model. '.__METHOD__);
            }
            $relations[] = $last_relation;
        }
        
        $restrictions = \Sl_Model_Factory::mapper($restriction)->fetchComplexRestrictions($restricted_object, $relations);
        if(!is_array($restrictions) || (count($restrictions) == 0)) {
            $restrictions = array(0);
        }
        if(!isset($this->_restrictions[$restriction->getMainObject()][$main_relation->getName()])) {
            $this->_restrictions[$restriction->getMainObject()][$main_relation->getName()] = array();
        }
        $this->_restrictions[$restriction->getMainObject()][$main_relation->getName()]
                    = array_merge($this->_restrictions[$restriction->getMainObject()][$main_relation->getName()], $restrictions);
        $this->_restrictions[$restriction->getMainObject()][$main_relation->getName()]
                    = array_unique($this->_restrictions[$restriction->getMainObject()][$main_relation->getName()]);
        $this->_restrictions_objects[$restriction->getMainObject()][$restriction->fetchMainRelation()][] = $restriction;
        return $this;
    }
    
    public static function add(\Sl\Module\Auth\Model\Restriction $restriction) {
        return self::getInstance()->addRestriction($restriction);
    }
    
    /**
     * Возвращает объект сервиса
     * 
     * @return \Sl\Model\Auth\Service\Restrictions
     */
    public static function getInstance() {
        if(!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getRestrictions(\Sl_Model_Abstract $object, \Sl\Modulerelation\Modulerelation $target_relation = null) {
        $object_string = self::object2String($object);
        if(is_null($target_relation)) {
            return isset($this->_restrictions_objects[$object_string])?$this->_restrictions_objects[$object_string]:array();
        } else {
            if(!isset($this->_restrictions[$object_string][$target_relation->getName()])) {
                return array();
            } else {
                return $this->_restrictions[$object_string][$target_relation->getName()];
            }
        }
    }
    
    public static function object2String(\Sl_Model_Abstract $object) {
        return $object->findModuleName().self::MAIN_OBJECT_SEP.$object->findModelName();
    }
    
    public static function string2Object($string) {
        list($module, $model) = explode(self::MAIN_OBJECT_SEP, $string);
        try {
            return \Sl_Model_Factory::object($model, \Sl_Module_Manager::getInstance()->getModule($module));
        } catch(\Exception $e) {
            return null;
        }
    }
}