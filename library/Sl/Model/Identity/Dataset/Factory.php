<?php
namespace Sl\Model\Identity\Dataset;

/**
 * Фабрика наборов данных
 * 
 */
class Factory {
    
    /**
     * Строит объект набора данных
     * 
     * @return \Sl\Model\Identity\Dataset\Datatables
     */
    public static function build() {
        // @TODO: Ввести контекстно-зависимое создание dataset-а. Что-то вроде Dependency Injection
        return new Datatables();
    }
}