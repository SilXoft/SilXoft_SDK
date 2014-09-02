<?php
namespace Sl\Module\Customers\Listener;

class Customercontact extends \Sl_Listener_Abstract implements \Sl_Listener_Model_Interface, \Sl\Listener\Action\Before\Edit {
    public function onAfterSave(\Sl_Event_Model $event) {
        
   /*       $model = $event -> getModel();
        if ($model instanceof \Sl\Module\Customers\Model\Contact) {
            
          $relation_email = \Sl_Modulerelation_Manager::getRelations($model,'contactemail');
          $relation_phone = \Sl_Modulerelation_Manager::getRelations($model,'contactphone');
          $model = \Sl_Model_Factory::mapper($model)->findRelation($model, $relation_email);
          $model = \Sl_Model_Factory::mapper($model)->findRelation($model, $relation_phone);
          
          $mail_obj = current($model->fetchRelated('contactemail'));
          $phone_obj = current($model->fetchRelated('contactphone'));
         
          $model->setBriefDescription($model->getName().' '.$phone_obj->getPhone().' '.$mail_obj->getMail());
           \Sl_Model_Factory::mapper($model)->save($model, FALSE, FALSE);
            
            
        }
     */   
    }

    public function onBeforeSave(\Sl_Event_Model $event) {
        
    }

    public function onBeforeEditAction(\Sl_Event_Action $event) {        
                
        $model = $event -> getModel();
        if ($model instanceof \Sl\Module\Customers\Model\Customer) {
            
        $available_relations = \Sl_Model_Factory::mapper($model, $model->findModuleName())->getAllowedRelations();
        
        $object = \Sl_Model_Factory::mapper($model, $model->findModuleName())->findExtended($model->getId(), $available_relations);
        $relations = $object->findFilledRelations();

        foreach ($relations as $value){

            $items = $object->fetchRelated($value); 
            
                foreach ($items as $obj){
                    
                    $obj = \Sl_Model_Factory::mapper($obj)->findAllowExtended($obj->getId());                   
                    $item_owner[$value] =$items;
                    
                }                
        }
        foreach($item_owner as $rel=> $rel_arr)
            $model->assignRelated($rel, $rel_arr);
        }
        
    }    
}