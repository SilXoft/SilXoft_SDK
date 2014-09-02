<?php
namespace Sl\Module\Home\Service;
use Sl\Module\Home as Module;

class File {
    
    public static function render(Module\Model\File $file) {
        foreach(self::getHeaders($file) as $header) {
            header($header);
        }
        echo file_get_contents($file->getLocation());
        die;
    }
    
    protected static function getHeaders(Module\Model\File $file) {
        $headers = array();
        switch(pathinfo($file->getLocation(), PATHINFO_EXTENSION)) {
            default:
                $f = new \finfo();
                $headers[] = 'Content-type: '.$f->file($file->getLocation(), FILEINFO_MIME);
                break;
        }
        return $headers;
    }
}