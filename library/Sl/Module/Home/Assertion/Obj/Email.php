<?php

namespace Sl\Module\Home\Assertion\Obj;

class Email implements \Zend_Acl_Assert_Interface {
  
    
    public function assert(\Zend_Acl $acl, \Zend_Acl_Role_Interface $role = null, \Zend_Acl_Resource_Interface $resource = null, $privilege = null) {
        
        $context = \Sl_Service_Acl::getContext();
        $resource_data = \Sl_Service_Acl::splitResourceName($resource);
        $resource_type = $resource_data['type'];
        

       //print_r($context);
       if ($resource_type == \Sl_Service_Acl::RES_TYPE_OBJ && $resource_data['name'] == 'email' && $context instanceof \Sl\Assertion\Context\Form) {
            
            
            if($resource_data['field'] != 'mail')   return false;
        
            
         //   die;
        }
        return true;

    }
    
}