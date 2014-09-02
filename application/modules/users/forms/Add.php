<?php
require_once APPLICATION_PATH . '/../library/Ext/Form.php';
class Users_Form_Add extends Ext_Form {

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
            'label' => $translate->_('Имя'),
            'required'   => true,
            'decorators' => $default_decorators,
        ));


        $email = new Zend_Form_Element_Text('email', array(
            'disableLoadDefaultDecorators' => true,
            'label' => $translate->_('E-Mail'),
            'required'   => true,
            'decorators' => $default_decorators,
        ));

        $password = new Zend_Form_Element_Password('password', array(
            'disableLoadDefaultDecorators' => true,
            'label' => $translate->_('Пароль'),
            'decorators' => $default_decorators,
        ));

        $role = new Zend_Form_Element_Select('role_id', array(
            'disableLoadDefaultDecorators' => true,
            'label' => $translate->_('Права'),
            'decorators' => $default_decorators,
            'multiOptions' => array(
                $translate->_('Роль пользователя') => array(
                    Application_Service_Acl::ADMIN => $translate->_('Администратор'),
                    Application_Service_Acl::USER => $translate->_('Пользователь'),
                    Application_Service_Acl::GUEST => $translate->_('Гость'),
                ),
            ),
        ));



        $submit = new Zend_Form_Element_Submit('submit', array(
            'label'=>$translate->_('Добавить'),
        ));





        $this->addElement($name);
        $this->addElement($email);
        $this->addElement($password);
        $this->addElement($role);
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
