<?php

class Sl_Service_QRcode {

    public static function generate($content, $filename = null, $eccLevel = null, $pixel_size = null, $margine = null, $save = false, $backColor = null, $foreColor = null) {

        if (isset($content)) {
            include_once "phpqrcode/qrlib.php";
            if ($eccLevel == null) {
                $eccLevel = "Q";
            }
            if ($pixel_size == null) {
                $pixel_size = 4;
            }
            if ($margine == null) {
                $margine = 4;
            }
            $filename = null;
             ob_start(); 
              
              
             QRcode::png($content, FALSE, $eccLevel, $pixel_size, $margine);
             $QR = ob_get_clean();
            return($QR);
        }
    }

}

?>
