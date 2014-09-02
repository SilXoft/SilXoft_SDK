<?php
namespace Sl\Module\Customers\Listener;

class Customerdealer extends \Sl_Listener_Abstract implements \Sl_Listener_Model_Interface {

	public function onAfterSave (\Sl_Event_Model $event) {
		$model = $event -> getModel();
		
		//error_reporting(E_ALL);
		
		if ($model instanceof \Sl\Module\Customers\Model\Customer) {
			$model_before_update = $event -> getModelBeforeUpdate();
			
			if ($model->getIsDealer() != $model_before_update->getIsDealer()){
				$dealer_model = \Sl_Model_Factory::object('\Sl\Module\Customers\Model\Dealer');	
				$relation = \Sl_Modulerelation_Manager::getRelations($model,'customerisdealer');
				if ($model->getIsDealer()){
					//якщо встановили галочку, створити дилера
					$model = \Sl_Model_Factory::mapper($model)->findRelation($model, $relation);
					if (!count($model ->fetchRelated($relation->getName()))){
						$dealer_model->setName($model->getName());
						$dealer_model->assignRelated($relation->getName(),array($model->getId()=>$model));
						\Sl_Model_Factory::mapper($dealer_model)->save($dealer_model);
					}
				} else {
					//якщо прибрали галочку, видалити дилера
					
					$model = \Sl_Model_Factory::mapper($model)->findRelation($model,$relation);
					if (count($model ->fetchRelated($relation->getName()))){
							
						$id = current(array_keys($model ->fetchRelated($relation->getName())));	
					
						
						$dealer_model=\Sl_Model_Factory::mapper($dealer_model)->find($id);
						
						\Sl_Model_Factory::mapper($dealer_model)->delete($dealer_model);
						
					}
					
					
				}
				
			}
			
		} elseif ($model instanceof \Sl\Module\Customers\Model\Dealer) {
			
		}
		 
	}

	public function onBeforeSave(\Sl_Event_Model $event) {
		

	}

}
