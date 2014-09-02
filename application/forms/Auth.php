<?php

class Application_Form_Auth extends Zend_Form {

    public function init() {
        $this->setMethod('POST');

        $this->addDecorator(new Zend_Form_Decorator_ViewScript(array(
            'viewScript'=>'forms/auth.phtml',
        )));

        $this->addElements(array(
            new Zend_Form_Element_Text('email', array(
                'disableLoadDefaultDecorators'=>true,
                'decorators'=>array(
                    new Zend_Form_Decorator_ViewHelper(),
                    new Zend_Form_Decorator_Label(),
                ),
                'placeholder'=>'Логин или e-mail',
            )),
            new Zend_Form_Element_Password('password', array(
                'disableLoadDefaultDecorators'=>true,
                'decorators'=>array(
                    new Zend_Form_Decorator_ViewHelper(),
                    new Zend_Form_Decorator_Label(),
                ),
                'placeholder'=>'Пароль',
            )),
            new Zend_Form_Element_Submit('submit', array(
                'disableLoadDefaultDecorators'=>true,
                'decorators'=>array(
                    new Zend_Form_Decorator_ViewHelper(),
                ),
                'label'=>'Войти',
            )),
        ));
 //       $this->addElement($email);
 //       $this->addElement($pass);
  //      $this->addElement($submit);

        //TODO: Сделать по-человечески
        /*
        $default_decorators = array(
            'ViewHelper',
            array(array('div'=>'HtmlTag'), array('tag'=>'div')),
            array('Label', array('placement'=>'prepend')),
            array(array('section'=>'HtmlTag'), array('tag'=>'section')),
        );

        $email = new Zend_Form_Element_Text('email', array(
            'disableLoadDefaultDecorators'=>true,
            'decorators'=> $default_decorators,
            'placeholder'=>'Логин или e-mail',
        ));

        $pass = new Zend_Form_Element_Password('password', array(
            'disableLoadDefaultDecorators'=>true,
            'decorators'=> $default_decorators,
            'placeholder'=>'Пароль',
        ));

        $fb = new Zend_Form_Element_Button('fb', array(

        ));

        $ga = new Zend_Form_Element_Button('ga', array(

        ));

        $submit = new Zend_Form_Element_Button('submit', array(
            'label'=>'Войти',
        ));

        $this->addElement($email);
        $this->addElement($pass);
        $this->addElement($fb);
        $this->addElement($ga);
        $this->addElement($submit);
        */
    }
}

?>
