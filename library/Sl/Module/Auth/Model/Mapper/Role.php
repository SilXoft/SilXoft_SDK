<?php
namespace Sl\Module\Auth\Model\Mapper;
use \Sl_Exception_Model as Exception;

class Role extends \Sl_Model_Mapper_Abstract {
    
    protected function _getMappedDomainName() {
        return '\Sl\Module\Auth\Model\Role';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Auth\Model\Table\Role';
    }
	
	public function findDefaultRole(){
		$data= $this->_getDbTable()->find(\Sl_Service_Acl::ROLE_DEFAULT);
		
		if (is_array($data)) {
			return $this -> _createInstance($data);
		} elseif ($data instanceof \Zend_Db_Table_Rowset) {
			$data=$data -> toArray();
			return $this->_createInstance($data[0]); 
			
		} elseif ($data instanceof \Zend_Db_Table_Row) {
			return $this -> _createInstance($data -> toArray());
		}
	}
    
    public function findByName($name) {
        $row = $this->_getDbTable()->fetchRow(array('name = ?'=>$name), array('id desc'));
        if(!$row) return null;
        if(!is_array($row)) $row = $row->toArray();
        return $this->_createInstance($row);
    }
}

