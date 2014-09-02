<?php
namespace Sl\Module\Menu;

class Module extends \Sl_Module_Abstract {
    
    public function getListeners() {
        return array(
            new Listener\Menu($this),
            new Listener\Context($this),
            array('listener' => new Listener\Breadcrumbbuttons($this), 
                  'order' => 1000),                  
            new Listener\EditPage($this),
        );
    }
	
	public function getModulerelations() {
		if (!($config_relations = $this -> section(parent::MODULERELATION_CONFIG_SECTION))) {
			$config_relations = $this -> _saveModuleConfig(array(), parent::MODULERELATION_CONFIG_SECTION);
		};

		return array_merge($config_relations -> toArray(), array(
            
        ));
	} 
	
	public function getCalculators (){
		
	}
	
}
