<?php
return array (
  '_default' => 
  array (
    'name' => '_default',
    'description' => 'По-умолчанию',
    'filter' => 
    array (
      'type' => 'multi',
      'comparison' => 1,
      'comps' => 
      array (
        '_system' => 
        array (
          'type' => 'multi',
          'comparison' => 1,
          'comps' => 
          array (
            'active' => 
            array (
              'type' => 'eq',
              'field' => 'active',
              'value' => 1,
            ),
          ),
        ),
        '_user' => 
        array (
          'type' => 'multi',
          'comparison' => 2,
          'comps' => 
          array (
            '_custom' => 
            array (
              'type' => 'multi',
              'comparison' => 1,
              'comps' => 
              array (
              ),
            ),
            '_id' => 
            array (
              'type' => 'in',
              'field' => 'id',
              'value' => 
              array (
              ),
            ),
          ),
        ),
      ),
    ),
  ),
);
