<span<?php echo ($this -> class?' class="'.$this->class.'"':''); ?> <?=(mb_strlen($this->list_value,'utf-8') > 40?'title="'.strip_tags($this->list_value).'"':'')?><?=' data-field="'.$this->name.'"'; ?>  <?=($this->control_field?' data-lists="'.$this->name.'_'.$this->control_value.'"':'')?> ><?
	
	echo ((!$this->field_data['html'] && mb_strlen($this->list_value,'utf-8') > 40)?mb_substr($this->list_value,0,40,'utf-8').'…':$this->list_value);
	
    
    
?></span>
