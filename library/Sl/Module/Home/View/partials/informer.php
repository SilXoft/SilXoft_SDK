<div id='btn_informer_w' class='allways-open'>
  
        <div id='btn_informer'>
     <? $this->informer_items = array();?>   
     <?php Sl_Event_Manager::trigger(new Sl_Event_View('informer', array('view' => $this))); ?>
     <? if (isset($this->informer_items) && count($this->informer_items)){
         foreach($this->informer_items as $item) echo $item;
     } ?>
        </div>
</div>