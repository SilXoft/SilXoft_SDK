<?php
namespace Sl\Printer\Template;

class Gspreadsheet extends Sl\Printer\Template {
    
    public function __construct($name, $file) {
        parent::__construct($name, $file);
        
    }
    
    /*
     public function __construct($name, $file) {
        parent::__construct($name, $file);
        $xls = \PHPExcel_IOFactory::load($this->getTpl());
        if(!$xls) {
            throw new \Exception('Can\'t load "'.$this->getTpl().'" file. '.__METHOD__);
        }
        $this->_excel_obj = $xls;
    }
     */
    
    public function render(array $data, array $templates) {
        
    }
}