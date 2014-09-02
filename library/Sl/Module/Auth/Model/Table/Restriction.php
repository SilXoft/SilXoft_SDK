<?php
namespace Sl\Module\Auth\Model\Table;

class Restriction extends \Sl\Model\DbTable\DbTable {
	protected $_name = 'auth_restriction';
	protected $_primary = 'id';

    public function fetchAllByRoles(array $roles, \Sl\Modulerelation\Modulerelation $role_relation) {
        $select = $this->getAdapter()->select();
        $select ->from($this->_name);
        
        $restriction = \Sl_Model_Factory::object($this);
        $role = $role_relation->getRelatedObject($restriction);
        
        $select = $this->_buildInnerJoin($select, $role, $role_relation);
        $select->where('restrictionroles.id in(?)', $roles);
        $select->where('auth_restriction.active > 0');
        //echo $select;
        //die;
        $this->_cleanJoinNames();
        
        return $this->getAdapter()->fetchAll($select, array(), \Zend_Db::FETCH_ASSOC);
    }
    
    /**
     * Вытаскивает ограничения для определенной модели по цепочке связей
     * 
     * @param \Sl_Model_Abstract $model Базовый объект
     * @param \Sl\Modulerelation\Modulerelation[] $relations Цепочка связей
     * @param int $uid Id пользователя
     * @return int[]
     */
    public function fetchComplexRestrictions(\Sl_Model_Abstract $model, array $relations, $uid) {
        $select = $this->getAdapter()->select();
        
        $main_rel_name = 'base';
        
        $model_table_name = \Sl_Model_Factory::dbTable($model)->info('name');
        $select ->from(array($main_rel_name.'_'.$model_table_name=>$model_table_name));
        
        $last_related = $model;
        
        foreach($relations as $rel) {
            $related = $rel->getRelatedObject($last_related);
            
            $mainTable = $rel->getDependedTable(get_class($related));
            $interTable = $rel->getDbTable();
            $depTable = $rel->getDependedTable(get_class(\Sl_Model_Factory::object($mainTable)));
            
            $references = $rel->findSortedReferences(get_class($last_related));
            $reference  = array_shift($references); 
            
            //$reference = $interTable->getReference(get_class($mainTable));
            
            $interName = $rel->getName().'_'.$interTable->info('name');
            $depName = $rel->getName().'_'.$depTable->info('name');
            
            $select->join(
                    array($interName => $interTable->info('name')),
                    $main_rel_name.'_'.$mainTable->info('name').'.'.$reference['refColums'].' = '.$interName.'.'.$reference['columns'],
                    array()
                );
            
            //$reference = $interTable->getReference(get_class($depTable));
            $reference  = array_shift($references);
            
            $select->join(array($depName=>$depTable->info('name')), $depName.'.'.$reference['refColums'].' = '.$interName.'.'.$reference['columns'], array());
            
            $main_rel_name = $rel->getName();
            
            $last_related = $related;
        }
        if(!$depName) {
            $depName = $main_rel_name.'_'.$model_table_name;
        }
        $select->where($depName.'.id = ?', $uid);
        //echo $select."\r\n\r\n";die;
        return $this->getAdapter()->fetchCol($select);
    }
}