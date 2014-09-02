<?php

class Default_Form_Auth extends Zend_Form
{

 public function init() {
        $this->setMethod('POST');

        //$this->addDecorator(new Zend_Form_Decorator_ViewScript(array(
        //    'viewScript'=>'forms/auth.phtml',
        //)));

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
            new Zend_Form_Element_Text('test', array(
                'disableLoadDefaultDecorators'=>true,
                'decorators'=>array(
                    new Sl_Form_Decorator_Acl(array('acl' => new Zend_Acl())),
                    new Zend_Form_Decorator_ViewHelper(),
                    new Zend_Form_Decorator_Label(),
                )
            )),
            new Zend_Form_Element_Submit('submit', array(
                'disableLoadDefaultDecorators'=>true,
                'decorators'=>array(
                    new Zend_Form_Decorator_ViewHelper(),
                ),
                'label'=>'Войти',
            )),
        ));

}
}
