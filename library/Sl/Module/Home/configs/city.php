<?php
return array (
  'model' => 
  array (
    'name' => 
    array (
      'label' => 'Название',
      'type' => 'text',
    ),
    'code' => 
    array (
      'label' => 'Код',
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
      'type' => 'text',
    ),
    'create' => 
    array (
      'label' => 'CREATE',
      'type' => 'text',
    ),
  ),
  'validator_groups' => 
  array (
    'gr_main' => 
    array (
      0 => 'emailAddress',
      'alnum' => 
      array (
        'allowWhiteSpace' => true,
      ),
    ),
    'gr_ext' => 
    array (
      0 => 'emailAddress',
    ),
  ),
);
