<?php namespace Sl\Module\Home\Informer;
class Item {
    protected $_title;
    protected $_id;
    protected $_show_icon;
    protected $_icon;
    protected $_show_badge;
    protected $_badge;
    protected $_badge_default_visibility = true;
    protected $_content;
    protected $_short_content;
    protected $_view;
    CONST ICON_PATH = '/img/png/small-white/';
    public function __toString() {
        try {
           $this->getView()->item = $this; 
           $this->getView()->icon_path = self::ICON_PATH;
             
           return $this->getView()->render('informeritem.phtml');
        } catch(\Exception $e) {
            return '';
        }
    }
    
    
    public function getView() {
        if(!isset($this->_view)) {
            $this->_view = new \Sl_View(array('scriptPath' => \Sl_Module_Manager::getViewDirectory('home')));
        }
        return $this->_view;
    }
    
    public function setView(\Sl_View $view) {
        $this->_view = $view;
        return $this;
    }
    
    public function setId($id){
        $this->_id = $id;
        return $this;
    }
    
    public function getId(){
        return $this->_id;
    }
    
    public function setContent($content){
        $this->_content = $content;
        return $this;
    }
    
    public function getContent(){
        return $this->_content;
    }
        
    public function setShortContent($short_content){
        $this->_short_content = $short_content;
        return $this;
    }
    
    public function getShortContent(){
        return $this->_short_content;
    }
    public function setBadge($badge){
        $this->_badge = $badge;
        return $this;
    }
    
    public function getBadge(){
        return $this->_badge;
    }
        
    public function setShowBadge($show_badge){
        $this->_show_badge = $show_badge;
        return $this;
    }
    
    public function getShowBadge(){
        return $this->_show_badge;
    }
    
    
    public function setBadgeDefaultVisibility($badge_default_visibility){
        $this->_badge_default_visibility = $badge_default_visibility;
        return $this;
    }
    
    public function getBadgeDefaultVisibility(){
        return $this->_badge_default_visibility;
    }
        
    public function setIcon($icon){
        $this->_icon = $icon;
        return $this;
    }
    
    public function getIcon(){
        return $this->_icon;
    }
        
    public function setShowIcon($show_icon){
        $this->_show_icon = $show_icon;
        return $this;
    }
    
    public function getShowIcon(){
        return $this->_show_icon;
    }
    
    public function setTitle($title){
        $this->_title = $title;
        return $this;
    }
    
    public function getTitle(){
        return $this->_title;
    }
    
    
} 