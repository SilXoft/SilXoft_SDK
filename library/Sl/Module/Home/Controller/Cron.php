<?php
namespace Sl\Module\Home\Controller;

class Cron extends \Sl_Controller_Action {
    
    
    
    public function cronAction() {
        try {
            $jobs = \Sl_Model_Factory::mapper('cronjob', 'home')->fetchAll();
            // @TODO: Нужно сделать, чтобі запускались все задания. Пока же сделаем то, что нам нужно
            \Sl_Event_Manager::trigger(new \Sl\Event\Cron('run'));
        } catch(\Exception $e) {
            
        }
        die;
    }
    
    
}