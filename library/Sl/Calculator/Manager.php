<?php

class Sl_Calculator_Manager {

	protected static $_instance;
	const CALCULATOR_CLASS_PREFIX = 'field';
	const CALCULATOR_CLASS_SEPARATOR = '-';
	const FORM_WARNINGS_KEY = 'form_warnings';
	const FORM_WARNINGS_DESCRIPTION_KEY = 'description';
	const FORM_WARNINGS_FIELDS_KEY = 'fields';
	protected static $_calculators = array();
	protected static $_model_calculators = array();
	protected static $_identity_calculators = array();

	protected function __construct() {

	}

	/**
	 * Вертає масив калькуляторів по моделі або identity
	 *
	 * @param Sl_Model_Abstract $Obj - об'єкт моделі
	 * @return array масив назв зв'язків
	 */
	public static function getCalculators($Obj) {
		if ($Obj instanceof \Sl_Model_Abstract){
			return (isset(self::$_model_calculators[get_class($Obj)])) ? self::$_model_calculators[get_class($Obj)] : array();
		}elseif($Obj instanceof \Sl\Model\Identity\Identity) {
			return (isset(self::$_identity_calculators[get_class($Obj)])) ? array(self::$_identity_calculators[get_class($Obj)]) : array();
		}
		
	}

	/**
	 * Вертає масив полів, пов'язаних з калькуляторами по моделі
	 *
	 * @param Sl_Model_Abstract $Obj - об'єкт моделі
	 * @return array масив назв зв'язків
	 */
	 
	public static function getFieldsCalculator(\Sl_Model_Abstract $Obj) {
	    
        \Sl_Service_Acl::setContext($Obj);
        
		$calculators = self::getCalculators($Obj);
		
		$fields = array();
		$calculators_required_fields = array();
		$calculators_unwarning_fields = array();
		$calculators_by_object = array();
        $modelname = $Obj->findModelName();
        $calculators_by_object [$modelname]=array();
        
		foreach ($calculators as $row) {
			foreach ($row as $calculator_name) {
				$calculator = self::getCalculator($calculator_name);
                $calculators_by_object[$modelname][]=$calculator->getName(); 
				$required_fields = $calculator -> getRequiredFields();
				$calculators_required_fields[$calculator_name]=$required_fields;
				if ($calculator instanceof \Sl\Calculator\Uniquechecker && count($calculator->getCheckedFields())){
					$calculators_unwarning_fields[$calculator_name]=$calculator->getCheckedFields();
				}
				foreach ($required_fields as $field_name) {
					if (!isset($fields[$field_name]))
						$fields[$field_name] = array();
					$fields[$field_name][] = $calculator -> getName();
				}

			}
		}

		$relations = \Sl_Modulerelation_Manager::getRelations($Obj);
		$modulerelations = array();
		$calculators_modulerelations_required_fields = array();
		$calculators_modulerelations_unwarning_fields = array();
		
		foreach ($relations as $relation) {

			// Якщо зв'язок - ітем і він доступний в ACL для редагування

			if ($relation -> getType() == \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER && \Sl_Service_Acl::isAllowed(array(
				$Obj,
				$relation -> getName()
			), \Sl_Service_Acl::PRIVELEGE_UPDATE)) {

				// дістати калькулятори для ітема

				$relation_calculators = self::getCalculators($relation -> getRelatedObject($Obj));
				//echo get_class($relation -> getRelatedObject($Obj));
				$relname = \Sl_Form_Factory::setElementRelationName($relation -> getName());
				$calculators_by_object[$relname] = array();
				
				if (count($relation_calculators)) {
					$relation_fields = array();
					
					foreach ($relation_calculators as $row) {
						
						foreach ($row as $calculator_name) {
							
							$calculator = self::getCalculator($calculator_name);
							$calculators_by_object[$relname][]=$calculator->getName();
							
							if ($calculator instanceof \Sl\Calculator\Uniquechecker && count($calculator->getCheckedFields())){
								$calculator_unwarning_fields=$calculator->getCheckedFields();
								$calculators_modulerelations_unwarning_fields[$relname]=array($calculator_name =>$calculator_unwarning_fields);
							}
							
							$required_fields = $calculator -> getRequiredFields();
							//echo $calculator_name;
							foreach ($required_fields as $field_name) {

								if (!isset($relation_fields[$field_name]))
									$relation_fields[$field_name] = array();
								$relation_fields[$field_name][] = $calculator -> getName();
							}
							if (count($required_fields)){
								if(!isset($calculators_modulerelations_required_fields[\Sl_Form_Factory::setElementRelationName($relation -> getName())]) || !is_array($calculators_modulerelations_required_fields[\Sl_Form_Factory::setElementRelationName($relation -> getName())])){
									$calculators_modulerelations_required_fields[\Sl_Form_Factory::setElementRelationName($relation -> getName())] = array();
								}
								$calculators_modulerelations_required_fields[\Sl_Form_Factory::setElementRelationName($relation -> getName())][$calculator_name]=$required_fields;
								//print_r($calculators_modulerelations_required_fields);
							}
						}
					}
					if (count($relation_fields))
						$modulerelations[\Sl_Form_Factory::setElementRelationName($relation -> getName())] = $relation_fields;
					
				}
			}
		}



		return array(
			'fields' => $fields,
			'modulerelations' => $modulerelations,
			'calculators_fields' => $calculators_required_fields,
			'relations_calculators_fields' => $calculators_modulerelations_required_fields,
			'unwarning_fields' => $calculators_unwarning_fields,
			'relations_unwarning_fields' => $calculators_modulerelations_unwarning_fields,
			'calculators_by_model' => $calculators_by_object
		);
	}

	public static function getAllCalculators() {
		return self::$_calculators;
	}

	public static function setCalculator(\Sl\Calculator\Calculator $Calculator, $sort_order = false) {
		
		if (!isset(self::$_calculators[$Calculator -> getName()])){
			self::$_calculators[$Calculator -> getName()] = $Calculator;
			
		}
		
			
		
		if ($Calculator instanceof \Sl\Calculator\Identitycalculator){
			
			self::$_identity_calculators[$Calculator -> getModelName()][]=$Calculator->getName();
			
		} else {

			if ( !isset(self::$_model_calculators[$Calculator -> getModelName()]))
				self::$_model_calculators[$Calculator -> getModelName()] = array();
			if (!$sort_order) {
				$sort_order = count(self::$_model_calculators[$Calculator -> getModelName()]) ? max(array_keys(self::$_model_calculators[$Calculator -> getModelName()])) + 10 : 10;
			}
			self::$_model_calculators[$Calculator -> getModelName()][$sort_order][] = $Calculator -> getName();
			ksort(self::$_model_calculators[$Calculator -> getModelName()]);	
		}
		
	}

	public static function getCalculator($object) {
		if ($object instanceof \Sl\Model\Identity\Identity){
			$identity_calculators = self::getCalculators($object); 
			if (count($identity_calculators)){
				$identity_calculator=array_shift($identity_calculators);
                if(is_array($identity_calculator)) {
                    $calcs = array();
                    foreach($identity_calculator as $name) {
                        $calcs[] = (isset(self::$_calculators[$name])) ? self::$_calculators[$name] : null;
                    }
                    return $calcs;
                }
			}
			return (isset(self::$_calculators[$identity_calculator])) ? self::$_calculators[$identity_calculator] : null;
		} else {
			return (isset(self::$_calculators[$object])) ? self::$_calculators[$object] : null;	
		}	
		

	}
	
	public static function Calculate ($calculators, $changed_fields = array(), $values = array(), $Obj = false){
		//print_r($values);	
		$result_values = array(self::FORM_WARNINGS_KEY=>array()); 
			
		$calculators = !is_array($calculators)?array($calculators):$calculators;
		$Obj = (!$Obj)? self::getCalculator(current($calculators))->getModel():$Obj;
		
		// якщо в реквесті передана назва масиву, в який вкладаються властивості моделі
		if (isset($values['relation_array_name'])){
			$relation_array_name = $values['relation_array_name'];
			unset($values['relation_array_name']);
			$values_array = $values[$relation_array_name];
			$values = current($values_array); 
		}
                if(!isset($values['relation_array_name']) ){
                    foreach ($values as $el){
                        
                        if(is_array($el)){
                            $values = $values+$el;
                        }
                    }
                }
                //print_r($values);
                //die();
		$Obj->setOptions($values);
		
		foreach ($calculators as $calculator_name){
			$calculator=self::getCalculator($calculator_name);
			if (get_class($Obj) == $calculator->getModelName() && $calculator->isValid($values)){
				
				
				$result_values = array_merge($calculator->setChangedFields($changed_fields)->calculate($Obj)->getValues(),$result_values);
				$result_values[self::FORM_WARNINGS_KEY] = array_merge($calculator->getWarnings(),$result_values[self::FORM_WARNINGS_KEY]);
				
			}
		} 
		
		
		if (isset($relation_array_name)){
			$transformed_values = array();
			foreach ($result_values as $name => $value){
				if ($name == self::FORM_WARNINGS_KEY){
					$transformed_values[$name]=array();
					if (count($value)){
						foreach ($value as $warning){
							$new_warning = $warning;
							if (isset($warning[self::FORM_WARNINGS_FIELDS_KEY])){
								foreach ($warning[self::FORM_WARNINGS_FIELDS_KEY] as $key => $field_name){
									$new_warning[self::FORM_WARNINGS_FIELDS_KEY][$key]=$relation_array_name.self::CALCULATOR_CLASS_SEPARATOR.$field_name;
								}
							}
							$transformed_values[$name][]=$new_warning;
						}
					}
					
				} else {
					$transformed_values[$relation_array_name.self::CALCULATOR_CLASS_SEPARATOR.$name]=$value;
				}
				
				
			}
			$result_values = $transformed_values;
		}
		
		return $result_values;
		 
	}
	
	public static function prepareFieldNames( array $values){
		$result_values = array();
		foreach ($values as $fieldname => $value){
			//Підготовка повідомлень про невідповідність	
			if (preg_match('/'.self::FORM_WARNINGS_KEY.'$/',$fieldname)){
				$warnings=$value;
				$new_warnings = array();	
				foreach ($warnings as $warning){
						
					$new_warning = $warning;
					
					if (isset($warning[self::FORM_WARNINGS_FIELDS_KEY])){
					
						foreach ($warning[self::FORM_WARNINGS_FIELDS_KEY] as $key => $name){
							$new_warning[self::FORM_WARNINGS_FIELDS_KEY][$key] = self::CALCULATOR_CLASS_PREFIX.self::CALCULATOR_CLASS_SEPARATOR.$name;
						}
					}
					$new_warnings[]=$new_warning;
					
				}
				
				$result_values[$fieldname]=$new_warnings;
			}else {
				$result_values [self::CALCULATOR_CLASS_PREFIX.self::CALCULATOR_CLASS_SEPARATOR.$fieldname]=$value;
			}
			
		}
		return $result_values;
	}
	

}

