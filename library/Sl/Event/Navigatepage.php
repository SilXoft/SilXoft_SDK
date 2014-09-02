<?php

class Sl_Event_Navigatepage extends \Sl_Event_Abstract {

    protected $_view;
    protected $_page;

    public function __construct($type, array $options = array()){         
        
        if (isset($options['page'])){$this->setPage($options['page']);}
        $this->setPage($options['page']);
        parent::__construct($type, $options);
    }


    public function setPage(Zend_Navigation_Page_Uri $view) {
        $this->_view = $view;
    }

    /**
     * 
     * @return Sl_View
     */
    public function getPage() {
        return $this->_view;
    }
}

