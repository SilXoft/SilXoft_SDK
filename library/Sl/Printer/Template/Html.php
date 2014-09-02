<?php

namespace Sl\Printer\Template;

class Html extends \Sl\Printer\Template {

    public $prepared_data;
    public $code_params = array('text' => 'If you are reading this text your request is empty. Hard to belive but true.',
        'backgroundColor' => '#FFFFFF',
        'foreColor' => '#000000',
        'padding' => 4, //array(10,5,10,5),
        'moduleSize' => 8,
        'version' => 10);

    public function embeddedimage() {


        $renderer_params = array('imageType' => 'png');
        return \Zend_Matrixcode::render('qrcode', $this->code_params, 'image', $renderer_params);
    }

    /**
     *
     * @return \Zend_Txt
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

    protected function DOMinnerHTML($element) {
        $innerHTML = "";
        $children = $element->childNodes;
        foreach ($children as $child) {
            $tmp_dom = new \DOMDocument();
            $tmp_dom->appendChild($tmp_dom->importNode($child, true));
            $innerHTML.=trim($tmp_dom->saveHTML());
        }
        return str_replace('<br>', '', $innerHTML);
        //TODO: переробити по людськи прибирання <br>
    }

    protected function replaseContent($content, $k = null, $attribute = null) {
        if (preg_match_all('/%([(' . $attribute . ':)a-zA-Z:0-9_]*?)(\|.+?)?%/', $content, $matches, PREG_SET_ORDER)) {
           //if ($k == 2){print_r($content); die;}
            //echo $cell->getValue()."\r\n";
            foreach ($matches as $match) {
                //print_R($k); echo '----';
              //  print_R($match); echo '--';
                //if(strlen($match[1]) > 20) continue;
                //echo 'F: '.$match[1].'; M: '.$match[2]."\r\n\r\n";
                $field_desc = $match[1];
                //print_r(array ($k.$field_desc => $this->prepared_data[$field_desc]));  echo '       ';
                $modifiers = $this->_parseModifiers(isset($match[2]) ? $match[2] : '');
                if ($field_desc) {
                    if (isset($this->prepared_data[$field_desc])) {
                        //print_R(array($this->prepared_data[$field_desc][$k] => current($this->prepared_data[$field_desc])));
                        // Process data
                        if ($k!=null){
                            //print_R(strrpos($field_desc, $attribute));
                           if (preg_match('/'.$attribute.'/', $field_desc)){
                                $cur_element = ($this->prepared_data[$field_desc][$k]);
                               // die($field_desc);
                            }else{
                                
                                $cur_element = current($this->prepared_data[$field_desc]);
                            }
                        } else{
                            $cur_element = current($this->prepared_data[$field_desc]);
                        }
                         if($modifiers['fl']) {
                                $cur_element = sprintf('%'.$modifiers['fl'], $cur_element);
                            }
                        

                        //print_r($cur_element); 
                        $date = null;
                        $format = 'Y-m-d';
                        $parts = array();
                        if (preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2})( ?([0-9]{2}:[0-9]{2}:[0-9]{2}))?$/', trim($cur_element), $parts)) {
                            if (isset($parts[3])) {
                                $format .= ' H:i:s';
                            }
                            $date = \DateTime::createFromFormat($format, $cur_element);
                        }
                        if ($date) {
                            if ($modifiers['dm']) {
                                try {
                                    $date->modify($modifiers['dm']);
                                    $cur_element = $date->format($format);
                                } catch (\Exception $e) {
                                    
                                }
                            }
                            if ($modifiers['df']) {
                                try {
                                    $cur_element = $date->format($modifiers['df']);
                                } catch (\Exception $e) {
                                    
                                }
                            }
                        }
                        if ($modifiers['morph']) {
                            $cur_element = \Sl\Service\Common::numberToString($cur_element);
                        }






                        $new_value = preg_replace('/(%' . $field_desc . '.*?%)/', $cur_element, $content);
                    } else {
                        // Nothing to do. Just replace
                        $new_value = preg_replace('/(%' . $field_desc . '.*?%)/', '-', $content);
                    }
                } else {
                    // Only modifiers
                    // Process modifiers
                    // .....
                    // Clean value
                    $new_value = preg_replace('/(%' . str_replace('|', '\|', $match[2]) . '%)/', '', $content);
                }
                //echo strlen($file_content)."\r\n";
                $content = $new_value;
            }
                   
            //echo "O: ".$file_content."N: ".$new_value."\r\n";
        }
        if (preg_match_all('/%([-\.a-zA-Z:0-9_]*?)(\|.+?)?%/', $content, $matches, PREG_SET_ORDER)) {

            //echo $cell->getValue()."\r\n";
            foreach ($matches as $match) {
                //if(strlen($match[1]) > 20) continue;
                //echo 'F: '.$match[1].'; M: '.$match[2]."\r\n\r\n";
                $field_desc = $match[1];
                $modifiers = $this->_parseModifiers(isset($match[2]) ? $match[2] : '');
                if ($field_desc) {
                    if (isset($this->prepared_data[$field_desc])) {

                        // Process data
                        $cur_element = current($this->prepared_data[$field_desc]);

                        $date = null;
                        $format = 'Y-m-d';
                        $parts = array();
                        if (preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2})( ?([0-9]{2}:[0-9]{2}:[0-9]{2}))?$/', trim($cur_element), $parts)) {
                            if (isset($parts[3])) {
                                $format .= ' H:i:s';
                            }
                            $date = \DateTime::createFromFormat($format, $cur_element);
                        }
                        if ($date) {
                            if ($modifiers['dm']) {
                                try {
                                    $date->modify($modifiers['dm']);
                                    $cur_element = $date->format($format);
                                } catch (\Exception $e) {
                                    
                                }
                            }
                            if ($modifiers['df']) {
                                try {
                                    $cur_element = $date->format($modifiers['df']);
                                } catch (\Exception $e) {
                                    
                                }
                            }
                        }
                        if ($modifiers['morph']) {
                            $cur_element = \Sl\Service\Common::numberToString($cur_element);
                        }






                        $new_value = preg_replace('/(%' . $field_desc . '.*?%)/', $cur_element, $content);
                    } else {
                        // Nothing to do. Just replace
                        $new_value = preg_replace('/(%' . $field_desc . '.*?%)/', '-', $content);
                    }
                } else {
                    // Only modifiers
                    // Process modifiers
                    // .....
                    // Clean value
                    $new_value = preg_replace('/(%' . str_replace('|', '\|', $match[2]) . '%)/', '', $content);
                }
                //echo strlen($file_content)."\r\n";
                $content = $new_value;
            }
        }

        return $content;
    }
   
    public function docRender($doc, $lists) {
        foreach ($lists as $list) {

            $final_body_content = null;
            $list_attribute = $list->getAttribute('mrm');
            $list_count = $list->getAttribute('unic');
            $list_content = self::DOMinnerHTML($list);
            // print_r($list_content);

            if (isset($this->prepared_data[$list_attribute . ':id'])) {
                foreach ($this->prepared_data[$list_attribute . ':id'] as $key => $value) {
                        //print_R($this->prepared_data[$list_attribute . ':id']); die;
                    $new_content = self::replaseContent($list_content, $key, $list_attribute);

                    $final_body_content = $final_body_content . '<br />' . $new_content;
                }
            } else {
                $new_content = self::replaseContent($list_content);
                $final_body_content = $new_content;
            } 
            $xp = new \DOMXPath($doc);
            $replacement = $doc->createDocumentFragment();
            $replacement->appendXML('<div>' . $final_body_content . '</div>');
            $oldNode = $xp->query('//list[@unic="' . $list_count . '"]')->item(0);

            $oldNode->parentNode->replaceChild($replacement, $oldNode);
        }
        return $doc;
    }

    public function render(array $data, array $templates) {
        //error_reporting(E_ALL);
        foreach ($data as $name => $value) {
            if (!is_array($value)) {
                $this->prepared_data[$name][0] = $value;
            }
            else
                $this->prepared_data[$name] = $value;
        } //print_r($this->prepared_data); 
        $templates = array_map(function($el) {
                    return $el;
                }, $templates);
        $file_content = $file;
        $doc = new \DomDocument;
       
        $doc->validateOnParse = true;
        $file = $this->getData();
        //print_r($file); die;
        $elementHtml = $doc->createElement('html');
        $elementHead = $doc->createElement('head');
        $elementBody = $doc->createElement('body');
        $elementTitle = $doc->createElement('title');
        $textTitre = $doc->createTextNode('My bweb page');
        $attrLang = $doc->createAttribute('lang');
        $attrLang->value = 'en';
        $doc->appendChild($elementHtml);
        $elementHtml->appendChild($elementHead);
        $elementHtml->appendChild($attrLang);
        $elementHead->appendChild($elementTitle);
        $elementTitle->appendChild($textTitre);
        $elementHtml->appendChild($elementBody);
        $fragment = $doc->createDocumentFragment();
        $fragment->appendXML($file);
        $doc->getElementsByTagName('body')->item(0)->appendChild($fragment);
        $doc->formatOutput = TRUE;
        $lists = $doc->getElementsByTagName('list');
        $count = 0;
        foreach ($lists as $list) {
            $list->setAttribute('unic', 'list' . $count);
            $count++;
        }

        $xpath = new \DOMXPath($doc);
        $lists = $xpath->query('descendant::list[not(descendant::list)]');
        while ($lists->length <> 0) {
            $doc = self::docRender($doc, $lists);
            $xpath = new \DOMXPath($doc);
            $lists = $xpath->query('descendant::list[not(descendant::list)]');
        }
        $body = $doc->getElementsByTagName('body')->item(0);
   
        $body_content = self::DOMinnerHTML($body);
        $new_body_content = self::replaseContent($body_content);
        $replacement = $doc->createDocumentFragment();
        $replacement->appendXML('<body onload=\'window.print()\'>'.$new_body_content.'</body>');
        $xp = new \DOMXPath($doc);
        $oldNode = $xp->query('//body')->item(0);
        $oldNode->parentNode->replaceChild($replacement, $oldNode);
        
        
        $xpath = new \DOMXPath($doc);
        $qrcodes = $doc->getElementsByTagName('QRcode');
        

        $count = 0;
        $QR_array = array();
        foreach ($qrcodes as $qrcode) {
            $qrcode->setAttribute('unic', 'QR' . $count);
            $count++;
        }
        
        $qrcods = $doc->getElementsByTagName('QRcode');
        foreach ($qrcods as $QRcode) {
            $QRcode_content = self::DOMinnerHTML($QRcode);
            $QRcode_count = $QRcode->getAttribute('unic');
            $QRcode_min_length = $QRcode->getAttribute('min-length');
            $QRcode_content = self::replaseContent($QRcode_content);    
            $QRcode_content = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $QRcode_content);    
            if($QRcode_min_length) {
                if(mb_strlen($QRcode_content, 'UTF-8') < $QRcode_min_length) {
                    $QRcode_content = str_pad($QRcode_content, $QRcode_min_length);
                }
            }
            $QR = \Sl_Service_QRcode::generate($QRcode_content);  
            $QR_array [$QRcode_count] = base64_encode($QR);
           }
          foreach ($QR_array as $unic=>$code){
            $xp = new \DOMXPath($doc);
            $replacement = $doc->createDocumentFragment();
            $replacement->appendXML('<img src="data:image/png;base64,'.$code.'"/>');
            $oldNode = $xp->query('//QRcode[@unic="' . $unic . '"]')->item(0);
  
            $oldNode->parentNode->replaceChild($replacement, $oldNode);
        
          }

        $new_html = $doc->saveXml($doc->documentElement);
        echo $new_html;
        die;
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