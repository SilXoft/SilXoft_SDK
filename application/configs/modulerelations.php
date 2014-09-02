<?php
return array (
  'menu' => 
  array (
  ),
  'home' => 
  array (
    'home.printform' => 
    array (
      'printformfile' => 
      array (
        'type' => '3',
        'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Printformfile',
      ),
      'attachmentprintform' => 
      array (
        'type' => '22',
        'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Attachmentprintform',
      ),
      'reverseattachmentprintform' => 
      array (
        'type' => '22',
        'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Attachmentprintform',
      ),
    ),
    'home.file' => 
    array (
      'printformfile' => 
      array (
        'type' => '3',
        'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Printformfile',
      ),
    ),
    'home.email' => 
    array (
      'emailemaildetails' => 
      array (
        'type' => '11',
        'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Emailemaildetails',
        'options' => 
        array (
          'customeremails' => 
          array (
            'name' => 'name',
            'phone' => 'customerphones.phone',
            'city' => 'customercity.name',
            'country' => 'customercountry.name',
            'company' => 'company_name',
            'ballans' => 'customerballance.ballance',
          ),
          'contactemail' => 
          array (
            'name' => 'name',
          ),
        ),
      ),
    ),
    'home.emaildetails' => 
    array (
      'emailemaildetails' => 
      array (
        'type' => '11',
        'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Emailemaildetails',
        'options' => 
        array (
          'customeremails' => 
          array (
            'name' => 'name',
            'phone' => 'customerphones.phone',
            'city' => 'customercity.name',
            'country' => 'customercountry.name',
            'company' => 'company_name',
            'ballans' => 'customerballance.ballance',
          ),
          'contactemail' => 
          array (
            'name' => 'name',
          ),
        ),
      ),
    ),
    'home.city' => 
    array (
      'citycountry' => 
      array (
        'type' => 21,
        'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Citycountry',
      ),
    ),
    'home.country' => 
    array (
      'citycountry' => 
      array (
        'type' => 12,
        'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Citycountry',
      ),
    ),
    'home.acnt' => 
    array (
      'parentacnt' => 
      array (
        'type' => 21,
        'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Parentacnt',
      ),
      'reverseparentacnt' => 
      array (
        'type' => 12,
        'db_table' => 'Sl\\Module\\Home\\Modulerelation\\Table\\Parentacnt',
      ),
    ),
  ),
  'auth' => 
  array (
    'auth.restriction' => 
    array (
      'restrictionroles' => 
      array (
        'type' => '22',
        'db_table' => 'Sl\\Module\\Auth\\Modulerelation\\Table\\Restrictionroles',
      ),
    ),
    'auth.role' => 
    array (
      'restrictionroles' => 
      array (
        'type' => '22',
        'db_table' => 'Sl\\Module\\Auth\\Modulerelation\\Table\\Restrictionroles',
      ),
      'userroles' => 
      array (
        'type' => 22,
        'db_table' => 'Sl\\Module\\Auth\\Modulerelation\\Table\\Userroles',
        'custom_configs' => true,
      ),
    ),
    'auth.user' => 
    array (
      'usersetting' => 
      array (
        'type' => '11',
        'handling' => true,
        'db_table' => 'Sl\\Module\\Auth\\Modulerelation\\Table\\Usersetting',
      ),
      'userroles' => 
      array (
        'type' => 22,
        'db_table' => 'Sl\\Module\\Auth\\Modulerelation\\Table\\Userroles',
        'custom_configs' => true,
      ),
    ),
    'auth.setting' => 
    array (
      'usersetting' => 
      array (
        'type' => '11',
        'handling' => true,
        'db_table' => 'Sl\\Module\\Auth\\Modulerelation\\Table\\Usersetting',
      ),
    ),
  ),
  'api' => 
  array (
    'api.authcode' => 
    array (
      'authcodeclient' => 
      array (
        'type' => '21',
        'db_table' => 'Sl\\Module\\Api\\Modulerelation\\Table\\Authcodeclient',
        'options' => 
        array (
        ),
      ),
    ),
    'api.client' => 
    array (
      'authcodeclient' => 
      array (
        'type' => 12,
        'db_table' => 'Sl\\Module\\Api\\Modulerelation\\Table\\Authcodeclient',
        'options' => 
        array (
        ),
      ),
      'accesstokenclient' => 
      array (
        'type' => 12,
        'db_table' => 'Sl\\Module\\Api\\Modulerelation\\Table\\Accesstokenclient',
        'options' => 
        array (
        ),
      ),
      'apiclientuser' => 
      array (
        'type' => '21',
        'db_table' => 'Sl\\Module\\Api\\Modulerelation\\Table\\Apiclientuser',
        'options' => 
        array (
        ),
      ),
    ),
    'api.accesstoken' => 
    array (
      'accesstokenclient' => 
      array (
        'type' => '21',
        'db_table' => 'Sl\\Module\\Api\\Modulerelation\\Table\\Accesstokenclient',
        'options' => 
        array (
        ),
      ),
    ),
    'auth.user' => 
    array (
      'apiclientuser' => 
      array (
        'type' => 12,
        'db_table' => 'Sl\\Module\\Api\\Modulerelation\\Table\\Apiclientuser',
        'options' => 
        array (
        ),
      ),
    ),
  ),
);
