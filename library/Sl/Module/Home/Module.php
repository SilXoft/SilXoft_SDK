<?php
namespace Sl\Module\Home;

class Module extends \Sl_Module_Abstract {

	public function getListeners() {
        return array(
			array(
				'listener' => new Listener\Home($this),
				'order' => 5
			),
			new Listener\Chain($this),
			new Listener\Printform($this),
			new Listener\Acntcreate($this),
			new Listener\Emaildetails($this),
			new Listener\Informer($this),
			new Listener\Detectdevice($this),
			new Listener\Ckeditorinclude($this),
			array(
				'listener' => new Listener\Buttons($this),
				'order' => 10
			),
			array(
				'listener' => new Listener\Cron($this),
				'order' => 300
			),
			array(
				'listener' => new Listener\Listdata($this),
				'order' => 10
			),
			array(
                            'listener' => new Listener\Basefields($this),
                            'order' => 20
			),
		);
	}

	public function getModulerelations() {
		if (!($config_relations = $this -> section(parent::MODULERELATION_CONFIG_SECTION))) {
			$config_relations = $this -> _saveModuleConfig(array(), parent::MODULERELATION_CONFIG_SECTION);
		};

		return array_merge($config_relations -> toArray(), array( 
		      array(
				'type' => \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
				'db_table' => 'Sl\Module\Home\Modulerelation\Table\Citycountry'
			   ),
			   
			   array(
                'type' => \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
                'db_table' => 'Sl\Module\Home\Modulerelation\Table\Parentacnt'
               ),
            ));
	}

	public function getCalculators() {
		//error_reporting(E_ALL);
        return array(
            array('calculator' => new Calculator\Printformname()), 
            array('calculator' => new Calculator\Acntidentity()),
            array('calculator' => new Calculator\Uniquecustomeremail(), ),
            array('calculator' => new Calculator\Uniquesettingsname(), ),
            array('calculator' => new Calculator\Settingsuppername(), ),
            array('calculator' => new Calculator\Uniquecityname(), ),
            
            //new Calculator\Printformname(),
			/* array ('calculator' => new Calculator\Uniquecustomeremail(),
			 'sort_order' => 20
			 ), */
		);
	}

}
