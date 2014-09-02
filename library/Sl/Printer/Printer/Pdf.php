<?php
namespace Sl\Printer\Printer;

class Pdf extends \Sl\Printer\Printer {
    
    protected function _prepareObjectItems(\Sl_Model_Abstract $object, $prefix = Null) {
        //if(!isset($object->_prepared_items)) {
        $raw_data = array();

        foreach ($object->toArray() as $key => $value) {; //якщо данні з массиву з іншими об"єктами, то 
            $key_name = $key;                         // дописуєм назву об"єкта поточного об"єкта в "name"
            if (isset($prefix)) {
                $key_name = $prefix . ':' . $key;
            }
            $raw_data[$key_name] = array(
                'name' => $key_name,
                'value' => $object->Lists($key),
            );
        }

        foreach ($object->fetchRelated() as $relation => $items) {
            if (count($items)) {
                if (isset($prefix)) {
                    $relation = $prefix . ':' . $relation;
                }
                foreach ($items as $item) {
                    $item_data = $item->toArray();
                    $values = array();
                    //if(isset($prefix)){$key_name=$prefix.':'.$key;}
                    foreach ($item_data as $key => $val) {
                        if (!isset($raw_data[$relation . '.' . $key])) {
                            $raw_data[$relation . '.' . $key] = array(
                                'name' => $relation . '.' . $key,
                                'relation' => $relation,
                                'value' => array(
                                    $item->Lists($key),
                                ),
                            );
                        } else {
                            $raw_data[$relation . '.' . $key]['value'][] = $val;
                        }
                    }
                }
            }
        }


        return $raw_data;
        //} 
    }
    
    protected function _prepareItems() {
        if (!isset($this->_prepared_items)) {
            $raw_data = array();
            //$dataObject_array[0]= $this->_getCurrentObject()->toArray(); 
            $data_array['main'] = $this->_prepareObjectItems($this->_getCurrentObject());
            foreach ($this->getAdditionObjects() as $key => $value) {
                $temp_array = array();
                foreach ($value as $k => $item_object) {

                    if (!isset($temp_array[$key])) {
                        $temp_array[$key] = array();
                    }
                    //$data_array[$key][] = $this->_prepareObjectItems($item_object, $key);
                    $temp_array[$key] = $this->_prepareObjectItems($item_object, $key);
                    $data_array = array_merge_recursive($data_array, $temp_array);
                    //$data_array+=$temp_array; 
                }
            }


            foreach ($data_array as $pref => &$rel) {
                foreach ($rel as $name => &$k) {

                    if (is_array($k['name']) && !isset($k['relation'])) {
                        $k['relation'] = '';
                    }
                    if (is_array($k['name'])) {
                        $k['name'] = $k['name'][0];
                    }
                    if (is_array($k['relation'])) {
                        $k['relation'] = $k['relation'][0];
                    }
                    if ($k['create']) {
                        $k['create'] = substr($k['create'], 0, 10);
                    }
                }
            }
            
            if($this->getExtras()) {
                foreach($this->getExtras() as $k=>$v) {
                    $data_array['extras']['extras:'.$k] = array(
                        'name' => 'extras:'.$k,
                        'value' => $v,
                        'relation' => true,
                    );
                }
            }
            
            $this->_prepared_items = $data_array;
        }
        return $this->_prepared_items;
    }
    
    protected function _prepareObjectData() {
        $data = array();
        foreach ($this->_prepareItems() as $key => $value) {
            foreach ($value as $name => $field) {
                $data[$name] = $field['value'];
            }
        } 
        return $data;
    }

    protected function _prepareObjectTemplates() {
        $template_data = array();
        foreach ($this->_prepareItems() as $key => $value) {
            $template_data+=array_map(function($el) {
                        return '%' . (isset($el['relation']) ? '' : '') . strtoupper($el['name']) . '%';
                    }, $value);
        } //print_r($template_data);die;
        return $template_data;
        //return array_keys($this->_prepareObjectData());
        //$identity = \Sl_Model_Factory::identity($this->_getCurrentObject());
        //return array_map(function($el) { return '%'.strtoupper($el).'%'; }, $identity->getObjectFields(true));
    }

    protected function save($save, \Sl\Printer\Template $template, array $data, array $templ_data, $mask=null) {
        $name = $this->_getCurrentObject();
        $type = $this->_getCurrentObject()->findModelName();
        if(!($template instanceof \Sl\Printer\Template\Pdf)) {
            throw new \Exception('Only Template\Pdf supported.');
        }
        /*@var $template \Sl\Printer\Template\Pdf*/
        $template->render($data, $templ_data);
        
   
        //$template->getPdf()->save('php://output');
        //die;
        
        //$writer = new \PHPExcel_Writer_Excel2007($template->getExcelObject());
        if($save) {
            if(!is_writable($save)) {
                $fh = fopen($save, 'w');
                if($fh) {
                    fclose($fh);
                }
            }
            if(is_writable($save)) {
                return $template->getPdf()->save($save);
            } 
        } else {
            ob_end_clean();
            
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            
            header('Content-type: application/pdf;'); 
            header("Content-Disposition: attachment;filename=".$this->fileName().".pdf");
            
            $template->getPdf()->save('php://output');
            die;
        }
    }
}
