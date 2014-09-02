<?php
return array (
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
);
