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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.tooltip');

$delimiter = json_decode($this->item->delimiter);
$enclosure = json_decode($this->item->enclosure);
$csv_child = json_decode($this->item->csv_child);

?>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'close') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
	else if(task == 'save'){
		var form = document.getElementById('adminForm');
		if( (!$hd('#local_db').is(':checked') ))	{
			if(form.driver.value == "") {
				alert("<?php echo JText::_('PLZ_ENTER_DRIVER'); ?>");
				return false;
			}
			if(form.host.value == "") {
				alert("<?php echo JText::_('PLZ_ENTER_HOST'); ?>");
				return false;
			}
			if(form.user.value == "") {
				alert("<?php echo JText::_('PLZ_ENTER_USER'); ?>");
				return false;
			}
			if(form.database.value == "") {
				alert("<?php echo JText::_('PLZ_ENTER_DATABASE'); ?>");
				return false;
			}
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

jQuery(document).ready(function(){
	
	jQuery('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	
});

$hd(function()	{
	$hd('#delimiter').on('change', function(){
		
		var opts = '';
		opts += '<option value=""><?php echo JText::_('SELECT_DELIMITER');?></option>';
		if($hd(this).val()!='comma'){
			opts += '<option ="comma"><?php echo JText::_('COMMA');?></option>';
		}
		if($hd(this).val()!='tab'){
			opts += '<option ="tab"><?php echo JText::_('TAB');?></option>';
		}
		if($hd(this).val()!='semicolon'){
			opts += '<option ="semicolon"><?php echo JText::_('SEMI_COLON');?></option>';
		}
		if($hd(this).val()!='space'){
			opts += '<option ="space"><?php echo JText::_('SPACE');?></option>';
		}
		if($hd(this).val()!='pipe'){
			opts += '<option ="pipe"><?php echo JText::_('PIPE');?></option>';
		}
		opts += '<option value="other"><?php echo JText::_('OTHER');?></option>';
		jQuery('select#child_sep').html(opts);
		jQuery('select#child_sep').trigger('liszt:updated');
		jQuery('input[name="child_del"]').remove();
			
		if($hd(this).val() == 'other'){
			$hd('#delimiter').parent().append('<input type="text" name="dother" value="<?php if(property_exists($delimiter, 'other')){echo htmlspecialchars($delimiter->other);}?>" />');
		}
		else{
			if(jQuery('input[name="dother"]').length>0){
				jQuery('input[name="dother"]').remove();
			}
		}
		
	});
	
	$hd('#enclosure').on('change', function(){
		if($hd(this).val() == 'other'){
			$hd('#enclosure').parent().append('<input type="text" name="eother" value="<?php if(property_exists($enclosure, 'other')){echo htmlspecialchars($enclosure->other);}?>" />');
		}
		else{
			if(jQuery('input[name="eother"]').length>0)
			jQuery('input[name="eother"]').remove();
		}
	});
	
	$hd('select#csv_child').on('change', function(){
		if($hd(this).val()==1)
			$hd('div#child_rec').show();
		else
			$hd('div#child_rec').hide();
	});
	
	$hd('select#child_sep').on('change', function(){
		if(($hd(this).val() == 'other') && ($hd('input[name="child_del"]').length==0)){
			$hd('select#child_sep').parent().append('<input type="text" name="child_del" value="<?php //if(property_exists($enclosure, 'other')){echo htmlspecialchars($enclosure->other);}?>" />');
		}
		else{
			$hd('input[name="child_del"]').remove();
		}
	});
	
	$hd('#local_db').on('change', function(event){
		//var checked = $hd('input[name="local_db"]:checked').length;
		if($hd(this).is(":checked")) {
			$hd('.rd_block').hide();
		}
		else{
			$hd('.rd_block').show();
		}
	});
	
	$hd("#icon_desc_toggle").mouseover(function(){
		$hd( "#desc_toggle" ).show( 'slide', 800);
	});	
	$hd( ".sub_desc .close" ).on( "click", function() {
		$hd( "#desc_toggle" ).hide( 'slide', 800);
	});
	
	$hd('select[name="xml_parent[node]"]').on('change', function(event){
		if($hd(this).val()=='other'){
			$hd('div#nodename').show();
		}
		else{
			$hd('div#nodename').hide();
		}
		
	});
	
	
});
</script>


<div class="adminform_box vdata_configuration">
	<form action="index.php?option=com_vdata&view=config" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
<div id="editcell">
		<fieldset class="adminform">
		<legend><?php echo JText::_('CONFIG'); ?> <span class="icon-info" id="icon_desc_toggle"></span>
			<div class="sub_desc" id="desc_toggle" style="display:none;"><a class="close" href="javascript:void(0);">×</a><?php echo JText::_('CONFIG_DESC_TT'); ?></div>
		</legend>
		
		<?php 
			echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general'));
			echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('GENERAL_SETTINGS'));	
		?>
		
		<table class="adminform table table-striped">
			<tr>
				<td width="200">
					<label class="hasTip required" title="<?php echo JText::_('BATCH_LIMIT_DESC');?>">
					<?php echo JText::_('BATCH_LIMIT'); ?></label>
				</td>
				<td>
					<input type="text" name="limit" value="<?php echo htmlentities($this->item->limit);?>" />
				</td>
			</tr>
			<tr>
				<td width="200">
					<label class="hasTip" title="<?php echo JText::_('COLUMN_LIMIT_DESC');?>">
					<?php echo JText::_('COLUMN_LIMIT'); ?></label>
				</td>
				<td>
					<input type="number" min="1" name="column_limit" class="required" value="<?php echo $this->item->column_limit;?>" />
				</td>
			</tr>
			<tr>
				<td width="200">
					<label class="hasTip required" title="<?php echo JText::_('ROW_LIMIT_DESC');?>">
					<?php echo JText::_('ROW_LIMIT'); ?></label>
				</td>
				<td>
					<input type="number" min="50" step="25" name="row_limit" value="<?php echo $this->item->row_limit;?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label class="hasTip required" title="<?php echo JText::_('LOGGING_STATUS_DESC');?>"><?php echo JText::_('LOGGING_STATUS'); ?></label>
				</td>
				<td>
					<select name="logging">
						<option value="1" <?php if($this->item->logging==1){echo 'selected="selected"';}?>><?php echo JText::_('VDATA_ENABLE');?></option>
						<option value="0" <?php if($this->item->logging==0){echo 'selected="selected"';}?>><?php echo JText::_('VDATA_DISABLE');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label class="hasTip" title="<?php echo JText::_('DATABASE_DESC');?>">
				<?php echo JText::_('DATABASE');?></label></td>
				<td>
					<span form="local_db" class="hasTip" title="<?php echo JText::_('USE_LOCAL_DB_DESC');?>"><?php echo JText::_('USE_LOCAL_DB');?></span>
					<input type="checkbox" id="local_db" name="local_db" value="1" <?php if($this->item->local_db==1) echo 'checked="checked"';?>/>
				</td>
			</tr>
			<tr class="rd_block" <?php if($this->item->local_db==1) echo ' style="display:none"'?>>
			<td><label class="hasTip required" title="<?php echo JText::_('DRIVER_DESC');?>"><?php echo JText::_('DRIVER'); ?></label></td>
			<td><input type="text" name="driver" id="driver" class="inputbox required" size="50" value="<?php echo $this->item->driver; ?>" /> 
			</td>
			</tr>
			<tr class="rd_block" <?php if($this->item->local_db==1) echo ' style="display:none"'?>>
			<td><label class="hasTip required" title="<?php echo JText::_('HOSTNAME_DESC');?>"><?php echo JText::_('HOSTNAME'); ?></label></td>
			<td><input type="text" name="host" id="host" class="inputbox required" size="50" value="<?php echo $this->item->host; ?>" /> 
			</td>
			</tr>
			<tr class="rd_block" <?php if($this->item->local_db==1) echo ' style="display:none"'?>>
			<td><label class="hasTip required" title="<?php echo JText::_('USERNAME_DESC');?>"><?php echo JText::_('USERNAME'); ?></label></td>
			<td><input type="text" name="user" id="user" class="inputbox required" size="50" value="<?php echo $this->item->user; ?>" /> 
			</td>
			</tr>
			<tr class="rd_block" <?php if($this->item->local_db==1) echo ' style="display:none"'?>>
			<td><label class="hasTip required" title="<?php echo JText::_('PASSWORD_DESC');?>"><?php echo JText::_('PASSWORD'); ?></label></td>
			<td><input type="password" name="password" id="password" class="inputbox required" size="50" value="<?php echo $this->item->password; ?>" /> 
			</td>
			</tr>
			<tr class="rd_block" <?php if($this->item->local_db==1) echo ' style="display:none"'?>>
			<td><label class="hasTip required" title="<?php echo JText::_('DATABASE_NAME_DESC');?>"><?php echo JText::_('DATABASE_NAME'); ?></label></td>
			<td><input type="text" name="database" id="database" class="inputbox required" size="50" value="<?php echo $this->item->database; ?>" /> 
			</td>
			</tr>
			<tr class="rd_block" <?php if($this->item->local_db==1) echo ' style="display:none"'?>>
			<td><label class="hasTip required" title="<?php echo JText::_('DB_PREFIX_DESC');?>"><?php echo JText::_('DB_PREFIX'); ?></label></td>
			<td><input type="text" name="dbprefix" id="dbprefix" class="inputbox required" size="50" value="<?php echo $this->item->dbprefix; ?>" /></td>
			</tr>
		</table>
		
		<?php
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.addTab', 'myTab', 'phpsetttings', JText::_('PHP_SETTINGS'));
		?>
		<table class="adminform table table-striped">
			<tr>
				<td>
					<label class="hasTip required" title="<?php echo JText::_('VDATA_MAX_EXEC_TIME_DESC');?>"><?php echo JText::_('VDATA_MAX_EXEC_TIME');?>
					</label>
				</td>
				<td>
					<input type="text" name="php_settings[max_execution]" value="<?php if(isset($this->item->php_settings->max_execution)){echo $this->item->php_settings->max_execution;}?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label class="hasTip required" title="<?php echo JText::_('VDATA_MEMORY_LIMIT_DESC');?>"><?php echo JText::_('VDATA_MEMORY_LIMIT');?>
					</label>
				</td>
				<td>
					<input type="text" name="php_settings[max_memory]" value="<?php if(isset($this->item->php_settings->max_memory)){echo $this->item->php_settings->max_memory;}?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label class="hasTip required" title="<?php echo JText::_('VDATA_MAX_POST_LIMIT_DESC');?>">
					<?php echo JText::_('VDATA_MAX_POST_LIMIT');?></label>
				</td>
				<td>
					<input type="text" name="php_settings[max_post]" value="<?php if(isset($this->item->php_settings->max_post)){echo $this->item->php_settings->max_post;}?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label class="hasTip required" title="<?php echo JText::_('VDATA_UPLOAD_LIMIT_DESC');?>">
					<?php echo JText::_('VDATA_UPLOAD_LIMIT');?></label>
				</td>
				<td>
					<input type="text" name="php_settings[max_upload]" value="<?php if(isset($this->item->php_settings->max_upload)){echo $this->item->php_settings->max_upload;}?>" />
				</td>
			</tr>
		</table>
		<?php
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.addTab', 'myTab', 'filesetttings', JText::_('CSV_XML_JSON_SETTINGS'));
		?>
		<table class="adminform table table-striped">
			<?php $csv_options = array('comma'=>JText::_('COMMA'), 'tab'=>JText::_('TAB'), 'semicolon'=>JText::_('SEMI_COLON'), 'space'=>JText::_('SPACE'), 'pipe'=>JText::_('PIPE'));?>
			<tr>
				<td>
					<label class="hasTip required" title="<?php echo JText::_('CSV_SEPARATER_DESC');?>">
					<?php echo JText::_('CSV_SEPARATER'); ?></label>
				</td>
				<td>
					<select name="delimiter" id="delimiter" class="demotesting">
						<option value="" <?php if($delimiter->value =='') echo 'selected="selected"';?>><?php echo JText::_('SELECT_DELIMITER');?></option>
						<option value="comma"<?php if($delimiter->value =='comma') echo 'selected="selected"';?>><?php echo JText::_('COMMA');?></option>
						<option value="tab"<?php if($delimiter->value =='tab') echo 'selected="selected"';?>><?php echo JText::_('TAB');?></option>
						<option value="semicolon"<?php if($delimiter->value =='semicolon') echo 'selected="selected"';?>><?php echo JText::_('SEMI_COLON');?></option>
						<option value="space"<?php if($delimiter->value =='space') echo 'selected="selected"';?>><?php echo JText::_('SPACE');?></option>
						<option value="pipe" <?php if($delimiter->value =='pipe') echo 'selected="selected"';?>><?php echo JText::_('PIPE');?></option>
						<option value="other"<?php if($delimiter->value =='other') echo 'selected="selected"';?>><?php echo JText::_('OTHER');?></option>
					</select>
					<?php if($delimiter->value == 'other'){?>
						<input type="text" name="dother" value="<?php echo htmlspecialchars($delimiter->other);//htmlentities?>" />
					<?php }?>
				</td>
			</tr>
			<tr>
				<td>
					<label class="hasTip required" title="<?php echo JText::_('CSV_ENCLOSURE_DESC');?>">
					<?php echo JText::_('CSV_ENCLOSURE'); ?></label>
				</td>
				<td>
					<select name="enclosure" id="enclosure">
						<option value="" <?php if($enclosure->value =='') echo ' selected="selected"';?>><?php echo JText::_('SELECT_ENCLOSURE');?></option>
						<option value="dquote"<?php if($enclosure->value =='dquote') echo ' selected="selected"';?>><?php echo JText::_('D_QUOTE');?></option>
						<option value="squote"<?php if($enclosure->value =='squote') echo ' selected="selected"';?>><?php echo JText::_("S_QUOTE");?></option>
						<option value="other"<?php if($enclosure->value =='other') echo ' selected="selected"';?>><?php echo JText::_('OTHER');?></option>
					</select>
					<?php if($enclosure->value == 'other'){?>
						<input type="text" name="eother" value="<?php echo htmlspecialchars($enclosure->other);//htmlentities?>" />
					<?php }?>
				</td>
			</tr>
			<tr>
				<td width="200">
					<label class="hasTip" title="<?php echo JText::_('CSV_CHILD_RECORDS_DESC');?>"><?php echo JText::_('CSV_CHILD_RECORDS');?></label>
				</td>
				<td>
					<select id="csv_child" name="csv_child">
						<option value="1" <?php if($csv_child->csv_child==1) echo 'selected="selected"';?>><?php echo JText::_('CSV_CHILD_COLUMNS');?></option>
						<option value="2" <?php if($csv_child->csv_child==2) echo 'selected="selected"';?>><?php echo JText::_('LINE_SEPERATED');?></option>
					</select>
					<div id="child_rec" style="<?php if(($csv_child->csv_child!=1)) echo ' display:none;';?>">
						<div id="row">
							<label class="hasTip" title="<?php echo JText::_('CHILD_SEPARATOR_DESC');?>"><?php echo JText::_('CHILD_SEPARATOR');?></label>
							<select name="child_sep" id="child_sep">
								<option value=""><?php echo JText::_('SELECT_DELIMITER');?></option>
								<?php foreach($csv_options as $op=>$csv_op){ if($delimiter->value!=$op){?>
									<option value="<?php echo $op;?>" <?php if(property_exists($csv_child, 'child_sep') && ($csv_child->child_sep ==$op)) echo 'selected="selected"';?>><?php echo $csv_op;?></option>
								<?php }}?>
								
								<?php /*?>
								<option value="comma"<?php if(property_exists($csv_child, 'child_sep') && ($csv_child->child_sep =='comma')) echo 'selected="selected"';?>><?php echo JText::_('COMMA');?></option>
								<option value="tab"<?php if(property_exists($csv_child, 'child_sep') && ($csv_child->child_sep =='tab')) echo 'selected="selected"';?>><?php echo JText::_('TAB');?></option>
								<option value="semicolon"<?php if(property_exists($csv_child, 'child_sep') && ($csv_child->child_sep =='semicolon')) echo 'selected="selected"';?>><?php echo JText::_('SEMI_COLON');?></option>
								<option value="space"<?php if(property_exists($csv_child, 'child_sep') && ($csv_child->child_sep =='space')) echo 'selected="selected"';?>><?php echo JText::_('SPACE');?></option>
								<option value="pipe" <?php if(property_exists($csv_child, 'child_sep') && ($csv_child->child_sep =='pipe')) echo 'selected="selected"';?>><?php echo JText::_('PIPE');?></option>
								<?php */?>
								
								<option value="other"<?php if(property_exists($csv_child, 'child_sep') && ($csv_child->child_sep =='other')) echo 'selected="selected"';?>><?php echo JText::_('OTHER');?></option>
							</select>
							<?php if(property_exists($csv_child, 'child_sep') && ($csv_child->child_sep == 'other')){?>
								<input type="text" name="child_del" value="<?php echo htmlspecialchars($csv_child->child_del);?>" />
							<?php }?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_CSV_HEADER_DESC');?>"><?php echo JText::_('VDATA_CSV_HEADER');?></label>
				</td>
				<td>
					<select name="csv_header">
						<option value="1" <?php if(isset($this->item->csv_header) && ($this->item->csv_header==1)){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_ENABLE');?></option>
						<option value="0" <?php if(isset($this->item->csv_header) && ($this->item->csv_header==0)){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_DISABLE');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_XML_PARENT_DESC');?>"><?php echo JText::_('VDATA_XML_PARENT');?></label>
				</td>
				<td>
					<div class="vdata_node vdata_document_root"><label><?php echo JText::_('VDATA_DOCUMENT_ROOT');?></label><input type="text" name="xml_parent[root]" value="<?php if(isset($this->item->xml_parent->root)){echo $this->item->xml_parent->root;}?>" /></div>
					<div class="vdata_node vdata_data_root"><label><?php echo JText::_('VDATA_DATA_ROOT');?></label><input type="text" name="xml_parent[data]" value="<?php if(isset($this->item->xml_parent->data)){echo $this->item->xml_parent->data;}?>" /></div>
					<div class="vdata_node vdata_child_data_root"><label><?php echo JText::_('VDATA_CHILD_DATA_ROOT');?></label><input type="text" name="xml_parent[child]" value="<?php if(isset($this->item->xml_parent->child)){echo $this->item->xml_parent->child;}?>" /></div>
				</td>
			</tr>
			<tr>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_XML_COLUMN_NODE_DESC');?>"><?php echo JText::_('VDATA_XML_COLUMN_NODE');?></label>
				</td>
				<td>
					<select name="xml_parent[node]">
						<option value="column" <?php if(isset($this->item->xml_parent->node) && ($this->item->xml_parent->node=='column')){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_XML_COLUMN_AS_NODE');?></option>
						<option value="other" <?php if(isset($this->item->xml_parent->node) && ($this->item->xml_parent->node=='other')){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_XML_OTHER_AS_NODE');?></option>
					</select>
					<div id="nodename" <?php if( !isset($this->item->xml_parent->node) || (isset($this->item->xml_parent->node) && ($this->item->xml_parent->node=='column')) ){echo ' style="display:none;"';}?>>
						<div class="nodename vdata_xml_data_node_title"><label><?php echo JText::_('VDATA_XML_DATA_NODE_TITLE');?></label>
						<input type="text" name="xml_parent[name]" value="<?php if(isset($this->item->xml_parent->name)){echo $this->item->xml_parent->name;}?>"/></div>
						<div class="nodename vdata_xml_data_node_attriubute"><label><?php echo JText::_('VDATA_XML_DATA_NODE_ATTRIBUTE');?></label>
						<input type="text" name="xml_parent[attribute]" value="<?php if(isset($this->item->xml_parent->attribute)){echo $this->item->xml_parent->attribute;}?>"/></div>
					</div>
				</td>
			</tr>
		</table>
		<?php
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.addTab', 'myTab', 'notifysettings', JText::_('VDATA_CONFIG_NOTIFICATION_SETTINGS'));
		?>
		<table class="adminform table table-striped">
			<tr>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_CONFIG_NOTIFICATION_STATUS_DESC');?>"><?php echo JText::_('VDATA_CONFIG_NOTIFICATION_STATUS');?></label>
				</td>
				<td>
					<select name="notification[status]">
						<option value="0" <?php if(isset($this->item->notification->status) && ($this->item->notification->status==0)){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_DISABLE');?></option>
						<option value="1" <?php if(isset($this->item->notification->status) && ($this->item->notification->status==1)){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_ENABLE');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_CONFIG_NOTIFICATION_RECIPIENTS_DESC');?>"><?php echo JText::_('VDATA_CONFIG_NOTIFICATION_RECIPIENTS');?></label>
				</td>
				<td>
					<input type="text" name="notification[recipients]" value="<?php if(isset($this->item->notification->recipients)){echo $this->item->notification->recipients;}?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_CONFIG_DAY_WEEK_MONTH_NOTIFICATION_DESC');?>"><?php echo JText::_('VDATA_CONFIG_DAY_WEEK_MONTH_NOTIFICATION');?></label>
				</td>
				<td>
					<select name="notification[interval]">
						<option value="0" <?php ?>><?php echo JText::_('VDATA_NONE');?></option>
						<option value="day" <?php if(isset($this->item->notification->interval) && ($this->item->notification->interval=='day')){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_DAILY');?></option>
						<option value="week" <?php if(isset($this->item->notification->interval) && ($this->item->notification->interval=='week')){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_WEEKLY');?></option>
						<option value="month" <?php if(isset($this->item->notification->interval) && ($this->item->notification->interval=='month')){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_MONTHLY');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label class="hasTip required" title="<?php echo JText::_('NOTIFICATION_CRON_URL_DESC');?>"><?php echo JText::_('NOTIFICATION_CRON_URL'); ?></label></td>
				<td>
				<?php echo "curl ".Juri::root()."index.php?option=com_vdata&task=get_notification";?>
				</td>
			</tr>
			<tr>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_CONFIG_DAY_WEEK_MONTH_NOTIFICATION_TMPL_DESC');?>"><?php echo JText::_('VDATA_CONFIG_DAY_WEEK_MONTH_NOTIFICATION_TMPL');?></label>
				</td>
				<td>
					<div class="vdata_editor">
					<legend><?php echo JText::_('VDATA_CONFIG_DAY_WEEK_MONTH_NOTIFICATION_TMPL');?></legend>
					<?php 
						$typeEditor = JFactory::getConfig()->get('editor');
						$editor = JEditor::getInstance($typeEditor);
						// $editor = JFactory::getEditor();
						
						$editorHtml = isset($this->item->notification->tmpl)? $this->item->notification->tmpl:'';
						echo $editor->display( 'notification[tmpl]', $editorHtml, '200', '200', '10', '10', false ,'notify_tmpl');
					?>
					</div>
					<div class="vdata_parameters">
					<h3><?php echo JText::_('VDATA_CONFIG_DAY_WEEK_MONTH_NOTIFICATION_TMPL_PARAMETERS');?></h3>
						<a class="hasTip" title="<?php echo JText::_('VDATA_CONFIG_DAY_WEEK_MONTH_NOTIFICATION_TOTAL_IMPORT');?>" onclick="if (typeof(jInsertEditorText) != 'undefined') jInsertEditorText('{TOTAL_IMPORT}', 'notify_tmpl');return false;" href="">{TOTAL_IMPORT}</a>
						<a class="hasTip" title="<?php echo JText::_('VDATA_CONFIG_DAY_WEEK_MONTH_NOTIFICATION_TOTAL_EXPORT');?>" onclick="if (typeof(jInsertEditorText) != 'undefined') jInsertEditorText('{TOTAL_EXPORT}', 'notify_tmpl');return false;" href="">{TOTAL_EXPORT}</a>
						<a class="hasTip" title="<?php echo JText::_('VDATA_CONFIG_DAY_WEEK_MONTH_NOTIFICATION_PROFILE_STATS');?>" onclick="if (typeof(jInsertEditorText) != 'undefined') jInsertEditorText('{PROFILE_STATS}', 'notify_tmpl');return false;" href="">{PROFILE_STATS}</a>
						<a class="hasTip" title="<?php echo JText::_('VDATA_CONFIG_DAY_WEEK_MONTH_NOTIFICATION_FEED_HITS');?>" onclick="if (typeof(jInsertEditorText) != 'undefined') jInsertEditorText('{FEED_HITS}', 'notify_tmpl');return false;" href="">{FEED_HITS}</a>
						<a class="hasTip" title="<?php echo JText::_('VDATA_CONFIG_DAY_WEEK_MONTH_NOTIFICATION_CRON_STATS');?>" onclick="if (typeof(jInsertEditorText) != 'undefined') jInsertEditorText('{CRON_STATS}', 'notify_tmpl');return false;" href="">{CRON_STATS}</a>
					</div>
				</td>
			</tr>
		</table>
		<?php 
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.endTabSet', 'myTab');
		?>
		
		</fieldset></div></div>
		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" name="option" value="com_vdata" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="config" />
	</form>
</div>