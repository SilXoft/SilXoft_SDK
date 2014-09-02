<?php

namespace Sl\Form\Decorator;

class FilesListTable extends \Zend_Form_Decorator_HtmlTag {

    public function render($content) {
        
        $placement = $this->getPlacement();
        $this->removeOption('placement');

        $attribs = $this->getOptions();

        switch ($placement) {
            case self::PREPEND:
                return $this->_buildList($attribs)
                        . $content;
            case self::APPEND:
            default:
                return $content
                        . $this->_buildList($attribs);
        }
    }

    protected function _buildList($attribs = array()) {
        $items = $this->getOption('items');
        $labels = $this->getOption('field_label');
        if (!is_array($items)) {
            $items = array();
        }
        
        $attr_string = '';
        if (count($attribs)) {
            $attr_string = $this->_htmlAttribs($attribs);
        }

        $text_header = '<div class="well wellform">
      
       <table class="datatable table table-striped table-bordered dataTable blockquote" ' . $attr_string . '>';

        
        foreach ($items as $item) {

            $object = $item['object'];            
            $model = \Sl\Service\Helper::getModelByExtend($object->getExtend());
            $object = \Sl_Model_Factory::mapper($model)->find($object->getId(), true,false);
          
            $item['href'] = \Sl\Service\Helper::getToEditUrl($object);
            $text_row .= '<tr>';

            $first_col = true;
            $i = 0;
            foreach ($item['fields'] as $name) {
                $i++;
                if ($first_col) {
                    $text_row .= '<td class="tostring" data-value="' . $object->__toString() . '" data-url="' . $item['href'] . '" ><a href="' . $item['href'] . '" ' . ($item['target'] ? ' target="' . $item['target'] . '"' : '') . '>' . $object->__toString() . '</a></td>';
                }
                $field = explode('.', $name);
                if (count($field) > 1) {

                    $priv_read = \Sl_Service_Acl::isAllowed(array( $object, $field[0] ), \Sl_Service_Acl::PRIVELEGE_READ);

                    if ($priv_read) {
                        if(!$object->issetRelated( $field[0] )) {
                            $object = \Sl_Model_Factory::mapper($object)->findExtended($object->getId(), $field[0]);
                        }
                        
                        $rel_obj = $object->fetchRelated($field[0]);

                        $field_val = array();
                        $field_url = array();
                        foreach ($rel_obj as $r_o) {

                            if (strtolower($r_o->findModelName()) == 'file') {

                                $text_row .= '<td class="' . str_replace('.', '-', $name) . '">
                                    <span data-url="/file/detailed/id/' . $r_o->getId() . '" data-value="' . $r_o->Lists($field[1]) . '">
                                        <a href="/file/detailed/id/' . $r_o->getId() . '" target="_blank">' . $r_o->Lists($field[1]) . '</a>
                                    </span>
                                    </td>';
                            } else {
                                $field_val[] = $r_o->Lists($field[1]);
                                $field_url[] = \Sl\Service\Helper::modelEditViewUrl($r_o);
                                $text_row .= '<td class="' . str_replace('.', '-', $name) . '">
                                <span data-url="' . implode(', ', $field_url) . '" data-value="' . implode(', ', $field_val) . '">' . implode(', ', $field_val) . '</span>
                                     </td>';
                            }
                        }
                    } else {
                        unset($labels[$i]);
                    }
                } else {

                    $priv_read = \Sl_Service_Acl::isAllowed(array(
                                $object,
                                $name
                                    ), \Sl_Service_Acl::PRIVELEGE_READ);

                    if ($priv_read) {
                        $text_row .= '<td class="' . $name . '" data-value="' . $object->Lists($name) . '">' . $object->Lists($name) . '</td>';
                    } else {
                        unset($labels[$i]);
                    }
                }
                $first_col = false;
            }

            $text_row .= '</tr>';
        }
        $text_thead .= '<thead><tr>';
        foreach ($labels as $label) {
            $text_thead .= '<th>' . $label . '</th>';
        }
        $text_thead .= '</tr></thead><tbody>';

        $text_footer .= '</tbody></table></div>';
        $text = $text_header . $text_thead . $text_row . $text_footer;
        return $text;
    }

}