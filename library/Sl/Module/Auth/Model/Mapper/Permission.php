<?php

namespace Sl\Module\Auth\Model\Mapper;

class Permission extends \Sl_Model_Mapper_Abstract {

    protected function _getMappedDomainName() {
        return '\Sl\Module\Auth\Model\Permission';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Auth\Model\Table\Permission';
    }

    public function fetchAllByRoleResource($role_id, $resource_id, array $active = array(1, 0)) {
        $where = ' role_id=' . $role_id . ' AND resource_id=' . $resource_id.=' AND active IN (' . implode(',', $active) . ') ';
        $rowset = $this->_getDbTable()->fetchAll($where);
        if (count($rowset) == 0)
            return array();
        if (!is_array($rowset))
            $rowset = $rowset->toArray();
        return array_map(array(
            $this,
            '_createInstance'
                ), $rowset);
    }

    /**
     * Масив зв'язків resource_name-permission-role_name 
     * @param array $roles
     * @return array
     */
    public function fetchAllByRoles(array $roles) {
        if (!count($roles))
            return array();
        $rowset = $this->_getDbTable()->fetchAllByRoles(array_keys($roles));

        return (is_object($rowset)) ? $rowset->toArray() : $rowset;
    }
    
    public function fetchAllByNameRoles($name, array $roles) {
        if(!count($roles)) {
            return array();
        }
        $rowset = $this->_getDbTable()->fetchAllByNameRoles($name, $roles);
        if(!is_array($rowset)) {
            $rowset = $rowset->toArray();
        }
        return $rowset;
    }

    /**
     * Удаление объекта
     * @param Sl_Model_Abstract $object
     * @throws Sl_Exception_Db
     */
    public function delete(\Sl_Model_Abstract $object) {
        $this->force_delete($object);
    }

}
