<?php
return array (
  'model' => 
  array (
    'name' => 
    array (
      'label' => 'Имя',
      'type' => 'text',
      'sort_order' => 10,
    ),
    'phone' => 
    array (
      'label' => 'Контактный телефон',
      'type' => 'text',
      'sort_order' => 15,
    ),
    'login' => 
    array (
      'label' => 'Логин',
      'type' => 'text',
      'sort_order' => 27,
      'required' => true,
      'readonly' => true,
    ),
    'password' => 
    array (
      'label' => 'Пароль',
      'type' => 'hidden',
      'sort_order' => 30,
      'visible' => false,
      'options' => 
      array (
        'disabled' => 'disabled',
      ),
    ),
    'email' => 
    array (
      'label' => 'Email',
      'type' => 'text',
      'sort_order' => 25,
    ),
    'id' => 
    array (
      'label' => 'ID',
      'type' => 'hidden',
      'options' => 
      array (
        'readonly' => 'readonly',
      ),
    ),
    'active' => 
    array (
      'label' => 'Запись активна',
      'type' => 'hidden',
      'sort_order' => 5,
    ),
    'create' => 
    array (
      'label' => 'CREATE',
      'type' => 'text',
    ),
    'archived' => 
    array (
      'label' => 'ARCHIVED',
      'type' => 'text',
    ),
    'blocked' => 
    array (
      'label' => 'Заблокирован',
      'type' => 'checkbox',
    ),
    'system' => 
    array (
      'label' => 'SYSTEM',
      'type' => 'hidden',
    ),
  ),
);
