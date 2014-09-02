<?php
namespace Sl\Form;

class Form extends \Zend_Form {
    
    public function __construct($options = null) {
        parent::__construct($options);
        $this   ->getPluginLoader('decorator')
                ->addPrefixPath('\\Sl\\Form\\Decorator\\', LIBRARY_PATH.'/Sl/Form/Decorator/');
    }
}