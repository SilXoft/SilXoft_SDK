<?php
namespace Sl\Module\Menu\Event;

class Pages extends \Sl_Event_Abstract {
    
    protected $_pages;
    
    public function __construct($type, array $options = array()) {
        if(!isset($options['pages']) || !is_array($options['pages'])) {
            throw new \Exception('Param \'pages\' is required or must me an array.');
        }
        $this->setPages($options['pages']);
        parent::__construct($type, $options);
    }
    
    public function setPages(array $pages) {
        $this->_pages = $pages;
    }
    
    public function getPages() {
        return $this->_pages;
    }
}

