<?php
return array (
  'forms' => 
  array (
    'model_address_form' => 
    array (
      'modulerelation_areaaddress' => 
      array (
        'sort_order' => 200,
        'label' => 'Область',
      ),
    'type' => 
      array (
        'sort_order' => 100,
        'label' => 'Область',  
        
      ),        
    'region' => 
      array (
        'sort_order' => 300,
        
      ),
    'locality' => 
      array (
        'sort_order' => 350,
        
      ),        
    'street' => 
      array (
        'sort_order' => 400,
        
      ),
    'zip' => 
      array (
        'sort_order' => 1000,
        
      ),          
    ),      
    'model_email_form' => 
    array (
      'modulerelation_emailemaildetails' => 
      array (
        'sort_order' => 20,
        'label' => 'Email Detail',
      ),
    ),
    'model_city_form' => 
    array (
      'modulerelation_citycountry' => 
      array (
        'sort_order' => 20,
        'label' => 'Страна',
      ),
      'modulerelation_stockcity' => 
      array (
        'sort_order' => 40,
        'label' => 'Склады',
      ),
    ),
    'model_printform_form' => 
    array (
      'name' => 
      array (
        'sort_order' => 10,
        'type' => 'select',
      ),
      'type' => 
      array (
        'sort_order' => 30,
        'type' => 'select',
      ),
      'modulerelation_attachmentprintform' => 
      array (
        'sort_order' => 45,
        'label' => 'Вложенные шаблоны',
        'request_fields' => 
        array (
          0 => 'name',
          1 => 'description',
          2 => 'printformfile:name',
        ),
      ),
      'modulerelation_printformfile' => 
      array (
        'sort_order' => 50,
        'label' => 'Шаблон',
        'required' => true,
        'field_filters' => 
        array (
          0 => 
          array (
            'field' => 'type',
            'matching' => 'like',
            'value' => 'type',
          ),
        ),
      ),
    ),
    'model_file_form' => 
    array (
      'location' => 
      array (
        'readonly' => true,
      ),
    ),
    'model_country_form' => 
    array (
      'modulerelation_citycountry' => 
      array (
        'sort_order' => 20,
        'label' => 'Города',
      ),
    ),
    'dateschart_form' => 
    array (
      'fields' => 
      array (
        'start' => 
        array (
          'type' => 'date',
          'label' => 'Дата начала',
        ),
        'period' => 
        array (
          'type' => 'select',
          'label' => 'Период',
          'options' => 
          array (
            'W' => 'Неделя',
            'm' => 'Месяц',
            'y' => 'Год',
          ),
          'value' => 'm',
        ),
        'delta' => 
        array (
          'type' => 'select',
          'label' => 'Дискретизация',
          'options' => 
          array (
            'd' => 'Дни',
            'W' => 'Недели',
            'm' => 'Месяцы',
          ),
          'value' => 'W',
        ),
        'chart' => 
        array (
          'type' => 'select',
          'label' => 'График',
          'options' => 
          array (
            'LineChart' => 'Линейный график',
            'AreaChart' => 'Наполенный график',
            'PieChart' => 'Круговая диаграмма',
            'ColumnChart' => 'Колонки',
          ),
          'value' => 'AreaChart',
        ),
        'go' => 
        array (
          'type' => 'submit',
          'label' => 'Показать',
        ),
      ),
    ),
    'createmodel_form' => 
    array (
      'fields' => 
      array (
        'Create' => 
        array (
          'type' => 'submit',
          'label' => 'Create',
        ),
        'module_name' => 
        array (
          'type' => 'select',
          'label' => 'Module',
        ),
        'name' => 
        array (
          'type' => 'text',
          'label' => 'Model name',
        ),
        'table_name' => 
        array (
          'type' => 'text',
          'label' => 'Table name',
        ),
        'create_controller' => 
        array (
          'type' => 'checkbox',
          'label' => 'Создать контроллер',
        ),
        'create_log' => 
        array (
          'type' => 'checkbox',
          'label' => 'Вести лог',
        ),
        'inherits' => 
        array (
          'type' => 'select',
          'label' => 'Inherits model',
        ),          
      ),
    ),
'createmodule' => array(
            'fields' => array(
                'create' =>
                array(
                    'type' => 'submit',
                    'label' => 'Создать',
                ),
                'name' =>
                array(
                    'type' => 'text',
                    'label' => 'Название',
                ),
                'create_main_controller' =>
                array(
                    'type' => 'checkbox',
                    'label' => 'Создать Main контроллер',
                ),
                'create_admin_controller' =>
                array(
                    'type' => 'checkbox',
                    'label' => 'Создать Admin контроллер',
                ),
                'activate' => array(
                    'type' => 'checkbox',
                    'label' => 'Активировать',
                ),
            ),
        ),
    'createmodulerelation_form' => 
    array (
      'fields' => 
      array (
        'Create' => 
        array (
          'type' => 'submit',
          'label' => 'Create',
        ),
        'module_name' => 
        array (
          'type' => 'select',
          'label' => 'Модуль',
        ),
        'model_name' => 
        array (
          'type' => 'select',
          'label' => 'Родительская модель',
        ),
        'target_model_name' => 
        array (
          'type' => 'select',
          'label' => 'Связанная модель',
        ),
        'modulerelation_name' => 
        array (
          'type' => 'text',
          'label' => 'Название связи',
        ),
        'relation_type' => 
        array (
          'type' => 'select',
          'label' => 'Тип связи',
        ),
        'table_name' => 
        array (
          'type' => 'text',
          'label' => 'Table name',
        ),
      ),
    ),
  ),
  'titles' => 
  array (
    'country' => 
    array (
      'modulerelation_citycountry' => 
      array (
        'label' => 'Города',
        'sort_order' => 70,
      ),
    ),
    'city' => 
    array (
      'modulerelation_citycountry' => 
      array (
        'label' => 'Страна',
      ),
      'modulerelation_stockcity' => 
      array (
        'label' => 'Склады',
      ),
    ),
  ),
  'listview_options' => 
  array (
    'file' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Название',
        'searchable' => true,
      ),
      'type' => 
      array (
        'order' => 20,
        'label' => 'Тип',
      ),
      'location' => 
      array (
        'order' => 30,
        'label' => 'Файл',
      ),
    ),
    'city' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Название',
        'sortable' => true,
        'searchable' => true,
      ),
      'citycountry.name' => 
      array (
        'label' => 'Страна',
        'sortable' => true,
        'searchable' => true,
        'order' => 15,
      ),
      'code' => 
      array (
        'order' => 20,
        'label' => 'Код',
      ),
    ),
    'country' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Название',
      ),
      'code' => 
      array (
        'order' => 20,
        'label' => 'Код',
      ),
    ),
    'printform' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Модель',
        'searchable' => true,
        'select' => true,
      ),
      'description' => 
      array (
        'order' => 30,
        'label' => 'Название',
      ),
      'printformfile.name' => 
      array (
        'order' => 40,
        'label' => 'Имя файла',
      ),
      'printformfile' => 
      array (
        'order' => 20,
        'label' => 'Шаблон',
      ),
    ),
    'email' => 
    array (
      'mail' => 
      array (
        'order' => 10,
        'label' => 'E-mail',
        'searchable' => true,
        'sortable' => true,
        'class' => 'span1-5',
      ),
      'emailemaildetails.name' => 
      array (
        'label' => 'Имя',
        'searchable' => true,
        'sortable' => true,
        'class' => 'span1-5',
      ),
      'emailemaildetails.company' => 
      array (
        'label' => 'Компания',
        'searchable' => true,
        'sortable' => true,
        'class' => 'span1-5',
      ),
      'emailemaildetails.phone' => 
      array (
        'label' => 'Телефон',
        'searchable' => true,
        'sortable' => true,
        'class' => 'span1-5',
      ),
      'emailemaildetails.country' => 
      array (
        'label' => 'Страна',
        'searchable' => true,
        'sortable' => true,
        'class' => 'span1-5',
      ),
      'emailemaildetails.city' => 
      array (
        'label' => 'Город',
        'searchable' => true,
        'sortable' => true,
        'class' => 'span1-5',
      ),
      'emailemaildetails.ballans' => 
      array (
        'label' => 'Баланс',
        'searchable' => true,
        'sortable' => true,
        'class' => 'span1-5',
      ),
    ),
    'phone' => 
    array (
      'phone' => 
      array (
        'order' => 10,
        'label' => 'Телефон',
      ),
    ),
    'cronjob' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Название',
      ),
      'minute' => 
      array (
        'order' => 20,
        'label' => 'Минуты',
      ),
      'hour' => 
      array (
        'order' => 30,
        'label' => 'Часы',
      ),
      'day' => 
      array (
        'order' => 40,
        'label' => 'Дни',
      ),
      'month' => 
      array (
        'order' => 50,
        'label' => 'Месяцы',
      ),
      'command' => 
      array (
        'order' => 60,
        'label' => 'Команда',
      ),
      'description' => 
      array (
        'order' => 70,
        'label' => 'Описание',
      ),
    ),
    'settings' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Ключ',
      ),
      'value' => 
      array (
        'order' => 20,
        'label' => 'Значение',
      ),
      'type' => 
      array (
        'order' => 30,
        'label' => 'TYPE',
        'type' => 'hidden',
      ),
    ),
    'acnt' => 
    array (
      'name' => 
      array (
        'order' => 5,
        'label' => 'Название',
      ),
      'model_name' => 
      array (
        'order' => 10,
        'label' => 'Документ',
      ),
    ),
    'emaildetails' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'NAME',
      ),
      'company' => 
      array (
        'order' => 20,
        'label' => 'NAME',
      ),
      'phone' => 
      array (
        'order' => 30,
        'label' => 'NAME',
      ),
      'country' => 
      array (
        'order' => 40,
        'label' => 'NAME',
      ),
      'city' => 
      array (
        'order' => 40,
        'label' => 'NAME',
      ),
      'ballans' => 
      array (
        'order' => 40,
        'label' => 'NAME',
      ),
      'archived' => 
      array (
        'order' => 20,
        'label' => 'ARCHIVED',
      ),
    ),
    'address' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'NAME',
      ),
      'region' => 
      array (
        'order' => 20,
        'label' => 'REGION',
      ),
      'street' => 
      array (
        'order' => 30,
        'label' => 'STREET',
      ),
      'zip' => 
      array (
        'order' => 40,
        'label' => 'ZIP',
      ),
      'extend' => 
      array (
        'order' => 50,
        'label' => 'EXTEND',
      ),
      'archived' => 
      array (
        'order' => 60,
        'label' => 'ARCHIVED',
      ),
      'type' => 
      array (
        'order' => 70,
        'label' => 'TYPE',
      ),
    ),
  ),
  'modulerelations' => 
  array (
    0 => 
    array (
      'type' => '3',
      'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Printformfile',
    ),
    1 => 
    array (
      'type' => '22',
      'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Attachmentprintform',
    ),
    2 => 
    array (
      'type' => '11',
      'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Emailemaildetails',
      'options' => 
      array (
        'customeremails' => 
        array (
          'name' => 'name',
          'phone' => 'customerphones.phone',
          'city' => 'customercity.name',
          'country' => 'customercountry.name',
          'company' => 'company_name',
          'ballans' => 'customerballance.ballance',
        ),
        'contactemail' => 
        array (
          'name' => 'name',
        ),
      ),
    ),
  ),
  'lists' => 
  array (
    'home_printform_types' => 
    array (
      'application/vnd.ms-excel' => 'Xls',
      'application/pdf' => 'Pdf',
      'application/text' => 'Txt',
      'application/Html' => 'Html',
      'email' => 'Email',
    ),
    'home_printform_roles' => 
    array (
      0 => 'Распечатка документа',
      1 => 'Email',
      2 => 'Распечатка списка документов',
    ),
    'home_address_type' => 
    array (
      1 => 'ЮА',
      2 => 'ФА',
      3 => 'ПА', 
    ),      
    'empty' => 
    array (
      '-' => '-',
    ),
  ),
  'detailed' => 
  array (
    'phone' => 
    array (
      'phone' => 
      array (
        'label' => 'Телефон',
        'type' => 'text',
        'sort_order' => 10,
      ),
      'id' => 
      array (
        'label' => 'ID',
        'type' => 'hidden',
      ),
      'active' => 
      array (
        'label' => 'ACTIVE',
        'type' => 'text',
      ),
      'create' => 
      array (
        'label' => 'CREATE',
        'type' => 'text',
      ),
    ),
    'email' => 
    array (
      'mail' => 
      array (
        'label' => 'E-mail',
        'type' => 'text',
        'sort_order' => 10,
      ),
      'id' => 
      array (
        'label' => 'ID',
        'type' => 'hidden',
      ),
      'active' => 
      array (
        'label' => 'ACTIVE',
        'type' => 'checkbox',
      ),
      'create' => 
      array (
        'label' => 'CREATE',
        'type' => 'text',
      ),
    ),
    'printform' => 
    array (
      'name' => 
      array (
        'sort_order' => 10,
        'type' => 'select',
      ),
      'type' => 
      array (
        'sort_order' => 30,
        'type' => 'select',
      ),
      'modulerelation_attachmentprintform' => 
      array (
        'sort_order' => 45,
        'label' => 'Вложенные шаблоны',
        'request_fields' => 
        array (
          0 => 'name',
          1 => 'description',
          2 => 'printformfile:name',
        ),
      ),
      'modulerelation_printformfile' => 
      array (
        'sort_order' => 50,
        'label' => 'Шаблон',
        'required' => true,
        'field_filters' => 
        array (
          0 => 
          array (
            'field' => 'type',
            'matching' => 'like',
            'value' => 'type',
          ),
        ),
      ),
    ),
    'file' => 
    array (
      'location' => 
      array (
        'readonly' => true,
      ),
    ),
    'city' => 
    array (
      'modulerelation_citycountry' => 
      array (
        'sort_order' => 20,
        'label' => 'Страна',
      ),
      'modulerelation_stockcity' => 
      array (
        'sort_order' => 40,
        'label' => 'Склады',
      ),
    ),
  ),
  'navigation_pages' => 
  array (
        array(
            'controller' => 'main',
            'module' => 'home',
            'action' => 'list',
            'visible' => 0,
            'label' => 'Главная',
            'order'=> 5,
        ),
     array(
            'id'=>'dictionaries',
            'label' => 'Словари',
             'nolabel' => true,
            'icon' => 'book',
            'order'=> 200,
          
          
        ),
     array(
            'id'=>'admin_model',
            'label' => 'Модель',
            //'nolabel' => true,            
            'order'=> 10,
            'parent' => 'admin'
        ),      
     array(
        'id' => 'admin',
        'label' => 'Настройки',
        'nolabel' => true,
        'icon' => 'wrench',
        'order'=> 1000
     
     ),
     array(
        'label' => 'Модели системы',
        'id' => 'models',
        'parent' => 'admin'
     ),
     array(
        'label' => 'Модели системы',
        'id' => 'models',
        'parent' => 'admin'
     ),
      array(
        'module' => 'home',
        'controller' => 'admin',
        'action' => 'createmodule',
        'label' => 'Создать модуль',
        'parent' => 'models'
    ),
     array(
        'module' => 'home',
        'controller' => 'admin',
        'action' => 'createmodel',
        'label' => 'Создать модель',
        'parent' => 'admin_model', 
    ),
     array(
        'module' => 'home',
        'controller' => 'admin',
        'action' => 'updatemodel',
        'label' => 'Добавить поле в модель',
        'parent' => 'admin_model', 
    ),
    array(
        'module' => 'home',
        'controller' => 'admin',
        'action' => 'createmodulerelation',
        'label' => 'Связать модели',
        'parent' => 'admin_model',
    ),
    array(
        'module' => 'home',
        'controller' => 'printform',
        'action' => 'list',
        'label' => 'Печатные формы',
        'parent' => 'admin'
    ),
    array(
        'module' => 'home',
        'controller' => 'settings',
        'action' => 'list',
        'label' => 'Системные настройки',
        'parent' => 'admin'
    ),   
  ),
);
