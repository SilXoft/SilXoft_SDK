<?php

class Sl_View extends Zend_View {
 
    public function settings($name){
        
        return \Sl_Service_Settings::value($name);
        
    }
    
}
