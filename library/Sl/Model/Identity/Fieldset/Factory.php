<?php
namespace Sl\Model\Identity\Fieldset;

use Sl\Model\Identity\Field;

/**
 * Фабрика групп полей
 * 
 */
class Factory {
    
    /**
     * Строит группу полей
     * 
     * @param mixed $source
     * @return \Sl\Model\Identity\Fieldset
     */
    public static function build($model, $context, array $fields = array()) {
        $fieldset = new \Sl\Model\Identity\Fieldset($model, $context);
        foreach($fields as $k=>$field) {
            try {
                $options = array();
                if(is_array($field)) {
                    $options = $field;
                    $field = $k;
                }
                $fieldset->createField($field, $options);
            } catch(\Exception $e) {
                // Nothing to do ..
            }
        }
        return $fieldset;
    }
}