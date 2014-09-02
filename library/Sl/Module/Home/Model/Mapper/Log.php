<?php
namespace Sl\Module\Home\Model\Mapper;

class Log extends \Sl_Model_Mapper_Abstract {

	protected function _getMappedDomainName() {
		return '\Sl\Module\Home\Model\Log';
	}

	protected function _getMappedRealName() {
		return '\Sl\Module\Home\Model\Table\Log';
	}

	public function find(\Sl_Model_Abstract $object) {

	}

	public function save(\Sl_Model_Abstract $object, \Sl\Module\Home\Model\Log $log_object, $no_userId = false) {

        $relations = \Sl_Modulerelation_Manager::getRelations($log_object);

        if (count($relations)) {
            throw new Exception("Log model can not to have a Modulerelations", 1);
        }

        $data = $data_copy = $log_object->toArray();

        foreach ($data_copy as $field => $value) {
            if (in_array($field, array('create', 'timestamp'))) {
                unset($data[$field]);
            }
            if ($value === '' && is_null($data[$field])) {
                unset($data[$field]);
            }
        }

        
        if (!($id = $log_object->getId())) {
            unset($data['id']);
            unset($data['archived']);
            unset($data['extend']);
            $data['create'] = date('Y-m-d H:i:s');
            if ($no_userId == true) {
                if (!isset($data['user_id']) || !$data['user_id']) {
                    $data['user_id'] = 0;
                }
            }
            $this->_getDbTable()->insert(\Sl\Service\Loger::getLogTableName($object), $data);
        } else {
            throw new Exception("The Log can not be updated ", 1);
        }
    }

	protected function saveCollection($row) {

	}
    
    public function getObjectFieldLog(\Sl_Model_Abstract $model, array $fields, $position = 1) {
        $log_table = \Sl\Service\Loger::getLogTableName($model);
        if(($position === 0) || ($position === '0')) {
            throw new \Exception('Position parameter has wrong value. '.__METHOD__);
        }
        if(!$model->getId()) {
            throw new \Exception('Can\'t get information without model Id. '.__METHOD__);
        }
        $order = '';
        $offset = (abs($position) - 1);
        if($position > 0) {
            $order = 'id asc';
        } else {
            $order = 'id desc';
        }
        return $this->_getDbTable()->getObjectFieldLog($log_table, $model->getId(), $fields, $offset, $order, $offset);
    }
    
    public function getObjectEditorsIds(\Sl_Model_Abstract $model, $field) {
        //error_reporting(E_ALL);
        $log_table = \Sl\Service\Loger::getLogTableName($model);
         return  $this->_getDbTable()->getObjectEditorsIds($log_table, $model->getId(), $field);   
    }
    
    
    
    public function fetchAllLogs(\Sl_Model_Abstract $model, array $search, $order, $limit, $offset) {
        if(!$model->isLoged() || !$model->getId()) {
            return array();
        }
        $log_table = \Sl\Service\Loger::getLogTableName($model);
        return $this->_getDbTable()->getObjectLog($log_table, $model->getId(), $search, $order, $limit, $offset);
    }

}
