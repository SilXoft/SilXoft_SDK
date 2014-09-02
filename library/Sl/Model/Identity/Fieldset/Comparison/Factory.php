<?php
namespace Sl\Model\Identity\Fieldset\Comparison;

/**
 * Фабрика сравнений
 * 
 */
class Factory {
    
    /**
     * Строит объект сравнения
     * 
     * @param array $data
     * @param \Sl\Model\Identity\Fieldset $fieldset
     * @return \Sl\Model\Identity\Fieldset\Comparison
     * @throws \Exception
     */
    public static function build($data, $fieldset = null) {
        if(!isset($data['type'])) {
            throw new \Exception('Wrong comparison type. '.__METHOD__);
        }
        /**
         * @TODO: Убрать отсюда. Должно создаваться как-то автоматически
         */
        // Выделение расширения
        $matches = array();
        if(preg_match('/^(.*)(like|eq|isnull|gt|multi|lt|in)$/', $data['type'], $matches)) {
            $data['type'] = $matches[2];
            $data['extension'] = $matches[1];
        }
        $comp_class = __NAMESPACE__.'\\'.ucfirst($data['type']);
        if(!class_exists($comp_class)) {
            throw new \Exception('Can\'t build comparison from given data. '.__METHOD__);
        }
        return new $comp_class($data, $fieldset);
    }
    
    /**
     * Возвращает доступные сравнения
     * 
     * @param bool $as_object Если true - пытается создавать объекты. <b style="color: red;">Не работает</b>
     * @return array
     * @throws \Exception
     */
    public static function getAvailableComparisons($as_object = false) {
        if($as_object) {
            throw new \Exception('Not implemented yet. '.__METHOD__);
        } else {
            $types = array();
            foreach(\Sl\Model\Identity\Field\Factory::getAvailableTypes() as $type) {
                foreach(self::getAvailableByType($type) as $k=>$v) {
                    if(!isset($types[$k])) {
                        $types[$k] = $v;
                    }
                }
            }
            return $types;
        }
    }
    
    /**
     * Возвращает массив доступных сравнений в зависимости от типа поля
     * 
     * @param string $type Тип поля
     * @param bool $as_object Если true - пытается создавать объекты. <b style="color: red;">Не работает</b>
     * @return array
     * @throws \Exception
     */
    public static function getAvailableByType($type, $as_object = false) {
        if($as_object) {
            /**
             * @TODO: Реализовать
             */
            throw new \Exception('Not implemented yet. '.__METHOD__);
        } else {
            $class_name = '\\Sl\\Model\\Identity\\Field\\'.ucfirst($type);
            if(!class_exists($class_name)) {
                throw new \Exception('Wrong field type "'.$type.'". '.__METHOD__);
            }
            return $class_name::getSuppotedComparisons();
        }
    }
}