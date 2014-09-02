<?php

namespace Sl\Module\Customers\Controller;

class Main extends \Sl_Controller_Action {

    public function getemailpflistAction() {
        // error_reporting(E_ALL);
        $model = \Sl_Model_Factory::mapper($this->getRequest()->getParam('ml'), $this->getRequest()->getParam('mle'))
                ->find($this->getRequest()->getParam('id', 0));

        $emails = self::getcustomeremails($model);

        $printforms = \Sl_Model_Factory::mapper('printform', \Sl_Module_Manager::getInstance()->getModule('home'))
                ->fetchAllByNameType(\Sl\Printer\Manager::type($model), array(), 'email');
        foreach ($printforms as $printform) {
            $prinform_array[$printform->getId()] = $printform->getDescription();
        }
        reset($printforms);
        $first_form = current($printforms);
        //print_R(array($printforms, $first_form)); die;
        $firstinfo['name'] = $first_form->getMask();
        $firstinfo['body'] = $first_form->getData();
        if (count($prinform_array)) {
            $this->view->result = true;
            if (count($emails) < 2) {
                $this->view->customeremails = current($emails);
            } else {
                $this->view->customeremails = implode(', ', $emails);
            }

            $this->view->printforms = $prinform_array;
            $this->view->first_form = $firstinfo;
            $this->view->modelidentifire = array('module' => $this->getRequest()->getParam('mle'), 'controller' =>
                $this->getRequest()->getParam('ml'), 'objid' => $this->getRequest()->getParam('id'));
            $this->view->title = '';
        } else {
            $this->view->description = $this->view->translate('Нет доступных печатных форм для этой модели');
        }
    }

    public function ajaxgetprintformAction() {

        $id = $this->getRequest()->getParam('id');
        $printform = \Sl_Model_Factory::mapper('printform', 'home')->find($id);
        $result['body'] = $printform->getData();
        $result['subject'] = $printform->getMask();
        $this->view->result = true;
        $this->view->printform = $result;
    }

    public function ajaxemailfileAction() {
        //print_R($this->getRequest()); die;

        $this->view->result = true;
        try {
            // error_reporting(E_ALL);
            //die($this->getRequest()->getParam('objid'));
            $available_relations = \Sl_Model_Factory::mapper($this->getRequest()->getParam('objmodel'), $this->getRequest()->getParam('objmodule'))
                    ->getAllowedRelations();
            $object = \Sl_Model_Factory::mapper($this->getRequest()->getParam('objmodel'), $this->getRequest()->getParam('objmodule'))
                    ->findExtended($this->getRequest()->getParam('objid', 0), $available_relations);

            $relations = $object->findFilledRelations();
            foreach ($relations as $key => $value) {
                $rl = \Sl_Modulerelation_Manager::getRelations($object, $value);
                if ($rl->getType() == \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER) {
                    $items = $object->fetchRelated($value);
                    foreach ($items as $obj) {
                        $obj = \Sl_Model_Factory::mapper($obj)->findAllowExtended($obj->getId());
                        $item_owner[$value] = $items;
                    }
                }
            }

            $pfid = $this->getRequest()->getParam('pfid', 0);
            $form = \Sl_Model_Factory::mapper('printform', \Sl_Module_Manager::getInstance()->getModule('home'))
                    ->find($pfid);

            if (!$form) {
                throw new \Exception($this->view->translate('Can\'t find needed subform.') . __METHOD__);
            }
            $form->setData($this->getRequest()->getParam('body'));
            $form->setMask($this->getRequest()->getParam('subject'));
            $printfile_relation = \Sl_Modulerelation_Manager::getRelations($form, 'printformfile');
            $atachment_relation = \Sl_Modulerelation_Manager::getRelations($form, 'attachmentprintform');
            $form = \Sl_Model_Factory::mapper($form)->findRelation($form, $printfile_relation);
            $form = \Sl_Model_Factory::mapper($form)->findRelation($form, $atachment_relation);
            $printer = \Sl\Printer\Manager::getPrinter($form);
            $printer->setCurrentObject($object, $item_owner);
            \Sl_Event_Manager::trigger(new \Sl\Event\Printer('beforePrintAction', array(
                'model' => $object,
                'printer' => $printer,
                'printform' => $form,
            )));
            ob_start();
            $printer->printIt();
            $message = ob_get_clean();
            $model = $object;
            /* $customer = \Sl_Model_Factory::object('customer', 'customers');
              $relations = \Sl_Modulerelation_Manager::getObjectsRelations($model, $customer);

              if (count($relations) == 1) {
              $relation = current($relations);

              if (!$model->issetRelated($relation->getName())) {
              $model = \Sl_Model_Factory::mapper($model)->findRelation($model, $relation);
              }
              if (count($model->fetchRelated($relation->getName()))) { */
            $emailstr = $this->getRequest()->getParam('useremail');


            $printform = \Sl_Model_Factory::object('printform', 'home');
            $printform_relations = \Sl_Modulerelation_Manager::getObjectsRelations($form, $printform);
            $printform_relation = $printform_relations['attachmentprintform'];
            if (!$form->issetRelated($printform_relation->getName())) {
                $form = \Sl_Model_Factory::mapper($form)->findRelation($printform, $printform_relation);
            }

            $printforms = $form->fetchRelated($printform_relation->getName());

            $counter = 0;
            foreach ($printforms as $printf) {
                $printfile_relation = \Sl_Modulerelation_Manager::getRelations($printf, 'printformfile');
                $atachment_relation = \Sl_Modulerelation_Manager::getRelations($printf, 'attachmentprintform');
                $printf = \Sl_Model_Factory::mapper($printf)->findRelation($printf, $printfile_relation);
                $printf = \Sl_Model_Factory::mapper($printf)->findRelation($printf, $atachment_relation);
                $cur_printer = \Sl\Printer\Manager::getPrinter($printf);
                $cur_printer->setCurrentObject($object, $item_owner);
                \Sl_Event_Manager::trigger(new \Sl\Event\Printer('beforePrintAction', array(
                    'model' => $object,
                    'printer' => $cur_printer,
                    'printform' => $printf,
                )));
                $name = $cur_printer->fileName();
                $type = $cur_printer->getType();
                $file_name = $name . '.' . $type;
                $dirname = \Sl\Service\Common::guid();
                mkdir($file_puth = APPLICATION_PATH . '/../uploads/' . $dirname);
                $dir_path = APPLICATION_PATH . '/../uploads/' . $dirname;
                $file_path = $dir_path . '/' . $file_name;
                $cur_printer->printIt(null, array(), $file_path);
                $content[$counter] = $file_path;
                $counter++;
            }
            /*    $customer = $model->fetchOneRelated($relation->getName());
              $email = \Sl_Model_Factory::object('email', 'home');
              $email_relation = \Sl_Modulerelation_Manager::getObjectsRelations($customer, $email);
              $emails = array();
              foreach ($email_relation as $r) {
              if (!$customer->issetRelated($r->getName())) {
              $customer = \Sl_Model_Factory::mapper($customer)->findRelation($customer, $r);
              }
              $obj_emails = $customer->fetchRelated($r->getName());
              }
             */ $emails = explode(', ', $emailstr);
            $mail = new \Zend_Mail('UTF-8');
            foreach ($emails as $email) {
                $mail->addTo($email);
            }
            //$mail->addTo($emailstr);
            foreach ($content as $key => $value) {
                $at = new \Zend_Mime_Part(file_get_contents($file_path));
                $at_header = $at->getHeadersArray();

                $at->type = $at_header[0][1];
                $at->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
                $at->encoding = \Zend_Mime::ENCODING_BASE64;
                $at->filename = basename($value);
                $mail->addAttachment($at);
            }
            $mail->setSubject($printer->getMask());
            $mail->setBodyHtml($message);
            $mail->send();
            foreach ($content as $key => $value) {
                unlink($value);
            }
            rmdir($dir_path);
            $this->view->message = $this->view->translate('Письмо отправлено клиенту.');

            //   }
            // } //$this->_redirect($_SERVER['HTTP_REFERER']);
        } catch (\Exception $e) {
            $this->view->result = false;

            throw new \Exception($e);
        }
    }

    protected function getcustomeremails($model) {

        $customer = \Sl_Model_Factory::object('customer', 'customers');
        $relations = \Sl_Modulerelation_Manager::getObjectsRelations($model, $customer);
        if (count($relations) == 1) {
            $relation = current($relations);

            if (!$model->issetRelated($relation->getName())) {
                $model = \Sl_Model_Factory::mapper($model)->findRelation($model, $relation);
            }
            if (count($model->fetchRelated($relation->getName()))) {
                $customer = $model->fetchOneRelated($relation->getName());
                $email = \Sl_Model_Factory::object('email', 'home');
                $email_relation = \Sl_Modulerelation_Manager::getObjectsRelations($customer, $email);
            }
            foreach ($email_relation as $r) {
                if (!$customer->issetRelated($r->getName())) {
                    $customer = \Sl_Model_Factory::mapper($customer)->findRelation($customer, $r);
                }
                $obj_emails = $customer->fetchRelated($r->getName());
            }
            $email_arr = array();
            foreach ($obj_emails as $obj_email) {
                $email_arr[$obj_email->getId()] = $obj_email->getMail();
            }
        } return $email_arr;
    }

}

