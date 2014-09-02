<?php 
$unsorts = $unsearchs =  $this->check_type?array(0,1,2,3):array(0,1,2);
$invisibles = array(0,1,2);
$visibles = array();
$popup = strlen($this->check_type)?1:0;
foreach($this->fields as $k=>$field) {
    if(isset($field['searchable']) && !$field['searchable']) {
        $unsearchs[] = ($k+3+$popup);
    }
    if(isset($field['sortable']) && !$field['sortable']) {
        $unsorts[] = ($k+3+$popup);
    }
    if(!isset($field['visible']) || (isset($field['visible']) && !$field['visible'])) {
        $invisibles[] = ($k+3+$popup);
    } else {
        $visibles[$k+3+$popup] = $field;
    }
    if(isset($field['list_hidden']) && $field['list_hidden']) {
        $invisibles[] = ($k+3+$popup);
    }	
}
?>

<script type="text/javascript">
var use_prev_data = true;
var popup_table;
entry_point = '<?=$this->selected_strings_url?>';
var export_entry_point = '<?=$this->export_entry_point;?>';
var selected_items_entry_point = '<?=$this->selected_items_entry_point;?>';
var popupTitle;

$.extend( $.fn.dataTableExt.oStdClasses, {
    "sWrapper": "dataTables_wrapper form-inline"
} );

$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
	return {
		"iStart":         oSettings._iDisplayStart,
		"iEnd":           oSettings.fnDisplayEnd(),
		"iLength":        oSettings._iDisplayLength,
		"iTotal":         oSettings.fnRecordsTotal(),
		"iFilteredTotal": oSettings.fnRecordsDisplay(),
		"iPage":          oSettings._iDisplayLength === -1 ?
			0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
		"iTotalPages":    oSettings._iDisplayLength === -1 ?
			0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
	};
};

$.fn.dataTableExt.oApi.fnVisibleToColumnIndex = function ( oSettings, iMatch )
{
    return oSettings.oApi._fnVisibleToColumnIndex( oSettings, iMatch );
};

$.fn.dataTableExt.oApi.fnColumnIndexToVisible = function ( oSettings, iMatch )
{
  return oSettings.oApi._fnColumnIndexToVisible( oSettings, iMatch );
};

$.extend( $.fn.dataTableExt.oPagination, {
	"bootstrap": {
		"fnInit": function( oSettings, nPaging, fnDraw ) {
			var oLang = oSettings.oLanguage.oPaginate;
			var fnClickHandler = function ( e ) {
				e.preventDefault();
				if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
					fnDraw( oSettings );
				}
			};

			$(nPaging).addClass('pagination').append(
				'<ul>'+
					'<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
					'<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
				'</ul>'
			);
			var els = $('a', nPaging);
			$(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
			$(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
		},

		"fnUpdate": function ( oSettings, fnDraw ) {
			var iListLength = 5;
			var oPaging = oSettings.oInstance.fnPagingInfo();
			var an = oSettings.aanFeatures.p;
			var i, ien, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

			if ( oPaging.iTotalPages < iListLength) {
				iStart = 1;
				iEnd = oPaging.iTotalPages;
			}
			else if ( oPaging.iPage <= iHalf ) {
				iStart = 1;
				iEnd = iListLength;
			} else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
				iStart = oPaging.iTotalPages - iListLength + 1;
				iEnd = oPaging.iTotalPages;
			} else {
				iStart = oPaging.iPage - iHalf + 1;
				iEnd = iStart + iListLength - 1;
			}

			for ( i=0, ien=an.length ; i<ien ; i++ ) {
				// Remove the middle elements
				$('li:gt(0)', an[i]).filter(':not(:last)').remove();

				// Add the new list items and their event handlers
				for ( j=iStart ; j<=iEnd ; j++ ) {
					sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
					$('<li '+sClass+'><a href="#">'+j+'</a></li>')
						.insertBefore( $('li:last', an[i])[0] )
						.bind('click', function (e) {
							e.preventDefault();
							oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
							fnDraw( oSettings );
						} );
				}

				// Add / remove disabled classes from the static elements
				if ( oPaging.iPage === 0 ) {
					$('li:first', an[i]).addClass('disabled');
				} else {
					$('li:first', an[i]).removeClass('disabled');
				}

				if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
					$('li:last', an[i]).addClass('disabled');
				} else {
					$('li:last', an[i]).removeClass('disabled');
				}
			}
		}
	}
} );

var dtCustomConfig = {
    "aaSorting": [[<?php echo $this->is_iframe?(3+$popup):0; ?>, "desc"]],
    'sAjaxSource': '<?php echo $this->ajax_base_url; ?>',
    //'sDom': 'ltipr',
    "iDisplayLength": 50,
    "sDom": "<'row'<'span6'l>r>t<'row'<'span6'i><'span6'p>>",
    "sPaginationType": "bootstrap",
    "aLengthMenu": [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
    "aoColumnDefs": [
        {
            "bSortable": false, 
            "aTargets": [<?php echo implode(', ', $unsorts); ?>]
        },
        {
            "bVisible": false, 
            "aTargets": [<?php echo implode(', ', $invisibles); ?>]
        }
    ],
    "fnServerData": fetchExtendedData,
    "fnServerParams": custServerParams,
    "fnDrawCallback": function(){
        $('body').trigger('dtdraw', this);
    },
    "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
        
        $(aData).each(function(){
            //var $this=$('<div/>').html(this);
            // /console.log(this);
            var $this=$('<div/>').html(''+this);
            if ($('span:first',$this).length && $('span:first',$this).attr('data-lists')){
                $(nRow).addClass($('span:first',$this).attr('data-lists'));
            } else {
                //console.log($this); 
            }
            
            if($('span[data-archived="1"]', $this).length > 0) {
                $(nRow).addClass('archived');
            }
        });
        var real_id = aData[0];
        if(aData[0].split(':').length > 1) {
            var tmp = aData[0].split(':');
            aData[0] = tmp[0];
            real_id = tmp[1];
        }
        $(nRow).attr('data-real-id', real_id);
        $(nRow).attr('data-id', aData[0]);
        $(nRow).attr('id','data-id-'+ aData[0]);
        $(nRow).attr('data-editable', aData[1]);
        $(nRow).attr('data-controller', aData[2]);
        if(getContextMenu() !== null) {
            $(nRow).attr('data-target', getContextMenu()).contextmenu();
        }
        return nRow;
    }
};
var base_edit_url = '<?php echo $this->rights['edit']['base_url']; ?>';
var base_detailed_url = '<?php echo $this->rights['detailed']['base_url']; ?>';

function custServerParams(aoData, return_result) {
    for(var i in table_request_data) {
        aoData.push({
            name: 'request_data['+i+']',
            value: table_request_data[i]
        });
        if(table != undefined && table.fnSettings().aoColumns[i] != undefined) {
            aoData.push({
                name: 'visibility_data['+i+']',
                value: table.fnSettings().aoColumns[i].bVisible?'1':'0'
            });
        }
    }
    for(var i in table_selected_data) {
        aoData.push({
            name: 'selected_data['+i+']',
            value: table_selected_data[i]
        });
    }
     for(var i in table_filter_data) {
        aoData.push({
            name: 'filter_data['+i+']',
            value: table_filter_data[i]
        });
    }
    for(var i in table_calcs_data) {
        aoData.push({
            name: 'calculators['+i+']',
            value: table_calcs_data[i]
        });
    }

    for(var i in table_custom_data) {
        aoData.push({
            name: 'custom_data['+i+']',
            value: table_custom_data[i]
        });
    }
    aoData.push({
        name: 'export',
        value: $('#export_value').val()
    });
    aoData.push({
        name: 'archived',
        value: $('#switch_archived').val()
    });
    aoData.push({
        name: 'check_type',
        value: '<?php echo $this->check_type; ?>'
    });
    <?php if(!is_null($this->handling)) { ?>
    aoData.push({
        name: 'handling',
        value: '<?php echo $this->handling?'1':'0'; ?>'
    });
    <?php } ?>
    if(return_result === true) {
        return aoData;
    }
}

table_request_data = {<?php
    echo "\r\n\t";
    foreach($this->request_data as $k=>$v) {
        echo '"'.$k.'": "'.$v.'",'."\r\n\t";
    }
?>};

table_filter_data = {<?php
    echo "\r\n\t";
    foreach($this->filter_fields as $k=>$v) {
        echo '"'.$k.'": "'.$v.'",'."\r\n\t";
    }
?>};
            
table_custom_data = {<?php
    echo "\r\n\t";
    foreach($this->customs as $k=>$v) {
        echo '"'.$k.'": "'.$v.'",'."\r\n\t";
    }
?>};

table_selected_data = {<?php  
    echo "\r\n\t";
	
    foreach($this->selected_data as $k=>$v) {
        echo '"'.$v.'": "'.$v.'",'."\r\n\t";
    }
?>};
                
table_calcs_data = {  
    0: "<?php echo preg_replace('/\\\/', '_', $this->calcs_data); ?>"
};
 
function fetchExtendedData(sSource, aoData, fnCallback) {
    if(!use_prev_data) {

        $.ajax({type:'POST', cache:false, url:sSource, data:aoData, success: function (json) {
               if(json.result) {
                fnCallback(json);
                if (json.result){
                     if (json.title ){
                         $('#popup_div').dialog('option','title',json.title); 

                     }
                 }
               } else {
               //    document.location.href = document.location.href;
               }
        }, error: function(){
            // document.location.href = document.location.href;
        }});
    }
}
var getContextMenu = function() {
    return null;
}
var returnfields;
<? 
 if($this->returnfields)
     {?>
      returnfields = '<?=$this->returnfields?>';
    <? } ?>  

</script>
<div class='table-controls-wrapper'>
<? //створення заголовка із вибраними значеннями
   

   if ($this->check_type){ ?>
   <script>
		$(function (){
			
if (Object.keys(table_selected_data).length){

        var extended_arr = ['url'];
        if (returnfields != undefined && returnfields.length>0)
        {
            extended_arr[extended_arr.length] = 'fields:' + returnfields;
        }
			 		$.post(entry_point, {ids:table_selected_data, "data-extended": extended_arr.join(';')}, function(data){
			 			
			 			if (data.result){
			 				
			 				$.each(data.objects,function(id,string){
			 					addSelectedmodel(id,string,$(table_controls_wrapper+':first').find(list_selected_models_wrapper+':first'), data.extra[id]);
			 					 
			 				});
			 			} else {
			 				alert(data.description);
			 			}
			 		});
		 		
		 		
		 	}
		 });	
	</script>
   
   <fieldset class='selected_models_wrapper panel panel-info'>
   	<legend class="panel-heading"><?=$this->translate('Выбранные');?>: <span class='selected_els_count badge'></span> 
   <?   $this->legend_buttons = is_array($this->legend_buttons)?$this->legend_buttons: array();
        \Sl_Event_Manager::trigger(new \Sl_Event_View('listViewTableLegendButtons', array('view' => $this, 'object' => $this->base_object))); ?>
   <span class='groupbtn input-append'>
   <? foreach ($this->legend_buttons as $button){
       echo $button;
   }
   ?> 
    </span>    	    
    </legend>	
	<div class='selected_models panel-body'></div>
  </fieldset>
<? } ?>
<div class="buttons_wrapper pull-right">
<?php
\Sl_Event_Manager::trigger(new \Sl_Event_View('beforeListViewTableButtons', array('view' => $this, 'object' => $this->base_object))); 
if(!$this->is_popup && ($this->rights['create']['access'] || $this->rights['edit']['access'])) {
    if($this->rights['create']['access']) {
        echo $this->partial('dt/create.php', array('value' => 'new', 'base_url' => $this->rights['create']['base_url']));
    }
}
?>
    <?php if(!$this->is_popup) { ?>
        <button class="hide_columns btn" title="<?php echo $this->translate('Hide columns'); ?>"><i class="icon-wrench"></i></button>
       
    <?php } ?>
    <input type="hidden" id="switch_archived" value="-1" />
    <?php if(!$this->is_popup) { ?>
        <button class="clean_filters btn" disabled="disabled" title="<?php echo $this->translate('Clean filters'); ?>"><i class="icon-remove"></i></button>
        <script type="text/javascript">
            $(document).ready(function(){
                if($('table thead .dt_search_input').length <= 0) {
                    $('.clean_filters.btn').hide();
                }
            });
        </script>
    <?php } ?>
</div>
<?php \Sl_Event_Manager::trigger(new \Sl_Event_View('beforeListViewTable', array('view' => $this, 'object' => $this->base_object))); ?>
<input type="hidden" id="export_value" value="0" />
<?php $export_limit = \Sl_Service_Settings::value('EXPORT_CONFIRM_LIMIT'); ?>
<input type="hidden" id="export_confirm_limit" value="<?php echo intval($export_limit); ?>">
<table class="<?=$this->class?> <?=$this->check_type?'check-table':''?> datatable table table-striped table-bordered">
    <?= $this->partial('dt/thead.php',array('check_type'=>$this->check_type, 'base_object' => $this->base_object,'fields'=>$this->fields, 'is_popup' => $this->is_popup))?>
    <tbody></tbody>
</table>
</div>
<?php \Sl_Event_Manager::trigger(new \Sl_Event_View('afterListViewTable', array('view' => $this, 'object' => $this->base_object))); ?>
<?php 
if($this->is_popup) {?>
	
<div class='clearfix'> </div>	
  <button type='button' class='set_selections fr'><?php echo $this->translate('Установить'); ?></button>  
 
<?    
}
?>
<div id="dt_selector" style="display: none;">
    <select>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="50">100</option>
        <option value="-1"><?php echo $this->translate('Все'); ?></option>
    </select>
</div>
<div id="hide_columns_div" style="display: none;">
    <table class="table table-bordered">
        <tbody>
            <?php $trs = array(array()); ?>
            <?php foreach($this->fields as $index=>$field) { ?>
                <?php if(($field['type'] != 'hidden') && $field['hidable'] || (isset($field['visible']) && $field['visible'])) { ?>
                    <?php if(count($trs[count($trs)-1]) > 2) { ?>
                        <?php $trs[] = array(); ?>
                    <?php } ?>
                    <?php $trs[count($trs)-1][] = '<td><strong>'.$field['label'].'</strong></td>
                        <td><input class="items" data-type="'.(isset($field['type'])?$field['type']:'text').'" data-searchable="'.(isset($field['searchable'])?((int) $field['searchable']):0).'" data-ind="'.($index+3+$popup).'" '.((isset($field['visible']) && ($field['visible'] == false))?' data-hidden="1" ':'').' data-desc="'.$field['name'].'" id="'.md5(preg_replace('/^(.+)id.+$/', '$1', $this->ajax_base_url).$field['column_name']).'" type="checkbox" /></td>'; ?>
                <?php } ?>
            <?php } ?>
            <?php while(count($trs[count($trs)-1]) < 3) { ?>
                <?php $trs[count($trs)-1][] = '<td></td><td></td>'; ?>
            <?php } ?>
            <tr>
                <?php echo implode('</tr><tr>', array_map(function($el){ return implode('', $el); }, $trs)); ?>
            </tr>
        </tbody>
    </table>
</div>