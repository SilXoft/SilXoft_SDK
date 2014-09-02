<?php
require_once APPLICATION_PATH . '/../library/Ext/Form.php';
class Dashboard_Form_Role_Create extends Ext_Form {

    public function init() {
$this->setMethod('POST');
        
$translate= Zend_Registry::isRegistered('Zend_Translate')?Zend_Registry::get('Zend_Translate'):null;


        $default_decorators = array(
            'ViewHelper',
            'Errors',
            array(array('div'=>'HtmlTag'), array('tag'=>'div')),
            array('Label', array('palcement'=>'prepend')),
            array(array('section'=>'HtmlTag'), array('tag'=>'section'))
        );

        $name = new Zend_Form_Element_Text('name', array(
            'disableLoadDefaultDecorators' => true,
            'label' => $translate->_('Название'),
            'required'   => true,
            'decorators' => $default_decorators,
        ));
        
          $nikname = new Zend_Form_Element_Text('nikname', array(
            'disableLoadDefaultDecorators' => true,
            'label' => $translate->_('Псевдоним'),
            'required'   => true,
            'decorators' => $default_decorators,
        ));      
        
        $description= new Zend_Form_Element_Text('description', array(
            'disableLoadDefaultDecorators' => true,
            'label' => $translate->_('Описание'),
            'required'   => true,
            'decorators' => $default_decorators,
        ));




        $submit = new Zend_Form_Element_Submit('submit', array(
            'label'=>$translate->_('Добавить'),
        ));





        $this->addElement($name);
        $this->addElement($nikname);
        $this->addElement($description);
        $this->addElement($submit);



        $this->removeDecorator('DtDdWrapper', 'HtmlTag');
        $this->setDecorators(array(
            new Zend_Form_Decorator_FormElements(),
            new Zend_Form_Decorator_HtmlTag(array('tag'=>'fieldset')),
            new Zend_Form_Decorator_Form(),
        ));
    }
}

?>
