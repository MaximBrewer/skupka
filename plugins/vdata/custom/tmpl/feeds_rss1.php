<?php
/*------------------------------------------------------------------------
# vData - custom Plugin
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2015 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript">

Joomla.submitbutton = function(task) {
	if (task == 'close' || task == 'close_st') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} 
	else if(task == 'export_start'){
		var form = document.adminForm;
		var blank = false;
		$hd('input[name^="field"]').each(function()	{
			if($hd(this).val() == "")
			{
				blank=true;
                return false;
			}
		});
		if(blank == true)
		{
			alert("<?php echo JText::_('PLZ_FILL_FIELD_TO_EXPORT_DATA'); ?>");
			return false;	
		}
		function addOption(aname,avalue,values){
			var index;
			// Find and replace option if there
			for (index = 0; index < values.length; ++index) {
					if (values[index].name == aname) {
						values[index].value = avalue;
						break;
					}
			}
			// Add it if it wasn't there
			if (index >= values.length) {
				values.push({
					name: aname,
					value: avalue
				});
			}
		}
		
		function batchExport(offset){
				var values, index;
				values = $hd("#adminForm").serializeArray();
				addOption('task', 'export_start', values);
				addOption('offset', offset, values);
				values = jQuery.param(values);
				
				return $hd.ajax({
				url: "index.php",
				type: "POST",
				dataType: "json",
				async: true,
				cache:false,
				data: values,
				beforeSend: function()	{
					if(offset == 0){
						console.log('export start...');
					}
					else{
						console.log('export continue...');
					}
					
					$hd(".loading").show();
				},
				complete: function()	{
					$hd(".loading").hide();
				},
				error: function(jqXHR, textStatus, errorThrown)	{
				  alert(textStatus);				  
				} 
			}).done(function(resp){
				if(resp.result == 'success'){
					console.log(resp.offset);
					batchExport(resp.offset);
				}
				else if(resp.result == 'error' && resp.hasOwnProperty("qty")) {
					console.log(resp.error);
					console.log('export complete...');
					var jmsgs = [resp.success_msg];
					Joomla.renderMessages({'message': jmsgs });//message,info
					if(resp.dlink){
						window.location = resp.dlink;
					}
					
				}
				else {
					console.log('export abort.');
					var jmsgs = [resp.error_msg,resp.error];
					Joomla.renderMessages({'error': jmsgs });
				}
			});
		}
		batchExport(0);
		return false;
	}
	else{
		var form = document.adminForm;
		var blank = false;
		$hd('input[name^="field"]').each(function()	{
			if($hd(this).val() == "")
			{
				blank=true;
                return false;
			}
		});
		if(blank == true)
		{
			alert("<?php echo JText::_('PLZ_FILL_FIELD_TO_EXPORT_DATA'); ?>");
			return false;	
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
		return false;
	}
	
}

$hd(function()	{
	$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
});

</script>

<?php 
$options = array();

if($profileid!=0){
	if($this->profile->quick){
		foreach($feedfileds as $k=>$qfield) {
			// $options[] = $qfield->Field;
			array_push($options, $qfield->Field);
		}
	}
	else{
		foreach($this->profile->params->fields as $column=>$params){
			if( ($params->data == "include") || ($params->data == "defined") ){
				 // $options[] = $column;
				 array_push($options, $column);
			}
			elseif(($params->data == "reference") && count($params->reftext)){
				foreach($params->reftext as $ref){ 
					// $options[] = $column.':'.$ref;
					array_push($options, $column.':'.$ref);
				}
			}
			
		}
		
	}
	
}
elseif($st && !empty($qry) && ($profileid==0) ) {
	foreach($sfields as $key=>$sfield){
		// $options[] = $key;
		 array_push($options, $key);
	}
}

?>
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'RSS1_DETAIL' ); ?></legend>
        <table class="adminform table table-striped adminformlist">
		<tr>
			<td colspan="2"><label class="hasTip" title="<?php echo JText::_('RSS1_ITEM_TAG_DESC');?>"><?php echo JText::_('RSS1_ITEM_TAG');?></label></td>
		</tr>
		<tr>
			<td width="200"><label class="hasTip" title="<?php echo JText::_('RSS1_ITEM_TITLE_DESC');?>"><?php echo JText::_('RSS1_ITEM_TITLE');?></label></td>
			<td>
				<select name="field[title]">
					<option value=""><?php echo JText::_('SELECT_COLUMNS');?></option>
					<?php foreach($options as $option){?>
						<option value="<?php echo $option;?>" <?php if( !empty($schedule_columns) && property_exists($schedule_columns->fields, 'title') && ($schedule_columns->fields->title==$option) ) echo 'selected="selected"';?>><?php echo $option;?></option>
					<?php }?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="200"><label class="hasTip" title="<?php echo JText::_('RSS1_ITEM_LINK_DESC');?>"><?php echo JText::_('RSS1_ITEM_LINK');?></label></td>
			<td>
				<select name="field[link]">
					<option value=""><?php echo JText::_('SELECT_COLUMNS');?></option>
					<?php foreach($options as $option){?>
						<option value="<?php echo $option;?>" <?php if( !empty($schedule_columns) && property_exists($schedule_columns->fields, 'link') && ($schedule_columns->fields->link==$option) ) echo 'selected="selected"';?>><?php echo $option;?></option>
					<?php }?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="200"><label class="hasTip" title="<?php echo JText::_('RSS_ITEM_DESCRIPTION_DESC');?>"><?php echo JText::_('RSS_ITEM_DESCRIPTION');?></label></td>
			<td>
				<select name="field[desc]">
					<option value=""><?php echo JText::_('SELECT_COLUMNS');?></option>
					<?php foreach($options as $option){?>
						<option value="<?php echo $option;?>" <?php if( !empty($schedule_columns) && property_exists($schedule_columns->fields, 'desc') && ($schedule_columns->fields->desc==$option) ) echo 'selected="selected"';?>><?php echo $option;?></option>
					<?php }?>
				</select>
			</td>
		</tr>
		<table class="adminform table table-striped col_block"></table>
        </table>
	</fieldset>
</div>