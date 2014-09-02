<?php
namespace Sl\Module\Auth\Controller;

class Restriction extends \Sl_Controller_Model_Action {

    public function testAction() {
        $object = \Sl_Model_Factory::object('customer', \Sl_Module_Manager::getInstance()->getModule('customers'));
        print_r(\Sl_Modulerelation_Manager::getRelations($object, 'customeruserresponsible'));die;
        print_r(\Sl\Module\Auth\Service\Restrictions::restrictions($object, \Sl_Modulerelation_Manager::getRelations($object, 'packagecustomer')));
        die;
    }
}