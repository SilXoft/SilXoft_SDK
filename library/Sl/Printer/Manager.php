<?php
namespace Sl\Printer;

class Manager {
    
    protected static $_printers = array();
    
    protected static $_module;
    protected static $_printform_tpl;
    
    const MODULE_NAME = 'home';
    
    /*public static function cleanPrinters() {
        self::$_printers = array();
    }
    
    public static function setPrinters(array $printers = array()) {
        self::cleanPrinters();
        self::addPrinters($printers);
    }
    
    public static function addPrinters(array $printers = array()) {
        foreach($printers as $printer) {
            if($printer instanceof Printer) {
                self::addPrinter($printer);
            }
        }
    }
    
    public static function addPrinter(Printer $printer) {
        self::$_printers[$printer->getName()] = $printer;
    }
    
    public static function getPrinter($type = null) {
        if(is_null($type)) {
            return self::$_printers;
        } else {
            return isset(self::$_printers[$type])?self::$_printers[$type]:array();
        }
    }*/
    
    protected static function _getModule() {
        if(!isset(self::$_module)) {
            self::$_module = \Sl_Module_Manager::getInstance()->getModule(self::MODULE_NAME);
        }
        return self::$_module;
    }
    
    protected static function _emptyPrintformTemplate() {
        if(!isset(self::$_printform_tpl)) {
            self::$_printform_tpl = \Sl_Model_Factory::object('printform', self::_getModule());
        }
        return self::$_printform_tpl;
    }
    
    public static function getAvailablePrinters(\Sl_Model_Abstract $model, $type = null) {
        $printer_type = self::type($model);
        if(isset(self::$_printers[$printer_type])) {
            if(is_null($type)) {
                return self::$_printers[$printer_type];
            } else {
                return isset(self::$_printers[$printer_type][$type])?self::$_printers[$printer_type][$type]:null;
            }
        } else {
            $ps = \Sl_Model_Factory::mapper(self::_emptyPrintformTemplate())->fetchAllByNameType($printer_type);
            $printers = array();
            foreach($ps as $printer) {
                $printers[$printer->getType()][] = $printer;
            }
            return self::$_printers[$printer_type] = $printers;
        }
    }
    
    public static function type(\Sl_Model_Abstract $model) {
        
        return \Sl\Service\Helper::getModelAlias($model);
       // return md5(get_class($model));
    }
    
    public static function getPrinter(\Sl\Module\Home\Model\Printform $form) {
        $type = $form->Lists('type', $form->getType());
       
        $printer_name = 'Sl\\Printer\\Printer\\'.ucfirst($type);
        if(!class_exists($printer_name)) {
            throw new \Exception('Can\'t find printer "'.$printer_name.'".'.__METHOD__);
        }
        $printer = new $printer_name();
        $template_name = 'Sl\\Printer\\Template\\'.ucfirst($type);
        if(!class_exists($printer_name)) {
            throw new \Exception('Can\'t find template "'.$template_name.'".'.__METHOD__);
        }
        if (!$form->issetRelated('printformfile')) $form = \Sl_Model_Factory::mapper($form)->findRelation($form, 'printformfile') ;
        $tpl_file = $form->fetchRelated('printformfile');
        
        if (count($tpl_file)){
        
        $template = new $template_name(current($tpl_file)->getLocation(), $form->getData());
        }
        else {
            $template = new $template_name(null,$form->getData());
            
        } 
        $printer->setTemplate($template);
        $printer->setMask($form->getMask());
        return $printer;
    }
}
