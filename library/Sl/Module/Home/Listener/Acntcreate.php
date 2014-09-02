<? namespace Sl\Module\Home\Listener;
use Sl\Module\Home\Model as Models;
class Acntcreate extends \Sl_Listener_Abstract implements \Sl_Listener_Model_Interface
{
    public function onAfterSave(\Sl_Event_Model $event){
        $model = $event -> getModel();
        if (!($model instanceof Models\Acnt) ){
            $acnt = \Sl_Model_Factory::object('Sl\Module\Home\Model\Acnt');
            $relations = \Sl_Modulerelation_Manager::getObjectsRelations($model,$acnt,array(\Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE),'main');
            if (count($relations)){
                if (count($relations) > 1){
                    throw new \Exception ($model->findModelName().' has more than 1 relation to '.$acnt->findModelName());
                }
                $relation = current($relations);
                if (!$model -> issetRelated($relation->getName())){
                    $model = \Sl_Model_Factory::mapper($model)->findRelation($model, $relation);
                    
                }
                
                $acnts_arr = $model -> fetchRelated($relation->getName());
                
                if (!count($acnts_arr)){
                    $acnt->assignRelated($relation->getName(), array($model->getId()=>$model));
                    $acnt->setMasterRelation($relation->getName());
                    \Sl_Model_Factory::mapper($acnt)->save($acnt); 
                } else{
                  
                }                 
            }
        }
        
        
        
    }       
    public function onBeforeSave(\Sl_Event_Model $event){}
                                                     
}