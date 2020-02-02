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

JPluginHelper::importPlugin('vdata', 'custom');
$dispatcher = JDispatcher::getInstance();
$session = JFactory::getSession();
if($session->has('remote_details')){
	$option = $session->get('remote_details');
	$db = JDatabaseDriver::getInstance( $option );
	$db->connect();
}
else{
	$db =  null;
}
$importitem = $session->get('importitem', null);
if( isset($schedule) && ($schedule->profileid==$importitem->profileid) ){
	$table = (!empty($schedule_columns) && property_exists($schedule_columns, 'table'))?$schedule_columns->table:null;
	$fields = (!empty($schedule_columns) && property_exists($schedule_columns, 'fields'))?json_decode(json_encode($schedule_columns->fields), true):null;
	$images = (!empty($schedule_columns) && property_exists($schedule_columns, 'images'))?$schedule_columns->images:null;
}
else{
	$table = $fields = null;
}

$operation = isset($this->profile->params->operation)?$this->profile->params->operation:0;
$keys = isset($this->profile->params->unqkey)?$this->profile->params->unqkey:array();
?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	
	if (task == 'close' || task == 'close_st') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} 
	else if(task == 'import_start'){
		//validation
		var form = document.getElementById('adminForm');
		if(form.table.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_TABLE'); ?>");
				return false;
		}
		var ioquick = $hd('input[name="quick"]:checked').length;
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
		
		function batchImport(offset){

				var values, index;
				values = $hd("#adminForm").serializeArray();
				addOption('task', 'import_start', values);
				addOption('limit', 1, values);
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
					console.log('import continue...');
					$hd('div#hd_progress').show();
				},
				complete: function()	{
				},
				error: function(jqXHR, textStatus, errorThrown)	{
					alert(textStatus);;
				}
			}).done(function(resp){
				if(resp.result == 'success') {
					console.log(resp.offset);
					$hd('div#hd_progress').show();
					var percentage = Math.round((resp.offset/resp.size)*100);
					var prog = percentage>100 ? 100 : percentage;
					$hd('div#hd_progress').width(prog+"%");
					batchImport(resp.offset);
				}
				else if(resp.result == 'error' && resp.hasOwnProperty("qty")) {
					<?php $session->clear('qty');
						$session->clear('totalRecordInFile');
						?>
					console.log('import complete...');
					console.log(resp.error);
					$hd('div#hd_progress').hide();
					var jmsgs = [resp.success_msg];
					Joomla.renderMessages({'message': jmsgs });
				}
				else {
					<?php $session->clear('qty');
						$session->clear('totalRecordInFile');
						?>
					console.log('import abort.');
					var jmsgs = [resp.error_msg,resp.error];
					Joomla.renderMessages({'error': jmsgs });
				}
			});
		}

		batchImport(0);
		
		return false;
	}
	else {
		
		var form = document.adminForm;
		
		if(form.table.value == "")	{
			alert("<?php echo JText::_('PLZ_SELECT_TABLE'); ?>");
			return false;
		}
			
		var ioquick = $hd('input[name="quick"]:checked').length;
		Joomla.submitform(task, document.getElementById('adminForm'));
		return false;
					
	}
}

$hd(function()	{
    
	$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	
    var columns = new Array();
	
	<?php if(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && ($this->profile->quick==1)){echo "custom_quick();";}?>
	
	$hd(document).on('change', 'input[name="quick"]', function(event)	{
		custom_quick(event);
	});
	
    $hd(document).on('change', 'select.rtable', function(event)	{
		
		var that = this;
		var hasIdx = ($hd(that).attr('data-idx')!=undefined && $hd(that).attr('data-idx').length)? true: false;
		var table = $hd(this).val();
		var default_opt = '<option value=""><?php echo JText::_('SELECT_FIELD');?></option>';
		var ref_opt = '<option value="reference"><?php echo JText::_('REFERENCE_FIELD');?></option>';
		
		$hd('div.ref_field_options').html('');
		
		if(table == "")	{
			if(hasIdx){
				var index = $hd(that).attr('data-idx');
				$hd('select.remote_column'+index).html(default_opt).trigger('liszt:updated');
				$hd('select.remote_column_part'+index).html(default_opt).trigger('liszt:updated');
				
			}
			else{
				$hd('select.remote_column').html(default_opt).trigger('liszt:updated');
				$hd('select.remote_column_part').html(default_opt).trigger('liszt:updated');
				
			}
			return false;
		}
		$hd.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_column_options', 'table':table, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1,'remote_db':1},
			beforeSend: function()	{
				$hd(".loading").show();
			},
			complete: function()	{
				$hd(".loading").hide();
			},
			success: function(res)	{
				window.columns = res.columns;
				if(res.result == "success")  {
					var options = res.html;
					if(hasIdx){
						var index = $hd(that).attr('data-idx');
						$hd('select.remote_column'+index).html(default_opt+ref_opt+options).trigger('liszt:updated');
						$hd('select.remote_column_part'+index).html(default_opt+options).trigger('liszt:updated');
						
					}
					else{
						$hd('select.remote_column').html(default_opt+ref_opt+options).trigger('liszt:updated');
						$hd('select.remote_column_part').html(default_opt+options).trigger('liszt:updated');
						
					}
				}
				else{
					alert(res.error);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)	{
				alert(textStatus);				  
			}
		});	
    });

	$hd(document).on('change','select.ref_table',function(event){
		var that = this;

		var table = $hd(this).val();
		var default_opt = '<option value=""><?php echo JText::_('SELECT_FIELD');?></option>';
	
		if(table==''){
			$hd(that).parent().find('.ref_column').html(default_opt).trigger('liszt:updated');
			$hd(that).closest('tr').next('tr').find('td > .chain_join').html('').trigger('liszt:updated');
			return false;
		}
		$hd.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_column_options', 'table':table, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1,'remote_db':1},
			beforeSend: function()	{
				$hd(".loading").show();
			},
			complete: function()	{
				$hd(".loading").hide();
			},
			success: function(res)	{

				if(res.result == "success")  {
					var options = default_opt+res.html;
					$hd(that).parent().find('.ref_column').html(options).trigger('liszt:updated');
				}
				else{
					alert(res.error);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)	{
				alert(textStatus);				  
			}
		});
		
	});
	
	$hd(document).on('change', 'select.live_ref', function(event)	{
		var that = this;
		if($hd(this).val() == 'reference'){
		var html = '';
		// var column = $hd(this).parent().parent().data('column');
		var column = $hd(this).attr('name');
		
		html += ' <span class="table_block"><span class="hd_label"><?php echo JText::_('JOIN'); ?></span><select class="field_table" name="'+column+'[table]"><option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>';
		html += '<?php
		foreach($this->remotetables as $tbl) :
			echo '<option value="'.$tbl.'">'.$tbl.'</option>';
		endforeach;	?>';
		html += '</select></span>';
		
		var opt = '';
		/* for(var i=0;i<window.columns.length;i++){
			opt += '<option value="'+window.columns[i]+'">'+window.columns[i]+'</option>';
		} */
		opt += $hd($hd(this).clone().find('[value="reference"]').remove().end().find(':selected').removeAttr("selected").end()).html();
		
		html += '<span class="hd_label"><?php echo JText::_('ON'); ?></span>';
		html += ' <select name="'+column+'[column1]" class="leftcolumn">'+opt+'</select> = ';
        html += ' <span class="leftcolumn_block"> <span class="field"><select name="'+column+'[column2]" class="rightcolumn"><option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option></select></span>';    
		html += '<?php echo JText::_('COLUMN_TO_IMPORT'); ?><span class="field"><select name="'+column+'[column]" class="importcolumns"><option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option></select></span></span>';
		$hd(this).parent().find('div.ref_field_options').html(html);
		}
		else    {
            $hd(that).parent().find('div.ref_field_options').html('');
        }
		
		$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
		
	});
	
	
	$hd(document).on('change', 'select.field_table', function(event){
		var that = this;
		var column = $hd(this).parent().parent().parent().parent().data('column');
		var table = $hd(this).val();
		$hd.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_table_columns', 'table':table, 'column':column, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1,'remote_db':1},
			beforeSend: function()	{
				$hd(".loading").show();
			  },
			complete: function()	{
				$hd(".loading").hide();
			  },
			success: function(res)	{
				if(res.result == "success")  {
					var html = '';
					for(var i=0;i<res.columns.length;i++){
						html += '<option value="'+res.columns[i]+'">'+res.columns[i]+'</option>';
					}
					$hd(that).parent().parent().find('select.rightcolumn').html(html).trigger('liszt:updated');
					$hd(that).parent().parent().find('select.importcolumns').html(html).trigger('liszt:updated');
                }
				else{
					alert(res.error);
				}
			  },
			error: function(jqXHR, textStatus, errorThrown)	{
				  alert(textStatus);				  
			}
		});
		
	});

});

function custom_quick(event) {
	
	var that = $hd('select[name="table"]');
	var ioquick = $hd('input[name="quick"]:checked').length;
	var table = $hd('select[name="table"]').val();
	if(ioquick == 0 && table == ''){
		$hd(that).prop('checked', true);
		alert("<?php echo JText::_('PLZ_SELECT_TABLE'); ?>");
		event.stopPropagation();
		event.preventDefault();
		return false;
	}
	if(table == '' || ioquick == 1)	{
		$hd('.col_block').html('<tr><td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_FIELDS');?></td><tr>');
	}
	else {
		$hd.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_import_columns', 'table':table, 'profileid' : <?php echo $this->profile->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
			beforeSend: function()	{
				$hd(".loading").show();
			},
			complete: function()	{
				$hd(".loading").hide();
			},
			success: function(res)	{
				
				if(res.result == "success")  {
					$hd('.col_block').html(res.html);
					$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
				}
				else{
					alert(res.error);
				}
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


<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'PROFILE_IMPORT_DETAILS' ); ?></legend>
        <table class="adminform table table-striped adminformlist">
            <tr>
                <td width="200"><label class="hasTip" title="<?php echo JText::_('TABLE_DESC');?>"><?php echo JText::_('TABLE'); ?></label></td>
                <td>
                    <select name="table" class="rtable">
                        <option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>
                        <?php foreach($this->remotetables as $tbl) { ?>
                        <option value="<?php echo $tbl; ?>"<?php if($tbl==$table){echo ' selected="selected"';}?>><?php echo $tbl; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
			<?php /*?><tr>
				<td width="200"><label class="hasTip" title="<?php echo JText::_('TABLE_UNIQUE_COLUMN_DESC');?>"><?php echo JText::_('TABLE_UNIQUE_COLUMN');?></label></td>
				<td>
					<select name="unique" class="remote_column">
						<option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option>
						<?php 
							$selected = isset($fields['unique'])?$fields['unique']:'';
							$res = $dispatcher->trigger('getColumnOptions', array($table, $selected, $db)); 
							if(!empty($res[0]->options)){
								echo $res[0]->options;
							} 
						?>
					</select>
				</td>
			</tr><?php */?>
        <?php if($this->profile->quick && $operation!=3){?>
            <tr>
            	<td><label class="hasTip" title="<?php echo JText::_('QUICK_IMPORT_DESC');?>"><?php echo JText::_('QUICK_IMPORT'); ?></td>
                <td>
                	<input type="checkbox" value="1" class="inputbox required" id="quick" name="quick" <?php if( !empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) ){ } elseif($this->profile->quick){echo "checked='checked'";}?> />
                </td>
            </tr>	
        <?php }?>
        <?php if($operation==3){
			foreach($keys as $key){
		?>
			<tr>
				<td width="200"><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $key);?>"><?php echo $key; ?></label></td>
				<td>
					<select name="fields[<?php echo $key;?>]" class="remote_column">
						<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<?php
							$selected = isset($fields[$key])?$fields[$key]:'';
							$res = $dispatcher->trigger('getColumnOptions', array($table, $selected, $db)); 
							if(!empty($res[0]->options)){
								echo $res[0]->options;
							} 
						?>
					</select>
				</td>
			</tr>
		<?php }}?>   
        <?php foreach($this->profile->params->fields as $column=>$params) : if($params->data != 'skip' && $params->data != 'defined'){?>
          <tr data-column="<?php echo $column; ?>">
            <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $column);?>"><?php echo $column; ?></label></td>
            <td>
                <?php if($params->data == 'file'){?>
                    <select name="fields[<?php echo $column; ?>]" class="remote_column live_ref">
                        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<option value="reference" <?php if(isset($fields[$column]) && is_array($fields[$column])){echo 'selected="selected"';}?>><?php echo JText::_("REFERENCE_FIELD"); ?></option>
						<?php
							$selected = isset($fields[$column])?$fields[$column]:'';
							$res = $dispatcher->trigger('getColumnOptions', array($table, $selected, $db)); 
							if(!empty($res[0]->options)){
								echo $res[0]->options;
							} 
						?>
                    </select>
                    <div class="ref_field_options">
						<?php if(isset($fields[$column]) && is_array($fields[$column])){?>
							<span class="table_block">
								<span class="hd_label"><?php echo JText::_('JOIN'); ?></span>
								<?php $live_ref_tbl = isset($fields[$column]['table'])?$fields[$column]['table']:null;?>
								<select class="field_table" name="fields[<?php echo $column;?>][table]">
									<option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>
									<?php foreach($this->remotetables as $tbl) { ?>
										<option value="<?php echo $tbl?>" <?php if($tbl==$live_ref_tbl){echo 'selected="selected"';}?>><?php echo $tbl?></option>;
									<?php }?>
								</select>
							</span>
							<span class="hd_label"><?php echo JText::_('ON'); ?></span>
								<?php $selected = isset($fields[$column]['column1'])?$fields[$column]['column1']:null;?>
								<select class="leftcolumn" name="fields[<?php echo $column;?>][column1]">
									<?php 
										$res = $dispatcher->trigger('getColumnOptions', array($table, $selected, $db)); 
										if(!empty($res[0]->options)){
											echo $res[0]->options;
										}
									?>
								</select>
								<?php echo JText::_(' = ');?>
							<span class="leftcolumn_block">
								<span class="field">
									<?php $selected = isset($fields[$column]['column2'])?$fields[$column]['column2']:null;?>
									<select name="fields[<?php echo $column;?>][column2]" class="rightcolumn">
										<option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option>
										<?php 
											$res = $dispatcher->trigger('getColumnOptions', array($live_ref_tbl, $selected, $db)); 
											if(!empty($res[0]->options)){
												echo $res[0]->options;
											}
										?>
									</select>
								</span>
							<?php echo JText::_('COLUMN_TO_IMPORT'); ?>
								<span class="field">
									<?php $selected = isset($fields[$column]['column'])?$fields[$column]['column']:null;?>
									<select name="fields[<?php echo $column;?>][column]" class="importcolumns">
										<option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option>
										<?php 
											$res = $dispatcher->trigger('getColumnOptions', array($live_ref_tbl, $selected, $db)); 
											if(!empty($res[0]->options)){
												echo $res[0]->options;
											}
										?>
									</select>
								</span>
							</span>
							
						<?php }?>
						
					</div>
					
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
				
					
                <?php } elseif($params->data == 'reference' && count($params->reftext)){?>
                	
                        <!--<select name="fields[join][column][]" class="remote_column_join">
                            <option value=""><?php //echo JText::_('SELECT_FIELD'); ?></option>
                        </select>-->
                    
                    <div>
						<?php $ref_table = isset($fields[$column]["table"])?$fields[$column]["table"]:null;?>
                        <select name="fields[<?php echo $column; ?>][table]" class="ref_table">
                            <option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>
                            <?php foreach($this->remotetables as $tbl) {?>
								<option value="<?php echo $tbl; ?>"<?php if($tbl==$ref_table){echo ' selected="selected"';}?>><?php echo $tbl; ?></option>
                            <?php } ?>
                        </select>
						<label class="hasTip" title="<?php echo JText::_('JOIN_FIELD_DESC');?>"><?php echo JText::_('JOIN_FIELD');?></label>
						<?php $selected = isset($fields[$column]["on"])?$fields[$column]["on"]:null;?>
						<select name="fields[<?php echo $column; ?>][on]" class="ref_column">
							<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
							<?php 
								$res = $dispatcher->trigger('getColumnOptions', array($ref_table, $selected, $db)); 
								if(!empty($res[0]->options)){
									echo $res[0]->options;
								} 
							?>
						</select>
						<label class="hasTip" title="<?php echo JText::_('VALUE_FIELD_DESC');?>"><?php echo JText::_('VALUE_FIELD');?></label>
						<?php $selected = isset($fields[$column]["value"])?$fields[$column]["value"]:null;?>
						<select name="fields[<?php echo $column; ?>][value]" class="remote_column_part">
							<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
							<?php 
								$res = $dispatcher->trigger('getColumnOptions', array($table, $selected, $db)); 
								if(!empty($res[0]->options)){
									echo $res[0]->options;
								} 
							?>
						</select>
                        <?php foreach($params->reftext as $ref){?>
                            <div class="field_option">
                                <label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $ref);?>"><?php echo $ref;?></label>
                                <span class="field">
                                    <select name="fields[<?php echo $column; ?>][refcol][<?php echo $ref;?>]" class="ref_column live_ref">
                                        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
										<?php 
											$selected = isset($fields[$column]['refcol'][$ref])?$fields[$column]['refcol'][$ref]:'';
											$res = $dispatcher->trigger('getColumnOptions', array($ref_table, $selected, $db)); 
											if(!empty($res[0]->options)){
												echo $res[0]->options;
											} 
										?>
                                    </select>
									
                                </span>
                            </div>
                        <?php }?>
                    </div>
                <?php }elseif($params->data == "asset_reference"){ ?>
		
            <?php 
			$db  = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('name');
			$query->from($db->quoteName('#__extensions'));  
			$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
			$query->where($db->quoteName('name') . ' = ' . $db->quote($params->table));
			$db->setQuery($query);
			$components = $db->loadResult();  
			
			if (strpos(strtolower($components), "com_") ===FALSE){
			$components = "com_".strtolower($components); 	
			}	
			?>
					
				<div class = "field_option">
					<label class="hasTip" title="<?php echo JText::_('Component Name');?>"><?php echo JText::_('Component Name');?></label>
					<span class="field">
						<select name="fields[<?php echo $column;?>][component]">
							<option value="<?php echo $params->table?>"><?php echo JText::_($components); ?></option>
							</select>
						
					</span>
				</div>
				<div class = "field_option"> 
					<label class="hasTip" title="<?php echo JText::_('Table For');?>"><?php echo JText::_('Table For');?></label>
					<span class="field">
						<select name="fields[<?php echo $column;?>][on]">
							<option value="<?php echo $params->on?>"><?php echo JText::_($params->on); ?></option>
							</select>
						
					</span>
				</div>
				<div class = "field_option"> 
					<label class="hasTip" title="<?php echo JText::_('RULES');?>"><?php echo JText::_('RULES');?></label>
					<span class="field">
						<select name="fields[<?php echo $column;?>][rules]" class="remote_column live_ref">
							<option value=""><?php echo JText::_("SELECT_RULES"); ?></option>
							<option value="reference" <?php if(isset($fields[$column]['rules']) && is_array($fields[$column]['rules'])){echo 'selected="selected"';}?>><?php echo JText::_("REFERENCE_FIELD"); ?></option>
							<?php 
								$selected = (isset($fields[$column]['rules']) && !is_array($fields[$column]['rules']))?$fields[$column]['rules']:'';
								$res = $dispatcher->trigger('getColumnOptions', array($table, $selected, $db)); 
								if(!empty($res[0]->options)){
									echo $res[0]->options;
								}
							?>
						</select>
						<div class="ref_field_options">
						<?php if(isset($fields[$column]['rules']) && is_array($fields[$column]['rules'])){?>
							<span class="table_block">
								<span class="hd_label"><?php echo JText::_('JOIN'); ?></span>
								<?php $live_ref_tbl = isset($fields[$column]['rules']['table'])?$fields[$column]['rules']['table']:null;?>
								<select class="field_table" name="fields[<?php echo $column;?>][rules][table]">
									<option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>
									<?php foreach($this->remotetables as $tbl) { ?>
										<option value="<?php echo $tbl?>" <?php if($tbl==$live_ref_tbl){echo 'selected="selected"';}?>><?php echo $tbl?></option>;
									<?php }?>
								</select>
							</span>
							<span class="hd_label"><?php echo JText::_('ON'); ?></span>
								<?php $selected = isset($fields[$column]['rules']['column1'])?$fields[$column]['rules']['column1']:null;?>
								<select class="leftcolumn" name="fields[<?php echo $column;?>][rules][column1]">
									<?php 
										$res = $dispatcher->trigger('getColumnOptions', array($table, $selected, $db)); 
										if(!empty($res[0]->options)){
											echo $res[0]->options;
										}
									?>
								</select>
								<?php echo JText::_(' = ');?>
							<span class="leftcolumn_block">
								<span class="field">
									<?php $selected = isset($fields[$column]['rules']['column2'])?$fields[$column]['rules']['column2']:null;?>
									<select name="fields[<?php echo $column;?>][rules][column2]" class="rightcolumn">
										<option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option>
										<?php 
											$res = $dispatcher->trigger('getColumnOptions', array($live_ref_tbl, $selected, $db)); 
											if(!empty($res[0]->options)){
												echo $res[0]->options;
											}
										?>
									</select>
								</span>
							<?php echo JText::_('COLUMN_TO_IMPORT'); ?>
								<span class="field">
									<?php $selected = isset($fields[$column]['rules']['column'])?$fields[$column]['rules']['column']:null;?>
									<select name="fields[<?php echo $column;?>][rules][column]" class="importcolumns">
										<option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option>
										<?php 
											$res = $dispatcher->trigger('getColumnOptions', array($live_ref_tbl, $selected, $db)); 
											if(!empty($res[0]->options)){
												echo $res[0]->options;
											}
										?>
									</select>
								</span>
							</span>
						<?php }?>
						</div>
					</span>
				</div>
			</td>
		</tr>
		<?php } ?>
            </td>
          </tr> 
        <?php } endforeach; ?>
			<?php 	$joins = isset($this->profile->params->joins) ? $this->profile->params->joins : null;
					if(!empty($joins)){
					$joins->table1 = isset($joins->table1) ? $joins->table1 : array();
					for($i=0; $i<count($joins->table1); $i++) {
			?>
            	<tr>
                	<td>
                    	<label class="hasTip" title="<?php echo JText::sprintf('CHILD_TABLE_TO_IMPORT', $joins->table2[$i]);?>"><?php echo $joins->table2[$i];?></label>
                    </td>
					<td>
						<?php $cur_join_table = isset($fields[$joins->table2[$i]]['table'])?$fields[$joins->table2[$i]]['table']:null;?>
						<select name="fields[<?php echo $joins->table2[$i];?>][table]" class="rtable" data-idx="<?php echo $i;?>">
                            <option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>
                            <?php foreach($this->remotetables as $tbl) : ?>
                            <option value="<?php echo $tbl; ?>" <?php if($cur_join_table==$tbl){echo ' selected="selected"';}?>><?php echo $tbl; ?></option>
                            <?php endforeach; ?>
                        </select>
						
						<label class="hasTip" title="<?php echo JText::_('JOIN_FIELD_DESC');?>"><?php echo JText::_('JOIN_FIELD');?></label>
						<select name="fields[<?php echo $joins->table2[$i];?>][on]" class="remote_column_part<?php echo $i;?>">
							<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
							<?php 
								$selected = isset($fields[$joins->table2[$i]]["on"])?$fields[$joins->table2[$i]]["on"]:'';
								$res = $dispatcher->trigger('getColumnOptions', array($cur_join_table, $selected, $db)); 
								if(!empty($res[0]->options)){
									echo $res[0]->options;
								} 
							?>
						</select>
						<label class="hasTip" title="<?php echo JText::_('VALUE_FIELD_DESC');?>"><?php echo JText::_('VALUE_FIELD');?></label>
						<?php 
							$pfx = '';
							if($joins->table1[$i]==$joins->table1[0]){
								$join_val_table = $joins->table1[0];
							}
							else{
								$pfx = array_search($joins->table1[$i], $joins->table2);
								$join_val_table = isset($fields[$joins->table1[$i]]["table"])?$fields[$joins->table1[$i]]["table"]:'';
							}
							
						?>
						<select name="fields[<?php echo $joins->table2[$i];?>][value]" class="remote_column_part<?php echo $pfx;?>">
							<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
							<?php
								$selected = isset($fields[$joins->table2[$i]]["value"])?$fields[$joins->table2[$i]]["value"]:'';
								$res = $dispatcher->trigger('getColumnOptions', array($join_val_table, $selected, $db)); 
								if(!empty($res[0]->options)){
									echo $res[0]->options;
								} 
							?>
						</select>
					</td>
				</tr>

				<?php if(isset($joins->columns[$i])){foreach($joins->columns[$i] as $key=>$column){?>
				<?php if($column->data=='file'){?>
					<tr>
					<td><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $key);?>"><?php echo $key;?></label></td>
					<td>
					<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>]" id="" class="remote_column<?php echo $i;?> live_ref">
						<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<option value="reference" <?php if(isset($fields[$joins->table2[$i]]['columns'][$key]) && is_array($fields[$joins->table2[$i]]['columns'][$key])){echo 'selected="selected"';}?>><?php echo JText::_("REFERENCE_FIELD"); ?></option>
						<?php 
							$selected = isset($fields[$joins->table2[$i]]['columns'][$key])?$fields[$joins->table2[$i]]['columns'][$key]:'';
							$res = $dispatcher->trigger('getColumnOptions', array($cur_join_table, $selected, $db)); 
							if(!empty($res[0]->options)){
								echo $res[0]->options;
							} 
						?>
					</select>
					<div class="ref_field_options">
						<?php if(isset($fields[$joins->table2[$i]]['columns'][$key]) && is_array($fields[$joins->table2[$i]]['columns'][$key])){?>
							<span class="table_block">
								<span class="hd_label"><?php echo JText::_('JOIN'); ?></span>
								<?php $live_ref_tbl = isset($fields[$joins->table2[$i]]['columns'][$key]['table'])?$fields[$joins->table2[$i]]['columns'][$key]['table']:null;?>
								<select class="field_table" name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>][table]">
									<option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>
									<?php foreach($this->remotetables as $tbl) { ?>
										<option value="<?php echo $tbl?>" <?php if($tbl==$live_ref_tbl){echo 'selected="selected"';}?>><?php echo $tbl?></option>;
									<?php }?>
								</select>
							</span>
							<span class="hd_label"><?php echo JText::_('ON'); ?></span>
								<?php $selected = isset($fields[$joins->table2[$i]]['columns'][$key]['column1'])?$fields[$joins->table2[$i]]['columns'][$key]['column1']:null;?>
								<select class="leftcolumn" name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>][column1]">
									<?php 
										$res = $dispatcher->trigger('getColumnOptions', array($cur_join_table, $selected, $db)); 
										if(!empty($res[0]->options)){
											echo $res[0]->options;
										}
									?>
								</select>
								<?php echo JText::_(' = ');?>
							<span class="leftcolumn_block">
								<span class="field">
									<?php $selected = isset($fields[$joins->table2[$i]]['columns'][$key]['column2'])?$fields[$joins->table2[$i]]['columns'][$key]['column2']:null;?>
									<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>][column2]" class="rightcolumn">
										<option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option>
										<?php 
											$res = $dispatcher->trigger('getColumnOptions', array($live_ref_tbl, $selected, $db)); 
											if(!empty($res[0]->options)){
												echo $res[0]->options;
											}
										?>
									</select>
								</span>
							<?php echo JText::_('COLUMN_TO_IMPORT'); ?>
								<span class="field">
									<?php $selected = isset($fields[$joins->table2[$i]]['columns'][$key]['column'])?$fields[$joins->table2[$i]]['columns'][$key]['column']:null;?>
									<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>][column]" class="importcolumns">
										<option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option>
										<?php 
											$res = $dispatcher->trigger('getColumnOptions', array($live_ref_tbl, $selected, $db)); 
											if(!empty($res[0]->options)){
												echo $res[0]->options;
											}
										?>
									</select>
								</span>
							</span>
						<?php }?>
					</div>
					</td>
				   </tr>
				<?php }elseif($column->data == "reference"  && count($column->reftext)){?>
					<tr>
					<td><label class="hasTip" title="<?php echo JText::sprintf('REF_FIELD_TO_IMPORT', $key);?>"><?php echo $key; ?></label></td>
					<td>
					<?php $cur_ref_table = isset($fields[$joins->table2[$i]]['columns'][$key]['table'])?$fields[$joins->table2[$i]]['columns'][$key]['table']:null;?>
					<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key; ?>][table]" class="ref_table">
						<option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>
						<?php foreach($this->remotetables as $tbl) : ?>
						<option value="<?php echo $tbl; ?>"<?php if($tbl==$cur_ref_table){echo ' selected="selected"';}?>><?php echo $tbl; ?></option>
						<?php endforeach; ?>
					</select>
					<label class="hasTip" title="<?php echo JText::_('JOIN_FIELD_DESC');?>"><?php echo JText::_('JOIN_FIELD');?></label>
					<?php $selected = isset($fields[$joins->table2[$i]]['columns'][$key]["on"])?$fields[$joins->table2[$i]]['columns'][$key]["on"]:null;?>
					<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key; ?>][on]" class="ref_column">
						<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<?php 
							$res = $dispatcher->trigger('getColumnOptions', array($cur_ref_table, $selected, $db)); 
							if(!empty($res[0]->options)){
								echo $res[0]->options;
							} 
						?>
					</select>
					<label class="hasTip" title="<?php echo JText::_('VALUE_FIELD_DESC');?>"><?php echo JText::_('VALUE_FIELD');?></label>
					<?php $selected = isset($fields[$joins->table2[$i]]['columns'][$key]['value'])?$fields[$joins->table2[$i]]['columns'][$key]['value']:null;?>
					<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key; ?>][value]" class="remote_column_part<?php echo $i;?>">
						<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<?php 
							$res = $dispatcher->trigger('getColumnOptions', array($cur_join_table, $selected, $db)); 
							if(!empty($res[0]->options)){
								echo $res[0]->options;
							} 
						?>
					</select>
						
					<?php foreach($column->reftext as $ref){?>
						<div class="field_option">
							<label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $ref);?>"><?php echo $ref;?></label>
							<span class="field">
								<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key; ?>][refcol][<?php echo $ref;?>]" class="ref_column live_ref">
									<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
									<?php 
										$selected = isset($fields[$joins->table2[$i]]['columns'][$key]['refcol'][$ref])?$fields[$joins->table2[$i]]['columns'][$key]['refcol'][$ref]:'';
										$res = $dispatcher->trigger('getColumnOptions', array($cur_ref_table, $selected, $db)); 
										if(!empty($res[0]->options)){
											echo $res[0]->options;
										} 
									?>
								</select>
								
							</span>
						</div>
					<?php }?>
					</td>
					</tr>
				<?php }elseif($column->data == "asset_reference"){?>
				<tr>
					<td width="200"><label class="hasTip" title="<?php echo JText::sprintf('REF_FIELD_TO_IMPORT', $key);?>"><?php echo $key; ?></label></td>
					<td>
					<?php 
						$db  = JFactory::getDbo();
						$query = $db->getQuery(true);
						$query->select('name');
						$query->from($db->quoteName('#__extensions'));  
						$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
						$query->where($db->quoteName('extension_id') . ' = ' . $db->quote($column->table));
						$db->setQuery($query);
						$components = $db->loadResult();  
						
						if (strpos(strtolower($components), "com_") !=0){
						$components = "com_".strtolower($components); 	
						}
					?>
						
						<div class = "field_option">
							<label class="hasTip" title="<?php echo JText::_('Component Name');?>"><?php echo JText::_('Component Name');?></label>
							<span class="field">
								<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>][component]">
									<option value="<?php echo $column->table?>"><?php echo JText::_($components); ?></option>
								</select>
								
							</span>
						</div>
						<div class = "field_option"> 
							<label class="hasTip" title="<?php echo JText::_('Table For');?>"><?php echo JText::_('Table For');?></label>
							<span class="field">
								<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>][on]">
									<option value="<?php echo $column->on?>"><?php echo JText::_($column->on); ?></option>
								</select>
								
							</span>
						</div>
						<div class = "field_option"> 
							<label class="hasTip" title="<?php echo JText::_('RULES');?>"><?php echo JText::_('RULES');?></label>
							<span class="field">
								<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>][rules]" class="remote_column<?php echo $i;?> live_ref">
									<option value=""><?php echo JText::_("SELECT_RULES"); ?></option>
									<option value="reference" <?php if(isset($fields[$joins->table2[$i]]['columns'][$key]['rules']) && is_array($fields[$joins->table2[$i]]['columns'][$key]['rules'])){echo 'selected="selected"';}?>><?php echo JText::_("REFERENCE_FIELD"); ?></option>
									<?php 
										$selected = isset($fields[$joins->table2[$i]]['columns'][$key]['rules'])?$fields[$joins->table2[$i]]['columns'][$key]['rules']:'';
										$res = $dispatcher->trigger('getColumnOptions', array($cur_join_table, $selected, $db)); 
										if(!empty($res[0]->options)){
											echo $res[0]->options;
										} 
									?>
								</select>
								<div class="ref_field_options">
								<?php if(isset($fields[$joins->table2[$i]]['columns'][$key]['rules']) && is_array($fields[$joins->table2[$i]]['columns'][$key]['rules'])){?>
									<span class="table_block">
										<span class="hd_label"><?php echo JText::_('JOIN'); ?></span>
										<?php $live_ref_tbl = isset($fields[$joins->table2[$i]]['columns'][$key]['rules']['table'])?$fields[$joins->table2[$i]]['columns'][$key]['rules']['table']:null;?>
										<select class="field_table" name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>][rules][table]">
											<option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>
											<?php foreach($this->remotetables as $tbl) { ?>
												<option value="<?php echo $tbl?>" <?php if($tbl==$live_ref_tbl){echo 'selected="selected"';}?>><?php echo $tbl?></option>;
											<?php }?>
										</select>
									</span>
									<span class="hd_label"><?php echo JText::_('ON'); ?></span>
										<?php $selected = isset($fields[$joins->table2[$i]]['columns'][$key]['rules']['column1'])?$fields[$joins->table2[$i]]['columns'][$key]['rules']['column1']:null;?>
										<select class="leftcolumn" name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>][rules][column1]">
											<?php 
												$res = $dispatcher->trigger('getColumnOptions', array($cur_join_table, $selected, $db)); 
												if(!empty($res[0]->options)){
													echo $res[0]->options;
												}
											?>
										</select>
										<?php echo JText::_(' = ');?>
									<span class="leftcolumn_block">
										<span class="field">
											<?php $selected = isset($fields[$joins->table2[$i]]['columns'][$key]['rules']['column2'])?$fields[$joins->table2[$i]]['columns'][$key]['rules']['column2']:null;?>
											<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>][rules][column2]" class="rightcolumn">
												<option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option>
												<?php 
													$res = $dispatcher->trigger('getColumnOptions', array($live_ref_tbl, $selected, $db)); 
													if(!empty($res[0]->options)){
														echo $res[0]->options;
													}
												?>
											</select>
										</span>
									<?php echo JText::_('COLUMN_TO_IMPORT'); ?>
										<span class="field">
											<?php $selected = isset($fields[$joins->table2[$i]]['columns'][$key]['rules']['column'])?$fields[$joins->table2[$i]]['columns'][$key]['rules']['column']:null;?>
											<select name="fields[<?php echo $joins->table2[$i];?>][columns][<?php echo $key;?>][rules][column]" class="importcolumns">
												<option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option>
												<?php 
													$res = $dispatcher->trigger('getColumnOptions', array($live_ref_tbl, $selected, $db)); 
													if(!empty($res[0]->options)){
														echo $res[0]->options;
													}
												?>
											</select>
										</span>
									</span>
								<?php }?>
								</div>
							</span>
						</div>
					</td>
				</tr>
				<?php }?>
				<?php }}?>
				
            <?php }}?>
			
			<?php if($this->profile->quick){?>
				<table class="adminform table table-striped col_block">
					<tr style="display:none;">
					<td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_FIELDS');?></td>
					</tr>
				</table>
			<?php }?>
        </table>
	</fieldset>
</div>