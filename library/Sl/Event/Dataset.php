<?php
namespace Sl\Event;

class Dataset extends \Sl\Event\Fieldset {
    
    protected $_dataset;
    
    protected $_key;
    
    protected $_item;
    
    public function __construct($type, array $options = array()) {
        if(!isset($options['dataset']) || (!($options['dataset'] instanceof \Sl\Model\Identity\Dataset))) {
            throw new \Exception('Dataset option must exists. ');
        }
        $this->setDataset($options['dataset']);
		if(isset($options['item']) && $options['item']) {
			$this->setItem($options['item']);
		}
		if(isset($options['key']) && $options['key']) {
			$this->setKey($options['key']);
		}
        \Sl_Event_Abstract::__construct($type, $options);
    }
    
    public function setDataset(\Sl\Model\Identity\Dataset $dataset) {
        $this->_dataset = $dataset;
        return $this;
    }
    
    /**
     * 
     * @return \Sl\Model\Identity\Dataset
     */
    public function getDataset() {
        return $this->_dataset;
    }
    
    public function getFieldset() {
        return $this->getDataset()->getFieldset();
    }
    
    public function setFieldset(\Sl\Model\Identity\Fieldset $fieldset) {
        throw new \Exception('Not implemented. Use setDataset(). '.__METHOD__);
    }
    
    public function setKey($key) {
        $this->_key = $key;
        return $this;
    }
    
    public function getKey() {
        return $this->_key;
    }
    
    public function setItem($item) {
        $this->_item = $item;
        return $this;
    }
    
    public function getItem() {
        return $this->_item;
    }
}