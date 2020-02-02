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
$images = (!empty($schedule_columns) && property_exists($schedule_columns, 'images'))?$schedule_columns->images:null;

?>

<script type="text/javascript">

Joomla.submitbutton = function(task) {
	if (task == 'close' || task == 'close_st') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} 
	else {
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
			alert("<?php echo JText::_('PLZ_FILL_FIELD_TO_IMPORT_DATA'); ?>");
			return false;	
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
		return false;
	}
}
$hd(function()	{
		<?php if( !empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && ($this->profile->quick==1) ){echo "custom_quick();";}?>
		
		$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
		
		$hd('input[name="quick"]').off("change").on('change', function(event)	{
			custom_quick();
		});
});

function custom_quick(){
	var ioquick = $hd('input[name="quick"]:checked').length;
	if(ioquick == 1){
		$hd('.col_block').html('<tr><td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_COLUMNS');?></td><tr>');
	}
	else{
		$hd.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_expo_columns', 'profileid' : <?php echo $this->profile->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
			beforeSend: function()	{
				$hd(".loading").show();
			},
			complete: function()	{
				$hd(".loading").hide();
			},
			success: function(res)	{
				if(res.result == "success")  {
					$hd('.col_block').html(res.html);
				}
				else
					alert(res.error);
			},
			error: function(jqXHR, textStatus, errorThrown)	{
			  alert(textStatus);				  
			}
		});
	}
}
function showValue(val,col){
	
	if(val=='directory'){
		$hd("#image_source_"+col).val("folder1/folder2/filepath/");
	}else if(val=='ftp'){
		var ftp = "ftp_host:FTP HOST|ftp_port:21|ftp_user:username|ftp_pass:password|ftp_directory:filepath/";
		$hd("#image_source_"+col).val(ftp);
	} else{
		$hd("#image_source_"+col).val("http://fileurl/");
	}
}
</script>

<?php  ?>

<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'PROFILE_IMPORT_DETAILS' ); ?></legend>
        <table class="adminform table table-striped adminformlist">
		
		 <?php if($this->profile->quick){?>
            <tr>
            	<td width="200"><label class="hasTip" title="<?php echo JText::_('QUICK_IMPORT_DESC');?>"><?php echo JText::_('QUICK_IMPORT'); ?></td>
                <td>
                	<input type="checkbox" value="1" class="inputbox required" id="quick" name="quick" <?php if( !empty($schedule_columns) && !empty($schedule_columns->quick) && ($schedule_columns->quick==1) ){echo "checked='checked'";} elseif(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0)){}elseif( $this->profile->quick ){echo "checked='checked'";}?> /><?php //empty($schedule_columns) && ?>
                </td>
            </tr>	
        <?php }?>
			
        <?php 
		foreach($this->profile->params->fields as $column=>$params) { ?>
        <?php
			if( ($params->data == "file") || ($params->data=='defined') ){
		?>
          <tr>
            <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('IMPORT_FIELD_AS', $column);?>"><?php echo $column; ?></label></td>
            <td>
				<input type="text" name="field[<?php echo $column;?>]" value="<?php if( !empty($schedule_columns) && !empty($schedule_columns->fields) && property_exists($schedule_columns->fields, $column) && !is_object($schedule_columns->fields->{$column}) ){ echo $schedule_columns->fields->$column; } else{ echo $column; }?>" />
				<?php if($params->format == "image"){
					$image_source= 'http://fileurl/';
					$path_type='';
					if(!empty($images) && property_exists($images,'root') && property_exists($images->root,$column)){
						$image_value = $images->root->{$column};
						if(property_exists($image_value,'image_source')){
							$image_source = $image_value->image_source;
						}
						if(property_exists($image_value,'path_type')){
							$path_type = $image_value->path_type;
						}
					}
					?>
				<select name="images[root][<?php echo $column; ?>][path_type]" id="path_type" onchange="showValue(this.value,'<?php echo $column?>')">
					<option value='' selected="selected"><?php echo JText::_('SELECT_FILE_PATH');?></option>
					<option value='url' <?php if($path_type=='url')echo 'selected="selected"'; ?>>URL</option>
					<option value='ftp' <?php if($path_type=='ftp')echo 'selected="selected"'; ?>>FTP</option>
					<option value='directory' <?php if($path_type=='directory')echo 'selected="selected"'; ?>>Directory</option>
				</select>
				<input name="images[root][<?php echo $column; ?>][image_source]" id="image_source_<?php echo $column?>" type="text" value="<?php echo $image_source; ?>">
			<?php }?>
				
			</td>
          </tr>
		  
        <?php } elseif($params->data == "reference"  && count($params->reftext)){?>
        	<tr>
                <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('IMPORT_REF_TABLE_FIELDS', $column);?>"><?php echo $column; ?></label></td>
                <td>
                	<?php foreach($params->reftext as $ref){ ?>
                	<div class = "field_option">
                    	<label class="hasTip" title="<?php echo JText::sprintf('IMPORT_FIELD_AS', $ref);?>"><?php echo $ref;?></label>
                        <span class="field">
							<input type="text" name="field[<?php echo $column;?>][<?php echo $ref;?>]" value="<?php if( !empty($schedule_columns)  && !empty($schedule_columns->fields) && property_exists($schedule_columns->fields, $column) && is_object($schedule_columns->fields->$column) && property_exists($schedule_columns->fields->$column,$ref) ){ echo $schedule_columns->fields->$column->$ref; } else{ echo $params->table.'_'.$ref; }?>" />
                        </span>
                    </div>
                     <?php }?>
                </td>
            </tr>
        <?php }?>
        <?php } ?>
        
		<?php 	$joins = isset($this->profile->params->joins) ? $this->profile->params->joins : null;
				if(!empty($joins)){
				$joins->table1 = isset($joins->table1) ? $joins->table1 : array();
				for($i=0; $i<count($joins->table1); $i++) {
		?>
				
					<?php if( isset($joins->columns[$i]) && !empty($joins->columns[$i]) ){ ?>
					<tr>
						<td>
							<label class="hasTip" title="<?php echo JText::sprintf('IMPORT_CHILD_TABLE_FIELDS', $joins->table2[$i]);?>"><?php echo $joins->table2[$i];?></label>
						</td>
						<td>	
					<?php foreach($joins->columns[$i] as $column){ ?>
							<div class="field_option">
								<label class="hasTip" title="<?php echo JText::sprintf('IMPORT_FIELD_AS', $column);?>"><?php echo $column;?></label>
								<span class="field">
								<input type="text" name="field[<?php echo $joins->table2[$i];?>][<?php echo $column;?>]" value="<?php if( !empty($schedule_columns)  && !empty($schedule_columns->fields) && property_exists($schedule_columns->fields, $joins->table2[$i]) && !empty($schedule_columns->fields->{$joins->table2[$i]}->$column) ){echo $schedule_columns->fields->{$joins->table2[$i]}->$column;} else {echo $column;} ?>" />
								</span>
							</div>
					<?php } ?>
						</td>
					</tr>
					<?php }?>
				
		<?php }}?>
		
		<?php if($this->profile->quick){?>
			<table class="adminform table table-striped col_block">
				<tr style="display:none;">
				<td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_COLUMNS');?></td>
				</tr>
			</table>
		<?php }?>
        </table>
	</fieldset>
</div>