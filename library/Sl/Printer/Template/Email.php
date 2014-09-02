<?php

namespace Sl\Printer\Template;

class Email extends \Sl\Printer\Template {

    protected $_txt;

   

    /**
     * 
     * @return \Zend_Text
     * 
     * @throws \Exception
     */
    public function getTxt() {

        if (!isset($this->_txt)) {
            if (!$this->getTpl()) {
                throw new \Exception('Can\'t find template. ' . __METHOD__);
            }
        }

        return $this->_txt;
    }

    public function render(array $data, array $templates) {
        //print_r($data); die;
        $file = $this->getData(); 
        
        foreach ($data as $name=>$value){
            if (!is_array($value)){
            $prepared_data[$name][0]=$value;
            } else $prepared_data[$name]= $value;
        }
        $templates = array_map(function($el) {
                    return $el;
                }, $templates);
        $file_content = $file;
        if (preg_match_all('/%([-\.a-zA-Z:0-9_]*?)(\|.+?)?%/', $file_content, $matches, PREG_SET_ORDER)) {
            //echo $cell->getValue()."\r\n";
            foreach ($matches as $match) {
                //if(strlen($match[1]) > 20) continue; 
                //echo 'F: '.$match[1].'; M: '.$match[2]."\r\n\r\n";
                $field_desc = $match[1];
                $modifiers = $this->_parseModifiers(isset($match[2]) ? $match[2] : '');
                if ($field_desc) {
                    if (isset($prepared_data[$field_desc])) {
                        // Process data
                        $cur_data_array = $prepared_data[$field_desc]; 
                        foreach($cur_data_array as $name=>&$value){ 
                            $date = null;
                            $format = 'Y-m-d';
                            $parts = array();
                            if (preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2})( ?([0-9]{2}:[0-9]{2}:[0-9]{2}))?$/', trim($value), $parts)) {
                            if (isset($parts[3])) {
                                $format .= ' H:i:s'; 
                            } 
                            $date = \DateTime::createFromFormat($format, $value); 
                        } 
                        if ($date) { 
                            if ($modifiers['dm']) {
                                try { 
                                    $date->modify($modifiers['dm']);
                                    $value = $date->format($format);
                                } catch (\Exception $e) {
                                    
                                }
                            }
                            if ($modifiers['df']) {
                                try {
                                    $value = $date->format($modifiers['df']);
                                } catch (\Exception $e) {
                                    
                                }
                            }
                        }
                        if ($modifiers['morph']) {
                           $value = \Sl\Service\Common::numberToString($value);
                        }
                            
                        } 
                        
                        if ($modifiers['l']) { // List values process later
                            continue;
                        }
                        if ($modifiers['a']) {
                            
                        }
                        if ($modifiers['s']) {
                            foreach ($cur_data_array as $i => $val) {
                                if ($i == 0) {
                                    $value = $val;
                                } else {
                                    if ($i == $modifiers['s']) {
                                        break;
                                    }
                                    $value = $value . ', ' . $val;
                                }
                            }
                        }
                        $new_value = preg_replace('/(%' . $field_desc . '.*?%)/', $value, $file_content);
                    } else {
                        // Nothing to do. Just replace
                        $new_value = preg_replace('/(%' . $field_desc . '.*?%)/', '-', $file_content);
                    }
                } else {
                    // Only modifiers
                    // Process modifiers
                    // .....
                    // Clean value
                    $new_value = preg_replace('/(%' . str_replace('|', '\|', $match[2]) . '%)/', '', $file_content);
                }
                //echo strlen($file_content)."\r\n";
                $file_content = $new_value;
            }

             //echo "O: ".$file_content."N: ".$new_value."\r\n";
        }
        
        $this->_txt = $file_content;
    }

    public function _parseModifiers($string) {
        $modifiers = array(
            'a' => '',
            'df' => '',
            'dm' => '',
            'f' => '',
            'fl' => '',
            'nl' => '',
            's' => ''
        );
        $matches = array();
        if (preg_match('/\|a/', $string, $matches)) {
            $modifiers['a'] = '1';
            $string = preg_replace('/(\|a)/', '', $string);
        }
        if (preg_match('/\|nl/', $string, $matches)) {
            $modifiers['nl'] = '1';
            $string = preg_replace('/(\|nl)/', '', $string);
        }
        if (preg_match('/\|df:([^\|]+)/', $string, $matches)) {
            $modifiers['df'] = $matches[1];
            $string = preg_replace('/(\|df[^\|]+)/', '', $string);
        }
        if (preg_match('/\|f:([^\|]+)/', $string, $matches)) {
            $modifiers['f'] = $matches[1];
            $string = preg_replace('/(\|f[^\|]+)/', '', $string);
        }
        if (preg_match('/\|fl:([^\|]+)/', $string, $matches)) {
            $modifiers['fl'] = $matches[1];
            $string = preg_replace('/(\|fl[^\|]+)/', '', $string);
        }
        if (preg_match('/\|dm:([^\|]+)/', $string, $matches)) {
            $modifiers['dm'] = $matches[1];
            $string = preg_replace('/(\|dm[^\|]+)/', '', $string);
        }
        if (preg_match('/\|s:([^\|]+)/', $string, $matches)) {
            $modifiers['s'] = $matches[1];
            $string = preg_replace('/(\|s[^\|]+)/', '', $string);
        }
        return $modifiers;
    }

}