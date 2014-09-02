<?php
namespace Sl\Module\Customers\Controller;

class Lead extends \Sl_Controller_Model_Action {
    
    public function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        parent::__construct($request, $response, $invokeArgs);
        /*if(!in_array($this->getRequest()->getActionName(), array('import', 'jsonurlimport', 'ajaxlist', 'list', 'nlist'))) {
            throw new \Exception('Not implemented.');
        }*/
    }
    
    public function jsonurlimportAction() {
        set_time_limit(300);
        try {
            if($this->getRequest()->isPost()) {
                $url = $this->getRequest()->getParam('url', '');
                $content = strip_tags(file_get_contents($url));
                //error_reporting(E_ALL);
                
                $content = str_replace('\\ ', '_ ', strip_tags(file_get_contents($url)));
                $content = mb_substr($content, 3, mb_strlen($content, 'UTF-8')-6, 'UTF-8');
                $matches = array();
                if(preg_match_all('/(\{.+\},)/', $content, $matches)) {
                    $data = array_map(function($el){ return json_decode('{'.$el.'}', true); }, explode('},{', $content));
                }
                $lead_tpl = \Sl_Model_Factory::object('lead', 'customers');
                foreach($data as $k=>$v) {
                    //$lead = clone $lead_tpl;
                    $lead = null;
                    $email = false;
                    if(isset($v['email-898']) && $v['email-898']) $email = $v['email-898'];
                    if(isset($v['email-606']) && $v['email-606']) $email = $v['email-606'];
                    
                    if($email) {
                        $lead = \Sl_Model_Factory::mapper($lead_tpl)->findByEmail($email);
                    }
                    if(!$lead) {
                        $lead = clone $lead_tpl;
                        $lead->setEmail($email);
                    }
                    /*@var $lead \Sl\Module\Customers\Model\Lead*/
                    if(isset($v['text-257']) && $v['text-257']) {
                        $name = implode(' ', array_diff(array(
                            isset($v['text-257'])?$v['text-257']:'',
                            isset($v['text-662'])?$v['text-662']:'',
                            isset($v['text-951'])?$v['text-951']:'',
                        ), array('')));
                        $lead->setName($name);
                    }
                    if(isset($v['menu-778']) && $v['menu-778']) {
                        $lead->setDestinationCountry($v['menu-778']);
                    }
                    if(isset($v['text-543']) && $v['text-543']) {
                        $lead->setDestinationCity($v['text-543']);
                    }
                    if(isset($v['menu-593']) && $v['menu-593']) {
                        $lead->setCountry($v['menu-593']);
                    }
                    if(isset($v['menu-775']) && $v['menu-775']) {
                        $lead->setDeliveryType($v['menu-775']);
                    }
                    if(isset($v['text-911']) && $v['text-911']) {
                        $lead->setWeight($v['text-911']);
                    }
                    if(isset($v['text-116']) && $v['text-116']) {
                        $lead->setVolume($v['text-116']);
                    }
                    if(isset($v['menu-45']) && $v['menu-45']) {
                        $lead->setCategory($v['menu-45']);
                    }
                    try {
                        \Sl_Model_Factory::mapper($lead)->save($lead, false, false);
                    } catch(\Exception $e) {
                        \Sl\Module\Home\Service\Errors::addError($e->getMessage());
                    }
                    unset($lead);
                }
            } else {
                \Sl\Module\Home\Service\Errors::addError('Email, ФИО, страна получения, город получения, страна отправления, тип доставки, вес, объем, категория груза', 'Импортируются только определенные поля');
            }
        } catch (\Exception $e) {
            \Sl\Module\Home\Service\Errors::addError($e->getMessage());
        }
    }
}