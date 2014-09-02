<?php

class Dashboard_MenuController extends Zend_Controller_Action
{
 protected $_menu;

    public function init() {


        /*
         * Меня по-умолчанию
         */
        $pages_array = array();
        $this->_menu = new Zend_Navigation($pages_array);

   //     $acl = Zend_Registry::isRegistered('acl')?Zend_Registry::get('acl'):null;
   //     $this->view->acl = $acl;  
    


        $this->view->menu = $this->_menu;
    }

    public function indexAction() {
        $this->view->menu = $this->_menu;
    }

    /**
     * Основное меню
     */
    public function sidenavAction() {

 $translate= Zend_Registry::isRegistered('Zend_Translate')?Zend_Registry::get('Zend_Translate'):null; 
          $pages = array(
            array(
                'module' => 'default',
                'controller'    => 'index',
                'action'    => 'index',
                'class'         => 'i_house',
                'label'         => $translate->_('Главная'),
                'resource'      => 'default',
                'privilege'     => 'index',
            ),
             array(
                'module' => 'users',
                'controller'    => 'index',
                'action'    => 'index',
                'label'         => $translate->_('Пользователи'),
                'resource'      => 'index',
                'privilege'     => 'index',
            ),           
             array(
                'type'          => 'uri',
                'href'          => '#',
                'label'         => $translate->_('Управление'),
                'class'         => 'i_book',
                'resource'      => 'index',
                'privilege'     => 'index',
                'pages' => array (
                                 array (
                                     'module'        => 'dashboard',
                                     'controller'    => 'role',
                                     'action'        => 'read',
                                     'label'         => $translate->_('Роли'),
                                 
                                                                  ),
                                 array (
                                     'module'        => 'dashboard',
                                     'controller'    => 'allow',
                                     'action'        => 'read',
                                     'label'         => $translate->_('Права')
                                  ),
                
                
                
            )), 
          array (
                'module'        => 'dashboard',
                'controller'    => 'role',
                'action'        => 'read',
                'label'         => $translate->_('Роли'),
                                 
                                                                  ),
          array (
                'module'        => 'dashboard',
                'controller'    => 'allow',
                'action'        => 'read',
                'label'         => $translate->_('Права')
                     ),                                                                 
                                                                              
        );
        $this->menu = new Zend_Navigation($pages);

        $this->view->menu = $this->menu;
    }

    /**
     * Подменю
     */

    public function secnavAction() {

        $pages_array = array();

        $this->view->menu = new Zend_Navigation($pages_array);;
    }

    /**
     * Меню пользователя
     */
    public function usernavAction() {
        $pages = array(
            array(
                'controller'    => 'auth',
                'action' => 'logout',
                'label'         => 'Выйти',
            ),
        );

        $menu = new Zend_Navigation($pages);

        $this->view->menu = $menu;
    }


}

