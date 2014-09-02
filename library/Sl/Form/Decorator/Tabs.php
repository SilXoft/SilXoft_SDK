<?php

namespace Sl\Form\Decorator;

class Tabs extends \Zend_Form_Decorator_HtmlTag {

    public function render($content) {

        $button = '';
        $content_tab = '';
        $i=0;
    
        foreach ($this->getElement()->getDisplayGroups() as $display_group => $tab) {


            $button_create = $this->getNameElement(array_keys($tab->getElements()), 'create');
            $button_btn = $this->getNameElement(array_keys($tab->getElements()), 'btn');
            
            $button .= '<li '.(($i==0) ? 'class="active"' : '' ).'><a href="#' . $tab->getName() . '_tab" data-toggle="tab">' . $tab->getLabel() . ' ' . $tab->getElement($button_create) . ' ' . $tab->getElement($button_btn) . '</a></li>';

            $tab->removeElement($button_create);
            $tab->removeElement($button_btn);
            $tab->removeAttrib('label');

            $content_tab .= '<div class="tab-pane '.(($i==0) ? 'active' : '' ).'" id="' . $tab->getName() . '_tab">' . $tab . '</div>';
            $i++;

        }
        $tab_content = '<div class="tab-content ">' . $content_tab . '</div>';
        $head = '<ul class="nav nav-tabs">' . $button . '</ul>';

        $string = $head . $tab_content . '';

        $placement = $this->getPlacement();
        $this->removeOption('placement');

        $attribs = $this->getOptions();

        switch ($placement) {
            case self::PREPEND:
                return $string . $content;
            case self::APPEND:
            default:
                return $content . $string;
        }
    }

    function getNameElement($fields = array(), $name = '') {
        if (count($fields > 0)) {
            foreach ($fields as $field) {
                if (preg_match('/^modulerelation_(.+)_' . $name . '$/', $field)) {
                    return $field;
                }
            }
        }
    }

}