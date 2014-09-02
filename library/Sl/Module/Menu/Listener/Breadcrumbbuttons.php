<?php
namespace Sl\Module\Menu\Listener;
use \Sl\View\Control as Button;
 
class Breadcrumbbuttons extends \Sl_Listener_Abstract implements \Sl\Module\Menu\Listener\Breadcrumbs, \Sl_Listener_View_Interface {

    const BUTTON_SAVE = 'bcbuttonssave';
    const BUTTON_SAVE_AND_RETURN = 'bcbuttonssaveandreturn';
    const BUTTON_SAVE_AND_SENDMAILER = 'bcbuttonssaveandsendmailer';
    const BUTTON_DELETE = 'bcbuttonsdelete';
    const BUTTON_BACK = 'bcbuttonsback';
    const BUTTON_RETURN_TO_EDIT = 'bcbuttonsreturntoedit';
    const BUTTON_DUPLICATE = 'bcbuttonsduplicate';
    const BUTTON_CREATE_DATE = 'bcbuttonscreatedate';
    const BUTTON_RECEIVE_DATE = 'bcbuttonsreceivedate';
    const BUTTON_GO_EDIT = 'bcbuttonsgoedit';
    const BUTTON_LOG = 'bcbuttonslog';
    const BUTTON_REFRESH = 'bcbuttonsrefresh';
    const BUTTON_ARCHIVE = 'bcbuttonsarchive';
    const BUTTON_EMAIL = 'bcbuttonsemail';
    const BUTTON_PRINT = 'bcprintbutton';
    const BUTTON_ARCHIVE_LIST = 'bcarchivelist';
    const BUTTON_CREATE = 'bcbuttonscreate';

    public function onBeforeBreadcrumbs(\Sl\Module\Menu\Event\Breadcrumbs $event) {
        $buttons = $event->getButtons();
        //error_reporting(E_ALL);
        if ($event->getRequest()) {
            if ($event->isIdBasedAction() || in_array($event->getCurrentAction(), array('create','nlist','filters')) ) {
                try {
                    $model = \Sl_Model_Factory::object($event->getCurrentController(), $event->getCurrentModule());
                } catch (\Exception $e) {
                    return;
                }

                if ($id = $event->getRequest()->getParam('id', 0))
                    $model = \Sl_Model_Factory::mapper($model)
                            ->find($event->getRequest()->getParam('id', 0));

                switch ($event->getCurrentAction()) {

                    case 'list':
                    case 'filters':
                    case 'nlist':
                        $buttons[] = $this->getCreateButton($event, $model);                       
                        $buttons[] = $this->getRefreshButton();
                        $buttons[] = $this->getListArchiveButton($event, $model);
                        $buttons[] = $this->getListExportButton($event, $model); 
                         if ($back_button = $this->getBackButton($event, $model)) {
                            $buttons[] = $back_button;
                        }
                        break;

                    case 'edit':

                        if ($email_button = $this->getEmailButton($event, $model)) {
                            $buttons[] = $email_button;
                        }
                        if ($log_button = $this->getLogButton($event, $model)) {
                            $buttons[] = $log_button;
                        }
                        if ($archive_button = $this->getArchiveButton($event, $model)) {
                            $buttons[] = $archive_button;
                        }
                        if ($print_button = $this->getPrintButton($event, $model)) {
                            $buttons[] = $print_button;
                        }
                        if ($model->isFinal()) {
                            if ($return2edit_button = $this->getReturnToEditButton($event, $model)) {
                                $buttons[] = $return2edit_button;
                            }
                        }
                        if ($duplicate_button = $this->getDuplicateButton($event, $model)) {
                            $buttons[] = $duplicate_button;
                        }
                        if (!$model->isFinal()) {
                            if ($save_button = $this->getSaveButton($event, $model)) {
                                $buttons[] = $save_button;
                            }
                            if ($saveandreturn_button = $this->getSaveAndReturnButton($event, $model)) {
                                $buttons[] = $saveandreturn_button;
                            }
                        }
                        if ($delete_button = $this->getDeleteButton($event, $model)) {
                            $buttons[] = $delete_button;
                        }
                        if ($sendmailer_button = $this->getSaveAndSendmailerButton($event, $model)) {
                            $buttons[] = $sendmailer_button;
                        }
                        if ($back_button = $this->getBackButton($event, $model)) {
                            $buttons[] = $back_button;
                        }
                        if ($model instanceof \Sl\Module\Logistic\Model\Package) {
                            if ($receivedate_button = $this->getReceiveDateButton($event, $model)) {
                                $buttons[] = $receivedate_button;
                            }
                        } else {
                            if ($createdate_button = $this->getCreateDateButton($event, $model)) {
                                $buttons[] = $createdate_button;
                            }
                        }
                        break;

                    case 'create':

                        if ($save_button = $this->getSaveButton($event, $model)) {
                            $buttons[] = $save_button;
                        }

                        if ($saveandreturn_button = $this->getSaveAndReturnButton($event, $model)) {
                                $buttons[] = $saveandreturn_button;
                            }

                        if ($back_button = $this->getBackButton($event, $model)) {
                            $buttons[] = $back_button;
                        }


                        if ($createdate_button = $this->getCreateDateButton($event, $model)) {
                            $buttons[] = $createdate_button;
                        }

                        break;

                    case 'detailed':
                        if ($log_button = $this->getLogButton($event, $model)) {
                            $buttons[] = $log_button;
                        }
                        if ($goedit_button = $this->getGoEditButton($event, $model)) {
                            $buttons[] = $goedit_button;
                        }
                        if ($print_button = $this->getPrintButton($event, $model)) {
                            $buttons[] = $print_button;
                        }
                        if ($duplicate_button = $this->getDuplicateButton($event, $model)) {
                            $buttons[] = $duplicate_button;
                        }
                        if ($delete_button = $this->getDeleteButton($event, $model)) {
                            $buttons[] = $delete_button;
                        }
                        if ($back_button = $this->getBackButton($event, $model)) {
                            $buttons[] = $back_button;
                        }
                        if ($createdate_button = $this->getCreateDateButton($event, $model)) {
                            $buttons[] = $createdate_button;
                        }
                        break;
                 /*   case 'nlist':
                        $buttons[] = $this->getGroupEditButton($event, $model);
                        break;
                   */
                }
            } else {

            }
        }
        $event->setButtons($buttons);
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

    }

    public function onFooter(\Sl_Event_View $event) {

    }

    public function onHeadLink(\Sl_Event_View $event) {
        $event->getView()->headLink()->appendStylesheet('/menu/buttons/bcbuttonscreatedate.css');
    }

    public function onHeadScript(\Sl_Event_View $event) {
        
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_SAVE . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_SAVE . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_DELETE . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_DELETE . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_BACK . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_BACK . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_SAVE_AND_RETURN . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_SAVE_AND_RETURN . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_RETURN_TO_EDIT . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_RETURN_TO_EDIT . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_DUPLICATE . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_DUPLICATE . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_CREATE_DATE . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_CREATE_DATE . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_RECEIVE_DATE . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_RECEIVE_DATE . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_GO_EDIT . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_GO_EDIT . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_LOG . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_LOG . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_ARCHIVE . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_ARCHIVE . '.js');
            }

            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_REFRESH . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_REFRESH . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_ARCHIVE_LIST . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_ARCHIVE_LIST . '.js');
            }

            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_PRINT . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_PRINT . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_EMAIL . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_EMAIL . '.js');
            }
            if ((!isset($event->getView()->deprecated_scripts))||(!in_array('/menu/buttons/' . self::BUTTON_CREATE . '.js', $event->getView()->deprecated_scripts))) {
                $event->getView()->headScript()->appendFile('/menu/buttons/' . self::BUTTON_CREATE . '.js');
            }            
    }
    
    public function onHeadTitle(\Sl_Event_View $event) {

    }

    public function onHeader(\Sl_Event_View $event) {

    }

    public function onLogo(\Sl_Event_View $event) {

    }

    public function onNav(\Sl_Event_View $event) {

    }

    public function onPageOptions(\Sl_Event_View $event) {

    }

    /**
     *
     * @param \Sl\Module\Menu\Event\Breadcrumbs $event
     * @param \Sl_Model_Abstract $model
     * @return Button\Lists|null
     */
    public function getPrintButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        if($event->inIframe()) {
            return null;
        }
        $print_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => \Sl\Service\Helper::PRINT_ACTION,
        ));
        \Sl_Service_Acl::setContext($model, 'form');
        if(\Sl_Service_Acl::isAllowed($print_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            $printforms = \Sl_Model_Factory::mapper('printform', \Sl_Module_Manager::getInstance()->getModule('home'))
                ->fetchAllByNameType(\Sl\Printer\Manager::type($model),'email');
            if(count($printforms)) {
                $list_button = new Button\Lists(array(
                    'icon_class' => 'print',
                    'title' => $this->getTranslator()->translate('Распечатать'),
                    'small' => true,
                    'drop_dir' => 'down',
                    'pull_right' => true,


                ));
                foreach($printforms as $printform) {
                    /*@var $printform \Sl\Module\Home\Model\Printform*/
                    if($printform->getRole() != $printform::STANDART_ROLE) {
                        continue;
                    }
                    $html_name = $model->findModuleName().'_'.$model->findModelName().'_pf_'.$printform->getId();
                    $list_button->addItem(new Button\Lists\Item(array(
                        'label' => $printform->getDescription(),
                        'href' => \Sl\Service\Helper::returnPrintUrl($model, $printform),
                        'html_name' => $html_name,
                        'class' =>'bcprintbutton'

                    )));
                }
                if(count($list_button->getItems())) {
                    return $list_button;
                }
            }
        }
        return null;
    }

    public function getEmailButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        if ($event->inIframe()) {
            return null;
        }
        try {
            $module = \Sl_Module_Manager::getInstance()-> getModule('customers');    
        } catch (\Exception $e) {
            return null;
        }
        
        
        
        $customer = \Sl_Model_Factory::object('customer', $module);
        $relations = \Sl_Modulerelation_Manager::getObjectsRelations($model, $customer);
        if (count($relations) == 1) {
            $relation = current($relations);
            //print_r($relations); die;
            if (!$model->issetRelated($relation->getName())) {
                $model = \Sl_Model_Factory::mapper($model)->findRelation($model, $relation);
            }
            if (count($model->fetchRelated($relation->getName()))) {
                $print_resource = \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => 'customers',
                            'controller' => 'main',
                            'action' => \Sl\Service\Helper::EMAIL_ACTION,
                ));
                \Sl_Service_Acl::setContext($model, 'form');
                if (\Sl_Service_Acl::isAllowed($print_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
                    $printforms = \Sl_Model_Factory::mapper('printform', \Sl_Module_Manager::getInstance()->getModule('home'))
                            ->fetchAllByNameType(\Sl\Printer\Manager::type($model), array(), 'email');
                    if (count($printforms)) {
                        $mail_button = new Button\Simple(array(
                            'icon_class' => 'envelope',
                            'small' => true,
                            'rel' => \Sl\Service\Helper::returnEmailFileUrl($model),
                            'pull_right' => true, 
                            'title' => $this->getTranslator()->translate('Отправить на почту'),
                            'class' => 'emailbutton',
                            
                           //   'rel' => \Sl\Service\Helper::returnEmailFileUrl($model),
                        ));
                        
                        return $mail_button;
                     /*   foreach ($printforms as $printform) {
                           
                            if ($printform->getRole() != $printform::STANDART_ROLE) {
                                continue;
                            }
                            $html_name = 'pef_' . $model->findModuleName() . '_' . $model->findModelName() . '_' . $printform->getId();
                            $list_button->addItem(new Button\Lists\Item(array(
                                'label' => $printform->getDescription(),
                                'href' => '#', //\Sl\Service\Helper::returnEmailFileUrl($model, $printform),
                                'rel' => \Sl\Service\Helper::returnEmailFileUrl($model, $printform),
                                'html_name' => $html_name,
                                'class' => 'emailbutton',
                            )));
                        }
                        if (count($list_button->getItems())) {
                            return $list_button;
                        }*/
                    }
                }
                return null;
            }
        }
    }


    /*
    public function getGroupEditButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model){
        $buttons = array();
        $group_actions_config = \Sl\Service\Groupactions::getGroupActions($model) -> toArray();

        if (count($group_actions_config)){
            $list_button = new Button\Lists(array(
                                'icon_class' => 'ok',
                                'title' => $this->getTranslator()->translate('Групповая обработка'),
                                'small' => true,
                                'drop_dir' => 'down',
                                'badge' => TRUE,
                                'badge_text' => '0',
                                'class'=>'groupbtn',
                            ));

            foreach ($group_actions_config  as $action => $conf_array){
               $resource = \Sl_Service_Acl::joinResourceName(array(
                    'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                    'module' => $event->getCurrentModule(),
                    'controller' => $event->getCurrentController(),
                    'action' => $action
                ));

                if(\Sl_Service_Acl::isAllowed($resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
                   $html_name = 'pef_' . $model->findModuleName() . '_' . $model->findModelName();
                   $list_button->addItem(new Button\Lists\Item(array(
                                    'label' =>  $this->getTranslator()->translate('title_action_'.$action),
                                    'href' => '#', //\Sl\Service\Helper::returnEmailFileUrl($model, $printform),

                                    'html_name' => $html_name,
                                    'class' => '',
                                )));
                }


            }
            if (count($list_button->getItems())) {
                $html_name = 'pef_select_all';
                $list_button->addItem(new Button\Lists\Item(array(
                                    'label' =>  $this->getTranslator()->translate('Выбрать все'),
                                    'href' => '#', //\Sl\Service\Helper::returnEmailFileUrl($model, $printform),

                                    'html_name' => $html_name,
                                    'class' => '',
                                )));
                $list_button->addItem(new Button\Lists\Item(array(
                                    'label' =>  $this->getTranslator()->translate('Очистить список'),
                                    'href' => '#', //\Sl\Service\Helper::returnEmailFileUrl($model, $printform),

                                    'html_name' => $html_name,
                                    'class' => '',
                                )));
                return $list_button;
            }


        }
    }
    */
    public function getReturnToEditButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        $returntoedit_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => \Sl\Service\Helper::RETURN_TO_EDIT_ACTION
        ));
        if(\Sl_Service_Acl::isAllowed($returntoedit_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            if($model->isFinal()) {
                return new Button\Simple(array(
                    'icon_class' => 'restart',
                    'small' => true,
                    'attribs' => array(
                        'data-rel' => \Sl\Service\Helper::returnToEditUrl($model),
                        'title' => $this->getTranslator()->translate('Вернуть для редактирования'),
                        'pull_right' => true,
                    ),
                    'id' => self::BUTTON_RETURN_TO_EDIT,
                ));
            }
        }
        return null;
    }

    public function getDuplicateButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        if($event->inIframe()) {
            return null;
        }
        $duplicate_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => \Sl\Service\Helper::DUPLICATE_ACTION
        ));
        if(\Sl_Service_Acl::isAllowed($duplicate_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            return new Button\Simple(array(
                'icon_class' => 'more-windows',
                'small' => true,
                'attribs' => array(
                    'data-rel' => \Sl\Service\Helper::duplicateUrl($model),
                    'title' => $this->getTranslator()->translate('Дублировать'),
                    'pull_right' => true,
                ),
                'id' => self::BUTTON_DUPLICATE,
            ));
        }
        return null;
    }

    public function getDeleteButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        if($event->inIframe()) {
            return null;
        }
        $delete_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => \Sl\Service\Helper::AJAX_DELETE_ACTION,
        ));
        \Sl_Service_Acl::setContext($model, 'form');
        if(\Sl_Service_Acl::isAllowed($delete_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            return new Button\Simple(array(
                'icon_class' => 'trash',
                'small' => true,
                'pull_right' => true,
                'attribs' => array(
                    'data-rel' => \Sl\Service\Helper::deleteUrl($model),
                    'title' => $this->getTranslator()->translate('Удалить'),
                    'data-list-rel' => \Sl\Service\Helper::listUrl($model),
                ),
                'id' => self::BUTTON_DELETE,
            ));
        }
        return null;
    }

    public function getBackButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        if($event->inIframe()) {
            return null;
        }
        $back_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => \Sl\Service\Helper::LIST_ACTION,
        ));
        \Sl_Service_Acl::setContext($model, 'form');
        if(\Sl_Service_Acl::isAllowed($back_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            return new Button\Simple(array(
                'icon_class' => 'arrow-left',
                'small' => true,
                'pull_right' => true,
                'attribs' => array(
                    'locker_resource' => get_class($model).':'.$model->getId(),
                    'title' => $this->getTranslator()->translate('Назад'),
                    'data-rel' => \Sl\Service\Helper::listUrl($model),
                ),
                //'onClick' => "history.go(-1); return false;",
                'id' => self::BUTTON_BACK,
            ));
        }
        return null;
    }

    public function getCreateDateButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        $method_name = \Sl_Model_Abstract::buildMethodName('create', 'get');
        if(method_exists($model, $method_name)) {
            $field_resource = \Sl_Service_Acl::joinResourceName(array(
                'type' => \Sl_Service_Acl::RES_TYPE_OBJ,
                'module' => $event->getCurrentModule(),
                'name' => $event->getCurrentController(),
                'field' => 'create',
            ));
            $read_priv = \Sl_Service_Acl::isAllowed($field_resource, \Sl_Service_Acl::PRIVELEGE_READ);
            $edit_priv = \Sl_Service_Acl::isAllowed($field_resource, \Sl_Service_Acl::PRIVELEGE_UPDATE);
            if($read_priv) {
                return new Button\Modelfield(array(
                    'small' => true,
                    'label' => $this->getTranslator()->translate('Дата создания'),
                    'id' => self::BUTTON_CREATE_DATE,
                    'field' => 'create',
                    'pull_right' => true,
                    'privs' => array(
                        'read' => $read_priv,
                        'update' => $edit_priv,
                    ),
                ));
            }
            return null;
        }
        return null;
    }

    public function getReceiveDateButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        $method_name = \Sl_Model_Abstract::buildMethodName('create', 'get');
        if(method_exists($model, $method_name)) {
            $field_resource = \Sl_Service_Acl::joinResourceName(array(
                'type' => \Sl_Service_Acl::RES_TYPE_OBJ,
                'module' => $event->getCurrentModule(),
                'name' => $event->getCurrentController(),
                'field' => 'receive_date',
            ));
            $read_priv = \Sl_Service_Acl::isAllowed($field_resource, \Sl_Service_Acl::PRIVELEGE_READ);
            $edit_priv = \Sl_Service_Acl::isAllowed($field_resource, \Sl_Service_Acl::PRIVELEGE_UPDATE);
            if($read_priv) {
                return new Button\Modelfield(array(
                    'small' => true,
                    'label' => $this->getTranslator()->translate('Дата принятия'),
                    'id' => self::BUTTON_RECEIVE_DATE,
                    'field' => 'receive_date',
                    'pull_right' => true,
                    'privs' => array(
                        'read' => $read_priv,
                        'update' => $edit_priv,
                    ),
                ));
            }
            return null;
        }
        return null;
    }

    public function getSaveButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        $edit_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => 'edit',
        ));
        \Sl_Service_Acl::setContext($model, 'form');
        if(\Sl_Service_Acl::isAllowed($edit_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            return new Button\Simple(array(
                'icon_class' => 'floppy-save',
                'small' => true,
                'pull_right' => true,
                'attribs' => array(
                    'locker_resource' => get_class($model).':'.$model->getId(),
                    'title' => $this->getTranslator()->translate('Сохранить'),
                    'validate_action' => \Sl\Service\Helper::ajaxValidateUrl($model),
                ),
                'id' => self::BUTTON_SAVE,
            ));
        }
        return null;
    }

    public function getSaveAndReturnButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        if($event->inIframe()) {
            return null;
        }
        $edit_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => 'edit',
        ));
        \Sl_Service_Acl::setContext($model, 'form');
        if(\Sl_Service_Acl::isAllowed($edit_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            return new Button\Simple(array(
                'icon_class' => 'floppy-disk',
                'small' => true,
                'pull_right' => true,
                'attribs' => array(
                    'data-rel' => \Sl\Service\Helper::modelEditViewUrl($model, true),
                    'title' => $this->getTranslator()->translate('Сохранить и остаться'),
                ),
                'id' => self::BUTTON_SAVE_AND_RETURN,
            ));
        }
        return null;
    }

        public function getSaveAndSendmailerButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        if($event->inIframe()) {
            return null;
        }
        $sendmailer_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => 'sendmailer',
        ));
        $edit_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => 'edit',
        ));
        \Sl_Service_Acl::setContext($model, 'form');
        if(\Sl_Service_Acl::isAllowed($edit_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS) &&
                \Sl_Service_Acl::isAllowed($sendmailer_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {

            return new Button\Simple(array(
                'icon_class' => 'message-out',
                'small' => true,
                'attribs' => array(
                    'data-rel' => '/'.$model->findModuleName().'/'.$model->findModelName().'/sendmailer/id/'.$model->getId(),
                    'title' => $this->getTranslator()->translate('Отправить'),
                ),
                'id' => self::BUTTON_SAVE_AND_SENDMAILER,
            ));
        }
        return null;
    }

    public function getGoEditButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        $edit_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => 'edit',
        ));
        \Sl_Service_Acl::setContext($model, 'form');
        if(\Sl_Service_Acl::isAllowed($edit_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            return new Button\Simple(array(
                'icon_class' => 'edit',
                'small' => true,
                'attribs' => array(
                    'data-rel' => \Sl\Service\Helper::modelEditViewUrl($model),
                    'title' => $this->getTranslator()->translate('Редактировать'),
                ),
                'id' => self::BUTTON_GO_EDIT,
            ));
        }
        return null;
    }
    // Для listView - без перевірки прав
    public function getRefreshButton(){
         return new Button\Simple(array(
                'icon_class' => 'refresh',
                'small' => true,
                'pull_right' => true,
                'attribs' => array(
                    'title' => $this->getTranslator()->translate('Обновить'),
                ),
                'id' => self::BUTTON_REFRESH,
            ));
    }

    public function getLogButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        if($event->inIframe()) {
            return null;
        }
        $log_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => 'log',
        ));
        \Sl_Service_Acl::setContext($model, 'form');
        if(\Sl_Service_Acl::isAllowed($log_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            return new Button\Simple(array(
                'icon_class' => 'list',
                'small' => true,
                'pull_right' => true,
                'attribs' => array(
                    'data-rel' => \Sl\Service\Helper::logUrl($model),
                    'title' => $this->getTranslator()->translate('История изменений'),
                ),
                'id' => self::BUTTON_LOG,
            ));
        }
        return null;
    }

    public function getArchiveButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        $archive_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => 'ajaxarchive',
        ));
        \Sl_Service_Acl::setContext($model, 'form');
        if(\Sl_Service_Acl::isAllowed($archive_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            $title = $model->getArchived()?'Извлечь из архива':'Архивировать';
            return new Button\Simple(array(
                'icon_class' => 'inbox',
                'small' => true,
                'pull_right' => true,
                'attribs' => array(
                    'data-rel' => \Sl\Service\Helper::ajaxarchiveUrl($model),
                    'data-list-rel' => \Sl\Service\Helper::listUrl($model),
                    'title' => $this->getTranslator()->translate($title),
                    'data-archived' => (int) $model->getArchived(),
                ),
                'id' => self::BUTTON_ARCHIVE,
            ));
        }
    }
    
   public function getListArchiveButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        $archive_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => 'ajaxarchive',
        ));

        if(\Sl_Service_Acl::isAllowed($archive_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            $archive_button = new Button\Lists(array(
                'icon_class' =>  "inbox",
                'small' => true,
                'class' => "switch_archived",
                'pull_right' => true,
                'drop_dir' => 'down',
                'id' => self::BUTTON_ARCHIVE_LIST,
                'title' => $this->getTranslator()->translate('Архив'),
            ));
            $archive_button->addItem(new Button\Lists\Item(array(
                        'label' => $this->getTranslator()->translate('Все, кроме архивных'),
                        'class' =>"archived_switcher",
                        'href' => '#',
                        'data' => array(
                            'value' => '-1',
                            'alias' => \Sl\Service\Helper::getModelAlias($model),
                        ),
                    )));
            $archive_button->addItem(new Button\Lists\Item(array(
                        'label' => $this->getTranslator()->translate('Только архивные'),
                        'class' =>"archived_switcher",
                        'href' => '#',
                        'data' => array(
                            'value' => '1',
                            'alias' => \Sl\Service\Helper::getModelAlias($model),
                        ),

                    )));
            $archive_button->addItem(new Button\Lists\Item(array(
                        'label' => $this->getTranslator()->translate('Все'),
                        'class' =>"archived_switcher",
                        'href' => '#',
                        'data' => array(
                            'value' => '0',
                            'alias' => \Sl\Service\Helper::getModelAlias($model),
                        ),
                    )));
          return $archive_button;
        }
    }
    
    public function getListExportButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        $export_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => 'export',
        ));
         if(\Sl_Service_Acl::isAllowed( $export_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
                $export_button = new Button\Lists(array(
                    'icon_class' =>  "download-alt",
                    'small' => true,
                    'pull_right' => true,
                    'drop_dir' => 'down',
                    'title' => $this->getTranslator()->translate('Экспорт'),
                ));
                $export_button->addItem(
                    new Button\Lists\Item(array(
                        'label' => $this->getTranslator()->translate('Отфильтрованные'),
                        'class' => 'export',
                        'href' => '#',
                        'data' => array(
                            'alias' => \Sl\Service\Helper::getModelAlias($model),
                        ),
                )));
                $export_button->addItem(
                    new Button\Lists\Item(array(
                        'label' => $this->getTranslator()->translate('Только видимые'),
                        'class' => 'export_page',
                        'data' => array(
                            'alias' => \Sl\Service\Helper::getModelAlias($model),
                        ),
                        'href' => '#',
                )));
            return $export_button;
        }
    }
    public function getCreateButton(\Sl\Module\Menu\Event\Breadcrumbs $event, \Sl_Model_Abstract $model) {
        $archive_resource = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
            'module' => $event->getCurrentModule(),
            'controller' => $event->getCurrentController(),
            'action' => 'create',
        ));
        \Sl_Service_Acl::setContext($model, 'form');
        if(\Sl_Service_Acl::isAllowed($archive_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS)) {
            $title = 'Создать';
            return new Button\Simple(array(
                'icon_class' => 'plus',
                'small' => true,
                'pull_right' => true,
                'attribs' => array(
                    'data-rel' => \Sl\Service\Helper::buildModelUrl($model, 'create'),
                    'title' => $this->getTranslator()->translate($title),
                ),
                'id' => self::BUTTON_CREATE,
            ));
        }
    }    
}