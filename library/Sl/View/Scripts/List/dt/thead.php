<thead>
        
        <tr role="row" data-counter="<?php echo (count($this->fields) + 4); ?>">
            <th class='hidden'></th>
            <th class='hidden'></th>
            <th class='hidden'></th>
            <?php if($this->check_type) { ?>
                <th class='check-header'><?if ($this->check_type=='checkbox'){
                    $this->list_button = new \Sl\View\Control\Lists( array(
                     'icon_class' => 'ok',
                     'title' => $this->translate('Групповая обработка'),
                     'small' => true, 
                     'drop_dir' => 'down',
                     'badge_text' => '0',
                     'class' => 'groupbtn',
                        
        ));
                    \Sl_Event_Manager::trigger(new \Sl_Event_View('beforeListViewTableGroupButton', array('view' => $this, 'object' => $this->base_object)));
                    //if ($this->base_object instanceof \Sl\Module\Logistic\Model\Package){ 
                    //\Sl_Event_Manager::trigger(new \Sl_Event_View('addPackageListTableGroupButton', array('view'=>$this, 'object' => $this->base_object)));
                    //}
                    echo $this->list_button;     
                    
                    ?>
                <?}?>
                    
                </th>
            <?php } ?>
            <?php
            $column_num = 2 +  ((int) $this->check_type);
            foreach ($this->fields as $field) {
                echo $this->partial('dt/th.search.php', array(
                                                            'name' => $field['name'],
                                                            'title' => $field['label'],
                                                            'id' => $field['column_name'],
                                                            'field' => $field,
                                                            'searchable' => $this->disable_search? false: $field['searchable'],
                                                            'column_number' => $column_num++,
                                                        )
                                    );
            }
            ?>
           
        </tr>
        <tr role="row" class='titles' data-counter="<?php echo (count($this->fields) + 4); ?>">
            <th class='hidden'><!-- Id --></th> 
            <th class='hidden'><!-- Editable --></th>
            <th class='hidden'><!-- Controller --></th>
            <?php if($this->check_type) { ?>
                <th></th>
            <?php } ?>
            <?php
            $counter = 2 + ((int) $this->check_type);
            //print_r(array_map(function($el){ return $el['name']; }, $this->fields));
            foreach ($this->fields as $field) {
                echo $this->partial('dt/th.php', array('counter' => $counter++, 'level' => 1, 'field' => $field));
            }
            ?>
           
        </tr>
    </thead>