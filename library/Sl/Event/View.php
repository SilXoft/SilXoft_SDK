<?php

class Sl_Event_View extends Sl_Event_Abstract {
    
    protected $_view;
    protected $_page;
    
    public function __construct($type, array $options = array()) {
        if(!isset($options['view']) || !($options['view'] instanceof Sl_View)) {
            throw new Sl_Exception_View('Param \'view\' is required');
        }
        if (isset($options['page'])){$this->setPage($options['page']);}
        $this->setView($options['view']);
        parent::__construct($type, $options);
    }
    
    /**
     * 
     * @param Sl_View $view
     */
    public function setView(Sl_View $view) {
        $this->_view = $view;
    }
    
    /**
     * 
     * @return Sl_View
     */
    public function getView() {
        return $this->_view;
    }
    //Тимчасове опрацювання об'єкту page тут. Має бути перенесене до відповідного івенту, який наразі не 
    //тригериться
    public function setPage(Zend_Navigation_Page_Uri $page) {
        $this->_page = $page;
    }
    
   
    public function getPage() {
        return $this->_page;
    }
}

?>
