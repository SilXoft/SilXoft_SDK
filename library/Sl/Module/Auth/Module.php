<?php
namespace Sl\Module\Auth;

class Module extends \Sl_Module_Abstract {

	public function getListeners() {
		return array(
			//new Sl_Module_Auth_Listener_Auth($this),
			array('listener' => new Listener\Auth($this), 
                  'order' => 10),
			
            new Listener\Password($this),
            new Listener\Table($this),
                    new Listener\Systemusers($this),
        );
	}

	public function getModulerelations() {
		if(!($config_relations = $this->section(parent::MODULERELATION_CONFIG_SECTION))){
		  	   $config_relations=$this->_saveModuleConfig(array(), parent::MODULERELATION_CONFIG_SECTION);
		  };
		  	
		  return array_merge ($config_relations->toArray(),
               array( array(
                    'type' => \Sl_Modulerelation_Manager::RELATION_MANY_TO_MANY,
                    'db_table' => 'Sl\Module\Auth\Modulerelation\Table\Userroles',
                    'custom_configs' => true
                )
        ));

	}

	public function getCalculators() {
        return array(
            array(
                'sort_order' => 10,
                'calculator' => new Calculator\Userlogin(),
            ),
            array('calculator'=>new Calculator\Uniqueuserlogin()),
        );
	}

}
