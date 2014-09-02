<?php
return array (
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
  '_roles_' => 
  array (
    29 => 
    array (
      'modulerelation_customerusersystem' => 
      array (
        'readonly' => false,
      ),
    ),
  ),
);
