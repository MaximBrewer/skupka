<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
//JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.modal');
jimport('joomla.filesystem.file');
$db = JFactory::getDbo();
$config = JFactory::getConfig();
/*echo '<pre>';
$data = shell_exec('top -b -n 1');print_r($data); jexit();*/

		$total_column_total = array();
		if(property_exists($this, 'table_data') && count($this->table_data)>0)
			$total_column_total = array_keys(get_object_vars($this->table_data[0]));
?>


<script type="text/javascript">
	var update_limit = 0;
	var prefix_value= "<?php echo $config->get('dbprefix');?>";
Joomla.submitbutton = function(task) {
	        var tables_name = jQuery('select[name="table_name"]').val();
			var sql_qury = jQuery('textarea[name="sql_query"]').val();
			sql_qury = sql_qury.replace("#__", prefix_value);
			if(task == 'add'){
				if(jQuery('textarea[name="sql_query"]').val()!=''){
				alert("<?php echo JText::_('COM_VDATA_CANNOT_INSERT_DATA');?>");
				return;	
				}
				else if(jQuery('select[name="table_name"]').val()==''){
				alert("<?php echo JText::_('COM_VDATA_PLEASE_SELECT_TABLE_NAME');?>");
				return;	
				}
				else
					Joomla.submitform(task, document.getElementById('adminForm'));
			}
		else if (task == 'export') {
			//alert(document.adminForm.boxchecked.value);
			var checked_value = [];
			var checked_id = '';
			jQuery('input[type=checkbox][name="cid[]"]:checked').each(function(){
				var ch_value = jQuery(this).val().split(':');
				checked_id = ch_value[1];
				checked_value.push(ch_value[0]);
			});
			
			if(tables_name=='' && sql_qury==''){
				alert("<?php echo JText::_('COM_VDATA_PLEASE_SELECE');?>");
				return;
			}
			var url = "<?php echo JURI::base()?>index.php?option=com_vdata&view=quick&layout=export&tmpl=component&table_names="+tables_name+"&sql_qury="+sql_qury+"&checked_value="+String(checked_value)+"&checked_id="+checked_id;
            SqueezeBox.loadModal(url,"iframe",'95%','95%');

			}
			else if(task == 'optimise'){
			if(tables_name==''){alert("<?php echo JText::_('COM_VDATA_PLZ_SELECT_TABLE_OPTIMISE');?>");return;}	
			jQuery('.optimise').dialog({
			appendTo: ".optimises",
			autoOpen: true,
			height: 250,
			width:350,
			modal: true,
		   title:"<?php echo JText::_('COM_VDATA_OPTIMIZATION_OF_TABLE');?>",
		   draggable:true,
			show: {
				effect: 'blind',
				duration: 1000
				},
		    hide: {
				effect: "blind",
				duration: 1000
				},
		buttons: {
		 Yes: function () {
			    var close_event = jQuery(this); 
				if(jQuery('input[type=radio][name="table_optimize"]:checked').val()!='selected_table' && jQuery('input[type=radio][name="table_optimize"]:checked').val()!='all_tables'){
				 alert("<?php echo JText::_('COM_VDATA_SELECT_OPTIMIZATION_OPTIONS');?>");
				 return;
				} 
                  
                            jQuery.ajax({
						url: "index.php",
						type: "POST",
						dataType: "json",
						data: {'option':'com_vdata', 'view':'quick', 'task':'optimization','table_for':jQuery('input[type=radio][name="table_optimize"]:checked').val(),'selected_table':jQuery('select[name="table_name"]').val(), "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
						
						beforeSend: function()	{
							jQuery(".vchart_overlay").show();
							},
							complete: function()	{
							jQuery(".vchart_overlay").hide();
							},
							success: function(res)	{
							if(res.result == "success"){
							alert('Table '+jQuery('select[name="table_name"]').val()+' optimized');
							jQuery(".optimise").dialog("close");
							close_event.dialog("close");
							}
							else
							alert(res.error);
							
							},
							error: function(jqXHR, textStatus, errorThrown)	{
							alert(textStatus);				  
							}	
				
								
								
							});
                                
                            },
		No: function () {
			jQuery(this).dialog("close");
		}
		},
		overlay: {
         opacity: 0.7,
         background: "black"
      },open:function(){
		  jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(0).addClass("btn"); 
		  jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(1).addClass("btn"); 
		  jQuery(this).closest(".ui-dialog")
        .find(".ui-dialog-titlebar-close")
		.addClass('ui-state-default')
        .html('<span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text"></span>');
	  
	  
	  
	  },
		close: function() 
		{
		jQuery(this).dialog( "close" );
		return;
		}
		});		
			
			}
			else if(task == 'repair'){
				if(tables_name==''){alert("<?php echo JText::_('COM_VDATA_PLZ_SELECT_TABLE_REPAIR');?>");return;}
			jQuery('.optimise').dialog({
			appendTo: ".optimises",
			autoOpen: true,
			height: 250,
			width:350,
			modal: true,
		    title:"<?php echo JText::_('COM_VDATA_SELECT_REPAIRED_OPTIONS');?>",
			draggable:true,
			show: {
				effect: 'blind',
				duration: 1000
				},
		hide: {
				effect: "blind",
				duration: 1000
				},
		buttons: {
		 Yes: function () {
			       var close_event = jQuery(this);              
				
				if(jQuery('input[type=radio][name="table_optimize"]:checked').val()!='selected_table' && jQuery('input[type=radio][name="table_optimize"]:checked').val()!='all_tables'){
				 alert("<?php echo JText::_('COM_VDATA_SELECT_REPAIRED_OPTIONS');?>");
				 return;
				} 
                  
                        jQuery.ajax({
						url: "index.php",
						type: "POST",
						dataType: "json",
						data: {'option':'com_vdata', 'view':'quick', 'task':'repaired','table_for':jQuery('input[type=radio][name="table_optimize"]:checked').val(),'selected_table':jQuery('select[name="table_name"]').val(), "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
						
						beforeSend: function()	{
							jQuery(".vchart_overlay").show();
							},
							complete: function()	{
							jQuery(".vchart_overlay").hide();
							},
							success: function(res)	{
							if(res.result == "success"){
							alert('Table '+jQuery('select[name="table_name"]').val()+' repaired');
							jQuery(".optimise").dialog("close");
							close_event.dialog("close");
								
							}
							else
							alert(res.error);
							
							},
							error: function(jqXHR, textStatus, errorThrown)	{
							alert(textStatus);				  
							}	
				
								
								
							});
                                
                            },
		No: function () {
			jQuery(this).dialog("close");
		}
		},
		overlay: {
         opacity: 0.7,
         background: "black"
      },open:function(){
		  jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(0).addClass("btn");
		  jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(1).addClass("btn");
          jQuery(this).closest(".ui-dialog")
        .find(".ui-dialog-titlebar-close")
		.addClass('ui-state-default')
        .html('<span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text"></span>');
	  },
		close: function() 
		{
		jQuery(this).dialog( "close" );
		return;
		}
		});		
			}
			else
				Joomla.submitform(task, document.getElementById('adminForm'));
		}
 
 SqueezeBox.loadModal = function(modalUrl,handler,x,y) {
        this.presets.size.x = 1024;
        this.initialize();      
        var options = {handler: 'iframe', size: {x: 1000, y: 550}, onClose: function() {}};      
        this.setOptions(this.presets, options);
        this.assignOptions();
        this.setContent(handler,modalUrl);
    };
	SqueezeBox.initialize({
	 onOpen:function(){ 
		 jQuery('#system-message-container').html('');
		 }
	}); 
jQuery(document).ready(function(){
	var error_message = '<?php echo $this->test_error;?>';
	var tables_name_message = jQuery('select[name="table_name"]').val();
	var sql_qury_message = jQuery('textarea[name="sql_query"]').val();
  if(error_message!=''&&jQuery('#table_name').val()=='')
       jQuery('#toolbar-out-2').find('button').attr('disabled',true);
   if(sql_qury_message=='' && tables_name_message=='')
       jQuery('#toolbar-out-2').find('button').attr('disabled',true);

jQuery('.delete_data').on('click',function(){
	if(jQuery(this).parent('span').attr('data-action')=='')
		return;
	var delete_value = jQuery(this).parent('span').attr('data-action');
	var table_name = '<?php echo isset($this->lists['main_table_name'])?$this->lists['main_table_name']:"";?>';
	var column_name = jQuery('input[name="column_name"]').val();
	var remove_tr = jQuery(this).parents('tr');
	jQuery.confirm({
    text: "<?php echo JText::_('COM_VDATA_CONFORMATION_DELETE_MESSAGE');?>",
    title: "<?php echo JText::_('COM_VDATA_CONFORMATION_DELETE_MESSAGE_TITLE');?>",
    confirm: function(button) {
     jQuery.ajax({url: "index.php",type: "POST",dataType: "json",data: {'option':'com_vdata', 'view':'quick', 'task':'delete_value','delete_id':delete_value,'column_name':column_name,'table_name':table_name, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},beforeSend: function()	{
							jQuery(".hexdata_overlay").show();
							},
							complete: function()	{
							jQuery(".hexdata_overlay").hide();
							},
							success: function(res)	{
							if(res.result == "success"){
							 remove_tr.remove();							 
							
							}
							else
							alert(res.error);
							
							},
							error: function(jqXHR, textStatus, errorThrown)	{
							alert(textStatus);				  
							}	
			});  
			},
			cancel: function(button) {
				// nothing to do
			},
			confirmButton: "Yes",
			cancelButton: "No",
			post: true,
			confirmButtonClass: "btn-danger",
			cancelButtonClass: "btn-default",
			dialogClass: "modal-dialog modal-lg" // Bootstrap classes for large modal
       });
   });
jQuery('#table_name').on('change',function(){ 
  
	if(typeof document.adminForm.limitstart!='undefined'){document.adminForm.limitstart.value=0;}
	if(typeof document.adminForm.filter_order!='undefined'){document.adminForm.filter_order.value='';}
	jQuery("#search").val("");
	document.adminForm.submit();
});	

jQuery('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});

	jQuery("#icon_desc_toggle").mouseover(function(){
		jQuery( "#desc_toggle" ).show( 'slide', 800);
	});	
	jQuery( ".sub_desc .close" ).on( "click", function() {
		jQuery( "#desc_toggle" ).hide( 'slide', 800);
	});
});	
	function check_drop_delete()
	{  
		
		if(document.adminForm.search.value!='')
		{
			var string = document.adminForm.search.value.toLowerCase();
			var substring1 = "delete";
			var substring2 = "drop";
			if(string.indexOf(substring1) > -1 || string.indexOf(substring2)> -1)
			{
				jQuery.confirm({
					text: "<?php echo JText::_('COM_VDATA_CONFORMATION_DELETE_MESSAGE');?>",
					title: "<?php echo JText::_('COM_VDATA_CONFORMATION_DELETE_MESSAGE_TITLE');?>",
					confirm: function(button) {
						if(typeof document.adminForm.limitstart!='undefined'){document.adminForm.limitstart.value=0;}
						if(typeof document.adminForm.filter_order!='undefined'){
							document.adminForm.filter_order.value='';
						}
						document.adminForm.filter_order.value='';
						jQuery('#table_name option').prop('selected', false);
						jQuery('#table_name').trigger('liszt:updated');
						document.adminForm.submit();
					},
					cancel: function(button) {
						return false;
					},
					confirmButton: "Yes",
					cancelButton: "No",
					post: true,
					confirmButtonClass: "btn-danger",
					cancelButtonClass: "btn-default",
					dialogClass: "modal-dialog modal-lg" // Bootstrap classes for large modal
				});
			}
			else{
				if(typeof document.adminForm.limitstart!='undefined'){document.adminForm.limitstart.value=0;}
				if(typeof document.adminForm.filter_order!='undefined'){
					document.adminForm.filter_order.value='';
				}
				document.adminForm.filter_order.value='';
				jQuery('#table_name option').prop('selected', false);
				jQuery('#table_name').trigger('liszt:updated');
				document.adminForm.submit();
			}
		}
		return false;
	}
</script>
<style>
.edit_data {
background
}
.delete_data {
	
}
</style>
<div class="adminform_box vdata_quick">
<form action="index.php?option=com_vdata&view=quick" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<legend><?php echo JText::_('QUICK'); ?> <span class="icon-info" id="icon_desc_toggle"></span>
			<div class="sub_desc" id="desc_toggle" style="display:none;"><a class="close" href="javascript:void(0);">×</a><?php echo JText::_('QUICK_DESC_TT'); ?></div>
		</legend>
<div style="display:none;" class="hexdata_overlay"> 
<img alt="" src="<?php echo JURI::root();?>media/com_vdata/images/loading_second.gif" class="hexdata-loading">
</div>
<?php if($this->test_error=='Error') {  echo '<div class="alert alert-error">'.$this->error_message.'</div>';}?>
<div id="vdatapanel">
<div class="right-pagination-box"><?php echo count($total_column_total)>0 ? $this->pagination->getLimitBox():'';?></div>
<div class="filter-select fltrt" style="float:right;">
<?php echo $this->tables; ?>
</div>

<div class="search_buttons">
        <div class="btn-wrapper quick_query">
		
			<div class="quick_query-buttons"><button class="btn go" onclick="check_drop_delete();return false;"><i class="icon-search"></i><span class="search_text"><?php echo JText::_('Go'); ?></button>
			<button class="btn reset" onclick="document.getElementById('search').value='';if(typeof document.adminForm.limitstart!='undefined') document.adminForm.limitstart.value=0;document.adminForm.filter_order.value='';this.form.submit();"><i class="icon-delete"></i> <?php echo JText::_( 'Reset' ); ?></button>
			<button class="btn add" onclick="document.getElementById('task').value='add_cron';this.form.submit();return false;"><i class="icon-new"></i> <?php echo JText::_( 'GO_CRON' ); ?></button>
			</div>
			<div class="query_value"><textarea placeholder="Write your query" rows="5" cols="10" class="quicksearch_textarea" name="sql_query" id="search" value="<?php //echo $this->lists['search'];?>" class="text_area"/><?php echo $this->lists['sql_query'];?></textarea></div>
			</div></div>
			 <?php if($this->test_error!='Error') { ?>
<div id="editcell" class="updatecell">
<?php


 if($this->lists['sql_query']!='' || $this->lists['table_name']!=''){ ?>
<table class="adminlist table"><tr><td><b><?php echo JText::_('COM_VDATA_TOTAL_NUMBER_RECORDS').': '.$this->total_records;?></b></td></tr></table>
<?php } ?>
<table class="adminlist table quick">
	<thead>
		<tr>
		   
		<?php 
		
		$total_column = array();
		/* print_r($this->column_details);
		print_r($this->table_data); jexit(); */
		if(count($this->table_data)>0){
		$total_column = array_keys(get_object_vars($this->table_data[0]));
		
		if(count($this->edit_functionality)>0){
		echo '<th nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>';}
		}
		echo $this->edit_functionality==true?'<th nowrap="nowrap">'.JText::_('COM_VDATA_QUICK_ACTION').'</th>':'';
		for($i=0;$i<count($total_column);$i++){ 
			
			 echo '<th nowrap="nowrap">'.JHTML::_("grid.sort",  $total_column[$i] , $total_column[$i], @$this->lists['order_Dir'], @$this->lists['order'] ).'</th>';
			}
			
			?>
			
		</tr>
	</thead>
	<tfoot>
    <tr>
     
		<td colspan="<?php echo count($total_column)+2;?>"><?php echo count($total_column)>0?$this->pagination->getListFooter():''; ?></td>
		<!--<td colspan="1"><?php //echo count($total_column)>0?$this->pagination->getLimitBox():'';?></td>-->
    </tr>
  	</tfoot>
    <?php
	$k = 0;
	
	$t_name = $this->lists['main_table_name'];//str_replace('#__','@',str_replace($db->getPrefix(),'@',$this->lists['main_table_name']));
	
	for($i=0, $n=count($this->table_data); $i < $n; $i++)
	{
		$row = &$this->table_data[$i];
		$link  = 'javascript:void(0);';
		$checked 	= count($this->edit_functionality)>0?'<input type="checkbox" onclick="Joomla.isChecked(this.checked);" value="'.$row->{$total_column[0]}.':'.$this->edit_functionality->Column_name.'" name="cid[]" id="cb'.$i.'">':'';
		if($checked!='')
			$link  = JRoute::_( 'index.php?option=com_vdata&view=quick&task=edit&cid[]='.$row->{$total_column[0]}.'&main_table_name='.$t_name.'&column_name='.$this->edit_functionality->Column_name);
		$edit_section ='';
		if($checked!='')
			$edit_section ='<span class="main_function" data-action="'.$row->{$total_column[0]}.':'.$this->edit_functionality->Column_name.'"><span class="edit_datas"><a class="edit_data icon-edit"href="'.$link.'"></a></span><span class="delete_data icon-delete"></span></span>';
		?>
		<tr class="row<?php echo $i % 2; ?>"><?php
		
		 echo $this->edit_functionality==true?'<td align="center">'.$checked.'</td>':'';
		 echo $this->edit_functionality==true?'<td class="edit-delete-section" width="55" align="center">'.$edit_section.'</td>':'';
         for($j=0;$j<count($total_column);$j++){ 
			if(count($this->column_details)>0) {
				 if(!isset($this->column_details[$j]))
					 continue;
			 $column_details = $this->column_details[$j];
			if(isset($column_details->Type)&& ($column_details->Type=='longblob' ||  $column_details->Type=='blob' || $column_details->Type=='mediumblob'|| $column_details->Type=='tinyblob')){
				
			echo '<td align="center"></td>';	
			}
			else{
            echo $j==0?'<td align="center">'.substr($row->{$total_column[$j]}, 0, 25).'</td>':'<td align="center">'.substr($row->{$total_column[$j]}, 0, 25).'</td>'; 
			}
			}
			else{
            echo $j==0?'<td align="center">'.substr($row->{$total_column[$j]}, 0, 25).'</td>':'<td align="center">'.substr($row->{$total_column[$j]}, 0, 25).'</td>'; 
			}
	 
		 }
         		 
		 ?>
		 </tr>
		 <?php
		$k = 1 - $k;
	}
	?>
	</tbody>
        
    </table>

</div>
			<?php } ?>
<div class="clr"></div></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" id="task" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" id="view" name="view" value="quick" />
<input type="hidden" name="column_name" value="<?php echo isset($this->edit_functionality->Column_name)?$this->edit_functionality->Column_name:''; ?>" />

<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

<input type="hidden" name="main_table_name" value="<?php echo $this->lists['main_table_name']; ?>" />

<div class="optimises"></div>
<div class="optimise" style="display:none">
<div class="optimise_sub_div"><label>
<input type="radio" name="table_optimize" id="table_optimize_testing" value="selected_table"><?php echo JText::_('COM_VDATA_SELECTED_TABLE');?></label>
<label>
<input type="radio" name="table_optimize" id="table_optimize_testing" value="all_tables"><?php echo JText::_('COM_VDATA_ALL_TABLE');?></label>
</div>
<div class="optimise_sub_div"></div>
</div>
</div>
</form>
</div>