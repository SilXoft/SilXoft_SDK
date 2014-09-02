<?php
namespace Sl\Model\Identity\Dataset;

use Sl\Model\Identity\Field;
use Sl\Model\Identity\Fieldset;
use Sl\Model\Identity\Fieldset\Filter;

/**
 * Набор данных для js-плагина dataTables 
 * 
 */
class Datatables extends \Sl\Model\Identity\Dataset {
    
    /**
     * Реализация обработки
     * 
     * @param mixed $item
     * @param string $key
     * @return type
     */
    protected function _processItem($item, $key) {
        $result = new \Zend_Config(array(), true);
        
        $_metas = isset($item['_meta'])?$item['_meta']:array();
        
        foreach($this->getFieldset()->getFields() as $field) {
            $value = isset($item[$field->getName()])?$item[$field->getName()]:null;
            if($field->isRelated()) {
                $alias_data = array_reverse(\Sl\Service\Alias::describeAlias($field->relationAlias(), $field->getModel()));
                $res_data = $this->__renderField($field, $value, $field->cleanName(), $_metas);
                
                foreach($alias_data as $alias_part) {
                    $res_data = array(
                        $alias_part => $res_data
                    );
                }
                $result->merge(new \Zend_Config($res_data, true));
            } else {
                $result->{$field->getName()} = $this->__renderField($field, $value, null, $_metas);
            }
        }
        $result->_meta = $_metas;
        return $result->toArray();
    }
    
    /**
     * Кастомная обработка поля
     * 
     * @param \Sl\Model\Identity\Field $field
     * @param string $value Значение
     * @param string $key Ключ в массиве
     * @return string
     */
    protected function __renderField(Field $field, $value, $key = null, array &$_metas = array()) {
        $result = $field->listview('td', $value);
        /*if ($field->getModel()->ListsAssociations($field->cleanName())){
                
            $rendered[\Sl\Serializer\Serializer::LISTVIEW_TR_ATTRIBUTES_KEY] = array();    
            $rendered[\Sl\Serializer\Serializer::LISTVIEW_TR_ATTRIBUTES_KEY]['class'] = implode('_',array($field->cleanName(),$value));    
            
        }elseif($field->getType() == 'checkbox') {
            $rendered[$key] = $value?'+':'-';
            $rendered[\Sl\Serializer\Serializer::LISTVIEW_TR_ATTRIBUTES_KEY] = array();    
            $rendered[\Sl\Serializer\Serializer::LISTVIEW_TR_ATTRIBUTES_KEY]['class'] = implode('_',array($field->cleanName(),$value));
        }*/
        
        if($field->getType() == 'checkbox') {
            @$_metas['classes'][] = implode('_',array($field->cleanName(),$value));
        }
        if (!$field->isRelated() && $field->getModel()->ListsAssociations($field->cleanName())){
            $value = str_replace( array('|','.'),array('',''), $value);
            $_metas['classes'][] = implode('_',array($field->cleanName(),$value));
        }
        
        //echo $field->getName().': '.$field->getType()."\r\n";
        
        if(!$field->isRelated()) {
            if($field->getType() == 'checkbox') {
                $result = $value?'<i class="icon icon-small icon-ok"></i>':'';
            }
            $result = $field->getModel()->Lists($field->cleanName(), $result);
        }
        
        if(!is_null($key)) {
            $result = array($key=>$result);
        }
        return $result;
        
    }
}
