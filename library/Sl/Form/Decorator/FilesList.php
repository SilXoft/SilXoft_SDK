<?php
namespace Sl\Form\Decorator;

class FilesList extends \Zend_Form_Decorator_HtmlTag {
    
    public function render($content) {
        $placement = $this->getPlacement();
        $this->removeOption('placement');
        
        $attribs = $this->getOptions();
        
        switch ($placement) {
            case self::PREPEND:
                return $this->_buildList($attribs)
                     . $content;
            case self::APPEND:
            default:
                return $content
                     . $this->_buildList($attribs);
        }
    }
    
    protected function _buildList($attribs = array()) {
        $items = $this->getOption('items');
        if(!is_array($items)) {
            $items = array();
        }
        $attr_string = '';
        if(count($attribs)) {
            $attr_string = $this->_htmlAttribs($attribs);
        }
        $text  = '<blockquote style="'.(empty($items)?' display: none; ':'').' max-height: 100px; overflow: auto;"><ul '.$attr_string.'>'.PHP_EOL;
        foreach($items as $item) {
            $text .= '<li><a href="'.$item['href'].'"'.($item['target']?' target="'.$item['target'].'"':'').'>'.$item['text'].'</a></li>';
        }
        $text .= '</ul></blockquote>'.PHP_EOL;
        return $text;
    }
}