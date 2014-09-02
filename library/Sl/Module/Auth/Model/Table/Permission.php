<?php

namespace Sl\Module\Auth\Model\Table;

class Permission extends \Sl\Model\DbTable\DbTable {

    protected $_name = 'permissions';
    protected $_primary = 'id';

    /**
     * Масив зв'язків resource_name-permission-role_name 
     * @param array $roles
     * @return rowset
     */
    public function fetchAllByRoles(array $roles) {
        $select = $this->getAdapter()->select();
        $select->from(array('p' => 'permissions'))
                ->join(array('r' => 'roles'), 'r.id = p.role_id', array('role_name' => 'r.name'))
                ->join(array('res' => 'resources'), 'res.id = p.resource_id', array('resource_name' => 'res.name'))
                ->where(' role_id in (?) ', $roles)
                ->where(' p.active = 1 ')
                ->where(' res.active = 1 ');

        return $this->getAdapter()->fetchAll($select);
    }

    public function fetchAllByNameRoles($name, array $roles) {
        $select = $this->getAdapter()->select();
        $select->from(array('p' => 'permissions'))
                ->join(array('r' => 'roles'), 'r.id = p.role_id', array('role_name' => 'r.name'))
                ->join(array('res' => 'resources'), 'res.id = p.resource_id', array('resource_name' => 'res.name'))
                ->where('res.name like \''.$name.'%\'')
                ->where('role_id in (?)', $roles)
                ->where('p.active = 1')
                ->where('res.active = 1');
        
        return $this->getAdapter()->fetchAll($select);
    }

}
