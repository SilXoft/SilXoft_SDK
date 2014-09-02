



<div class="detailed-view">

    <? foreach ($this->data as $name => $element) { ?>
        <? if (isset($element['itemowner'])) { ?>
           <div class='itemowner'><div class='itemowner-label'><?=$element['label']?>:</div> 
                <? foreach ($element['value'] as $num => $arr) { ?>
                    <div class='itemowner-item'><?
                        foreach ($arr as $item => $val) {
                            echo $this->partial('detailexpload.phtml', array('array' => $val));
                        }
                        ?></div>
            <? } ?></div>
        <?
        } else {
           // echo $this->partial('detailexpload.phtml', array('array' => $element));
        }
    }
    ?> 

</div>                  





















