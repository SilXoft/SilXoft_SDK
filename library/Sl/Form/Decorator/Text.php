<?php
namespace Sl\Form\Decorator;

class Text extends \Zend_Form_Decorator_HtmlTag {
    
    public function render($content) {
        $tag       = $this->getTag();
        $placement = $this->getPlacement();
        $cont = $this->getOption('content');
        $this->removeOption('content');
        $this->removeOption('openOnly');
        $this->removeOption('closeOnly');
        
        $attribs = $this->getOptions();
        
        switch ($placement) {
            case self::APPEND:
                return $content
                     . $this->_getOpenTag($tag, $attribs)
                     . $cont
                     . $this->_getCloseTag($tag);
            case self::PREPEND:
                return $this->_getOpenTag($tag, $attribs)
                     . $cont
                     . $this->_getCloseTag($tag)
                     . $content;
            default:
                return $this->_getOpenTag($tag, $attribs) . $content . $cont . $this->_getCloseTag($tag);
        }
    }
}