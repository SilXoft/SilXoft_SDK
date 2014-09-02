<?php
namespace Sl\Module\Customers\Model\Table;
class Customer extends \Sl\Model\DbTable\DbTable {

	protected $_name = 'cust_customers';
	protected $_primary = 'id';
    
    
    public function findDealerIdByCustomer($customer_id, \Sl\Modulerelation\Modulerelation $c_d_relation, \Sl\Modulerelation\Modulerelation $c_is_d_relation) {
        $this->_cleanJoinNames();    
        $select = $this->getAdapter()->select();
        
        $select ->from($this->_name, array());
        
        $customer = \Sl_Model_Factory::object($this);
        $dealer = $c_d_relation->getRelatedObject($customer);
        
        $select = $this->_buildInnerJoin($select, $dealer, $c_d_relation);
        $select = $this->_buildInnerJoin($select, $customer, $c_is_d_relation, array('id'), $c_d_relation->getName());
        
        $select ->where('cust_customers.id = ?', $customer_id);
        //echo $select; die;
        $this->_cleanJoinNames();
        
        return (int) $this->getAdapter()->fetchOne($select);
    }
    	
}
