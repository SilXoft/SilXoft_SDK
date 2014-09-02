<?php
namespace Sl\Module\Home\Model\Table;
	     //\Sl\Module\Home\Model\Table\Сity
class Locker extends \Sl\Model\DbTable\DbTable {

	protected $_name = 'locked_objects';
	protected $_primary = 'id';
	
	public function eraseOldRecords($minutes = 1){
		$this->delete('timestamp < DATE_SUB(NOW(),INTERVAL '.intval($minutes).' MINUTE)');
		
	}
	
	public function checkModelOnEdit($name, $user_id){
		$select = $this->select();
		$select->from($this->_name,array('count'=>'COUNT(*)'));
		$select->where('name ="'.$name.'" AND user_id <> '.$user_id);
		return $this->getAdapter()->fetchOne($select);
	}
	
    public function getModelEditor($name){
        $select = $this->select();
        $select->from($this->_name,array('user_id'=>'user_id'));
        $select->where('name =?',$name);
        $select->limit(1);
        return $this->getAdapter()->fetchOne($select);
    }
    
	public function unlockModel($name, $user_id){
		$this->delete('name = "'.$name.'" AND user_id='.$user_id);
		
	}
	
	public function lockModel($name, $user_id){
		try {
              $id = $this->insert(array('name'=>$name,'user_id'=>$user_id));
            } catch(\Zend_Db_Statement_Exception $e) {
            	
                if(preg_match('/SQLSTATE\[23000\]/', $e->getMessage())) {
                    // Entry already exists no need to insert
                    $select = $this->select();
					$select->from($this->_name,array('id'));
					$select -> where ('name = "'.$name.'"');
					$id = $this->getAdapter()->fetchOne($select);
                    if(!$id) {
                        throw new Exception('DB answer unique constraint violation but can\'t find such row');
                    }
                    $this->update(array('create'=>date('Y-m-d H:i:s')),'id='.$id);
                } else {
                    throw new Exception($e);
                }
            }
		
	}
	
}
