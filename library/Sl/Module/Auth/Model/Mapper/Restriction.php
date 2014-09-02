<?php
namespace Sl\Module\Auth\Model\Mapper;

class Restriction extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Auth\Model\Restriction';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Auth\Model\Table\Restriction';
    }
    
    public function fetchAllByRoles(array $roles = array()) {
        $role_relation = \Sl_Modulerelation_Manager::getRelations(\Sl_Model_Factory::object($this), 'restrictionroles');
        if(!$role_relation) {
            throw new \Exception('Can\'t find "restrictionroles" relation.');
        }
        if(!count($roles)) return array();
        $rowset = $this->_getDbTable()->fetchAllByRoles(array_map(function($el) { return $el->getId(); }, $roles), $role_relation);
        
        if(count($rowset) == 0) return array();
        return array_map(array($this, 'create'), $rowset);
    }
    
    public function fetchComplexRestrictions(\Sl_Model_Abstract $model, array $relations) {
        return $this->_getDbTable()->fetchComplexRestrictions($model, $relations, \Zend_Auth::getInstance()->getIdentity()->getId());
    }
}