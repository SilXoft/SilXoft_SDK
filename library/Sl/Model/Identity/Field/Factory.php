<?php
namespace Sl\Model\Identity\Field;

use Sl\Model\Identity as Ide;

class Factory {
    
    /**
     * Данные о загруженных конфигах для моделей
     *
     * @var type
     */
    protected static $_data;

    /**
     * Строит поле
     * 
     * @param string $name Имя поля
     * @param \Sl\Model\Identity\Fieldset $fieldset Набор, для которого оно строится
     * @param array $options Доп. данные. Перезапишут стандартные
     * @return \Sl\Model\Identity\Field
     * @throws \Exception
     */
    public static function build($name, Ide\Fieldset $fieldset, array $options = array()) {
        return new Ide\Field($name, $fieldset->getContextType(), array_merge(self::_getData($fieldset, $name), $options));
    }
    
    protected static function _getData(Ide\Fieldset $fieldset, $name = null) {
        if(!isset(self::$_data)) {
            self::$_data = new \SplObjectStorage();
        }
        if(!isset(self::$_data[$fieldset])) {
            $config = \Sl\Service\Config::read($fieldset->getModel(), $fieldset->getContextType(), \Sl\Service\Config::MERGE_FIELDS);
            if(!$config) {
                throw new \Exception('Can\'t build config for this path. '.__METHOD__);
            }
            try {
                $config->merge(\Sl\Module\Auth\Service\Usersettings::read($fieldset->getModel(), $fieldset->getContextType()));
            } catch (\Exception $e) {
                // 
            }
            self::$_data[$fieldset] = $config->toArray();
        }
        if(is_null($name)) {
            return self::$_data[$fieldset];
        } else {
            return isset(self::$_data[$fieldset][$name])?self::$_data[$fieldset][$name]:array();
        }
    }
    
    public static function getAvailableTypes($as_object = false) {
        if($as_object) {
            throw new \Exception('Not implemented yet. '.__METHOD__);
        } else {
            return array(
                'hidden',
                'text',
                'textarea',
                'date',
                'select',
                'checkbox',
            );
        }
    }
}