<th data-sort="<?php echo $this->sort_order; ?>" data-field-name="<?php echo $this->name; ?>" class="<?php echo $this->class?$this->class:''; ?>" style="padding-right: 10px;"><?php
    $classes = array('dt_search_input');
    if($this->inputClasses) {
        if(is_array($this->inputClasses)) {
            $classes = $this->inputClasses;
        } elseif(is_string($this->inputClasses)) {
            $classes[] = $this->inputClasses;
        }
    }
    if($this->field['class']) {
        $classes[] = $this->field['class'];
    }
    if($this->searchable) {
        if($this->field['type'] == 'date') {
            $classes[] = 'date';
            $classes[] = 'span1';
            if($this->field['single_date_search']) {
                $classes[] = 'single_date_search';
                ?>
                    <input data-ind="<?php echo $this->column_number; ?>" type="text" name="<?php echo $this->name; ?>" id="<?php echo $this->id; ?>" class="<?php echo implode(' ', $classes); ?>" />
                <?php
            } else {
                ?>
                    <span style="white-space: nowrap;">
                        <input data-ind="<?php echo $this->column_number; ?>" type="text" name="<?php echo $this->name; ?>_begin" id="<?php echo $this->id; ?>_begin" class="<?php echo implode(' ', $classes); ?>" />
                        <!--<hr style="height: 1px; line-height: 1px; padding: 0px; margin: 0px;" />-->
                        <input data-ind="<?php echo $this->column_number; ?>" type="text" name="<?php echo $this->name; ?>_end" id="<?php echo $this->id; ?>_end" class="<?php echo implode(' ', $classes); ?>" />
                    </span>
                <?php
            }
        }elseif($this->field['type'] == 'datetime') {
            $classes[] = 'datetime';
            $classes[] = 'span1';
            if($this->field['single_datetime_search']) {
                $classes[] = 'single_datetime_search';
                ?>
                    <input data-ind="<?php echo $this->column_number; ?>" type="text" name="<?php echo $this->name; ?>" id="<?php echo $this->id; ?>" class="<?php echo implode(' ', $classes); ?>" />
                <?php
            } else {
                ?>
                    <span style="white-space: nowrap;">
                        <input data-ind="<?php echo $this->column_number; ?>" type="text" name="<?php echo $this->name; ?>_begin" id="<?php echo $this->id; ?>_begin" class="<?php echo implode(' ', $classes); ?>" />
                        <!--<hr style="height: 1px; line-height: 1px; padding: 0px; margin: 0px;" />-->
                        <input data-ind="<?php echo $this->column_number; ?>" type="text" name="<?php echo $this->name; ?>_end" id="<?php echo $this->id; ?>_end" class="<?php echo implode(' ', $classes); ?>" />
                    </span>
                <?php
            }
        }  
        
        elseif($this->field['select'] && $this->field['options'] && count($this->field['options'])) {
            ?>
                <select data-ind="<?php echo $this->column_number; ?>" name="<?php echo $this->name; ?>" id="<?php echo $this->id; ?>" class="<?php echo implode(' ', $classes); ?>">
                    <?php foreach($this->field['options'] as $value=>$name) { ?>
                        <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                </select>
            <?php
        } else {
            ?><input class="<?php echo implode(' ', $classes); ?>" data-ind="<?php echo $this->column_number; ?>" type="text" name="<?php echo $this->name; ?>" id="<?php echo $this->id; ?>" class="<?php echo implode(' ', $classes); ?>" /><?php
        }
    }
?></th>
