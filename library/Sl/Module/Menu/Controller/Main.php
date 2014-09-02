<?php

namespace Sl\Module\Menu\Controller;

class Main extends \Sl_Controller_Action {

    protected $_menu;

    public function init() {

        /*
         * Меня по-умолчанию
         */
        $pages = array(
 
            array(
                'module' => 'Itftc',
                'controller' => 'Application',
                'action' => 'list',
                'icon' => 'th-large',
                'label' => 'Заявки',
                'type' => 'uri',
                'href' => '#',
                'pages' => array(
                    array(
                        'module' => 'itftc',
                        'controller' => 'application',
                        'action' => 'filters',
                        'label' => 'Заявки',
                        'resource' => 'mvc:itftc|application|filters',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    /*
                    array(
                        'module' => 'itftc',
                        'controller' => 'appconnect',
                        'action' => 'create',
                        'label' => 'Заявка на подк.',
                        'resource' => 'mvc:itftc|appconnect|create',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'itftc',
                        'controller' => 'apppause',
                        'action' => 'create',
                        'label' => 'Заявка приостановление',
                        'resource' => 'mvc:itftc|apppause|create',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),   
                    */
                    ),
                ),            
            array(
                'module' => 'Itftc',
                'controller' => 'Subscriber',
                'action' => 'list',
                'icon' => 'th-large',
                'label' => 'ITFTC',
                'type' => 'uri',
                'href' => '#',
                'pages' => array(
                    array(
                        'module' => 'itftc',
                        'controller' => 'subscriber',
                        'action' => 'list',
                        'label' => 'Абонент',
                        'resource' => 'mvc:itftc|subscriber|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'itftc',
                        'controller' => 'partner',
                        'action' => 'list',
                        'label' => 'Партнер',
                        'resource' => 'mvc:itftc|partner|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),  
                    /*
                    array(
                        'module' => 'accounts',
                        'controller' => 'account',
                        'action' => 'list',
                        'label' => 'Контрагент',
                        'resource' => 'mvc:accounts|account|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                     * 
                     */
                    array(
                        'module' => 'itftc',
                        'controller' => 'subscription',
                        'action' => 'list',
                        'label' => 'Подписка',
                        'resource' => 'mvc:itftc|subscription|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'customers',
                        'controller' => 'contact',
                        'action' => 'list',
                        'label' => 'Контакты',
                        'resource' => 'mvc:customers|contact|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),                    
                    ),
                ),
                array(
                'module' => 'dictionary',
                'controller' => 'main',
                'action' => 'list',
                'label' => 'Словари',
                'icon' => 'book',
                'type' => 'uri',
                'href' => '#',
                'resource' => '',
                'pages' => array(
                    array(
                        'module' => 'dictionary',
                        'controller' => 'area',
                        'action' => 'list',
                        'label' => 'Области',
                        'resource' => 'mvc:dictionary|area|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'itftc',
                        'controller' => 'tariffprovider',
                        'action' => 'list',
                        'label' => 'Тарифный план',
                        'resource' => 'mvc:itftc|tariffprovider|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'dictionary',
                        'controller' => 'typeactivity',
                        'action' => 'list',
                        'label' => 'Тип деятельности',
                        'resource' => 'mvc:dictionary|typeactivity|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),        
                    array(
                        'module' => 'dictionary',
                        'controller' => 'attendance',
                        'action' => 'list',
                        'label' => 'Послуги',
                        'resource' => 'mvc:dictionary|attendance|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),  
                    array(
                        'module' => 'dictionary',
                        'controller' => 'termscooperation',
                        'action' => 'list',
                        'label' => 'Условия сотрудничества',
                        'resource' => 'mvc:dictionary|termscooperation|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),   
                    array(
                        'module' => 'dictionary',
                        'controller' => 'share',
                        'action' => 'list',
                        'label' => 'Акции',
                        'resource' => 'mvc:dictionary|share|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'dictionary',
                        'controller' => 'satisfaction',
                        'action' => 'list',
                        'label' => 'Удовлетворение',
                        'resource' => 'mvc:dictionary|satisfaction|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),                    
                    array(
                        'module' => 'itftc',
                        'controller' => 'equipment',
                        'action' => 'list',
                        'label' => 'Обородувание',
                        'resource' => 'mvc:itftc|equipment|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),                    
                    array(
                        'module' => 'itftc',
                        'controller' => 'faq',
                        'action' => 'list',
                        'label' => 'База знаний',
                        'resource' => 'mvc:itftc|faq|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),                     
                    ),
     ),            
            array(
                'module' => 'Products',
                'controller' => 'Stock',
                'action' => 'list',
                'icon' => 'th-large',
                'label' => 'Склад',
                'type' => 'uri',
                'href' => '#',
                'pages' => array(
                    array(
                        'module' => 'products',
                        'controller' => 'stock',
                        'action' => 'list',
                        'label' => 'Склады',
                        'resource' => 'mvc:products|stock|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'products',
                        'controller' => 'product',
                        'action' => 'list',
                        'label' => 'Номенклатура',
                        'resource' => 'mvc:products|product|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'products',
                        'controller' => 'producttype',
                        'action' => 'list',
                        'label' => 'Категории товаров',
                        'resource' => 'mvc:products|producttype|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'products',
                        'controller' => 'productstocksettings',
                        'action' => 'list',
                        'label' => 'Настройки товаров',
                        'resource' => 'mvc:products|productstocksettings|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    )
                ),
            ),
            /*
            array(
                'label' => 'Клиенты',
                'type' => 'uri',
                'icon' => 'heart',
                'href' => '#',
                'pages' => array(array(
                        'module' => 'customers',
                        'controller' => 'customer',
                        'action' => 'list',
                        'label' => 'Клиенты',
                        'resource' => 'mvc:customers|customer|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'customers',
                        'controller' => 'dealer',
                        'action' => 'list',
                        'label' => 'Дилеры',
                        'resource' => 'mvc:customers|dealer|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'customers',
                        'controller' => 'contact',
                        'action' => 'list',
                        'label' => 'Контакты',
                        'resource' => 'mvc:customers|contact|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'customers',
                        'controller' => 'customergroup',
                        'action' => 'list',
                        'label' => 'Группы клиентов',
                        'resource' => 'mvc:customers|customergroup|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'logistic',
                        'controller' => 'dealertariffsets',
                        'action' => 'list',
                        'label' => 'Настройки дилеров',
                        'resource' => 'mvc:logistic|dealertariffsets|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                ),
            ),*/
            array(
                'label' => 'Доставка',
                'type' => 'uri',
                'href' => '#',
                'icon' => 'globe',
                'pages' => array(
                    array(
                        'module' => 'logistic',
                        'controller' => 'package',
                        'action' => 'list',
                        'label' => 'Посылки',
                        'resource' => 'mvc:logistic|package|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                        /*'pages' => array(
                            array(
                            'module' => 'logistic',
                            'controller' => 'package',
                            'action' => 'create',
                            'label' => '+',
                            'resource' => 'mvc:logistic|package|create',
                            'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                                ),
                            ),*/
                    ),
                        
                    array(
                        'module' => 'logistic',
                        'controller' => 'transportaction',
                        'action' => 'list',
                        'label' => 'Движения пакетов',
                        'resource' => 'mvc:logistic|transportaction|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'logistic',
                        'controller' => 'theft',
                        'action' => 'list',
                        'label' => 'Кражи',
                        'resource' => 'mvc:logistic|theft|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'logistic',
                        'controller' => 'logistictariff',
                        'action' => 'list',
                        'label' => 'Тарифные планы',
                        'resource' => 'mvc:logistic|logistictariff|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'logistic',
                        'controller' => 'dealertariffsets',
                        'action' => 'list',
                        'label' => 'Настройки дилеров',
                        'resource' => 'mvc:logistic|dealertariffsets|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'logistic',
                        'controller' => 'transportaction',
                        'action' => 'receive',
                        'label' => 'Прием пакетов',
                        'resource' => 'mvc:logistic|transportaction|receive',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'logistic',
                        'controller' => 'chain',
                        'action' => 'list',
                        'label' => 'Цепочки перемещения',
                        'resource' => 'mvc:logistic|chain|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'logistic',
                        'controller' => 'packinglist',
                        'action' => 'list',
                        'label' => 'Упаковочные листы',
                        'resource' => 'mvc:logistic|packinglist|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                )
            ),
            array(
                'module' => 'sales',
                'controller' => 'order',
                'action' => 'display',
                'label' => 'Продажи',
                'type' => 'uri',
                'icon' => 'shopping-cart',
                'href' => '#',
                'pages' => array(array(
                        'module' => 'sales',
                        'controller' => 'order',
                        'action' => 'list',
                        'label' => 'Заказы',
                        'resource' => 'mvc:sales|order|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                ),
            ),
            array(
                'label' => 'Финансы',
                'type' => 'uri',
                'icon' => 'briefcase',
                'href' => '#',
                'pages' => array(array(
                        'module' => 'finance',
                        'controller' => 'cashdesc',
                        'action' => 'list',
                        'label' => 'Кассы',
                        'resource' => 'mvc:finance|cashdesc|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'finance',
                        'controller' => 'currency',
                        'action' => 'list',
                        'label' => 'Валюты',
                        'resource' => 'mvc:finance|currency|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'finance',
                        'controller' => 'payment',
                        'action' => 'list',
                        'label' => 'Платежи',
                        'resource' => 'mvc:finance|payment|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'finance',
                        'controller' => 'payment',
                        'action' => 'dateschart',
                        'label' => 'Отчет платежей',
                        'resource' => 'mvc:finance|payment|dateschart',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'docs',
                        'controller' => 'bill',
                        'action' => 'list',
                        'label' => 'Счета',
                        'resource' => 'mvc:docs|bill|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'finance',
                        'controller' => 'paymove',
                        'action' => 'list',
                        'label' => 'Перемещения средств',
                        'resource' => 'mvc:finance|paymove|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'finance',
                        'controller' => 'bonus',
                        'action' => 'list',
                        'label' => 'Бонусы',
                        'resource' => 'mvc:finance|bonus|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'finance',
                        'controller' => 'finacnt',
                        'action' => 'listtree',
                        'label' => 'Финансовый учет',
                        'resource' => 'mvc:finance|finacnt|listtree',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                /*  array(

                  'module' => 'finance',
                  'controller' => 'finoperation',
                  'action' => 'list',
                  'label' => 'Транзакции покупателей',
                  'resource' => 'mvc:finance|finoperation|list',
                  'privilege' => (string)\Sl_Service_Acl::PRIVELEGE_ACCESS,
                  ), */
                ),
            ),
            array(
                'label' => 'CRM',
                'type' => 'uri',
                'icon' => 'calendar',
                'href' => '#',
                'pages' => array(
                    array(
                        'module' => 'crm',
                        'controller' => 'task',
                        'action' => 'list',
                        'label' => 'Задачи',
                        'resource' => 'mvc:crm|task|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'crm',
                        'controller' => 'mailergroup',
                        'action' => 'list',
                        'label' => 'Группы рассылки',
                        'resource' => 'mvc:crm|mailergroup|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'crm',
                        'controller' => 'note',
                        'action' => 'list',
                        'label' => 'Заметки',
                        'resource' => 'mvc:crm|note|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
        
                    
                    /*
                    array(
                        'module' => 'customers',
                        'controller' => 'dealer',
                        'action' => 'list',
                        'label' => 'Дилеры',
                        'resource' => 'mvc:customers|dealer|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'customers',
                        'controller' => 'contact',
                        'action' => 'list',
                        'label' => 'Контакты',
                        'resource' => 'mvc:customers|contact|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'customers',
                        'controller' => 'customergroup',
                        'action' => 'list',
                        'label' => 'Группы клиетов',
                        'resource' => 'mvc:customers|customergroup|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'logistic',
                        'controller' => 'dealertariffsets',
                        'action' => 'list',
                        'label' => 'Настройки дилеров',
                        'resource' => 'mvc:logistic|dealertariffsets|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),*/
                ),
            ),
            array(
                'module' => 'home',
                'controller' => 'main',
                'action' => 'list',
                'label' => '',
                'icon' => 'book',
                'type' => 'uri',
                'href' => '#',
                'resource' => '',
                'pages' => array(
                    array(
                        'module' => 'products',
                        'controller' => 'productssettings',
                        'action' => 'list',
                        'label' => 'Характеристики товаров',
                        'resource' => 'mvc:products|productssettings|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'logistic',
                        'controller' => 'route',
                        'action' => 'list',
                        'label' => 'Маршруты',
                        'resource' => 'mvc:logistic|route|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'logistic',
                        'controller' => 'wrapping',
                        'action' => 'list',
                        'label' => 'Упаковка',
                        'resource' => 'mvc:logistic|wrapping|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'home',
                        'controller' => 'city',
                        'action' => 'list',
                        'label' => 'Города',
                        'resource' => 'mvc:home|city|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    )
                    ,
                    array(
                        'module' => 'home',
                        'controller' => 'country',
                        'action' => 'list',
                        'label' => 'Страны',
                        'resource' => 'mvc:home|country|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                ),
            ),
            array(
                'module' => 'home',
                'controller' => 'admin',
                'action' => 'list',
                'label' => '',
                'icon' => 'wrench',
                'type' => 'uri',
                'href' => '#',
                'resource' => '',
                'pages' => array(
                    array(
                        'module' => 'comments',
                        'controller' => 'main',
                        'action' => 'setmodelcommented',
                        'label' => 'Включить комментарии',
                        'resource' => 'mvc:comments|main|setmodelcommented',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'auth',
                        'controller' => 'admin',
                        'action' => 'permissions',
                        'label' => 'Права доступа',
                        'resource' => 'mvc:auth|admin|permissions',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'auth',
                        'controller' => 'role',
                        'action' => 'list',
                        'label' => 'Роли',
                        'resource' => 'mvc:auth|role|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'auth',
                        'controller' => 'restriction',
                        'action' => 'list',
                        'label' => 'Ограничения ролей',
                        'resource' => 'mvc:auth|restriction|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'auth',
                        'controller' => 'user',
                        'action' => 'list',
                        'label' => 'Пользователи',
                        'resource' => 'mvc:auth|user|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'home',
                        'controller' => 'admin',
                        'action' => 'createmodel',
                        'label' => 'Создать модель',
                        'resource' => 'mvc:home|admin|createmodel',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                     array(
                        'module' => 'home',
                        'controller' => 'admin',
                        'action' => 'updatemodel',
                        'label' => 'Добавить поле в модель',
                        'resource' => 'mvc:home|admin|updatemodel',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'home',
                        'controller' => 'admin',
                        'action' => 'createmodulerelation',
                        'label' => 'Связать модели',
                        'resource' => 'mvc:home|admin|createmodulerelation',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'home',
                        'controller' => 'printform',
                        'action' => 'list',
                        'label' => 'Печатные формы',
                        'resource' => 'mvc:home|printform|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                    array(
                        'module' => 'home',
                        'controller' => 'settings',
                        'action' => 'list',
                        'label' => 'Системные настройки',
                        'resource' => 'mvc:home|settings|list',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                ),
            ),
            array(
                'module' => 'Auth',
                'controller' => 'user',
                'action' => 'display',
                'label' => 'Профиль',
                'icon' => 'user',
                'type' => 'uri',
                'href' => '#',
                'flag_identifier' => 'user',
                'pages' => array(
                    array(
                        'module' => 'auth',
                        'controller' => 'user',
                        'action' => 'password',
                        'label' => 'Сменить пароль',
                        'resource' => 'mvc:auth|user|password',
                        'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
                    ),
                     array(
                'module' => 'auth',
                'controller' => 'main',
                'action' => 'logout',
                'label' => 'Выйти из системы',
                //'icon' => 'circle-arrow-right',
                'resource' => 'mvc:auth|main|logout',
                'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
            ),
                ),
            ),
            /*
            array(
                'module' => 'auth',
                'controller' => 'main',
                'action' => 'logout',
                'label' => '',
                'icon' => 'circle-arrow-right',
                'resource' => 'mvc:auth|main|logout',
                'privilege' => (string) \Sl_Service_Acl::PRIVELEGE_ACCESS,
            ), */
        );
      $event = new \Sl\Module\Menu\Event\Pages ('PagesPrepare', array('pages' => $pages));  
      //error_reporting(E_ALL); 
      \Sl_Event_Manager::trigger($event);
     $pages = $event->getPages();
        
        $pages = $this->_prepareMenuPages();
        
        
        $this->_menu = new \Zend_Navigation($pages);
        
        \Zend_Registry::set('Zend_Navigation', $this->_menu);
    }

    public function indexAction() {
        $this->view->menu = $this->_menu;
    }

    public function breadcrumbAction() {
        $empty = (bool) $this->getRequest()->getParam('empty', false);
        if ($empty) {
            $this->view->menu = new \Zend_Navigation(array(
                array(
                    'label' => '',
                    'type' => 'uri',
                    'href' => '#',
                    'resource' => '',
                )
            ));
        } else {
            $this->view->menu = $this->_menu;
        }
    }

    /**
     * Основное меню
     */
    public function sidenavAction() {

        $this->view->menu = $this->_menu;
    }

    /**
     * Подменю
     */

    /**
     * Меню пользователя
     */
    public function usernavAction() {
        $pages = array(array(
                'controller' => 'auth',
                'action' => 'logout',
                'label' => 'Выйти',
            ),);

        $menu = new \Zend_Navigation($pages);

        $this->view->menu = $menu;
    }
    
    protected function _prepareMenuPages(){
        $pages_configs = array();
        foreach (\Sl_Module_Manager::getInstance()->getModules() as $module){
            $pages_configs = array_merge($pages_configs, $module->getMenuPages());                
        }
        
        return $this->_recBuildMenuArray($pages_configs);
        
    }
    
    protected function _recBuildMenuArray(array $pages, $parent = ''){
        $pages_assoc = array();
        foreach($pages as $page){
            $page['parent'] = isset($page['parent'])?$page['parent']:'';
            if ($page['parent'] == $parent){
                $order = isset($page['order'])?$page['order']:0;
                $page = $this->_buildPageNode($page, $pages);
                if (!isset($pages_assoc[$order]))$pages_assoc[$order] = array();
                $pages_assoc[$order][]=$page;
            }
            
        }
        ksort($pages_assoc);
        $pages = array();
        foreach($pages_assoc as $page_arr){
            $pages = array_merge($pages, $page_arr);    
        }
        return $pages;
        
    }
    
    protected function _buildPageNode(array $page, array $pages){
           $p = array();
           $p['label'] = $page['label'];
           $p['title'] = $page['label'];
           
           if (isset($page['icon'])) $p['icon'] = $page['icon'];
           if (isset($page['nolabel'])) $p['nolabel'] = $page['nolabel'];
           
           $p['visible'] = isset($page['visible'])?(bool) $page['visible']:true;
           
           if(isset($page['module']) && 
                    isset($page['controller']) && 
                    isset($page['action'])) {
                        
               $p['module'] = $page['module'];
               $p['controller'] = $page['controller'];
               $p['action'] = $page['action'];
               $p['id'] = $page['id']?$page['id']:implode('.',array($page['module'],$page['controller'],$page['action']));
               $p['resource'] = (isset($page['resource']) && $page['resource'])?$page['resource']:\Sl_Service_Acl::joinResourceName(array('type'=>\Sl_Service_Acl::RES_TYPE_MVC, 
                                                                                          'module'=>$p['module'], 
                                                                                          'controller'=>$p['controller'],
                                                                                          'action'=>$p['action']));
               $p['privilege'] = isset( $page['privilege'])?$page['privilege']: (string) \Sl_Service_Acl::PRIVELEGE_ACCESS;                                                                                           
           } else {
             $p['id'] = $page['id'];    
             $p['type'] = 'uri';
             $p['href'] = isset($page['href'])?$page['href']:'#';
               
           
           }
              $subpages = isset($p['id'])? $this-> _recBuildMenuArray($pages, $p['id']):array();
              if ($p['action'] == \Sl\Service\Helper::LIST_ACTION){
                  $pp = $p;
                  $pp['action'] = \Sl\Service\Helper::CREATE_ACTION;
                  $pp['title'] = $pp['label'] = $this->view -> translate('Создать');
                  $pp['class']= 'create_model';
                  $pp['image']= '<i class="icon-plus"></i>' ;
                  $pp['id'] = implode('.',array($pp['module'],$pp['controller'],$pp['action']));
                  $pp['resource'] = \Sl_Service_Acl::joinResourceName(array('type'=>\Sl_Service_Acl::RES_TYPE_MVC, 
                                                                                          'module'=>$pp['module'], 
                                                                                          'controller'=>$pp['controller'],
                                                                                          'action'=>$pp['action']));
                  array_unshift($subpages, $pp); 
                                                                                                   
              }
               //\Sl\Service\Helper::CREATE_ACTION
              if (count($subpages)) $p['pages'] = $subpages;
              
              return $p;         
    }
    
}
