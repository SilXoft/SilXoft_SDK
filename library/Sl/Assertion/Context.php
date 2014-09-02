<?
class Sl_Assertion_Context {
	
	protected $_context;
	protected $_type;
	
	
	 
    public function __construct($context, $type = null) {
        
        $this->setContext($context);
        
        if ($type)
        $this->setType($type);
        
    }
    
    /** 
     * Устанавливает тип контекста. 
     * @param string $type
     * @return Sl_Assertion_Context
     */
    public function setType($type) {
        $this->_type = $type;
        return $this;
    }
    
    /**
     * Возвращает тип события
     * @return string
     */
    public function getType() {
        return $this->_type;
    }
    
	/**
     * Устанавливает контекст. 
     * @return Sl_Assertion_Context
     */
    public function setContext($context) {
        $this->_context = $context;
        return $this;
    }
    
    /**
     * Возвращает контекст
     */
    public function getContext() {
        return $this->_context;
    }
	
}
