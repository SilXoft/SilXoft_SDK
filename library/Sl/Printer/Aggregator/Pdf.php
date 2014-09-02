<?php
namespace Sl\Printer\Aggregator;

class Pdf extends \Sl\Printer\Aggregator {
    
    /**
     * Сливает все pdf-ы в один и отдает
     * 
     * @param array $files
     * @return \Zend_Pdf
     */
    protected function _mergeResult(array $files, $save = null) {
        $pdf = new \Zend_Pdf();
        $pages = array();
        foreach($files as $filename) {
            try {
                $pdf_file = new \Zend_Pdf($filename, null, true);
                foreach($pdf_file->pages as $p) {
                    $pages[] = clone $p;
                } 
            } catch (\Exception $e) {

            }
        }
        $pdf->pages = $pages;
        if($save) {
            return $pdf->save($save);
        } else {
            return $pdf->render();
        }
    }

}