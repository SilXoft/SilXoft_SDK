<?php

namespace Sl\Form\Decorator;

class Accordionsubform extends \Zend_Form_Decorator_HtmlTag {

    public function render($content) {
         $button = '';
        $content_accordion = '';
        $group_accordion = '';
        
        $i=0;
        
        foreach ($this->getElement()->getDisplayGroups() as $display_group => $tab) {
            
            $id_accordion = $tab->getForm()->getName();
            $button_create = $this->getNameElement(array_keys($tab->getElements()), 'create');
            $button_btn = $this->getNameElement(array_keys($tab->getElements()), 'btn');
            
            $button = '
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#'.$tab->getForm()->getName().'" href="#'.$tab->getName().'_accordion">
                ' . $tab->getLabel() . ' ' . $tab->getElement($button_create) . ' ' . $tab->getElement($button_btn) . '</a></div>';
            

            $tab->removeElement($button_create);
            $tab->removeElement($button_btn);
            $tab->removeAttrib('label');
            $content_accordion = ' <div id="'.$tab->getName().'_accordion" class="accordion-body collapse in">
                                        <div class="accordion-inner">
                                                ' . $tab . '
                                    </div>
                                </div>'; 
            
            $group_accordion .= '<div class="accordion-group">'.($button.$content_accordion).'</div>';
            
            //var_dump($group_accordion);
            $i++;
            
        }
        
        $string ='<div class="accordion" id="'.$id_accordion.'">'.$group_accordion.'</div>';

        $placement = $this->getPlacement();
        $this->removeOption('placement');

        $attribs = $this->getOptions();

        switch ($placement) {
            case self::PREPEND:
                return $string . $content;
            case self::APPEND:
            default:
                return $string;
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