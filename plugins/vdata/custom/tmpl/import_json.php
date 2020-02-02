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
$baseFields = (!empty($schedule_columns) && property_exists($schedule_columns, 'base'))?$schedule_columns->base:null;
if(!empty($baseFields) && count($baseFields)>1){
	$base = $baseFields[count($baseFields)-2];
}
else{
	$base = '';
}

$jsonfield = (!empty($schedule_columns) && property_exists($schedule_columns, 'fields'))?$schedule_columns->fields:null;
$root = ((!empty($schedule_columns) && property_exists($schedule_columns, 'root')))?$schedule_columns->root:null;
$images = (!empty($schedule_columns) && property_exists($schedule_columns, 'images'))?$schedule_columns->images:null;
$operation = isset($this->profile->params->operation)?$this->profile->params->operation:0;
$keys = isset($this->profile->params->unqkey)?$this->profile->params->unqkey:array();
?>

<script type="text/javascript">

Joomla.submitbutton = function(task) {
	if (task == 'close' || task == 'close_st') {
		
		Joomla.submitform(task, document.getElementById('adminForm'));
	} 
	else if(task == 'import_start'){
		var form = document.getElementById('adminForm');
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
					if(offset == 0){
						console.log('import start...');
					}
					else{
						console.log('import continue...');
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
					$hd('div#hd_progress').width(prog+"%");
					batchImport(resp.offset);
				}
				else if(resp.result == 'error' && resp.hasOwnProperty("qty")) {
					<?php $session->clear('qty');
						$session->clear('totalRecordInFile');
						?>
					var percentage = Math.round((resp.offset/resp.size)*100);
					var prog = percentage>100 ? 100 : percentage;
					$hd('div#hd_progress').width(prog+"%");
					
					console.log(resp.error);
					console.log('import complete...');
					$hd('div#hd_progress').hide();
					var jmsgs = [resp.success_msg];
					Joomla.renderMessages({'message': jmsgs });//message,info
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
		Joomla.submitform(task, document.getElementById('adminForm'));
		return false;			
	}
}
$hd(function()	{
	
	$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	
	<?php if(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && ($this->profile->quick==1)){echo "custom_quick();";}?>
	
	$hd('input[name="quick"]').off("change").on('change', function(event)	{
		custom_quick();
	});
	
	$hd(document).off('change.root').on('change.root', 'select.root', function(event){
		
		var this_ref = this;
		var root = $hd(this).val();
		var default_opt = '<option value=""><?php echo JText::_('SELECT_FIELD');?></option>';
		var selected = ($hd(this_ref).attr('data-idx')!=undefined && $hd(this_ref).attr('data-idx').length)? true: false;
		
		
		$hd(this_ref).parent().find('span.child_field').remove();
		if(selected){
			var idx = $hd(this_ref).attr('data-idx');
			// $hd('select.jsonfield'+idx).parent().find('span.child_field').remove();
			$hd('select.jsonfield'+idx).html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
		}
		else{
			$hd('select.jsonfield').parent().find('span.child_field').remove();
			$hd('select.childroot, select.subfields').parent().find('span.child_field').remove();
			$hd('select.childroot, select.subfields').html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
		}
		
		if(root=='load_field'){
			
			var rootVal = $hd(this_ref).parent().parent().find('>select.root').val();
			if(selected){
				if(typeof rootVal==="undefined"){
					var root = '';
					$hd('select[name="base[]"]').each(function(){
						if($hd(this).val()=='load_field'){
							return false;
						}
						else{
							root = $hd(this).val();
						}
					});
				}
				else{
					root = rootVal;
				}
			}
			else{
				root = (typeof rootVal==="undefined")?'':rootVal;
			}
			
			$hd(this_ref).parent().find('span.child_field').remove();
			$hd.ajax({
				url:'index.php',
				type:'POST',
				dataType:'json',
				data:{
					"option":"com_vdata", "view":"profiles", "task":"plugintaskajax", "plugin":"custom.loadJsonFields", "<?php echo JSession::getFormToken(); ?>":1, "root":root
				},
				beforeSend: function()	{
					$hd(".loading").show();
				},
				complete: function()	{
					$hd(".loading").hide();
				},
				success: function(res){
					if(res.result=='success'){
						var load_field_opt = '<option value="load_field"><?php echo JText::_('LOAD_FIELDS');?></option>';
						var custom_path = '<option value="custom_path"><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>';
						if(selected){
							var idx = $hd(this_ref).attr('data-idx');
							$hd('select.jsonfield'+idx).parent().find('span.child_field').remove();
							$hd('select.jsonfield'+idx).html(default_opt+custom_path+res.html).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
						}
						else{
							$hd('select.childroot').html(default_opt+load_field_opt+res.html).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
							
							$hd('select.jsonfield').parent().find('span.child_field').remove();
							$hd('select.jsonfield').html(default_opt+custom_path+res.html).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
							
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
		}
		else if($hd("option:selected", this).attr('data-child')=='yes'){
			var select_name = $hd(this).attr('name');
			$hd.ajax({
				url:'index.php',
				type:'POST',
				dataType:'json',
				data:{
					"option":"com_vdata", "view":"profiles", "task":"plugintaskajax", "plugin":"custom.loadJsonFields", "<?php echo JSession::getFormToken(); ?>":1, "root":root
				},
				beforeSend: function()	{
					$hd(".loading").show();
				},
				complete: function()	{
					$hd(".loading").hide();
				},
				success: function(res){
					if(res.result=='success'){
						var load_field_opt = '<option value="load_field"><?php echo JText::_('LOAD_FIELDS');?></option>';
						if(selected){
							var idx = $hd(this_ref).attr('data-idx');
							$hd('select.jsonfield'+idx).parent().find('span.child_field').remove();
							$hd(this_ref).parent().find('span.child_field').remove();
							
							var html = '<span class="child_field"><select name="'+select_name+'" class="root childroot rootsubfields" data-idx="'+idx+'">'+default_opt+load_field_opt+res.html+'</select></span>';
							$hd(this_ref).parent().append(html);
							$hd(this_ref).parent().find('span.child_field>select.root').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
						}
						else{
							$hd(this_ref).parent().find('span.child_field').remove();
							
							var html = '<span class="child_field"><select name="'+select_name+'" class="root rootsubfields base">'+default_opt+load_field_opt+res.html+'</select></span>';
							
							$hd(this_ref).parent().append(html);
							$hd(this_ref).parent().find('span.child_field>select.root').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
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
		}
		
		
	});
	
	$hd(document).on('change.subfields', 'select.subfields', function(event){
		
		var this_ref = this;
		var root =  $hd(this).val();
		var default_opt = '<option value=""><?php echo JText::_('SELECT_FIELD');?></option>';
		$hd(this_ref).parent().find('span.child_field').remove();
		if(root==''){
			return false;
		}
		else if($hd(this_ref).val()=="custom_path"){
			$hd(this_ref).parent().append('<span class="child_field"><input type="text" name="'+$hd(this).attr('name')+'" value=""/></span>');
			return false;
		}
		
		if($hd("option:selected", this).attr('data-child')=='yes'){
			var select_name = $hd(this).attr('name');
			$hd.ajax({
				url:'index.php',
				type:'POST',
				dataType:'json',
				data:{
					"option":"com_vdata", "view":"profiles", "task":"plugintaskajax", "plugin":"custom.loadJsonFields", "<?php echo JSession::getFormToken(); ?>":1, "root":root
				},
				beforeSend: function()	{
					$hd(".loading").show();
				},
				complete: function()	{
					$hd(".loading").hide();
				},
				success: function(res){
					if(res.result=='success'){
						var load_field_opt = '<option value="load_field"><?php echo JText::_('LOAD_FIELDS');?></option>';
						
						var html = '<span class="child_field"><select name="'+select_name+'" class="subfields">'+default_opt+res.html+'</select></span>';
						$hd(this_ref).parent().find('span.child_field').remove();
						$hd(this_ref).parent().append(html);
						$hd(this_ref).parent().find('span.child_field>select.subfields').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
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
		else{
			$hd(this_ref).parent().find('span.child_field').remove();
		}
	});
	
});

function custom_quick(){
	var ioquick = $hd('input[name="quick"]:checked').length;
	if(ioquick == 1){
		$hd('.col_block').html('<tr><td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_FIELDS');?></td><tr>');
	}
	else{
		var root = '';
		$hd('select[name="base[]"] :selected').each(function(index, value){
			if(index==0 && $hd(this).val()=='load_field'){
				root = 'load_field';
				return false;
			}
			if($hd(this).val()=='load_field'){
				return false;
			}
			root = $hd(this).val();
		});
		
		$hd.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_json_columns', 'profileid' : <?php echo $this->profile->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'root':root, 'isRoot':1},
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
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'PROFILE_IMPORT_DETAILS' ); ?></legend>
        <table class="adminform table table-striped adminformlist">
		<tr>
			<td><label class="hasTip" title="<?php echo JText::_('SELECT_FIELD_DESC');?>"><?php echo JText::_('SELECT_FIELD');?></label></td>
			<td>
				<?php $selectedBase = isset($baseFields[0])?$baseFields[0]:'';?>
				<select name="base[]" class="root rootsubfields base">
					<option value=""><?php echo JText::_('SELECT_FIELD');?></option>
					<option value="load_field" <?php if($selectedBase=='load_field'){echo 'selected="selected"';}?>><?php echo JText::_('LOAD_FIELDS');?></option>
					<?php
						$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), '', $selectedBase));
						if(!empty($res[0]->html)){
							echo $res[0]->html;
						}
					?>
				</select>
				<?php if($baseFields && count($baseFields)>1){?>
					<span class="child_field">
						<?php for($i=1;$i<count($baseFields);$i++){?>
							<span class="child_field">
								<select name="base[]" class="root rootsubfields base">
									<option value=""><?php echo JText::_('SELECT_FIELD');?></option>
									<option value="load_field" <?php if($baseFields[$i]=='load_field'){echo 'selected="selected"';}?>><?php echo JText::_('LOAD_FIELDS');?></option>
									<?php 
										$selectedBase = isset($baseFields[$i])?$baseFields[$i]:'';
										$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $baseFields[$i-1], $selectedBase));
										if(!empty($res[0]->html)){
											echo $res[0]->html;
										}
									?>
								</select>
							</span>
						<?php }?>
					</span>
				<?php }?>
			</td>
		</tr>
		<?php 
		if($this->profile->quick && $operation!=3){?>
			<tr>
            	<td width="200"><label class="hasTip" title="<?php echo JText::_('QUICK_IMPORT_DESC');?>"><?php echo JText::_('QUICK_IMPORT'); ?></td>
                <td>
                	<input type="checkbox" value="1" class="inputbox required" id="quick" name="quick" <?php if( !empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==1) ){echo "checked='checked'";} elseif(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && ($this->profile->id==$profile_id)){} elseif(  $this->profile->quick){echo "checked='checked'";} ?> />
                </td>
            </tr>
		<?php }?>
		<?php if($operation==3){
			foreach($keys as $key){
		?>
			<tr>
				<td width="200"><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $key);?>"><?php echo $key; ?></label></td>
				<td>
					<select name="jsonfield[<?php echo $key;?>][]" class="jsonfield subfields">
						<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<?php 
							$fieldSelected = isset($jsonfield->{$key})?$jsonfield->{$key}:false;
							$selectedField = ($fieldSelected)?$fieldSelected[0]:false;
							$loadDirectPath = false;
							
							if(!empty($fieldSelected) && (count($fieldSelected)==2) && ($fieldSelected[0]=='custom_path')){
								$loadDirectPath = true;
							
							?>
							<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
							<?php
							}
							if(!empty($jsonfield)){
								$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $base, $selectedField));
								if(!empty($res[0]->html)){
									echo $res[0]->html;
								}
							}
					?>
					</select>
				</td>
			</tr>
		<?php }}?>
        <?php foreach($this->profile->params->fields as $column=>$params) { ?>
        <?php
			if($params->data=="file") {
		?>
          <tr>
            <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $column);?>"><?php echo $column; ?></label></td>
            <td>
				<select name="jsonfield[<?php echo $column; ?>][]" class="jsonfield subfields">
					<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
					<?php 
						$fieldSelected = isset($jsonfield->{$column})?$jsonfield->{$column}:false;
						$selectedField = ($fieldSelected)?$fieldSelected[0]:false;
						$loadDirectPath = false;
						
						if(!empty($fieldSelected) && (count($fieldSelected)==2) && ($fieldSelected[0]=='custom_path')){
							$loadDirectPath = true;
						
						?>
						<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
						<?php
						}
						if(!empty($jsonfield)){
							$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $base, $selectedField));
							if(!empty($res[0]->html)){
								echo $res[0]->html;
							}
						}
					?>
				</select>
				<?php if($fieldSelected && count($fieldSelected)>1){?>
					<span class="child_field">
						<?php if($loadDirectPath){?>
						<input type="text" name="jsonfield[<?php echo $column; ?>][]" value="<?php echo $fieldSelected[count($fieldSelected)-1];?>"/>
						<?php } else{for($i=1;$i<count($fieldSelected);$i++){?>
							<span class="child_field">
								<select name="jsonfield[<?php echo $column; ?>][]" class="jsonfield subfields">
									<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
									<?php
										$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $fieldSelected[$i-1], $fieldSelected[$i]));
										if(!empty($res[0]->html)){
											echo $res[0]->html;
										}
									?>
								</select>
							</span>
						<?php }}?>
					</span>
				<?php }?>
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
                <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('REF_FIELD_TO_IMPORT', $column);?>"><?php echo $column; ?></label></td>
                <td>
                	<?php foreach($params->reftext as $ref){?>
						<div class = "field_option">
							<label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $ref);?>"><?php echo $ref;?></label>
							<span class="field">
								<select name="jsonfield[<?php echo $column;?>][<?php echo $ref;?>][]" class="jsonfield subfields">
									<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
									<?php 
										$fieldSelected = isset($jsonfield->{$column}->{$ref})?$jsonfield->{$column}->{$ref}:false;
										$selectedField = ($fieldSelected)?$fieldSelected[0]:false;
										$loadDirectPath = false;
										if(!empty($fieldSelected) && (count($fieldSelected)==2) && ($fieldSelected[0]=='custom_path')){
											$loadDirectPath = true;
										
										?>
										<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
										<?php
										}
										if(!empty($jsonfield)){
										$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $base, $selectedField));
										if(!empty($res[0]->html)){
											echo $res[0]->html;
										}
										}
									?>
								</select>
								<?php if($fieldSelected && count($fieldSelected)>1){?>
									<span class="child_field">
										<?php if($loadDirectPath){?>
										<input type="text" name="jsonfield[<?php echo $column;?>][<?php echo $ref;?>][]" value="<?php echo $fieldSelected[count($fieldSelected)-1];?>"/>
										<?php } else{ for($i=1;$i<count($fieldSelected);$i++){?>
											<span class="child_field">
												<select name="jsonfield[<?php echo $column;?>][<?php echo $ref;?>][]" class="jsonfield subfields">
													<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
													<?php
														$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $fieldSelected[$i-1], $fieldSelected[$i]));
														if(!empty($res[0]->html)){
															echo $res[0]->html;
														}
													?>
												</span>
											</span>
										<?php }}?>
									</span>
								<?php }?>
							</span>
						</div>
                    <?php }?>
				</td>
			</tr>
		<?php }elseif($params->data == "asset_reference"){ ?>
		<tr>
            <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('REF_FIELD_TO_IMPORT', $column);?>"><?php echo $column; ?></label></td>
			<td>
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
						<select name="jsonfield[<?php echo $column;?>][component]">
							<option value="<?php echo $params->table?>"><?php echo JText::_($components); ?></option>
						</select>
					</span>
				</div>
				<div class = "field_option"> 
					<label class="hasTip" title="<?php echo JText::_('Table For');?>"><?php echo JText::_('Table For');?></label>
					<span class="field">
						<select name="jsonfield[<?php echo $column;?>][on]">
							<option value="<?php echo $params->on?>"><?php echo JText::_($params->on); ?></option>
						</select>
					</span>
				</div>
				<div class = "field_option"> 
					<label class="hasTip" title="<?php echo JText::_('RULES');?>"><?php echo JText::_('RULES');?></label>
					<span class="field">
						<select name="jsonfield[<?php echo $column;?>][rules][]" class="jsonfield subfields">
							<option value=""><?php echo JText::_("SELECT_RULES"); ?></option>
							<?php 
								$fieldSelected = isset($jsonfield->{$column}->rules)?$jsonfield->{$column}->rules:false;
								$selectedField = ($fieldSelected)?$fieldSelected[0]:false;
								$loadDirectPath = false;
							if(!empty($fieldSelected) && (count($fieldSelected)==2) && ($fieldSelected[0]=='custom_path')){
									$loadDirectPath = true;
							?>
								<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
							<?php
								}
							if(!empty($jsonfield)){
								$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $base, $selectedField));
								if(!empty($res[0]->html)){
									echo $res[0]->html;
								}
							}
							?>
						</select>
						<?php if($fieldSelected && count($fieldSelected)>1){?>
							<span class="child_field">
								<?php if($loadDirectPath){?>
									<input type="text" name="jsonfield[<?php echo $column;?>][rules][]" value="<?php echo $fieldSelected[count($fieldSelected)-1];?>"/>
								<?php } else{ for($i=1;$i<count($fieldSelected);$i++){?>
									<span class="child_field">
										<select name="jsonfield[<?php echo $column;?>][rules][]" class="jsonfield subfields">
											<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
											<?php
												$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $fieldSelected[$i-1], $fieldSelected[$i]));
												if(!empty($res[0]->html)){
													echo $res[0]->html;
												}
											?>
										</select>
									</span>
								<?php }}?>
							</span>
						<?php }?>
					</span>
				</div>
			</td>
		</tr>
		<?php } ?>
        <?php } ?>
		<?php 	$joins = isset($this->profile->params->joins) ? $this->profile->params->joins : null;
				if(!empty($joins)){
				$joins->table1 = isset($joins->table1) ? $joins->table1 : array();
				for($i=0; $i<count($joins->table1); $i++) {
		?>
					
					<?php if( isset($joins->columns[$i]) && !empty($joins->columns[$i]) ){ ?>
					<tr>
						<td>
							<label class="hasTip" title="<?php echo JText::sprintf('CHILD_TABLE_TO_IMPORT', $joins->table2[$i]);?>"><?php echo $joins->table2[$i];?></label>
						</td>
						<td>
							<select name="root[<?php echo $joins->table2[$i];?>][]" class="root childroot rootsubfields" data-idx="<?php echo $i;?>">
								<option value=""><?php echo JText::_('SELECT_FIELD');?></option>
								<?php 
									if(isset($root->{$joins->table2[$i]}) && count($root->{$joins->table2[$i]})>1){
										$child_root = $root->{$joins->table2[$i]}[count($root->{$joins->table2[$i]})-2];
									}
									else{
										$child_root = $base;
									}
									$selected_root= isset($root->{$joins->table2[$i]})?$root->{$joins->table2[$i]}:false;
									$rootSelected = ($selected_root)? $selected_root[0]:false;
								if(!empty($jsonfield)){
								?>
								<option value="load_field" <?php if($rootSelected=='load_field'){echo 'selected="selected"';}?>><?php echo JText::_('LOAD_FIELDS');?></option>
								<?php
									$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $base, $rootSelected));
									if(!empty($res[0]->html)){
										echo $res[0]->html;
									}
								}
								?>
							</select>
							<?php if($selected_root && count($selected_root)>1){?>
								<span class="child_field">
									<?php for($j=1;$j<count($selected_root);$j++){?>
										<span class="child_field">
											<select name="root[<?php echo $joins->table2[$i];?>][]" class="root childroot rootsubfields" data-idx="<?php echo $i;?>">
												<option value=""><?php echo JText::_('SELECT_FIELD');?></option>
												<option value="load_field" <?php if($selected_root[$j]=='load_field'){echo 'selected="selected"';}?>><?php echo JText::_('LOAD_FIELDS');?></option>
												<?php 
													$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $selected_root[$j-1], $selected_root[$j]));
													if(!empty($res[0]->html)){
														echo $res[0]->html;
													}
												?>
											</select>
										</span>
									<?php }?>
								</span>
							<?php }?>
						</td>
					</tr>
					
					<?php foreach($joins->columns[$i] as $key=>$column){?>
						<?php if($column->data == "file"){?>
						<tr>
							<td><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $key);?>"><?php echo $key;?></label></td>
							<td>
								<select name="jsonfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][]"  class="jsonfield<?php echo $i;?> subfields">
									<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
									<?php 
										$fieldSelected = isset($jsonfield->{$joins->table2[$i]}->{$key})?$jsonfield->{$joins->table2[$i]}->{$key}:false;
										$selectedField = ($fieldSelected)?$fieldSelected[0]:false;
										$loadDirectPath = false;
										if(!empty($fieldSelected) && (count($fieldSelected)==2) && ($fieldSelected[0]=='custom_path')){
											$loadDirectPath = true;
									?>
										<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
									<?php
										}
										if(!empty($jsonfield)){
										$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $child_root, $selectedField));
										if(!empty($res[0]->html)){
											echo $res[0]->html;
										}
										}
									?>
								</select>
								<?php if($fieldSelected && count($fieldSelected)>1){?>
									<span class="child_field">
										<?php if($loadDirectPath){?>
										<input type="text" name="jsonfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][]" value="<?php echo $fieldSelected[count($fieldSelected)-1];?>"/>
										<?php } else{ for($j=1;$j<count($fieldSelected);$j++){?>
											<span class="child_field">
												<select name="jsonfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][]"  class="jsonfield<?php echo $i;?> subfields">
													<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
													<?php 
														$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $fieldSelected[$j-1], $fieldSelected[$j]));
														if(!empty($res[0]->html)){
															echo $res[0]->html;
														}
													?>
												</select>
											</span>
										<?php }}?>
									</span>
								<?php }?>
							</td>
						</tr>
						<?php }elseif($column->data == "reference"  && count($column->reftext)){?>
							<tr>
								<td><label class="hasTip" title="<?php echo JText::sprintf('REF_FIELD_TO_IMPORT', $key);?>"><?php echo $key; ?></label></td>
								<td>
									<?php foreach($column->reftext as $ref){?>
									<div class = "field_option">
										<label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $ref);?>"><?php echo $ref;?></label>
										<span class="field">
											<select name="jsonfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][<?php echo $ref;?>][]" class="jsonfield<?php echo $i;?> subfields">
												<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
												<?php 
													$fieldSelected = isset($jsonfield->{$joins->table2[$i]}->{$key}->{$ref})?$jsonfield->{$joins->table2[$i]}->{$key}->{$ref}:false;
													$selectedField = ($fieldSelected)?$fieldSelected[0]:false;
													$loadDirectPath = false;
													if(!empty($fieldSelected) && (count($fieldSelected)==2) && ($fieldSelected[0]=='custom_path')){
														$loadDirectPath = true;
												?>
													<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
												<?php
													}
													if(!empty($jsonfield)){
													$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $child_root, $selectedField));
													if(!empty($res[0]->html)){
														echo $res[0]->html;
													}
													}
												?>
											</select>
											<?php if($fieldSelected && count($fieldSelected)>1){?>
												<span class="child_field">
													<?php if($loadDirectPath){?>
													<input type="text" name="jsonfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][<?php echo $ref;?>][]" value="<?php echo $fieldSelected[count($fieldSelected)-1];?>"/>
													<?php } else{ for($j=1;$j<count($fieldSelected);$j++){?>
														<span class="child_field">
															<select name="jsonfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][<?php echo $ref;?>][]" class="jsonfield<?php echo $i;?> subfields">
																<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
																<?php 
																	$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $fieldSelected[$j-1], $fieldSelected[$j]));
																	if(!empty($res[0]->html)){
																		echo $res[0]->html;
																	}
																?>
															</select>
														</span>
													<?php }}?>
												</span>
											<?php }?>
										</span>
									</div>
									<?php } ?>
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
										<select name="jsonfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][component]">
											<option value="<?php echo $column->table?>"><?php echo JText::_($components); ?></option>
										</select>
										
									</span>
								</div>
								<div class = "field_option"> 
									<label class="hasTip" title="<?php echo JText::_('Table For');?>"><?php echo JText::_('Table For');?></label>
									<span class="field">
										<select name="jsonfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][on]">
											<option value="<?php echo $column->on?>"><?php echo JText::_($column->on); ?></option>
											</select>
										
									</span>
								</div>
								<div class = "field_option"> 
									<label class="hasTip" title="<?php echo JText::_('RULES');?>"><?php echo JText::_('RULES');?></label>
									<span class="field">
										<select name="jsonfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][rules][]" class="jsonfield<?php echo $i;?> subfields">
											<option value=""><?php echo JText::_("SELECT_RULES"); ?></option>
											<?php 
												$fieldSelected = isset($jsonfield->{$joins->table2[$i]}->{$key}->rules)?$jsonfield->{$joins->table2[$i]}->{$key}->rules:false;
												$selectedField = ($fieldSelected)?$fieldSelected[0]:false;
												$loadDirectPath = false;
												if(!empty($fieldSelected) && (count($fieldSelected)==2) && ($fieldSelected[0]=='custom_path')){
													$loadDirectPath = true;
											?>
												<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
											<?php
												}
												if(!empty($jsonfield)){
												$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $child_root, $selectedField));
												if(!empty($res[0]->html)){
													echo $res[0]->html;
												}
												}
											?>
										</select>
										<?php if($fieldSelected && count($fieldSelected)>1){?>
											<span class="child_field">
												<?php if($loadDirectPath){?>
												<input type="text" name="jsonfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][rules][]" value="<?php echo $fieldSelected[count($fieldSelected)-1];?>"/>
												<?php } else{ for($j=1;$j<count($fieldSelected);$j++){?>
													<span class="child_field">
														<select name="jsonfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][rules][]" class="jsonfield<?php echo $i;?> subfields">
															<option value=""><?php echo JText::_("SELECT_RULES"); ?></option>
															<?php 
																$res = $dispatcher->trigger('getJsonField', array($session->get('file_path', ''), $fieldSelected[$j-1], $fieldSelected[$j]));
																if(!empty($res[0]->html)){
																	echo $res[0]->html;
																}
															?>
														</select>
													</span>
												<?php }}?>
											</span>
										<?php }?>
									</span>
								</div>
							</td>
						</tr>
						<?php }?>
					<?php }?>
				<?php }?>
		<?php }}?>
		<?php if($this->profile->quick && $operation!=3){?>
			<table class="adminform table table-striped col_block">
				<tr style="display:none;">
				<td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_FIELDS');?></td>
				<tr>
			</table>
        <?php }?>
		</table>
	</fieldset>
</div>