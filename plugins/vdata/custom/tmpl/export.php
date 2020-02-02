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
					
					$hd('div#hd_progress').show();
				},
				complete: function()	{
				},
				error: function(jqXHR, textStatus, errorThrown)	{
					alert(textStatus);				  
				} 
			}).done(function(resp){
				if(resp.result == 'success'){
					console.log(resp.offset);
					$hd('div#hd_progress').show();
					var percentage = Math.round((resp.offset/resp.size)*100);
					var prog = percentage>100 ? 100 : percentage;
					// $hd('div#hd_progress').width(prog+"%");
					$hd('div#hd_progress').animate({width: prog+"%"}, 500, 'swing');
					batchExport(resp.offset);
				}
				else if(resp.result == 'error' && resp.hasOwnProperty("qty")) {
					console.log(resp.error);
					console.log('export complete...');
					$hd('div#hd_progress').hide();
					var jmsgs = [resp.success_msg];
					Joomla.renderMessages({'message': jmsgs });//message,info
					if(resp.dlink){
						window.location = resp.down;	 
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
		<?php if(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && ($this->profile->quick==1)){echo "custom_quick();";}
		?>
		
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
</script>

<?php  ?>

<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'PROFILE_EXPORT_DETAILS' ); ?></legend>
        <table class="adminform table table-striped adminformlist">
		
		<?php if($profileid!=0){?>
		
		 <?php if($this->profile->quick){?>
            <tr>
            	<td width="200"><label class="hasTip" title="<?php echo JText::_('QUICK_EXPORT_DESC');?>"><?php echo JText::_('QUICK_EXPORT'); ?></td>
                <td>
                	<input type="checkbox" value="1" class="inputbox required" id="quick" name="quick" <?php if( !empty($schedule_columns) && !empty($schedule_columns->quick) && ($schedule_columns->quick==1) ){echo "checked='checked'";} elseif(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0)){}elseif( $this->profile->quick ){echo "checked='checked'";}?> /><?php //empty($schedule_columns) && ?>
                </td>
            </tr>	
        <?php }?>
			
        <?php 
		foreach($this->profile->params->fields as $column=>$params) : ?>
        <?php
			if( ($params->data == "include") || ($params->data=='defined') ){
		?>
          <tr>
            <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('EXPORT_FIELD_AS', $column);?>"><?php echo $column; ?></label></td>
            <td>
				<input type="text" name="field[<?php echo $column;?>]" value="<?php if( !empty($schedule_columns) && !empty($schedule_columns->fields) && property_exists($schedule_columns->fields, $column) && !is_object($schedule_columns->fields->{$column}) ){ echo $schedule_columns->fields->$column; } else{ echo $column; }?>" />
			</td>
          </tr>
        <?php } elseif(($params->data == "reference") && isset($params->reftext) && count($params->reftext)){?>
        	<tr>
                <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('EXPORT_REF_TABLE_FIELDS', $column);?>"><?php echo $column; ?></label></td>
                <td>
                	<?php foreach($params->reftext as $ref){ ?>
                	<div class = "field_option">
                    	<label class="hasTip" title="<?php echo JText::sprintf('EXPORT_FIELD_AS', $ref);?>"><?php echo $ref;?></label>
                        <span class="field">
							<input type="text" name="field[<?php echo $column;?>][<?php echo $ref;?>]" value="<?php if( !empty($schedule_columns)  && !empty($schedule_columns->fields) && property_exists($schedule_columns->fields, $column) && is_object($schedule_columns->fields->$column) && property_exists($schedule_columns->fields->$column,$ref) ){ echo $schedule_columns->fields->$column->$ref; } else{ echo $params->table.'_'.$ref; }?>" />
                        </span>
                    </div>
                     <?php }?>
                </td>
            </tr>
        <?php }?>
        <?php endforeach; ?>
        
		<?php 	$joins = isset($this->profile->params->joins) ? $this->profile->params->joins : null;
				if(!empty($joins)){
				$joins->table1 = isset($joins->table1) ? $joins->table1 : array();
				for($i=0; $i<count($joins->table1); $i++) {
		?>
				
					<?php if( isset($joins->columns[$i]) && !empty($joins->columns[$i]) ){ ?>
					
					<tr>
						<?php if(strtolower($source)!='xml'){?>
						<td colspan="2">
							<label class="hasTip" title="<?php echo JText::sprintf('EXPORT_CHILD_TABLE_FIELDS', $joins->table2[$i]);?>"><?php echo $joins->table2[$i];?></label>
						</td>
						<?php }else{?>
							<td>
								<label class="hasTip" title="<?php echo JText::sprintf('EXPORT_CHILD_TABLE_FIELDS', $joins->table2[$i]);?>"><?php echo $joins->table2[$i];?></label>
							</td>
							<td>
								<input type="text" name="field[<?php echo $joins->table2[$i];?>][root_tag]" value="<?php if( !empty($schedule_columns)  && !empty($schedule_columns->fields) && property_exists($schedule_columns->fields, $joins->table2[$i]) && !empty($schedule_columns->fields->{$joins->table2[$i]}->root_tag) ){echo $schedule_columns->fields->{$joins->table2[$i]}->root_tag;} else {echo str_replace('#__', $dbprefix, $joins->table2[$i]);} ?>" />
							</td>
						<?php }?>
					</tr>
					<?php foreach($joins->columns[$i] as $column=>$params){ ?>
						<?php if(($params->data == "include") || ($params->data=='defined')){?>
						<tr>
							<td width="200">
								<label class="hasTip" title="<?php echo JText::sprintf('EXPORT_FIELD_AS', $column);?>"><?php echo $column; ?></label>
							</td>
							<td>
								<input type="text" name="field[<?php echo $joins->table2[$i];?>][<?php echo $column;?>]" value="<?php if( !empty($schedule_columns)  && !empty($schedule_columns->fields) && property_exists($schedule_columns->fields, $joins->table2[$i]) && !empty($schedule_columns->fields->{$joins->table2[$i]}->$column) ){echo $schedule_columns->fields->{$joins->table2[$i]}->{$column};} else {echo $column;} ?>" />
							</td>
						</tr>
						<?php } elseif($params->data == "reference"  && count($params->reftext)){?>
						<tr>
							<td width="200"><label class="hasTip" title="<?php echo JText::sprintf('EXPORT_REF_TABLE_FIELDS', $column);?>"><?php echo $column; ?></label></td>
							<td>
								<?php foreach($params->reftext as $ref){ ?>
									<div class = "field_option">
										<label class="hasTip" title="<?php echo JText::sprintf('EXPORT_FIELD_AS', $ref);?>"><?php echo $ref;?></label>
										 <span class="field">
											<input type="text" name="field[<?php echo $joins->table2[$i];?>][<?php echo $column;?>][<?php echo $ref?>]" value="<?php if( !empty($schedule_columns)  && !empty($schedule_columns->fields) && property_exists($schedule_columns->fields, $joins->table2[$i]) && !empty($schedule_columns->fields->{$joins->table2[$i]}->$column)  && (isset($schedule_columns->fields->{$joins->table2[$i]}->{$column}->{$ref}))){echo $schedule_columns->fields->{$joins->table2[$i]}->{$column}->{$ref};} else {echo $ref;} ?>" />
										 </span>
									</div>
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					<?php } ?>
					
					<?php }?>
				
		<?php }}?>
		
		<?php }?>
		
		<?php if($st && !empty($qry) && ($profileid==0) ) {?>
		<tr>
			<td colspan="2"><?php echo JText::_('QUERY_COLUMNS');?></td>
		</tr>
			<?php if(!empty($sfields)){ foreach($sfields as $key=>$sfield){?>
				<tr>
					<td width="200"><label class="hasTip" title="<?php echo JText::_('');?>"><?php echo $key;?></label></td>
					<td>
						<input type="text" name="field[<?php echo $key;?>]" value="<?php if( !empty($schedule_columns) && property_exists($schedule_columns->fields, $key) ){ echo $schedule_columns->fields->{$key}; } else{ echo $key; }?>" />
					</td>
				</tr>
			<?php }}?>
		<?php }?>
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