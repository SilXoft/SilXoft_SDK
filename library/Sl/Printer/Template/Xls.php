<?php

namespace Sl\Printer\Template;

class Xls extends \Sl\Printer\Template {

    protected $_excel_obj;
    protected $_counter = array();

    const COUNTER = 'COUNTER';
    const LIST_PREFIX = 'L';
    const SEPARATE_PREFIX = 'S';
    const DATE_FORMAT_PREFIX = 'D';

    public function __construct($file) {
        parent::__construct($file);
        $xls = \PHPExcel_IOFactory::load($this->getTpl());
        if (!$xls) {
            throw new \Exception('Can\'t load "' . $this->getTpl() . '" file. ' . __METHOD__);
        }
        $this->_excel_obj = $xls;
    }

    /**
     * 
     * @return \PHPExcel
     */
    public function getExcelObject() {
        return $this->_excel_obj;
    }

    public function render(array $data, array $templates) {
        $xls = $this->getExcelObject();
        /* @var $xls \PHPExcel */
        $ws = $xls->getActiveSheet();
        /* @var $ws \PHPExcel_Worksheet */
        
        $highestRow = $ws->getHighestRow();
        $highestColumn = $ws->getHighestColumn(); // например, 'F'
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

        /**
         * @TODO: Обработать листы
         */
        for ($column = 0; $column < $highestColumnIndex; $column++) {
            for ($row = 1; $row < $highestRow; $row++) {
                $cell = $ws->getCellByColumnAndRow($column, $row);
                $cell_value = $cell->getValue();
                $new_value = $cell->getValue();
                $matches = array();
                if(preg_match_all('/%([^\|]*?)(\|.+?)?%/', $cell->getValue(), $matches, PREG_SET_ORDER)) {
                    //echo $cell->getValue()."\r\n";
                    foreach($matches as $match) {
                        //echo 'F: '.$match[1].'; M: '.$match[2]."\r\n";
                        $field_desc = $match[1];
                        $modifiers = $this->_parseModifiers(isset($match[2])?$match[2]:'');
                        if($field_desc) {
                            if(isset($data[$field_desc])) {
                                // Process data
                                $cur_data_array = $data[$field_desc];
                                $value = is_array($cur_data_array)?current($cur_data_array):$cur_data_array;
                                if($modifiers['l']) { // List values process later
                                    continue;
                                }
                                if($modifiers['a']) {
                                    echo 'a';
                                }
                                if($modifiers['s']) {
                                    if(isset($cur_data_array[$modifiers['s']])) {
                                        $value = $cur_data_array[$modifiers['s']];
                                    }
                                }
                                $date = null;
                                $format = 'Y-m-d';
                                $parts = array();
                                if(preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2})( ?([0-9]{2}:[0-9]{2}:[0-9]{2}))?$/', trim($value), $parts)) {
                                    if(isset($parts[3])) {
                                        $format .= ' H:i:s';
                                    }
                                    $date = \DateTime::createFromFormat($format, $value);
                                }
                                if($date) {
                                    if($modifiers['dm']) {
                                        try {
                                            $date->modify($modifiers['dm']);
                                            $value = $date->format($format);
                                        } catch(\Exception $e) {
                                            
                                        }
                                    }
                                    if($modifiers['df']) {
                                        try {
                                            $value = $date->format($modifiers['df']);
                                        } catch(\Exception $e) {
                                            
                                        }
                                    }
                                }
                                if($modifiers['morph']) {
                                    $value = \Sl\Service\Common::numberToString($value);
                                }
                                $new_value = preg_replace('/(%'.$field_desc.'.*?%)/', $value, $cell_value);
                            } else {
                                // Nothing to do. Just replace
                                $new_value = preg_replace('/(%'.$field_desc.'.+?%)/', '-', $cell_value);
                            }
                        } else {
                            // Only modifiers
                            // Process modifiers
                            // .....
                            // Clean value
                            $new_value = preg_replace('/(%'.str_replace('|', '\|', $match[2]).'%)/', '', $cell_value);
                        }
                    }
                    $cell->setValue($new_value);
                    //echo "O: ".$cell_value."N: ".$new_value."\r\n";
                }
            }
        }
        //die;
        //print_r(array($data, $templates));die;
        return $xls;
        
    }
    
    public function _parseModifiers($string) {
        $modifiers = array(
            'df' => '',
            'dm' => '',
            'dt' => '',
            's' => '',
            'a' => '',
            'f' => '',
            'fl' => '',
            'nl' => '',
            'morph' => '',
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
        if (preg_match('/\|morph/', $string, $matches)) {
            $modifiers['morph'] = '1';
            $string = preg_replace('/(\|morph)/', '', $string);
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
