<?php
namespace Sl\Module\Home\Calculator;

class Acntidentity extends \Sl\Calculator\Identitycalculator {

	protected $_model_name = 'Sl\\Module\\Home\\Model\\Identity\\Acnt';
    protected $_blank_model;                         
	protected $_aggregated_fields = array('model_name' => array('name'), );
    
    

	public function calculate($Obj) {
	    
		if ($Obj['id'] > 0){
    		
            $acnt = \Sl_Model_Factory::mapper($this->_getModel()) -> find($Obj['id']);
            $master_obj = current($acnt -> fetchRelated($acnt->getMasterRelation()));
            if ($master_obj instanceof \Sl_Model_Abstract){
                $title = implode('_',array('title',$master_obj->findModelName(),$master_obj->findModuleName()));
                $title = implode(' ',array(self::getTranslator()->translate($title), $master_obj->__toString()));
                $this->setRowController($master_obj->findModelName());
                $this->setRowModule($master_obj->findModuleName());
                $Obj['id'] = $master_obj->getId();                
            } else {
                $title = '-----';
            }
            
            $Obj['model_name'] = $title; 
        }

		return $Obj;

	}
    
    protected function _getModel(){
        if (!$this->_blank_model){
            $this->_blank_model = \Sl_Model_Factory::object('Sl\Module\Home\Model\Identity\Acnt');
        }    
        return $this->_blank_model;
    }
}
