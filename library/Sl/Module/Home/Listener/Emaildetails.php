<?php
namespace Sl\Module\Home\Listener;

class Emaildetails extends \Sl_Listener_Abstract implements \Sl_Listener_Model_Interface {

    public function onAfterSave(\Sl_Event_Model $event) {

        $model = $event->getOption('model');
        if (!($model instanceof \Sl\Module\Home\Model\Emaildetails)) {

            $email_obj = \Sl_Model_Factory::object('\Sl\Module\Home\Model\Email');
            $relation = \Sl_Modulerelation_Manager::getObjectsRelations($model, $email_obj);
            $emailemaildetails_rel = \Sl_Modulerelation_Manager::getRelations($email_obj, 'emailemaildetails');
            if (! ($emailemaildetails_rel instanceof \Sl\Modulerelation\Modulerelation))
                throw new \Exception ('There is no emailemaildetails relation');
           
            /*
            
            $form_name = strtolower('model_' . $email_obj->findModelName() . '_form');
            $form_options = \Sl_Module_Manager::getInstance()->getCustomConfig($email_obj->findModuleName(), 'forms', $form_name);
            $email_rel = $form_options->toArray();
            */
            if ($relation) {

                foreach ($relation as $rel_key => $rel) {

                    if (!$model->issetRelated($rel->getName())) {
                        $model = \Sl_Model_Factory::mapper($model)->findRelation($model, $rel);
                    }

                    $options = $emailemaildetails_rel->getOption($rel_key) ;

                    $emal_obj_arr = $model->fetchRelated($rel_key);

                    if ($options) {
                        if (count($emal_obj_arr) > 0) {
                            
                            foreach ($emal_obj_arr as $email_obj_val) {
                                if (!$email_obj_val->issetRelated('emailemaildetails')) {
                                    $email_obj_val = \Sl_Model_Factory::mapper($email_obj_val)->findRelation($email_obj_val, 'emailemaildetails');
                                }

                                if ($email_obj_val->fetchRelated('emailemaildetails')) {
                                    
                                    $emEmId = current($email_obj_val->fetchRelated('emailemaildetails'));

                                    foreach ($options as $set_val => $get_val) {
                                        $emEmId->{$emEmId->buildMethodName($set_val, 'set')}($this->getDataForSet($model,$get_val));
                                    }

                                    $emEmId->assignRelated('emailemaildetails', array($email_obj_val));
                                    try {
                                        \Sl_Model_Factory::mapper($emEmId)->save($emEmId, false, false);
                                    } catch (\Exception $e) {
                                        
                                    }
                                } else {

                                    $emaildetails_obj = \Sl_Model_Factory::object('\Sl\Module\Home\Model\Emaildetails');

                                    foreach ($options as $set_val => $get_val) {
                                        $emaildetails_obj->{$emaildetails_obj->buildMethodName($set_val, 'set')}($this->getDataForSet($model,$get_val));
                                    }

                                    $emaildetails_obj->assignRelated('emailemaildetails', array($email_obj_val));

                                    try {
                                        \Sl_Model_Factory::mapper($emaildetails_obj)->save($emaildetails_obj, false, false);
                                    } catch (\Exception $e) {
                                        
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function onBeforeSave(\Sl_Event_Model $event) {
        
    }
    
    public static function getDataForSet($object, $field_name)
            {
                $field = explode('.', $field_name);
               
                if(count($field)==1)
                    {
                    return $object->{$object->buildMethodName($field[0], 'get')}();
                    }
                 if(count($field)==2)
                     {
                         if (!$object->issetRelated($field[0])) {
                                $object = \Sl_Model_Factory::mapper($object)->findRelation($object, $field[0]);
                            }
                            $rel_obj_arr = $object->fetchRelated($field[0]);
                            
                            foreach ($rel_obj_arr as $ob)
                                {
                                    $rez[] = $ob->{$ob->buildMethodName($field[1], 'get')}();
                                }
                        
                            return implode(', ', $rez);
                     }  
                     
                     return '';
            }

}
