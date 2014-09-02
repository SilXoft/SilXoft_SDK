<?php
namespace Sl\Module\Home\Model\Mapper;

class Printform extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\Printform';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\Printform';
    }
    
    public function fetchAllByNameType($name, $type_exclude = array(),  $type_include= array()) {
        $where = array(
            'name = ?' => $name,
            'active = 1',
            )
        ;
        if (count($type_include)){
        $where['type in (?)'] =  $type_include;}
        if (count($type_exclude)){
        $where['type not in (?)'] = $type_exclude;}
        $rowset = $this->_getDbTable()->fetchAll($where, array(
            'type'
        ));
        return $this->create($rowset);
    }
    
}