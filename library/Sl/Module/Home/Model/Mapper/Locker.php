<?php
namespace Sl\Module\Home\Model\Mapper;

class Locker extends \Sl_Model_Mapper_Abstract {
    
    protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\Locker';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\Locker';
    }
	
	
	public function eraseOldRecords(){
		
		$this->_getDbTable()-> eraseOldRecords();
	}
	
	
	public function checkModel(\Sl_Model_Abstract $model){
		
		$current_user = \Zend_Auth::getInstance() -> getIdentity();	
		
		if (!($current_user instanceof \Sl_Model_Abstract) || !$current_user->getId()) return false;
			
		if (!$model->getId()) return true;
		
		$this->eraseOldRecords();
		
		$Locker =  \Sl_Model_Factory::object($this);
		
		$Locker->setName($model);
		$Locker->setUserId($current_user->getId());
		
		if ($this->_getDbTable()-> checkModelOnEdit($Locker->getName(),$Locker->getUserId())){
			return false;
		} else {
			$this->_getDbTable()-> lockModel($Locker->getName(),$Locker->getUserId());	
			return true;
		};
		
	}
	
    
    
    public function getEditorId(\Sl_Model_Abstract $model) {
        if ($model->getId()) {
            $Locker = \Sl_Model_Factory::object($this);
            $Locker->setName($model);
            return $this->_getDbTable()->getModelEditor($Locker->getName());
        }
    }
    
    
	public function unlockModel(\Sl_Model_Abstract $model){
		
		$current_user = \Zend_Auth::getInstance() -> getIdentity();	
		
		if (!$current_user->getId()) return false;
			
		if (!$model->getId()) return true;
		
		
		
		$Locker =  \Sl_Model_Factory::object($this);
		
		$Locker->setName($model);
		$Locker->setUserId($current_user->getId());
		
		$this->_getDbTable()-> unlockModel($Locker->getName(),$Locker->getUserId());
		return true;
		
		
	}
	
}

