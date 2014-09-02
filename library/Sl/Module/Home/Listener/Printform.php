<?php
namespace Sl\Module\Home\Listener;

class Printform extends \Sl_Listener_Abstract implements \Sl\Listener\Modelaction, 
                                                          \Sl_Listener_View_Interface,
                                                           \Sl_Listener_Router_Interface{
    protected $_view_script;
    protected $_view_help_action;
    public function onAfter(\Sl\Event\Modelaction $event) {
        
    }

    public function onAfterPost(\Sl\Event\Modelaction $event) {
        
    }

    public function onBefore(\Sl\Event\Modelaction $event) {
        $model = $event->getModel();
        if($model instanceof \Sl\Module\Home\Model\Printform) {
            if(in_array($event->getCurrentAction(), array('create', 'edit', 'detailed'))){
                $this->_view_script = true;
               
            }
            if(in_array($event->getCurrentAction(), array('create', 'edit'))){
             $field_resource_helper = \Sl_Service_Acl::joinResourceName(array(
				'type' => \Sl_Service_Acl::RES_TYPE_MVC,
				'module' =>'home',
				'controller' => 'printform',
				'action' => 'ajaxprintformhelp'
			));
           
          $priv_access_helper = \Sl_Service_Acl::isAllowed($field_resource_helper, \Sl_Service_Acl::PRIVELEGE_READ);
              if ($priv_access_helper){ 
              $this->_view_help_action = true;
          }  
               
            }
           
        
            if(in_array($event->getCurrentAction(), array('create', 'edit', 'ajaxvalidate', 'detailed'))) {
                $form = $event->getView()->form;
                if($form && ($form instanceof \Sl\Form\Form)) {
                   
                    $available_models = \Sl_Module_Manager::getAvailableModels();
                    $select_models = array();
                    foreach($available_models as $module_name=>$models) {
                        $module = \Sl_Module_Manager::getInstance()->getModule($module_name);
                        
                        foreach($models as $model) {
                            $tmpO = \Sl_Model_Factory::object($model, $module);
                            
                            $select_models[$event->getView()->translate('title_module_'.$module_name)][\Sl\Printer\Manager::type($tmpO)]
                                                = $event->getView()->translate('title_'.$model.'_'.$module_name);
                        }
                    }
                    $form->getElement('name')->setMultiOptions($select_models);
                }
            }
        }
    }

    public function onBeforePost(\Sl\Event\Modelaction $event) {
        //error_reporting(E_ALL);
        
        $model = $event->getModel();
       
        if($model instanceof \Sl\Module\Home\Model\Printform) {
            if(in_array($event->getCurrentAction(), array('create', 'edit', 'ajaxvalidate', 'detailed'))) {
                $form = $event->getView()->form;// $destination = 
                if($form && ($form instanceof \Sl\Form\Form)) {
                  $el = $form->getElement('modulrelation_printformfile');
                   if ($el && in_array($event->getRequest()->getParam('type',''),array('email','application/text'))){
                       $el->setRequired(false); 
                   }
                }
            }
                }
        
    }

    public function onAfterContent(\Sl_Event_View $event) {
           
    }

    public function onBeforeContent(\Sl_Event_View $event) {
        
    }

    public function onBeforePageHeader(\Sl_Event_View $event) {
        
    }

    public function onBodyBegin(\Sl_Event_View $event) {
        
    }

    public function onBodyEnd(\Sl_Event_View $event) {
        
 
    }

    public function onContent(\Sl_Event_View $event) {
        $view = $event->getView(); 
        if ($this->_view_help_action)
        {?>
         <script>
             var printform_help_action = true;
         </script>   
       <? }
        if ($this->_view_script){
            ?>
<script> 
    var printform_type_toview = {
        
         hide : { 
             email: ['modulerelation_printformfile_list', 'email'],
            'application/text':   ['modulerelation_printformfile_list','modulerelation_attachmentprintform_btn'],
            'application/vnd.ms-excel':   ['modulerelation_attachmentprintform_btn'],
            'application/pdf':   ['modulerelation_attachmentprintform_btn'],
            'application/Html':  ['modulerelation_attachmentprintform_btn','modulerelation_printformfile_list','email']
             
        },
        label:{
            mask:{
              email: '<?=$view->translate('Тема письма')?>',
               
                      
            }
        }
            
        }
    
</script>
                <? 
        }
     
   }    
             
    

    public function onFooter(\Sl_Event_View $event) {
        
    }

    public function onHeadLink(\Sl_Event_View $event) {
        
    }

    public function onHeadScript(\Sl_Event_View $event) {
       
 
    
    }

    public function onHeadTitle(\Sl_Event_View $event) {
          $view = $event->getView();
        if ($this->_view_script){
           
            $view->headScript()->appendFile('/home/printform/printformview.js');
            
        }
        
    }

    public function onHeader(\Sl_Event_View $event) {
       
    }

    public function onLogo(\Sl_Event_View $event) {
        
    }

    public function onNav(\Sl_Event_View $event) {
        
    }

    public function onPageOptions(\Sl_Event_View $event) {
        
    }

    public function onAfterInit(\Sl_Event_Router $event) {
        
    }

    public function onBeforeInit(\Sl_Event_Router $event) {
        
    }

    public function onDispatchLoopShutdown(\Sl_Event_Router $event) {
        
    }

    public function onDispatchLoopStartup(\Sl_Event_Router $event) {
        
    }

    public function onGetRequest(\Sl_Event_Router $event) {
        
    }

    public function onGetResponse(\Sl_Event_Router $event) {
        
    }

    public function onPostDispatch(\Sl_Event_Router $event) {
     
       $controller = $event->getRequest()->getParam('controller'); 
       $module = $event->getRequest()->getParam('module'); 
       $action = $event->getRequest()->getParam('action'); 
       if((in_array($action, array('create', 'edit', 'detailed'))&&($module=='home')&&($controller='printform'))){
                $this->_view_script = true;
               
            }
        
    }

    public function onPreDispatch(\Sl_Event_Router $event) {
        
    }

    public function onRouteShutdown(\Sl_Event_Router $event) {
        
    }

    public function onRouteStartup(\Sl_Event_Router $event) {
        
    }

    public function onSetRequest(\Sl_Event_Router $event) {
        
    }

    public function onSetResponse(\Sl_Event_Router $event) {
        
    }
}