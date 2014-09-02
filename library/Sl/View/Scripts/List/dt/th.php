<?php //echo '<!-- level: '.$this->field['name'].' : '.((int) $this->field['visible']).'-->'; ?>
<?php //echo '<!-- field: '.print_r($this->field, true).'-->'; ?>
<?php if($this->level == 1) { ?>
    <?php if(isset($this->field['rel_name'])) { ?>
        <?php if($this->field['is_first']) { ?>
            <?php if($this->field['rel_columns'] > 1) { ?>
                <th <?=($this->field['calculate']?'data-calc="'.$this->field['calculate'].'"':'')?>  data-sort="<?php echo $this->field['sort_order']; ?>" data-type="1" data-ind="<?php echo $this->counter; ?>" colspan="<?php echo $this->field['rel_columns']; ?>" <?=' field-name="'.($this->field['rel_name']?$this->field['rel_name'].'-':'').$this->field['name'].'"'?> >
                    <?php echo $this->field['label']?$this->field['label']:$this->field['rel_name']; ?> 
                </th>
            <?php } else { ?>
                <th <?=($this->field['calculate']?'data-calc="'.$this->field['calculate'].'"':'')?> data-sort="<?php echo $this->field['sort_order']; ?>" data-type="2" data-ind="<?php echo $this->counter; ?>" <?=' field-name="'.($this->field['rel_name']?$this->field['rel_name'].'-':'').$this->field['name'].'"'?>><?php
                    echo $this->field['label']?$this->field['label']:$this->field['rel_name'];
                ?></th>
            <?php } ?>
        <?php } else { ?>
                <th <?=($this->field['calculate']?'data-calc="'.$this->field['calculate'].'"':'')?> data-sort="<?php echo $this->field['sort_order']; ?>" <?=' field-name="'.$this->field['name'].'"'?> data-ind="<?php echo $this->counter; ?>"></th>
        <?php } ?>
    <?php } else { ?>
        <th <?=($this->field['calculate']?'data-calc="'.$this->field['calculate'].'"':'')?> data-type="3" data-sort="<?php echo $this->field['sort_order']; ?>" data-ind="<?php echo $this->counter; ?>"  <?=' field-name="'.$this->field['name'].'"'?>><?php echo $this->field['label'];?> </th>
    <?php } ?>
<?php } else { ?>
    <?php if($this->field['rel_name']) { ?>
        <?php if($this->field['rel_columns'] && ($this->field['rel_columns'] > 1)) { ?>
            <th <?=($this->field['calculate']?'data-calc="'.$this->field['calculate'].'"':'')?> data-sort="<?php echo $this->field['sort_order']; ?>" data-ind="<?php echo $this->counter; ?>" data-name="<?php echo $this->field['name']; ?>" data-cols="<?php echo $this->field['rel_columns']; ?>" <?=' field-name="'.$this->field['name'].'"'?> <?php echo $this->field['label']; ?>></th>
        <?php } else { ?>
            <th <?=($this->field['calculate']?'data-calc="'.$this->field['calculate'].'"':'')?> data-sort="<?php echo $this->field['sort_order']; ?>"   data-ind="<?php echo $this->counter; ?>" >
        <?php } ?>
    <?php } else { ?>
        <!--  data-ind="<?php echo $this->counter; ?>" Ничего выводить не нужно (<?php echo $this->field['name'].' no rel_name.'; ?>) -->
    <?php } ?>
<?php } ?>
