<?php
namespace Sl\Module\Auth\Listener;

class Password extends \Sl_Listener_Abstract implements \Sl_Listener_Model_Interface {

    public function onBeforeSave(\Sl_Event_Model $event) {

    }

    public function onAfterSave(\Sl_Event_Model $event) {
        $model = $event->getModel();
        $before_model = $event->getModelBeforeUpdate();
               
        if($model instanceof \Sl\Module\Auth\Model\User) {
            if(!$before_model->getId() && $model->getId()) {
                $password = substr(md5(time()), rand(1, 5), 10);
                $h_password = \Sl\Service\Helper::hash($password);
                $login = $model->getLogin();
                if(!\Zend_Validate::is($model->getEmail(), 'EmailAddress')) {
                    throw new \Exception('Поле Email вказано невірно.');
                }
                \Sl_Model_Factory::mapper('user', 'auth')->passwordUpdate($model->getId(), $h_password);
                $translator = \Zend_Registry::get('Zend_Translate');
                $mail = new \Zend_Mail('UTF-8');
                $mail->addTo($model->getEmail(), $model->getName());
                if ($copy_email = \Sl_Service_Settings::value('COPY_USERS_EMAIL'))  $mail->addBcc($copy_email);
                $mail->setSubject($translator->translate('Login info'));
                 $pfid = \Sl_Service_Settings::value('PFID_NEW_USER_LOGPASS');
                 $form = \Sl_Model_Factory::mapper('printform', \Sl_Module_Manager::getInstance()->getModule('home'))
                            ->find($pfid);
                    
             
            if (!$form) {
                throw new \Exception($translator->translate('Can\'t find needed subform.') . __METHOD__);
            }
               
               $printer = \Sl\Printer\Manager::getPrinter($form);
               $printer->setCurrentObject($model);
               $printer->addExtras(array(
                            'user.login' => $login,
                            'user.password' => $password,
                        ));
                $filename = '/tmp/'.md5($model->getId());
                $printer->printIt(null,null,$filename);
                
                $message = file_get_contents($filename);
                unlink($filename);
                
                $mail->setBodyHtml($message);
                $mail->send();
            }
        }
    }
}
