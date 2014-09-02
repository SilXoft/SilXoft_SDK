<?php
namespace Sl\Module\Customers;
class Module extends \Sl_Module_Abstract {

	public function getListeners() {
		return array(
		/*  array('listener' => new Listener\Customerdealer($this), 
                  'order' => 150),
          array('listener' => new Listener\Customeremails($this), 
                  'order' => 100),
          array('listener' => new Listener\Customlist($this), 
                  'order' => 110),
          array('listener' => new Listener\Firstpage($this), 
                  'order' => 220),
          array('listener' => new Listener\Customercontact($this), 
                  'order' => 230),
                              
            */
        );
	}

	public function getModulerelations() {
		if (!($config_relations = $this -> section(parent::MODULERELATION_CONFIG_SECTION))) {
			$config_relations = $this -> _saveModuleConfig(array(), parent::MODULERELATION_CONFIG_SECTION);
		};

		return array_merge($config_relations -> toArray(), array(
			/*
			 array(
			 'type' => \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER,
			 'db_table' => 'Sl\Module\Customers\Modulerelation\Table\Dealeremails'
			 ),

			 array(
			 'type' => \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER,
			 'db_table' => 'Sl\Module\Customers\Modulerelation\Table\Dealerphones'
			 ),
			 */
			array(
				'type' => \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER,
				'db_table' => 'Sl\Module\Customers\Modulerelation\Table\Customeremails'
			),
			array(
				'type' => \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER,
				'db_table' => 'Sl\Module\Customers\Modulerelation\Table\Customerphones'
			),
			array(
				'type' => \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
				'db_table' => 'Sl\Module\Customers\Modulerelation\Table\Customerdealer'
			),
			array(
				'type' => \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
				'db_table' => 'Sl\Module\Customers\Modulerelation\Table\Customercity'
			),
			array(
				'type' => \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
				'db_table' => 'Sl\Module\Customers\Modulerelation\Table\Customercountry'
			),
			array(
				'type' => \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE,
				'db_table' => 'Sl\Module\Customers\Modulerelation\Table\Customerisdealer'
			),

			array(
				'type' => \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
				'db_table' => 'Sl\Module\Customers\Modulerelation\Table\Customercustsource'
			),
		));

	}

	// /Customername
	public function getCalculators() {
		return array(
			array(
				'calculator' => new Calculator\Customername(),
				'sort_order' => 10
			),

			array(
				'calculator' => new Calculator\Uniquecustomername(),
				'sort_order' => 10
			),
			array(
				'calculator' => new Calculator\Uniquecustomeremail(),
				'sort_order' => 20
			),
			array(
				'calculator' => new Calculator\Uniquecustomerphone(),
				'sort_order' => 10
			),
			array(
				'calculator' => new Calculator\Uniquecustomerskype(),
				'sort_order' => 20
			),
			array(
				'calculator' => new Calculator\Uniquecustomerqq(),
				'sort_order' => 10
			),
            /*array(
                'calculator' => new Calculator\Customeridentity(),
                'sort_order' => 50,
            ),*/
		);
	}

}
