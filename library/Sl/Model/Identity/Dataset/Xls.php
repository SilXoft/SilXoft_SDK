<?php

namespace Sl\Model\Identity\Dataset;

class Xls extends \Sl\Model\Identity\Dataset {

    protected function _processItem($item, $key) {
        $result = array();
        foreach ($this->getFieldset()->getFields() as $field) {
            $result[$field->getName()] = $this->__renderField($field, $item[$field->getName()]);
        }
        return $result;
    }

    protected function __renderField(\Sl\Model\Identity\Field $field, $value) {
        $result = strip_tags($value);
        if ($field->getType() == 'checkbox') {
            $result = $result ? '+' : '-';
        }
        if (!$field->isRelated()) {
            $result = $field->getModel()->Lists($field->cleanName(), $result);
        }
        return $result;
    }

}
