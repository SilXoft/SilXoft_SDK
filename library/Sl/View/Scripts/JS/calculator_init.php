<? if ((is_array($this->fields['fields']) && count($this->fields['fields']))||(is_array($this->fields['modulerelations']) && count($this->fields['modulerelations']))): 
	$field_prefix= $this->field_prefix.$this->separator;
	$field_separator= $this->separator;
?>
    if (calculator_selectors == undefined) {
        var calculator_selectors={};    
    }
	 
	calculator_selectors['<?=$this->model_name?>'] = {};
<? if ((is_array($this->fields['fields']) && count($this->fields['fields']))){ ?>
	calculator_selectors['<?=$this->model_name?>']['fields'] = {};
	<? foreach($this->fields['fields'] as $field_name=>$field_array): ?>
	calculator_selectors['<?=$this->model_name?>']['fields']['<?=$field_prefix.$field_name?>']=['<?=implode("', '",$field_array)?>'];
	<? endforeach;?> 
<? } ?>
<? if ((is_array($this->fields['modulerelations']) && count($this->fields['modulerelations']))){ ?>
	calculator_selectors['<?=$this->model_name?>']['modulerelations'] = {};
	<? foreach($this->fields['modulerelations'] as $relation_name=>$relation_array): ?>
		calculator_selectors['<?=$this->model_name?>']['modulerelations']['<?=$relation_name?>'] = {};
		<? foreach($relation_array as $field_name=>$field_array): ?>
		calculator_selectors['<?=$this->model_name?>']['modulerelations']['<?=$relation_name?>']['<?=$field_prefix.$relation_name.$field_separator.$field_name?>']=['<?=implode("', '",$field_array)?>'];
		<? endforeach;?>
	<? endforeach;?> 
<? }?>
    
    calculator_selectors['<?=$this->model_name?>']['comformity'] = {};

<? if (is_array($this->fields['calculators_by_model']) && count(is_array($this->fields['calculators_by_model']))){
        foreach ($this->fields['calculators_by_model'] as $selector => $calcs){
            if (!count($calcs)) continue;
            ?>
            
            calculator_selectors['<?=$this->model_name?>']['comformity']['<?=$selector?>']=['<?=implode("','",$calcs)?>'];
            <?
        }        
        
    
    } ?>    

	calculator_selectors['<?=$this->model_name?>']['calculators_fields'] = {};
<? if ((is_array($this->fields['calculators_fields']) && count($this->fields['calculators_fields']))){ ?>
	//calculators_fields
	<? foreach($this->fields['calculators_fields'] as $calculator_name=>$fields): 
		$field_array = array_map(function($el) use ($field_prefix) { return $field_prefix.$el;},$fields);
	?>
	
	calculator_selectors['<?=$this->model_name?>']['calculators_fields']['<?=$calculator_name?>']=['<?=implode("', '",$field_array)?>'];
	<? endforeach;?> 
<? } ?>
	calculator_selectors['<?=$this->model_name?>']['unwarning_fields'] = {};
<? if ((is_array($this->fields['unwarning_fields']) && count($this->fields['unwarning_fields']))){ ?>
	
	<? foreach($this->fields['unwarning_fields'] as $calculator_name=>$fields): 
		$field_array = array_map(function($el) use ($field_prefix) { return $field_prefix.$el;},$fields);
	?>
	calculator_selectors['<?=$this->model_name?>']['unwarning_fields']['<?=$calculator_name?>']=['<?=implode("', '",$field_array)?>'];
	<? endforeach;?> 
<? } ?>


<? if ((is_array($this->fields['relations_calculators_fields']) && count($this->fields['relations_calculators_fields']))){ ?>
	/*	
	<? //print_r($this->fields['relations_calculators_fields']); ?>
	*/
	<?
		foreach($this->fields['relations_calculators_fields'] as $relation_name=>$relation_array): 
		
		?>
		//relations_calculators_fields
		<? foreach($relation_array as $calculator_name=>$fields):
		 	$field_array = array_map(function($el) use ($field_prefix, $relation_name,$field_separator) { return $field_prefix.$relation_name.$field_separator.$el;},$fields);
		?>
	calculator_selectors['<?=$this->model_name?>']['calculators_fields']['<?=$calculator_name?>'] = ['<?=implode("', '",$field_array)?>'];
		<? endforeach;?>
	<? endforeach;?> 
<? }?>

<? if ((is_array($this->fields['relations_unwarning_fields']) && count($this->fields['relations_unwarning_fields']))){ ?>

	<? foreach($this->fields['relations_unwarning_fields'] as $relation_name=>$relation_array): ?>
		<? foreach($relation_array as $calculator_name=>$fields):
		 	$field_array = array_map(function($el) use ($field_prefix, $relation_name,$field_separator) { return $field_prefix.$relation_name.$field_separator.$el;},$fields);
		?>
	calculator_selectors['<?=$this->model_name?>']['unwarning_fields']['<?=$calculator_name?>'] = ['<?=implode("', '",$field_array)?>'];
		<? endforeach;?>
	<? endforeach;?> 
<? }?>

<? endif;?>