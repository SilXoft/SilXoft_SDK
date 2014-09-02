<?php
namespace Sl\Module\Customers\Model\Mapper;

class Customer extends \Sl_Model_Mapper_Abstract {

    protected function _getMappedDomainName() {
        return '\Sl\Module\Customers\Model\Customer';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Customers\Model\Table\Customer';
    }

    /**
     * Вытаскивает все объекты
     * @return Sl_Model_Abstract[]
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null, $relations = array()) {

        if (is_null($where) || !$where || trim($where) === '') {
            $where = array(' id < 0 ');
        }

        return parent::fetchAll($where, $order, $count, $offset, $relations);
    }

    public function fetchAllExtended(\Sl\Model\Identity\Identity $identity) {
        try {
            $comps_count = count($identity->getComps());
        } catch (\Exception $e) {
            $comps_count = 0;
        }
        if (!$comps_count) {

            $Obj = \Sl_Model_Factory::object($identity);
            $resource_name = \Sl_Service_Acl::joinResourceName(array(
                'type' => \Sl_Service_Acl::RES_TYPE_CUSTOM,
                'module' => $Obj -> findModuleName(),
                'name' => \Sl\Module\Customers\Listener\Customlist::RES_SHOW_CUSTOMERS
            ));
            if (!\Sl_Service_Acl::isAllowed($resource_name)) {
                if($identity->getCurrentField() && $identity->getCurrentField()->isIncomplete()) {
                    $identity->getCurrentField()->addTest('=', md5(time()));
                }
                $identity -> field('id') -> lt(0);
            }

        }
        return $this -> _getDbTable() -> fetchAllExtended($identity);
    }

    public function findDealer(\Sl\Module\Customers\Model\Customer $customer) {

        if ($customer -> getId()) {
            $dealer_relation = \Sl_Modulerelation_Manager::getRelations($customer, 'customerdealer');
            if (!$dealer_relation) {
                throw new Exception('Can\'t find "Customerdealer" relation');
            }

            $is_dealer_relation = \Sl_Modulerelation_Manager::getRelations($customer, 'customerisdealer');
            if (!$dealer_relation) {
                throw new Exception('Can\'t find "Customerisdealer" relation');
            }

            $dealer = $dealer_relation -> getRelatedObject($customer);

            $dealer_id = $this -> _getDbTable() -> findDealerIdByCustomer($customer -> getId(), $dealer_relation, $is_dealer_relation);

            return $this -> find($dealer_id);
        }

    }

}
