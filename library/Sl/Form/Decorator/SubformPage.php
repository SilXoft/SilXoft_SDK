<?php

namespace Sl\Form\Decorator;

class SubformPage extends \Zend_Form_Decorator_FormElements {
    
        public function render($content)
    {
            
          //  error_reporting(E_ERROR);
        $form    = $this->getElement();

        if ((!$form instanceof \Zend_Form) && (!$form instanceof \Zend_Form_DisplayGroup)) {
            
            return $content;
        }

        $belongsTo      = ($form instanceof \Zend_Form) ? $form->getElementsBelongTo() : null;
        $elementContent = '';
        $displayGroups  = ($form instanceof \Zend_Form) ? $form->getDisplayGroups() : array();
        $separator      = $this->getSeparator();
        $translator     = $form->getTranslator();
        $items          = array();
        $view           = $form->getView();
     
        foreach ($form as $item) {
            
            $item->setView($view)->setTranslator($translator);
            if ($item instanceof \Zend_Form_Element) {
              //  $item->setAttrib('bb',$belongsTo.'-'.$item->getAttrib('id'));
              //  var_dump($item->getName());
                
               
                foreach ($displayGroups as $group) {
                    
                    $elementName = $item->getName();
                    $element     = $group->getElement($elementName);                  
                    //$element->setAttrib('bb2','test');
                    if ($element) {
                        // Element belongs to display group; only render in that
                        // context.
                        continue 2;
                    }
                }
                
                $item->setBelongsTo($belongsTo);
            } elseif (!empty($belongsTo) && ($item instanceof \Zend_Form)) {
                if ($item->isArray()) {
                    $name = $this->mergeBelongsTo($belongsTo, $item->getElementsBelongTo());                    
                    
                    $item->setElementsBelongTo($name, true);
                    $item->setAttrib('bb2','test');
                    
                } else {
                    $item->setElementsBelongTo($belongsTo, true);
                    //$item->setAttrib('bb2','test');
                    
                    
                }
            } 
            
            elseif (!empty($belongsTo) && ($item instanceof \Zend_Form_DisplayGroup)) {
                foreach ($item as $element) {
                   //print_r($element); 
                    //$element->setAttrib('bb2','test');
                }
            }
            

//           $items[] = $item->render();

            if (($item instanceof \Zend_Form_Element_File)
                || (($item instanceof \Zend_Form)
                    && (\Zend_Form::ENCTYPE_MULTIPART == $item->getEnctype()))
                || (($item instanceof \Zend_Form_DisplayGroup)
                    && (\Zend_Form::ENCTYPE_MULTIPART == $item->getAttrib('enctype')))
            ) {
                if ($form instanceof \Zend_Form) {
                    $form->setEnctype(\Zend_Form::ENCTYPE_MULTIPART);
                } elseif ($form instanceof \Zend_Form_DisplayGroup) {
                    $form->setAttrib('enctype', \Zend_Form::ENCTYPE_MULTIPART);
                }
            }
        }
        //$elementContent = implode($separator, $items);
        return '';
/*
        switch ($this->getPlacement()) {
            case self::PREPEND:
                return $elementContent . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $elementContent;
        }
        */
    }
    
}