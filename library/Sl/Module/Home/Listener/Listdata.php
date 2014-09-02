<? 
namespace Sl\Module\Home\Listener;
class Listdata extends \Sl_Listener_Abstract implements \Sl\Listener\Dataset {
    
    
    public function onBeforeProcessAll(\Sl\Event\Dataset $event) {}
    
    public function onAfterProcessAll(\Sl\Event\Dataset $event) {}
    
    public function onBeforeProcessItem(\Sl\Event\Dataset $event){
        
    }

    public function onAfterProcessItem(\Sl\Event\Dataset $event){
        /*$model = $event->getModel();
        $fieldset = $event->getFieldset();
        $dataset = $event->getOption('dataset');
        $item = $event->getOption('item');
        $fieldname = 'id';
        $tr_key = \Sl\Serializer\Serializer::LISTVIEW_TR_ATTRIBUTES_KEY;
        if (array_key_exists($fieldname, $item)){
                    
            if(!isset($item[$fieldname][$tr_key])){
                $item[$fieldname]['attributesTR'] = array();
            }
            
            if (!isset($item[$fieldname][$tr_key]['data-controller'])){
                $item[$fieldname]['attributesTR']['data-controller'] = \Sl\Service\Helper::getModelAlias($model);
            }
            
            if (!isset($item[$fieldname][$tr_key]['data-real-id'])){
                $item[$fieldname]['attributesTR']['data-real-id'] = $item[$fieldname]['value'];
            }
            
            if (!isset($item[$fieldname][$tr_key]['data-id'])){
                $item[$fieldname]['attributesTR']['data-id'] = $item[$fieldname]['value'];
            }
            
            if (!isset($item[$fieldname][$tr_key]['data-editable'])){
                
                
                
                if (\Sl_Service_Acl::isAllowed(
                                \Sl_Service_Acl::joinResourceName(array(
                                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                    'module' => $model->findModuleName(),
                                    'controller' => $model->findModelName(),
                                    'action' => 'edit'
                                ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                            ))
                {
                    $item[$fieldname][$tr_key]['data-editable'] = 1;   
                } elseif(\Sl_Service_Acl::isAllowed(
                                \Sl_Service_Acl::joinResourceName(array(
                                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                    'module' => $model->findModuleName(),
                                    'controller' => $model->findModelName(),
                                    'action' => 'detailed'
                                ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                            )) 
                {
                  $item[$fieldname][$tr_key]['data-editable'] = 0;    
                } else {unset($item[$fieldname]['attributesTR']['data-editable']);}
                            
 
                
            }
             
            
            $options = $event->getOptions();
            $options['item'] = $item;
            $event->setOptions($options);
            
        } // data-real-id="784" data-id="784" id="data-id-784" data-editable="1" data-controller="customers.customer"
        
        */
    }
    
    public function onListPrepare(\Sl\Event\Fieldset $event){
       
    
    }
}    