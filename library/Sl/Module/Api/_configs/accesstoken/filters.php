<?php
return array (
  '_default' => 
  array (
    'name' => 'По-умолчанию',
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
              'field' => 'active',
              'type' => 'eq',
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
              'field' => 'id',
              'type' => 'in',
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
