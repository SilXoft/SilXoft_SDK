<?php
namespace Sl\Module\Home\Model\Mapper;

class Event extends \Sl_Model_Mapper_Abstract {
    
    protected $_custom_mandatory_fields = array();
    
	protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\Event';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\Event';
    }
    
    public function __construct (){
        //відкриття прав на збереження полів подій
        $open_fields = array_keys(\Sl_Model_Factory::object($this)->toArray()); 
        
        $this->_custom_mandatory_fields = array_merge($this->_custom_mandatory_fields, $open_fields); 
        
        parent::__construct();
        
    }
    
}