<?php
return array (
  'navigation_pages' => 
  array (
  ),
  'forms' => 
  array (
    'model_client_form' => 
    array (
      'name' => 
      array (
        'readonly' => 'readonly',
        'class' => 'span5',
      ),
      'secret' => 
      array (
        'readonly' => 'readonly',
        'class' => 'span5',
      ),
      'redirect_uri' => 
      array (
        'class' => 'span5',
      ),
    ),
  ),
  'modulerelations' => 
  array (
    0 => 
    array (
      'type' => '22',
      'db_table' => 'Sl\\Module\\Api\\Modulerelation\\Table\\Authcodeclient',
      'options' => 
      array (
      ),
    ),
    1 => 
    array (
      'type' => '22',
      'db_table' => 'Sl\\Module\\Api\\Modulerelation\\Table\\Accesstokenclient',
      'options' => 
      array (
      ),
    ),
    2 => 
    array (
      'type' => '21',
      'db_table' => 'Sl\\Module\\Api\\Modulerelation\\Table\\Apiclientuser',
      'options' => 
      array (
      ),
    ),
  ),
);
