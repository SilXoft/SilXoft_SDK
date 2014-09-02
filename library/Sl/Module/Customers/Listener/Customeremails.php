<?php

namespace Sl\Module\Customers\Listener;

use Sl\Module\Customers as Mod;

class Customeremails extends \Sl_Listener_Abstract implements \Sl_Listener_Model_Interface {

    public function onAfterSave(\Sl_Event_Model $event) {
        $model = $event->getModel();
        $saved_model = $event->getModelBeforeUpdate();

        $translator = \Zend_Registry::get('Zend_Translate');

        // Email-а контрагенту при создании
        // Если утановлен влажок отправлять Email
        if ($model instanceof Mod\Model\Customer) {
            if (!$saved_model->getId() && $model->getId()) {
                // Пользователь создан
                /* @var $model \Sl\Module\Customers\Model\Customer */
                if ($model->getNotifyEmail()) {
                    try {
                        $mail = new \Zend_Mail('UTF-8');

                        $to_name = implode(' ', array_diff(array(
                            ucfirst($model->getLastName()),
                            $model->getFirstName() ? (mb_strtoupper(mb_substr($model->getFirstName(), 0, 1, 'UTF-8'), 'UTF-8') . '.') : null,
                            $model->getMiddleName() ? (mb_strtoupper(mb_substr($model->getMiddleName(), 0, 1, 'UTF-8'), 'UTF-8') . '.') : null,
                        ), array(null)));

                        $need_relations = array(
                            'customeremails',
                            'customerphones'
                        );

                        if (\Sl_Module_Manager::getInstance()->getModule('logistic')) {
                            $need_relations[] = 'customeridentifiercustomer';
                        }

                        if (\Sl_Module_Manager::getInstance()->getModule('products')) {
                            $need_relations[] = 'stockcustomer';
                        }

                        $model = \Sl_Model_Factory::mapper($model)->findExtended($model->getId(), $need_relations);

                        $emails = $model->fetchRelated('customeremails');
                        $phones = $model->fetchRelated('customerphones');

                        if(\Sl_Module_Manager::getInstance()->getModule('logistic')) {
                            $identifier = $model->fetchRelated('customeridentifiercustomer');
                        }

                        if (\Sl_Module_Manager::getInstance()->getModule('products')) {
                            $stocks = $model->fetchRelated('stockcustomer');
                        }

                        if (count($emails) && count($phones)) {
                            $identifier = current($identifier);
                            $stock = current($stocks);

                            /* @var $email \Sl\Module\Home\Model\Email */
                            $mail->addBcc('s.kachan@silencatech.com', $to_name);
			    if ($copy_email = \Sl_Service_Settings::value('COPY_USERS_EMAIL'))  $mail->addBcc($copy_email);
                            //$mail->addTo('demchuk@silencatech.com', $to_name);
                            foreach($emails as $em) {
				$mail->addTo($em->getMail(), $to_name);
			    }
                            //$mail->addTo('s.kachan@silencatech.com', 'Сергей');
                            $subject = $translator->translate('Вам присвоен индивидуальный код получателя грузов %CUSTOMER_ID%');
                            $mail->setSubject(preg_replace(array(
                                '/%CUSTOMER_ID%/'
                            ), array(
                                $identifier?$identifier->getName():'-'
                            ), $subject));
                            
                            /* Внедряем принтформы */
                            try {
                                $printforms = \Sl_Model_Factory::mapper('printform', \Sl_Module_Manager::getInstance()->getModule('home'))
                                        ->fetchAllByNameType(\Sl\Printer\Manager::type($model));
                                foreach($printforms as $k=>$printform) {
                                    if(!$printform->isEmail()) {
                                        unset($printforms[$k]);
                                        continue;
                                    }
                                }
                                $form = null;
                                switch (count($printforms)) {
                                    case 0:
                                        throw new \Exception($translator->translate('There are no printforms for this object.') . __METHOD__);
                                        break;
                                    default:
                                        $form = current($printforms);
                                        break;
                                }
                                if (!$form) {
                                    throw new \Exception($translator->translate('Can\'t find needed subform.') . __METHOD__);
                                }
                                $printfile_relation = \Sl_Modulerelation_Manager::getRelations($form, 'printformfile');
                                $form = \Sl_Model_Factory::mapper($form)->findRelation($form, $printfile_relation); 
                                $printer = \Sl\Printer\Manager::getPrinter($form);
                                if(!($printer instanceof \Sl\Printer\Printer\Txt)) {
                                    throw new \Exception('Supports only Txt printer');
                                }
                                $printer->setCurrentObject($model,array(
                                    'identifier' => array($identifier),
                                    'stock' => array($stock),
                                    'emails' => $emails,
                                    'phones' => $phones,
                                ));
                                $fname = '/tmp/'.md5(time());
                                $printer->printIt(null, array(), $fname);
                                $tpl = file_get_contents($fname);
                                unlink($fname);
                                /* Внедряем принтформы */
                            } catch(\Exception $e) {
                                $view = $this->_getView();

                                $tpl = $view->partial('partials/email.phtml', array(
                                    'pretty_name' => $to_name,
                                    'identifier' => $identifier?$identifier->getName():'',
                                    'email' => implode(', ', array_map(function($el) { return $el->getMail(); }, $emails)),
                                    'customer' => $model,
                                    'phone' => implode(', ', array_map(function($el) { return $el->getPhone(); }, $phones)),
                                    'stock' => $stock?$stock->getName():'',
                                ));
                            }
                            $mail->setBodyHtml($tpl);
                            $mail->send();
                        }
                    } catch(\Exception $e) {
                        // Ничего не делаем. Просто не шлем ничего.
                        //echo $e->getMessage();
                        //die;
                    }
                }
            }
        }

        if (\Sl_Module_Manager::getInstance()->getModule('logistic')) {
            // Изменение идентификатора пользователя
            if($model instanceof \Sl\Module\Logistic\Model\Customeridentifier) {
                if ($model->getId() && $saved_model->getId()) {
                    // Мы на изменении
                    if($model->getName() != $saved_model->getName()) {
                        // То, что мы и хотим
                        if(!$model->issetRelated('customeridentifiercustomer')) {
                            $model = \Sl_Model_Factory::mapper($model)->findExtended($model->getId(), 'customeridentifiercustomer');
                        }
                        $customers = $model->fetchRelated('customeridentifiercustomer');
                        if(count($customers) != 1) {
                            throw new \Exception('Something wrong with \'customeridentifiercustomer\' relation');
                        }
                        $customer = \Sl_Model_Factory::mapper(current($customers))->findExtended(current($customers)->getId(), array(
                            'customeremails'
                        ));
                        unset($customers);
                        if (!$customer) {
                            throw new \Exception('Can\'t find related \'customer\'');
                        }
                        if($customer->getNotifyEmail()) {
                            if (!count($customer->fetchRelated('customeremails'))) {
                                throw new \Exception('No emails assigned to related \'customer\'');
                            }
                            $email = current($emails);
                            /* @var $email \Sl\Module\Home\Model\Email */
                            $to_name = implode(' ', array(
                                $customer->getLastName(),
                                $customer->getFirstName() ? (mb_strtoupper(mb_substr($customer->getFirstName(), 0, 1, 'UTF-8'), 'UTF-8') . '.') : null,
                                $customer->getMiddleName() ? (mb_strtoupper(mb_substr($customer->getMiddleName(), 0, 1, 'UTF-8'), 'UTF-8') . '.') : null,
                            ));

                            $mail = new \Zend_Mail('UTF-8');

                            $mail->addTo('kachan.s.s@gmail.com', $to_name);
                            $mail->setSubject($translator->translate('Identifier changed'));

                            $tpl  = '<h2>Dear customer!</h2>';
                            $tpl .= '<p>We change your identifier to: "%CUST_IDENTIFIER%"</p>';
                            $tpl .= '<p>You should use new identifier for new packages.</p>';
                            $tpl .= '<p>Old packages you can track using old identifier</p>';
                            $tpl .= '<br /><br />';
                            $tpl .= '<p>Best regards</p>';
                            $tpl .= '<p>Your %COMPANY_NAME%</p>';

                            $message = $translator->translate($tpl);

                            $mail->setBodyHtml(preg_replace(array(
                                                '/%CUST_IDENTIFIER%/',
                                                '/%COMPANY_NAME%/',
                                            ), array(
                                                $model->getName(),
                                                'Cargo80',
                                            ), $message));
                            $mail->send();
                        }
                    }
                }
            }
        }
    }

    public function onBeforeSave(\Sl_Event_Model $event) {
        
    }
    
    protected function _getView() {
        if(!isset($this->_view)) {
            $this->_view = new \Sl_View(array('scriptPath' => APPLICATION_PATH.'/'.\Sl_Module_Manager::getViewDirectory($this->getModule()->getName())));
        }
        return $this->_view;
    }

}
