<?php
namespace Sl\Module\Customers\Model\Table;

class Lead extends \Sl\Model\DbTable\DbTable {
	protected $_name = 'cust_leads';
	protected $_primary = 'id';
    
    public function findByEmail($email) {
        return $this->fetchRow('email = '.$this->getAdapter()->quote($email));
    }
}