<?php

namespace Sl\Printer\Template;

class Pdf extends \Sl\Printer\Template {

    protected $_pdf;

    /**
     * 
     * @return \Zend_Pdf
     * 
     * @throws \Exception
     */
    public function getPdf() {
        if (!isset($this->_pdf)) {
            if (!$this->getTpl()) {
                throw new \Exception('Can\'t find template. ' . __METHOD__);
            }
            $this->_pdf = new \Zend_Pdf($this->getTpl(), null, true);
        }
        return $this->_pdf;
    }

    public function render(array $data, array $templates) {
        $pdf = $this->getPdf();
        $font = \Zend_Pdf_Font::fontWithPath(APPLICATION_PATH . '/../public/fonts/arial.ttf');

        $page = $pdf->pages[0];
        /* @var $page \Zend_Pdf_Page */

        $width = $page->getWidth();
        $height = $page->getHeight();

        $templates = array_map(function($el) {
                    return '/' . $el . '/';
                }, $templates);

        $default_font_size = 5;
        $page->setFont($font, $default_font_size);
        $coords = explode(PHP_EOL, $this->getData());
        foreach ($coords as $v) {
            // По неямным причинам выдает ошибки и не дает печатать форму
            $v = trim($v);
	    if(!$v) continue;
            list($x, $y, $field_name) = explode('::', $v);
            
            $new_lines = false;
            
            $string_fields = array();
            $string_font_size = $default_font_size;
            if (preg_match_all('/%.+?%/', $field_name, $string_fields)) {
                $string_fields = array_shift($string_fields);
                foreach ($string_fields as $field) {
                    // Сторока с названием шаблона и модификаторами
                    $matches = array();
                    if (preg_match('/^%([^\|]+?)(\|.+)?%$/', $field, $matches)) {
                        $model_field = $matches[1];
                        
                        $modifiers = $this->_parseModifiers(isset($matches[2])?$matches[2]:'');
                        if (isset($data[$model_field])) {
                            $value = is_array($data[$model_field]) ? current($data[$model_field]) : $data[$model_field];
                            if ($modifiers['a']) {
                                $value = is_array($data[$model_field]) ? implode(', ', $data[$model_field]) : $data[$model_field];
                            }
                            if($modifiers['nl']) {
                                $new_lines = true;
                            }
                            if ($modifiers['s']) {
                                $value = is_array($data[$model_field]) ? (isset($data[$model_field][$modifiers['s']]) ? $data[$model_field][$modifiers['s']] : current($data[$model_field])) : $data[$model_field];
                            }
                            if($modifiers['fl']) {
                                $value = sprintf('%'.$modifiers, $value);
                            }
                            $date = null; // Пытаемся что-то сделать с датой
                            $format = 'Y-m-d';
                            $matches = array();
                            if (preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2}) ?([0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $value, $matches)) {
                                if ($matches[2]) {
                                    $format = 'Y-m-d H:i:s';
                                }
                                if($value == '0000-00-00') {
                                    $value = '';
                                } elseif ($value) {
                                    $date = \DateTime::createFromFormat($format, $value);
                                }
                            }
                            if ($modifiers['f']) {
                                $string_font_size = intval($modifiers['f']);
                            }
                            if ($date && ($date->format('U') > 0)) {
                                /* @var $date \DateTime */
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
                            $field_name = preg_replace('/(%' . $model_field . '[^%]+%)/', $value, $field_name);
                        } else {
                            $field_name = preg_replace('/(%' . $model_field . '[^%]+%)/', '-', $field_name);
                        }
                    } else {
                        // Ищем только модификаторы стиля строки
                        if(preg_match('/^%(\|.+)?%$/', $field, $matches)) {
                            $modifiers = $this->_parseModifiers($matches[1]);
                            if ($modifiers['f']) {
                                $string_font_size = intval($modifiers['f']);
                            }
                            $field_name = preg_replace('/(%[^%]+%)/', '', $field_name);
                        }
                    }
                }
            }
            if ($x && $y && $field_name) {
                if($modifiers['nl']) {
                    $page->saveGS();
                    $page->setFont($font, $string_font_size);
                    $data_array = array_map('trim', explode('<br />', nl2br($field_name)));
                    foreach($data_array as $v) {
                        $page->drawText($v, $x, $height - ($y+=($string_font_size + 2)), 'UTF-8');
                    }
                    $page->restoreGS();
                } else {
                    $page->saveGS();
                    $page->setFont($font, $string_font_size);
                    $page->drawText($field_name, $x, $height - $y, 'UTF-8');
                    $page->restoreGS();
                }
            }
        }
        $pdf->pages = array($page);
        return;
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
