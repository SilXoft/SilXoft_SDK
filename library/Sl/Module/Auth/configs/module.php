<?php
return array (
  'custom_configs' => 
  array (
    'userroles' => 
    array (
      31 => 
      array (
        'forms' => 
        array (
          'model_user_form' => 
          array (
            'modulerelation_customeruserresponsible' => 
            array (
              'type' => 'hidden',
            ),
          ),
        ),
      ),
      33 => 
      array (
        'forms' => 
        array (
          'model_user_form' => 
          array (
            'modulerelation_customeruserresponsible' => 
            array (
              'type' => 'hidden',
            ),
          ),
        ),
      ),
    ),
  ),
  'forms' => 
  array (
    'auth_form' => 
    array (
      'fields' => 
      array (
        'login' => 
        array (
          'type' => 'text',
          'label' => 'Логин',
          'required' => true,
        ),
        'password' => 
        array (
          'type' => 'password',
          'label' => 'Пароль',
          'validators' => 
          array (
            'sl' => 
            array (
              'name' => 'StringLength',
              'options' => 
              array (
                'min' => 5,
                'max' => 15,
              ),
            ),
          ),
        ),
        'btn' => 
        array (
          'type' => 'submit',
          'label' => 'Войти',
          'class' => 'btn-success',
          'withoutLabel' => 1,
        ),
        'new_pass' => 
        array (
          'type' => 'button',
          'label' => 'Восстановить пароль',
          'class' => 'new-password',
          'withoutLabel' => 1,
        ),
      ),
    ),
    'restore_password' => 
    array (
      'fields' => 
      array (
        'login' => 
        array (
          'type' => 'text',
          'label' => 'Login',
          'required' => true,
          'validators' => 
          array (
            'ea' => 
            array (
              'name' => 'EmailAddress',
            ),
          ),
        ),
        'btn' => 
        array (
          'type' => 'submit',
          'label' => 'Выслать',
          'withoutLabel' => 1,
          'class' => 'send-password btn-danger',
        ),
        'cancel' => 
        array (
          'type' => 'button',
          'label' => 'А, я вспомнил!',
          'withoutLabel' => 1,
          'class' => 'close-newpassword btn-info',
        ),
        'check' => 
        array (
          'type' => 'hidden',
          'value' => 'change',
        ),
      ),
    ),
    'password_form' => 
    array (
      'fields' => 
      array (
        'current_password' => 
        array (
          'type' => 'password',
          'label' => 'Текущий пароль',
          'validators' => 
          array (
            'sl' => 
            array (
              'name' => 'StringLength',
              'options' => 
              array (
                'min' => 5,
                'max' => 15,
              ),
            ),
          ),
        ),
        'new_password' => 
        array (
          'type' => 'password',
          'label' => 'Новый пароль',
          'validators' => 
          array (
            'sl' => 
            array (
              'name' => 'StringLength',
              'options' => 
              array (
                'min' => 5,
                'max' => 15,
              ),
            ),
          ),
        ),
        'password_confirm' => 
        array (
          'type' => 'password',
          'label' => 'Еще раз',
          'validators' => 
          array (
            'sl' => 
            array (
              'name' => 'StringLength',
              'options' => 
              array (
                'min' => 5,
                'max' => 15,
              ),
            ),
          ),
        ),
        'btn' => 
        array (
          'type' => 'submit',
          'label' => 'Сохранить',
          'withoutLabel' => 1,
          'class' => 'submit',
        ),
      ),
    ),
    'model_user_form' => 
    array (
      'password' => 
      array (
        'sort_order' => 100,
      ),
      'modulerelation_userroles' => 
      array (
        'sort_order' => 20,
        'label' => 'Установленные роли',
        'required' => true,
      ),
      'modulerelation_cashdescuser' => 
      array (
        'sort_order' => 40,
        'label' => 'Управляет кассами',
      ),
      'modulerelation_stockuser' => 
      array (
        'sort_order' => 60,
        'label' => 'Работает на складах',
      ),
      'modulerelation_customeruserresponsible' => 
      array (
        'sort_order' => 70,
        'label' => 'Отвечает за',
      ),
      'modulerelation_customerusersystem' => 
      array (
        'sort_order' => 80,
        'label' => 'Связанный "Клиент"',
      ),
    ),
  ),
  'detailed' => 
  array (
    'user' => 
    array (
      'password' => 
      array (
        'sort_order' => 100,
      ),
      'modulerelation_userroles' => 
      array (
        'sort_order' => 20,
        'label' => 'Установленные роли',
        'required' => true,
      ),
      'modulerelation_cashdescuser' => 
      array (
        'sort_order' => 40,
        'label' => 'Управляет кассами',
      ),
      'modulerelation_stockuser' => 
      array (
        'sort_order' => 60,
        'label' => 'Работает на складах',
      ),
      'modulerelation_customeruserresponsible' => 
      array (
        'sort_order' => 70,
        'label' => 'Отвечает за',
      ),
      'modulerelation_customerusersystem' => 
      array (
        'sort_order' => 80,
        'label' => 'Связанный "Клиент"',
      ),
    ),
    'permission' => 
    array (
    ),
    'resource' => 
    array (
    ),
    'restriction' => 
    array (
      'name' => 
      array (
        'label' => 'Название',
        'type' => 'text',
        'sort_order' => 10,
      ),
      'main_object' => 
      array (
        'label' => 'Объект ограничения',
        'type' => 'text',
        'sort_order' => 20,
      ),
      'type' => 
      array (
        'label' => 'Тип ограничения',
        'type' => 'text',
        'sort_order' => 30,
      ),
      'null_include' => 
      array (
        'label' => 'Включать непривязанные',
        'type' => 'text',
        'sort_order' => 35,
      ),
      'rules' => 
      array (
        'label' => 'Правила',
        'type' => 'textarea',
        'sort_order' => 40,
      ),
      'create' => 
      array (
        'label' => 'CREATE',
        'type' => 'hidden',
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
        'sort_order' => 50,
      ),
    ),
    'role' => 
    array (
      'name' => 
      array (
        'label' => 'Название',
        'type' => 'text',
      ),
      'parent' => 
      array (
        'label' => 'Родительская',
        'type' => 'text',
      ),
      'description' => 
      array (
        'label' => 'Описание',
        'type' => 'text',
      ),
      'id' => 
      array (
        'label' => 'ID',
        'type' => 'text',
      ),
      'active' => 
      array (
        'label' => 'ACTIVE',
        'type' => 'checkbox',
      ),
    ),
  ),
  'titles' => 
  array (
    'user' => 
    array (
      'modulerelation_userroles' => 
      array (
        'label' => 'Роли',
        'sort_order' => 30,
      ),
      'modulerelation_cashdescuser' => 
      array (
        'label' => 'Управляет кассами',
      ),
      'modulerelation_stockuser' => 
      array (
        'label' => 'Работает на складе',
      ),
    ),
  ),
  'listview_options' => 
  array (
    'user' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Имя',
        'searchable' => true,
        'sortable' => true,
      ),
      'phone' => 
      array (
        'order' => 20,
        'label' => 'Контактный телефон',
      ),
      'login' => 
      array (
        'order' => 30,
        'label' => 'Логин',
        'searchable' => true,
      ),
      'email' => 
      array (
        'order' => 50,
        'label' => 'Email',
        'searchable' => true,
      ),
      'userroles.name' => 
      array (
        'label' => 'Роли',
        'order' => 40,
        'searchable' => true,
      ),
      'userroles' => 
      array (
        'label' => 'Роли',
      ),
    ),
    'role' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Название',
      ),
      'parent' => 
      array (
        'order' => 20,
        'label' => 'Родительская',
      ),
      'description' => 
      array (
        'order' => 30,
        'label' => 'Описание',
      ),
    ),
    'restriction' => 
    array (
      'name' => 
      array (
        'order' => 10,
        'label' => 'Название',
      ),
      'main_object' => 
      array (
        'order' => 20,
        'label' => 'Объект ограничения',
      ),
      'type' => 
      array (
        'order' => 30,
        'label' => 'Тип',
      ),
      'restrictionroles' => 
      array (
        'order' => 40,
        'label' => 'Роли',
      ),
      'restrictionroles.name' => 
      array (
        'order' => 50,
        'label' => 'Роли',
      ),
      'null_include' => 
      array (
        'label' => 'Включать непривязанные',
        'order' => 60,
        'visible' => false,
        'hidable' => true,
      ),
      'rules' => 
      array (
        'label' => 'Правила',
        'order' => 70,
        'visible' => false,
        'hidable' => true,
      ),
    ),
    'setting' => 
    array (
      'master_relation' => 
      array (
        'order' => 10,
        'label' => 'MASTER_RELATION',
      ),
      'listview' => 
      array (
        'order' => 20,
        'label' => 'LISTVIEW',
      ),
      'filters' => 
      array (
        'order' => 30,
        'label' => 'FILTERS',
      ),
      'filedsets' => 
      array (
        'order' => 40,
        'label' => 'FILEDSETS',
      ),
      'archived' => 
      array (
        'order' => 50,
        'label' => 'ARCHIVED',
      ),
    ),
  ),
  'modulerelations' => 
  array (
    0 => 
    array (
      'type' => '22',
      'db_table' => 'Sl\\Module\\Auth\\Modulerelation\\Table\\Restrictionroles',
    ),
    1 => 
    array (
      'type' => '11',
      'handling' => true,
      'db_table' => 'Sl\\Module\\Auth\\Modulerelation\\Table\\Usersetting',
    ),
  ),
  'lists' => 
  array (
    'auth_restrictions_type' => 
    array (
      0 => 'Свободное',
      1 => 'Строгое',
    ),
    'auth_restrictions_nullinclude' => 
    array (
      0 => 'Нет',
      1 => 'Да',
    ),
  ),
  'navigation_pages' => 
  array (
    0 => 
    array (
      'module' => 'auth',
      'controller' => 'admin',
      'action' => 'permissions',
      'label' => 'Права доступа',
      'parent' => 'admin',
    ),
    1 => 
    array (
      'module' => 'auth',
      'controller' => 'role',
      'action' => 'list',
      'label' => 'Роли',
      'parent' => 'admin',
    ),
    2 => 
    array (
      'module' => 'auth',
      'controller' => 'restriction',
      'action' => 'list',
      'label' => 'Ограничения ролей',
      'parent' => 'auth.role.list',
    ),
    3 => 
    array (
      'module' => 'auth',
      'controller' => 'user',
      'action' => 'list',
      'label' => 'Пользователи',
      'parent' => 'admin',
    ),
    4 => 
    array (
      'label' => 'Профиль',
      'icon' => 'user',
      'id' => 'usersettings',
      'order' => 10000,
    ),
    5 => 
    array (
      'module' => 'auth',
      'controller' => 'user',
      'action' => 'password',
      'label' => 'Сменить пароль',
      'parent' => 'usersettings',
    ),
    6 => 
    array (
      'module' => 'auth',
      'controller' => 'main',
      'action' => 'logout',
      'label' => 'Выйти из системы',
      'parent' => 'usersettings',
    ),
  ),
);
