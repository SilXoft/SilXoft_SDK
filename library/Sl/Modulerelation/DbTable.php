<?php

namespace Sl\Modulerelation;

abstract class DbTable extends \Sl\Model\DbTable\DbTable {

    public function findDependedTable($classname = false) {
        if ($classname) {
            
            $model = \Sl_Model_Factory::object($classname);          
            if ($model->checkExtend($model)) {
                $classname = $model->Extend();
            }
            
            if (isset($this->_referenceMap[$classname])) {

                $map = $this->_referenceMap;
                unset($map[$classname]);

                $depended = array_shift($map);

                return $depended['refTableClass'];
            } elseif ($model->checkExtend($model) && isset($this->_referenceMap[get_class($model)])) {
                
                
                $map = $this->_referenceMap;
                unset($map[get_class($model)]);

                $depended = array_shift($map);

                return $depended['refTableClass'];
            } else {
             //   echo $classname;
              //  die('4444');

                throw new \Sl_Exception_Modulerelation("Error when find depended table: key for class {$classname} is not exist.!");
            }
        } else {
            $array = array();
            foreach ($this->_referenceMap as $relation_array)
                $array[] = $relation_array['refTableClass'];
            return $array;
        }
    }

    public function findRefetenceArray($classname = false) {
        if ($classname) {
            if (isset($this->_referenceMap[$classname])) {

                return $this->_referenceMap[$classname];
            }
            $model = new $classname;

            $parents = \Sl_Model_Factory::mapper($model)->findParents(false);
            foreach ($parents as $parent) {

                if (isset($this->_referenceMap[$parent])) {

                    return $this->_referenceMap[$parent];
                }
            }
            throw new \Sl_Exception_Modulerelation("Error when find depended table: key for class {$classname} is not exist.!!");
        } else {

            return $this->_referenceMap;
        }
    }

    public function findRelatedModelsKeys($classname = false) {
        if ($classname) {
            
            if( $classname != 'reverse' ){
                $model = \Sl_Model_Factory::object($classname);          
                    if ($model->checkExtend($model)) {
                        $classname = $model->Extend();               
                        }
            }
            
            if (isset($this->_referenceMap[$classname])) {
                $array = $this->_referenceMap;
                unset($array[$classname]);
                $array_keys = array_keys($array);
                $model_name = array_shift($array_keys);

                $model_name = ($model_name == \Sl_Modulerelation_Manager::SELFRELATION_PREFIX) ? $classname : $model_name;
                return $model_name;
            } elseif ($model->checkExtend($model) && isset($this->_referenceMap[get_class($model)])) {
                $array = $this->_referenceMap;
                unset($array[$classname]);
                $array_keys = array_keys($array);
                $model_name = array_shift($array_keys);

                $model_name = ($model_name == \Sl_Modulerelation_Manager::SELFRELATION_PREFIX) ? $classname : $model_name;

                return $model_name;
//            } 
            
            }else {
                throw new \Sl_Exception_Modulerelation("Error when find depended table: key for class {$classname} is not exist.!!!");
            }
        } else {
            return array_keys($this->_referenceMap);
        }
    }


    public function getName() {
        return \Sl_Modulerelation_Manager::buildModulerelationName(get_class($this));
    }

}
