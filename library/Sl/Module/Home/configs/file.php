<?php
return array (
  'model' => 
  array (
      'name' => 
        array (
          'label' => 'Название',
          'type' => 'text',
        ),
      'id' => 
        array (
          'label' => 'ID',
          'type' => 'hidden',
        ),
    'active' => 
        array (
          'label' => 'ACTIVE',
          'type' => 'hidden',
        ),
    'create' => 
        array (
          'label' => 'CREATE',
          'type' => 'hidden',
        ),
    'type' => 
        array (
          'label' => 'Тип',
          'type' => 'hidden',
            'validators' => array(
                //'Alnum',
            ),
        ),
    'location' => 
        array (
          'label' => 'Файл',
          'type' => 'file',
            'validators' => array(
                //'File_Exists',
            ),
        ),
  ),
);
