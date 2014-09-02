<?php
namespace Sl\View\Control;

class Lists extends \Sl\View\Control {
    
    protected $_drop_dir = 'down';
    protected $_items = array();
    
    protected $_allowed_dirs = array(
        'up',
        'down'
    );
    
    public function setDropDir($dir) {
        if(!in_array($dir, $this->_allowed_dirs)) {
            throw new \Exception('Wrong drop direction');
        }
        $this->_drop_dir = $dir;
        return $this;
    }
    
    public function getDropDir($full = false) {
        if($full) {
            return 'drop'.$this->getDropDir();
        } else {
            return $this->_drop_dir;
        }
    }
    
    /**
     * 
     * @param \Sl\View\Control\Lists\Item $item
     * @param type $key
     * @return \Sl\View\Control\Lists
     */
    public function addItem(Lists\Item $item, $key = null) {
        if(!is_null($key)) {
            $this->_items[$key] = $item;
        } else {
            $this->_items[] = $item;
        }
        return $this;
    }
    
    /**
     * 
     * @param \Sl\View\Control\Lists\Item[] $items
     * @return \Sl\View\Control\Lists
     */
    public function addItems(array $items) {
        foreach($items as $k=>$item) {
            if($item instanceof Lists\Item) {
                $this->addItem($item, $k);
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param \Sl\View\Control\Lists\Item[] $items
     * @return \Sl\View\Control\Lists
     */
    public function setItems(array $items) {
        return $this->addItems($items);
    }
    
    /**
     * 
     * @param type $key
     * @return \Sl\View\Control\Lists\Item|null
     */
    public function getItem($key) {
        if(isset($this->_items[$key])) {
            return $this->_items[$key];
        }
        return null;
    }
    
    /**
     * 
     * @return \Sl\View\Control\Lists\Item[]
     */
    public function getItems() {
        return $this->_items;
    }
}
