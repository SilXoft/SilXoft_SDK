<?php
namespace Sl\Module\Home\Model\Mapper;
use \Sl_Exception_Model as Exception;

class File extends \Sl_Model_Mapper_Abstract {
	protected function _getMappedDomainName() {
        return '\Sl\Module\Home\Model\File';
    }

    protected function _getMappedRealName() {
        return '\Sl\Module\Home\Model\Table\File';
    }
    
    public function save(\Sl_Model_Abstract $file, $return = false, $events = true) {
        if(!file_exists($file->getLocation())) {
            throw new Exception('File "'.$file->getLocation().'" doesn\'t exists. '.__METHOD__);
        }
        return parent::save($file, $return, $events);
    }
}