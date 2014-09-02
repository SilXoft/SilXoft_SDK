<?php
namespace Sl\Module\Auth\Listener;

use Sl\Module\Auth\Service\Restrictions as Restrictions;

class Table extends \Sl_Listener_Abstract implements \Sl\Listener\Model\Table, \Sl\Listener\Action\On\Idbased, \Sl\Listener\Fieldset {
    
    public function onBeforeQuery(\Sl\Event\Table $event) {
        $restricts = \Sl\Module\Auth\Service\Restrictions::restrictions($event->getModel());
        if($restricts) {
            foreach($restricts as $relation_name=>$restrictions) {
                if(!is_array($restrictions) || (count($restrictions) == 0)) continue;
                $main_relation = current($restrictions)->fetchMainRelation(true);
                /*@var $main_relation \Sl\Modulerelation\Modulerelation*/
                if($main_relation->getName() !== $relation_name) {
                    throw new \Exception('Something wrong with main relation. ');
                }
                $relation_alias = false;
                foreach($event->getQuery()->getPart(\Zend_Db_Select::FROM) as $alias=>$data) {
                    if($data['tableName'] == $main_relation->getIntersectionDbTable()->info('name')) {
                        $relation_alias = $alias;
                    }
                }
                
                $references = $main_relation->findSortedReferences(get_class($event->getModel()));
                $reference  = array_shift($references); 
                
                if(!$relation_alias) {
                    // Нужно добавить join, чтобы было что вытаскивать
                    $main_table = \Sl_Model_Factory::dbTable($event->getModel());
                    $main_table_name = $main_table->info('name');
                    $inter_table = $main_relation->getIntersectionDbTable();
                    $inter_table_name = $inter_table->info('name');
                    
                    //$reference = $main_relation->getIntersectionDbTable()->getReference(get_class($main_table));
                    
                    $relation_alias = $main_relation->getName().'_'.$inter_table_name;
                    
                    $event->getQuery()->join(array($relation_alias=>$inter_table_name), $main_table_name.'.'.$reference['refColums'].' = '.$relation_alias.'.'.$reference['columns'], array());
                }
                $related_model = $main_relation->getRelatedObject($event->getModel());
                $related_table = \Sl_Model_Factory::dbTable($related_model);
                $reference  = array_shift($references);
                //$reference = $main_relation->getIntersectionDbTable()->getReference(get_class($related_table));

                $null_include = '';
                if(current($restrictions)->getNullInclude()) {
                    $null_include = ' or '.$relation_alias.'.'.$reference['columns'].' is null';
                }
                $restrict_ids = \Sl\Module\Auth\Service\Restrictions::restrictions($event->getModel(), $main_relation);
                if(count($restrict_ids) == 0) {
                    $restrict_ids = array(0);
                }
                $event->getQuery()->where($relation_alias.'.'.$reference['columns'].' in(?)'.$null_include, $restrict_ids);
            }
        }
    }

    public function onAfterConstruct(\Sl_Event_Action $event) {
        $restricts = \Sl\Module\Auth\Service\Restrictions::restrictions($event->getModel());
        $model = \Sl_Model_Factory::mapper($event->getModel())->findExtended($event->getModel()->getId(), array_keys($restricts));
        foreach(array_keys($restricts) as $rel_name) {
            $res_array = $restricts[$rel_name];
            $cur_res = null;
            if(is_array($res_array) && (count($res_array) > 0)) {
                $cur_res = current($res_array);
            }
            if($cur_res && $cur_res->getNullInclude()) {
                if(count($model->fetchRelated($rel_name)) == 0) continue;
            }
            if(count(
                    array_intersect(
                            array_map(
                                    function($el){ return $el->getId(); },
                                    $model->fetchRelated($rel_name)
                            ), 
                            \Sl\Module\Auth\Service\Restrictions::restrictions(
                                    $model,
                                    \Sl_Modulerelation_Manager::getRelations($model, $rel_name)
                            )
                    )) <= 0) {
                        throw new \Exception('You don\'t have permissions to access this page. Relation: '.$rel_name.'; Item has: '.implode(', ', array_map(
                                    function($el){ return $el->getId(); },
                                    $model->fetchRelated($rel_name)
                            )).' and you can: '.implode(', ', \Sl\Module\Auth\Service\Restrictions::restrictions(
                                    $model,
                                    \Sl_Modulerelation_Manager::getRelations($model, $rel_name)
                            )));
            }
        } 
    }

    public function onPrepare(\Sl\Event\Fieldset $event) {
        
    }

    public function onPrepareAjax(\Sl\Event\Fieldset $event) {
        $restricts = Restrictions::restrictions($event->getModel());
        foreach($restricts as $relation_name=>$restrictions) {
            if(!is_array($restrictions) || (count($restrictions) == 0)) continue;
            $main_relation = current($restrictions)->fetchMainRelation(true);
            if($main_relation->getName() !== $relation_name) {
                throw new \Exception('Something wrong with main relation. ');
            }
            $restrict_ids = Restrictions::restrictions($event->getModel(), $main_relation);
            if(current($restrictions)->getNullInclude()) {
                $restrict_ids[] = 'null';
            }
            if(count($restrict_ids) == 0) {
                $restrict_ids = array(0);
            }
            $field_rules = explode(':', current($restrictions)->getRules());
            array_splice($field_rules, -1, 1, 'id');
            $field_name = implode('.', $field_rules);
            $event->getFieldset()->addComps(array(\Sl\Model\Identity\Fieldset\Comparison\Factory::build(array(
                'type' => 'in',
                'field' => $field_name,
                'value' => $restrict_ids,
            ), $event->getFieldset())));
        }
    }

}