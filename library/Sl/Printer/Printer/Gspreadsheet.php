<?php
namespace Sl\Printer\Printer;

class Gspreadsheet extends Sl\Printer\Printer {
    
    protected $_username;
    protected $_password;
    
    public function setUsername($username) {
        $this->_username = $username;
        return $this;
    }
    
    public function setPassword($password) {
        $this->_password = $password;
        return $this;
    }
    
    public function getUsername() {
        return $this->_username;
    }
    
    public function getPassword() {
        return $this->_password;
    }
    
    public function init() {
        $service = \Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
        $client = \Zend_Gdata_ClientLogin::getHttpClient('s.kachan@silencatech.com', 'ghjcnjgfhjkm', $service);
        $docs = new \Zend_Gdata_Spreadsheets($client);
        $feed = $docs->getSpreadsheetFeed();
        
        /*@var $feed \Zend_Gdata_Spreadsheets_ListFeed*/
        foreach($feed as $item) {
            /*@var $item \Zend_Gdata_Spreadsheets_ListEntry*/
            echo $item->getTitle()."\r\n";
            $links = $item->getLink('alternate');
            echo "Link: ".$links->getHref()."\r\n";
        }
        die;
    }
    
    protected function _prepareObjectData() {
        
    }

    protected function _prepareObjectTemplates() {
        
    }

    protected function save($save, \Sl\Printer\Template $template, array $data, array $templ_data) {
        
    }
}
