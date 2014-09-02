<?php
namespace Sl\Service;

class Alias {
    
    protected static $_aliases;
    
    public static function getDbPath($alias, \Sl_Model_Abstract $model, $pretty = false) {
        $alias_data = self::_getAlias($alias, $model);
        if($pretty) {
            return self::getDbPathInfo($alias, $model);
        } else {
            return $alias_data['db']['path'];
        }
    }
    
    public static function getDbPathInfo($path, \Sl_Model_Abstract $model = null) {
        $relations_array = self::splitDbPath($path);
        foreach($relations_array as $v) {
            if(!\Sl_Modulerelation_Manager::relationExists($v, $model)) {
                return array();
            }
        }
        return $relations_array;
    }
    
    protected static function _getAlias($name, \Sl_Model_Abstract $model = null) {
        if(!isset(self::$_aliases)) {
            self::_loadAliasData();
        }
        if(!isset(self::$_aliases[$name])) {
            // Строим сами
            // Нужны путь, модель, модель с другой стороны
            self::$_aliases[$name] = self::_fillAliasData($name, $model);
        }
        return isset(self::$_aliases[$name])?self::$_aliases[$name]:null;
    }
    
    protected static function _loadAliasData() {
        $data = \Sl_Module_Manager::getAvailableModels();
        foreach($data as $module_name=>$models) {
            $module = \Sl_Module_Manager::getInstance()->getModule($module_name);
            $config_data = $module->section('alias');
            if($config_data) {
                $config_data = $config_data->toArray();
                foreach($models as $model_name) {
                    if(isset($config_data[$model_name])) {
                        foreach($config_data[$model_name] as $alias_name=>$alias_data) {
                            print_r(array($alias_name, $alias_data));
                            self::$_aliases[$alias_name] = self::_fillAliasData($alias_name, $alias_data);
                        }
                    }
                }
            }
        }
        
        if(is_null(self::$_aliases)) {
            self::$_aliases = array();
        }
    }
    
    public static function splitDbPath($path) {
        return explode('.', $path);
    }
    
    public static function joinDbPath(array $relations = array()) {
        return implode('.', $relations);
    }
    
    protected static function _fillAliasData($alias, \Sl_Model_Abstract $model = null) {
        $data = array(
            'model' => array(),
            'db' => array(),
        );
        if($model) {
            // Все просто. Создаем связь и берем из нее данные
            $oRelation = \Sl_Modulerelation_Manager::getRelations($model, $alias);
            if(!$oRelation) {
                throw new \Exception('Invalid alias "'.$alias.'". '.__METHOD__);
            }
            
        } else {
            // Пытаемся найти модель и конфиг
        }
        
        if(!isset($alias_data['model'])) {
            $alias_data['model'] = array();
        }
        if(!isset($alias_data['model']['base'])) {
            $alias_data['model']['base'] = \Sl\Service\Helper::getModelAlias($model_name, $module_name);
        }
    }
    
    /**
     * Преобразовывает псевдоним связи в массив названий связей
     * 
     * @param type $alias
     * @param \Sl_Model_Abstract $model
     * @return type
     */
    public static function describeAlias($alias, \Sl_Model_Abstract $model, $extended = false) {
        $aliases = explode('.', $alias);
        $complex = (count($aliases) == 1)?false:true;
        
        if($complex) { // В псевдониме есть разделитель
            $res = array();
            $tmp_model = $model;
            foreach($aliases as $a) {
                

                
                $tmp_res = self::describeAlias($a, $tmp_model, $extended);
                if($tmp_res) {
                    $rel = \Sl_Modulerelation_Manager::getRelations($tmp_model, $a);
                    $tmp_model = $rel->getRelatedObject($tmp_model);
                    $res = array_merge($res, $tmp_res);
                }
            }
            return $res;
        } else {
            
            $rel = \Sl_Modulerelation_Manager::getRelations($model, $alias);
            if($rel) { // Граничный случай. На входе просто название связи
                if($extended) {
                    return array(
                        $rel->getName() => array(
                            'src' => \Sl\Service\Helper::getModelAlias($model),
                            'dest' => \Sl\Service\Helper::getModelAlias($rel->getRelatedObject($model)),
                        )
                    );
                } else {
                    return array($rel->getName());
                }
            } else { // На входе псевдоним
                /** @TODO
                 * На входе может быть 
                 */
                $config = \Sl_Module_Manager::getInstance()->getModule($model->findModuleName())->section('alias');
                $model_name = $model->findModelName();
                if(isset($config->$model_name) && isset($config->$model_name->$alias) && isset($config->$model_name->$alias->path)) {
                    if($extended) {
                        return self::describeAlias($config->$model_name->$alias->path, $model, $extended);
                    } else {
                        return explode('.', $config->$model_name->$alias->path);
                    }
                }
            }
        }
    }
    
    public function buildDbDestAlias(\Sl\Model\Identity\Field $field) {
        $data = self::describeAlias($field->getRelationAlias(), $field->getModel(), true);
        $last = array_pop($data);
        list($module, $model) = explode('.', $last['dest']);
        if(!$model || !$module) {
            throw new \Exception('Can\'t determine destination table. '.__METHOD__);
        }
        return self::aliasPrefix($field->getRelationAlias()).\Sl_Model_Factory::dbTable($model, $module)->info('name').'.'.$field->cleanName();
    }
}