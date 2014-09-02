<?php
namespace Sl\Module\Auth\Model\Mapper;

class Resource extends \Sl_Model_Mapper_Abstract {
    
    protected function _getMappedDomainName() {
        return '\Sl\Module\Auth\Model\Resource';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Auth\Model\Table\Resource';
    }
    


    public function fetchAllByTypeModule($type, $module) {
        if(!is_string($type)) {
            throw new Exception('Wrong "types" parametr. Array or string required. '.__METHOD__);
        }
        $rowset = $this->_getDbTable()->fetchAll(array(
            'name like \''.$type.\Sl_Service_Acl::RES_TYPE_SEPARATOR.$module.\Sl_Service_Acl::RES_DATA_SEPARATOR.'%\'', 'active > 0'
        ));
        if($rowset->count() == 0) return array();
        return $this->create($rowset);
    }
    
    public function findByName($name) {
        $row = $this->_getDbTable()->fetchRow(array('name = ?'=>$name), array('id desc'));
        if(!$row) return null;
        if(!is_array($row)) $row = $row->toArray();
        return $this->_createInstance($row);
    }
}

