<?php

class Sl_Plugin_Acl extends Zend_Controller_Plugin_Abstract {

    public function routeStartup(Zend_Controller_Request_Abstract $request) {
        $acl = new Zend_Acl();
        
        $acl->addRole(new Zend_Acl_Role('guest'));
        $acl->addRole(new Zend_Acl_Role('user'),'guest');
        $acl->addRole(new Zend_Acl_role('admin'), 'user');


        $acl->add(new Zend_Acl_Resource('error'));
        $acl->add(new Zend_Acl_Resource('index'));
        $acl->add(new Zend_Acl_Resource('auth'));
        $acl->add(new Zend_Acl_Resource('user'));
        $acl->add(new Zend_Acl_Resource('menu'));
        $acl->add(new Zend_Acl_Resource('bank'));
        $acl->add(new Zend_Acl_Resource('chanel'));
        $acl->add(new Zend_Acl_Resource('company'));
        $acl->add(new Zend_Acl_Resource('contacts'));
        $acl->add(new Zend_Acl_Resource('currency'));
        $acl->add(new Zend_Acl_Resource('deals'));
        $acl->add(new Zend_Acl_Resource('insurer'));
        $acl->add(new Zend_Acl_Resource('item'));
        $acl->add(new Zend_Acl_Resource('line'));
        $acl->add(new Zend_Acl_Resource('rates'));
        $acl->add(new Zend_Acl_Resource('size'));
        $acl->add(new Zend_Acl_Resource('typerates'));
        $acl->add(new Zend_Acl_Resource('brandline'));
        $acl->add(new Zend_Acl_Resource('numcontract'));
        $acl->add(new Zend_Acl_Resource('discount'));        

        $acl->allow('guest', 'auth');                   // Авторизация
        $acl->allow('guest', 'error');                  // Вывод ошибок

        $acl->allow('user', 'index');
        $acl->allow('user', 'menu');
        $acl->allow('user', 'deals');
        $acl->allow('user', 'insurer');
        $acl->allow('user', 'line','poplist');
        $acl->allow('user', 'line','ajaxsearch');
        $acl->allow('user', 'contacts','poplist');
        $acl->allow('user', 'brandline','poplist');     

        $acl->allow('admin', 'user');                   // Все пользователи
        $acl->allow('admin', 'bank');
        $acl->allow('admin', 'chanel');
        $acl->allow('admin', 'company');
        $acl->allow('admin', 'contacts');
        $acl->allow('admin', 'currency');
        $acl->allow('admin', 'item');
        $acl->allow('admin', 'line');
        $acl->allow('admin', 'rates');
        $acl->allow('admin', 'size');
        $acl->allow('admin', 'typerates');
        $acl->allow('admin', 'brandline');
        $acl->allow('admin', 'numcontract');
        $acl->allow('admin', 'discount');
        
        Application_Service_Acl::setAcl($acl);

        parent::routeStartup($request);
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if(!Application_Service_Acl::getAcl()->isAllowed(Application_Service_Acl::getRole(), $request->getControllerName(), $request->getActionName())) {
            throw new Application_Exception_Acl();
        }
        parent::preDispatch($request);
    }
}

?>
