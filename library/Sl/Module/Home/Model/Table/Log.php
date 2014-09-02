<?php
namespace Sl\Module\Home\Model\Table;
	     //\Sl\Module\Home\Model\Table\Сity
class Log extends \Sl\Model\DbTable\DbTable {

	protected $_name = '';
	protected $_primary = 'id';
	
	
	public function insert($table_name, array $data){
			
		$this->getAdapter()->insert($table_name,$data);
		
	}
	
    
    public function getObjectFieldLog($log_table, $model_id, array $fields, $offset, $order, $offset) {
        
        $select = $this->getAdapter()->select();
        
        $select ->from($log_table, $fields)
                ->where('object_id = ?', $model_id)
                ->order($order)
                ->limit(1, $offset)
                ;  
        //echo $select;die;
        return $this->getAdapter()->fetchRow($select, array(), \Zend_Db::FETCH_ASSOC);
    }
    
    public function getObjectEditorsIds($log_table, $model_id, $field) {
    
        $select = $this->getAdapter()->select();
        $user_id = $log_table.'.'.$field;
        $select ->from($log_table, array('userids' => 'distinct('.$user_id.')'));
        $select ->where('object_id = ?', $model_id);
               
                
        
           return $this->getAdapter()->fetchCol($select);
    }
    
    
    public function getObjectLog($log_table, $model_id, $where, $order, $limit, $offset) {
        
        $select = $this->getAdapter()->select();
        
        $select ->from(array('t'=>$log_table), array('t.timestamp', 'field_name', 'old_value', 'new_value', 'user_id'))
                ->where('object_id = ?', $model_id)
                ->where('field_name not in(?)', array('active', 'timestamp', 'create', 'id'))
                ->order($order)
                ->limit($limit, $offset)
                ;
        foreach($where as $field=>$condition) {
            $select->where($field.' = ?', $condition);
        }
        $result = $this->getAdapter()->fetchAll($select, array(), \Zend_Db::FETCH_ASSOC);
        $select ->reset(\Zend_Db_Select::LIMIT_OFFSET)
                ->reset(\Zend_Db_Select::LIMIT_COUNT)
                ->reset(\Zend_Db_Select::COLUMNS)
                ->columns(array('sum'=>'COUNT(id)'));
        $result['filtered'] = $this->getAdapter()->fetchOne($select);
        $select ->reset(\Zend_Db_Select::WHERE)
                ->where('object_id = ?', $model_id)
                ->where('field_name not in(?)', array('active', 'timestamp', 'create', 'id'));
        $result['total'] = $this->getAdapter()->fetchOne($select);
        return $result;
    }
}
