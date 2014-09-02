<?php
namespace Sl\Validate\Required;

class Item extends \Zend_Validate_Abstract {
    
	protected $_model_class;
	protected $_data_key;
	
	const IS_EMPTY        = 'isEmpty';
   

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::IS_EMPTY        => "This field is empty",
       
    );
	
    public function __construct($model_class) {
    	
		$this->_model_class = isset($model_class['class_name'])?$model_class['class_name']:null;
		$this->_data_key = isset($model_class['field'])?$model_class['field']:null;
        //parent::__construct();
      
    }
    
    public function isValid($value, $context = null) {
         	
		$clear_model = \Sl_Model_Factory::object($this->_model_class);	
		
		if (isset($context[$this->_data_key]) && is_array($context[$this->_data_key])){
			foreach($context[$this->_data_key] as $element_options){
				if (!$element_options['delete']){
					$model = clone $clear_model;
					$model->setOptions($element_options);
                    if (!$model->isEmpty()){
						return true;
					}
				}	
				
								
			}
			
		}	
		
		
		
		$this->_error(self::IS_EMPTY);
		 
      	return FALSE;
       

        
    }
}