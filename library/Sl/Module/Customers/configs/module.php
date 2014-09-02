<?php
return array (
  'custom_configs' => 
  array (
    'userroles' => 
    array (
      29 => 
      array (
        'forms' => 
        array (
          'model_customer_form' => 
          array (
            'modulerelation_customerusersystem' => 
            array (
              'readonly' => false,
            ),
          ),
        ),
      ),
    ),
  ),
  'forms' => 
  array (
    'model_contact_form' => 
    array (
      'modulerelation_contactemail' => 
      array (
        'label' => 'Email',
      ),
      'modulerelation_contactphone' => 
      array (
        'label' => 'Телефон',
      ),
      'modulerelation_customercontact' => 
      array (
        'label' => 'Клиент',
      ),
    ),
    'model_customer_form' => 
    array (
      'first_name' => 
      array (
        'sort_order' => 10,
        'required' => true,
      ),
      'middle_name' => 
      array (
        'sort_order' => 20,
      ),
      'last_name' => 
      array (
        'sort_order' => 30,
        'required' => true,
      ),
      'name' => 
      array (
        'sort_order' => 40,
      ),
      'modulerelation_customercustomergroup' => 
      array (
        'label' => 'Входит в группу',
      ),
      'modulerelation_customeremails' => 
      array (
        'label' => 'Email',
        'sort_order' => 420,
        'required' => true,
      ),
      'modulerelation_customerphones' => 
      array (
        'label' => 'Контактный тел.',
        'sort_order' => 510,
        'required' => true,
      ),
      'modulerelation_stockcustomer' => 
      array (
        'label' => 'Ближайший склад',
        'sort_order' => 60,
        'required' => true,
      ),
      'modulerelation_customerdealer' => 
      array (
        'label' => 'Привлек дилер',
        'sort_order' => 860,
      ),
      'modulerelation_customercustsource' => 
      array (
        'label' => 'Канал привлечения',
        'sort_order' => 870,
        'required' => true,
      ),
      'modulerelation_customercity' => 
      array (
        'label' => 'Город',
        'sort_order' => 90,
        'required' => true,
        'field_filters' => 
        array (
          0 => 
          array (
            'field' => 'citycountry:id',
            'matching' => 'in',
            'value' => 'modulerelation_customercountry',
          ),
        ),
      ),
      'create' => 
      array (
        'label' => 'Дата создания',
        'sort_order' => 250,
        'type' => 'date',
        'class' => 'current-date',
      ),
      'post_code' => 
      array (
        'sort_order' => 120,
      ),
      'address' => 
      array (
        'sort_order' => 150,
      ),
      'modulerelation_customercountry' => 
      array (
        'label' => 'Страна',
        'required' => true,
        'sort_order' => 110,
      ),
      'modulerelation_customerballance' => 
      array (
        'label' => 'Текущий баланс',
        'sort_order' => 60,
      ),
      'modulerelation_customeridentifiercustomer' => 
      array (
        'label' => 'Идентификатор',
        'sort_order' => 50,
        'readonly' => true,
      ),
      'notify_sms' => 
      array (
        'label' => 'sms',
        'sort_order' => 950,
      ),
      'notify_email' => 
      array (
        'label' => 'email',
        'sort_order' => 1000,
      ),
      'modulerelation_customeruserresponsible' => 
      array (
        'label' => 'Ответственный',
        'sort_order' => 1010,
      ),
      'modulerelation_customerusersystem' => 
      array (
        'label' => 'Пользователь системы',
        'request_fields' => 
        array (
          0 => 'name',
          1 => 'email',
        ),
        'field_filters' => 
        array (
          0 => 
          array (
            'field' => 'email',
            'matching' => 'in',
            'value' => 'modulerelation_customeremails-mail',
          ),
        ),
        'readonly' => true,
        'sort_order' => 1020,
      ),
      'modulerelation_customercontact' => 
      array (
        'label' => 'Контакты',
        'include_decorator' => 'FilesListTable',
        'show_field' => 
        array (
          0 => 'post',
          1 => 'contactemail.mail',
          2 => 'contactphone.phone',
        ),
        'show_field_label' => 
        array (
          0 => 'Имя',
          1 => 'Должность',
          2 => 'Email',
          3 => 'Телефон',
        ),
      ),
    ),
    'model_dealer_form' => 
    array (
      'modulerelation_customerdealer' => 
      array (
        'label' => 'Клиенты дилера',
      ),
      'modulerelation_customerisdealer' => 
      array (
        'label' => 'Является клиентом',
      ),
      'modulerelation_dealeremails' => 
      array (
        'label' => 'Email',
        'sort_order' => 420,
      ),
      'modulerelation_dealerphones' => 
      array (
        'label' => 'Контактный тел.',
        'sort_order' => 510,
      ),
    ),
  ),
  'titles' => 
  array (
    'customer' => 
    array (
      'modulerelation_customeremails' => 
      array (
        'label' => 'Email',
        'sort_order' => 30,
      ),
      'modulerelation_customerphones' => 
      array (
        'label' => 'Контактный тел.',
        'sort_order' => 40,
      ),
      'modulerelation_customeridentifiercustomer' => 
      array (
        'label' => 'Идентификатор',
        'sort_order' => 10,
        'sortable' => true,
      ),
      'notify_sms' => 
      array (
        'visible' => false,
      ),
      'notify_email' => 
      array (
        'visible' => false,
      ),
      'passport' => 
      array (
        'visible' => false,
      ),
      'passport_date' => 
      array (
        'visible' => false,
      ),
      'modulerelation_customercity' => 
      array (
        'label' => 'Город',
        'sort_order' => 43,
      ),
      'modulerelation_customercountry' => 
      array (
        'label' => 'Страна',
        'sort_order' => 45,
        'visible' => false,
      ),
      'modulerelation_stockcustomer' => 
      array (
        'label' => 'Ближайший склад',
        'sort_order' => 50,
      ),
      'modulerelation_customerdealer' => 
      array (
        'label' => 'Привлечен дилером',
        'sort_order' => 60,
        'visible' => false,
      ),
      'modulerelation_customerballance' => 
      array (
        'label' => 'Баланс',
        'sort_order' => 80,
      ),
      'modulerelation_customerisdealer' => 
      array (
        'label' => 'Является дилером',
        'sort_order' => 70,
      ),
      'modulerelation_customercustsource' => 
      array (
        'label' => 'Канал привлечения',
        'sort_order' => 200,
      ),
      'post_code' => 
      array (
        'visible' => false,
      ),
      'create' => 
      array (
        'visible' => false,
      ),
      'name' => 
      array (
        'sort_order' => 5,
      ),
      'skype' => 
      array (
        'sort_order' => 15,
      ),
      'qq' => 
      array (
        'sort_order' => 20,
      ),
      'notify_dealer' => 
      array (
        'sort_order' => 25,
      ),
      'description' => 
      array (
        'sort_order' => 30,
      ),
      'is_dealer' => 
      array (
        'sort_order' => 35,
      ),
    ),
    'dealer' => 
    array (
      'modulerelation_dealeremails' => 
      array (
        'label' => 'Email',
        'sort_order' => 30,
      ),
      'modulerelation_dealerphones' => 
      array (
        'label' => 'Контактный тел.',
        'sort_order' => 40,
      ),
      'modulerelation_customerdealer' => 
      array (
        'label' => 'Привлек контрагентов',
        'sort_order' => 60,
      ),
      'modulerelation_customerisdealer' => 
      array (
        'label' => 'Является контрагентом',
        'sort_order' => 80,
      ),
    ),
  ),
  'lists' => 
  array (
    'customer_attracts' => 
    array (
      0 => 'не определено',
      1 => 'реклама',
      2 => 'реклама1',
      3 => 'реклама2',
      4 => 'реклама3',
      5 => 'реклама4',
    ),
    'customer_status' => 
    array (
      1 => 'Холодный',
      2 => 'Теплый',
      3 => 'Горячий',
    ),
    'dealer_types' => 
    array (
      0 => 'не определено',
      1 => 'тип 1',
      2 => 'тип 2',
      3 => 'тип 3',
    ),
  ),
  'listview_options' => 
  array (
    'customer' => 
    array (
      'name' => 
      array (
        'label' => 'ФИО',
        'order' => 20,
        'searchable' => true,
        'sortable' => true,
      ),
      'customercity.name' => 
      array (
        'label' => 'Город',
        'order' => 40,
        'searchable' => true,
        'sortable' => true,
        'class' => 'span1-5',
      ),
      'status' => 
      array (
        'label' => 'Статус',
        'sort_order' => 28,
        'searchable' => true,
        'sortable' => true,
        'select' => true,
      ),
      'customerphones.phone' => 
      array (
        'label' => 'Телефон получателя',
        'order' => 100,
        'searchable' => true,
        'sortable' => true,
        'class' => 'span1-5',
      ),
      'sender_phone' => 
      array (
        'label' => 'Телефон отправителя',
        'order' => 105,
        'searchable' => true,
        'sortable' => true,
        'class' => 'span1-5',
      ),
      'customerballance.ballance' => 
      array (
        'label' => 'Баланс',
        'order' => 110,
        'searchable' => true,
        'sortable' => true,
        'class' => 'span1',
      ),
      'create' => 
      array (
        'order' => 107,
        'label' => 'Создан',
        'sortable' => true,
      ),
      'is_dealer' => 
      array (
        'visible' => false,
        'hidable' => true,
      ),
      'address' => 
      array (
        'label' => 'Адрес',
        'order' => 200,
        'visible' => false,
        'hidable' => true,
      ),
      'passport' => 
      array (
        'label' => 'Паспорт',
        'order' => 210,
        'visible' => false,
        'hidable' => true,
      ),
      'passport_date' => 
      array (
        'label' => 'Дата выдачи',
        'order' => 220,
        'visible' => false,
        'hidable' => true,
      ),
      'customercustsource.name' => 
      array (
        'label' => 'Канал привлечения',
        'order' => 230,
        'visible' => false,
        'hidable' => true,
        'searchable' => true,
        'sortable' => true,
      ),
      'notify_email' => 
      array (
        'label' => 'email',
        'order' => 240,
        'visible' => false,
        'hidable' => true,
      ),
      'notify_sms' => 
      array (
        'label' => 'sms',
        'order' => 250,
        'visible' => false,
        'hidable' => true,
      ),
      'post_code' => 
      array (
        'label' => 'Индекс',
        'order' => 260,
        'visible' => false,
        'hidable' => true,
      ),
      'first_name' => 
      array (
        'label' => 'Имя',
        'order' => 270,
        'visible' => false,
        'hidable' => true,
      ),
      'last_name' => 
      array (
        'label' => 'Фамилия',
        'order' => 280,
        'visible' => false,
        'hidable' => true,
      ),
      'middle_name' => 
      array (
        'label' => 'Отчество',
        'order' => 290,
        'visible' => false,
        'hidable' => true,
      ),
      'notify_dealer' => 
      array (
        'label' => 'Извещать дилера',
        'order' => 300,
        'visible' => false,
        'hidable' => true,
      ),
      'description' => 
      array (
        'label' => 'Комментарий',
        'order' => 310,
        'visible' => false,
        'hidable' => true,
      ),
      'company_name' => 
      array (
        'label' => 'Компания',
        'order' => 320,
        'searchable' => true,
        'visible' => false,
        'hidable' => true,
        'class' => 'span1-5',
      ),
      'customeremails.mail' => 
      array (
        'label' => 'E-mail',
        'order' => 330,
        'searchable' => true,
        'visible' => false,
        'hidable' => true,
        'class' => 'span1-5',
      ),
    ),
    'dealer' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'ФИО',
        'searchable' => true,
      ),
      'type' => 
      array (
        'order' => 20,
        'label' => 'Тип',
      ),
      'skype' => 
      array (
        'order' => 30,
        'label' => 'Skype',
      ),
      'qq' => 
      array (
        'order' => 40,
        'label' => 'QQ',
      ),
      'notify_email' => 
      array (
        'order' => 50,
        'label' => 'Email',
      ),
      'notify_sms' => 
      array (
        'order' => 60,
        'label' => 'sms',
      ),
    ),
    'customersource' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Источник',
      ),
    ),
    'contact' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Имя',
        'searchable' => true,
        'sortable' => true,
      ),
      'post' => 
      array (
        'order' => 20,
        'label' => 'Должность',
        'sortable' => true,
      ),
      'description' => 
      array (
        'order' => 38,
        'label' => 'Описание',
      ),
      'contactemail.mail' => 
      array (
        'order' => 33,
        'label' => 'Email',
        'searchable' => true,
      ),
      'contactphone.phone' => 
      array (
        'order' => 36,
        'label' => 'Телефон',
      ),
      'archived' => 
      array (
        'order' => 40,
        'label' => 'ARCHIVED',
      ),
    ),
    'customergroup' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Название',
      ),
      'archived' => 
      array (
        'order' => 20,
        'label' => 'ARCHIVED',
      ),
    ),
    'lead' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'ФИО',
        'class' => 'span1',
        'sortable' => true,
        'searchable' => true,
      ),
      'email' => 
      array (
        'order' => 20,
        'label' => 'Email',
        'class' => 'span1',
        'sortable' => true,
        'searchable' => true,
      ),
      'destination_country' => 
      array (
        'order' => 30,
        'label' => 'Куда',
        'class' => 'span1',
        'sortable' => true,
        'searchable' => true,
      ),
      'destination_city' => 
      array (
        'order' => 40,
        'label' => 'Город',
        'class' => 'span1',
        'sortable' => true,
        'searchable' => true,
      ),
      'country' => 
      array (
        'order' => 50,
        'label' => 'Откуда',
        'class' => 'span1',
        'sortable' => true,
        'searchable' => true,
      ),
      'delivery_type' => 
      array (
        'order' => 60,
        'label' => 'Тип доставки',
        'class' => 'span1',
        'sortable' => true,
        'searchable' => true,
      ),
      'weight' => 
      array (
        'order' => 70,
        'label' => 'Вес',
        'class' => 'span1',
        'sortable' => true,
        'searchable' => true,
      ),
      'volume' => 
      array (
        'order' => 80,
        'label' => 'Объем',
        'class' => 'span1',
        'sortable' => true,
        'searchable' => true,
      ),
      'category' => 
      array (
        'order' => 90,
        'label' => 'Категория',
        'class' => 'span1',
        'sortable' => true,
        'searchable' => true,
      ),
    ),
  ),
  'modulerelations' => 
  array (
    0 => 
    array (
      'type' => '22',
      'db_table' => 'Sl\\Module\\Customers\\Modulerelation\\Table\\Customeruserresponsible',
    ),
    1 => 
    array (
      'type' => '11',
      'db_table' => 'Sl\\Module\\Customers\\Modulerelation\\Table\\Customerusersystem',
    ),
    2 => 
    array (
      'type' => '2',
      'db_table' => 'Sl\\Module\\Customers\\Modulerelation\\Table\\Contactemail',
    ),
    3 => 
    array (
      'type' => '2',
      'db_table' => 'Sl\\Module\\Customers\\Modulerelation\\Table\\Contactphone',
    ),
    4 => 
    array (
      'type' => '12',
      'db_table' => 'Sl\\Module\\Customers\\Modulerelation\\Table\\Customercontact',
    ),
    5 => 
    array (
      'type' => '21',
      'db_table' => 'Sl\\Module\\Customers\\Modulerelation\\Table\\Customercustomergroup',
    ),
  ),
  'detailed' => 
  array (
    'customer' => 
    array (
      'first_name' => 
      array (
        'sort_order' => 10,
        'required' => true,
      ),
      'middle_name' => 
      array (
        'sort_order' => 20,
      ),
      'last_name' => 
      array (
        'sort_order' => 30,
        'required' => true,
      ),
      'name' => 
      array (
        'sort_order' => 40,
      ),
      'modulerelation_customeremails' => 
      array (
        'label' => 'Email',
        'sort_order' => 420,
        'required' => true,
      ),
      'modulerelation_customerphones' => 
      array (
        'label' => 'Контактный тел.',
        'sort_order' => 510,
        'required' => true,
      ),
      'modulerelation_stockcustomer' => 
      array (
        'label' => 'Ближайший склад',
        'sort_order' => 60,
        'required' => true,
      ),
      'modulerelation_customerdealer' => 
      array (
        'label' => 'Привлек дилер',
        'sort_order' => 860,
      ),
      'modulerelation_customercustsource' => 
      array (
        'label' => 'Канал привлечения',
        'sort_order' => 870,
        'required' => true,
      ),
      'modulerelation_customercity' => 
      array (
        'label' => 'Город',
        'sort_order' => 90,
        'required' => true,
        'field_filters' => 
        array (
          0 => 
          array (
            'field' => 'citycountry:id',
            'matching' => 'in',
            'value' => 'modulerelation_customercountry',
          ),
        ),
      ),
      'create' => 
      array (
        'label' => 'Дата создания',
        'sort_order' => 250,
        'type' => 'date',
        'class' => 'current-date',
      ),
      'post_code' => 
      array (
        'sort_order' => 120,
      ),
      'address' => 
      array (
        'sort_order' => 150,
      ),
      'modulerelation_customercountry' => 
      array (
        'label' => 'Страна',
        'required' => true,
        'sort_order' => 110,
      ),
      'modulerelation_customerballance' => 
      array (
        'label' => 'Текущий баланс',
        'sort_order' => 60,
      ),
      'modulerelation_customeridentifiercustomer' => 
      array (
        'label' => 'Идентификатор',
        'sort_order' => 50,
        'readonly' => true,
      ),
      'notify_sms' => 
      array (
        'label' => 'sms',
        'sort_order' => 950,
      ),
      'notify_email' => 
      array (
        'label' => 'email',
        'sort_order' => 1000,
      ),
      'modulerelation_customeruserresponsible' => 
      array (
        'label' => 'Ответственный',
        'sort_order' => 1010,
      ),
      'modulerelation_customerusersystem' => 
      array (
        'label' => 'Пользователь системы',
        'sort_order' => 1020,
      ),
    ),
  ),
  'navigation_pages' => 
  array (
            
  ),
);
