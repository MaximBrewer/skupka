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
$baseTag = (!empty($schedule_columns) && property_exists($schedule_columns, 'base'))?$schedule_columns->base:null;
$images = (!empty($schedule_columns) && property_exists($schedule_columns, 'images'))?$schedule_columns->images:null;
if(!empty($baseTag) && count($baseTag)>1){
	$base = $baseTag[count($baseTag)-2];
}
else{
	$base = $baseTag;
}
$xmlfield = (!empty($schedule_columns) && property_exists($schedule_columns, 'fields'))?$schedule_columns->fields:null;
$root = ((!empty($schedule_columns) && property_exists($schedule_columns, 'child_tags')))?$schedule_columns->child_tags:null;

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
	<?php if(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && ($this->profile->quick==1)){echo "custom_quick();";}?>
	
	$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	
	$hd('input[name="quick"]').off("change").on('change', function(event)	{
		custom_quick();
	});
	
	
	$hd(document).off('change.root').on('change.root', 'select.root',function(event){
		
		var this_ref = this;
		var root = $hd(this).val();
		var default_opt = '<option value=""><?php echo JText::_('SELECT_FIELD');?></option>';
		var selected = ($hd(this_ref).attr('data-idx')!=undefined && $hd(this_ref).attr('data-idx').length)? true: false;
		
		if(root==''){
				
				if(selected){
					var index = $hd(this_ref).attr('data-idx');
					$hd('select.child_field'+index).parent().find('span.child_tag').remove();
					$hd('select.child_field'+index).html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
				}
				else{
					$hd('select.xml_field').parent().find('span.child_tag').remove();
					$hd('select.xml_field').html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
					
					$hd('select.child_col').parent().find('span.child_tag').remove();
					$hd('select.child_table, select.child_col').html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
					
					$hd(this_ref).parent().find('span.child_tag').remove();
				}
			
			return false;
		}
		
		if(!selected){
			if(root!='load_child'){
				
				var load_child_opt = '<option value="load_child"><?php echo JText::_('LOAD_CHILD');?></option>';
				var select_name = $hd(this).attr('name');
				if( ($hd("option:selected", this).attr('data-child')=='yes') ){
					$hd.ajax({
						url:'index.php',
						type: 'POST',
						dataType: 'json',
						data: {"option":"com_vdata", "view":"profiles", "task":"plugintaskajax", "plugin":"custom.loadXmlTags", "<?php echo JSession::getFormToken(); ?>":1, "root":root},
						beforeSend: function()	{
							$hd(".loading").show();
						},
						complete: function()	{
							$hd(".loading").hide();
						},
						success: function(res)	{
							if(res.result == "success"){
								
								var html = '<span class="child_tag"><select name="'+select_name+'" class="root">'+default_opt+load_child_opt+res.html+'</select></span>';
								$hd(this_ref).parent().find('span.child_tag').remove();
								$hd(this_ref).parent().append(html);	
								$hd(this_ref).parent().find('select.root').children().remove('optgroup[label="Attribute"]');
								$hd(this_ref).parent().find('span.child_tag>select.root').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
								
								$hd('select.xml_field').parent().find('span.child_tag').remove();
								$hd('select.xml_field').html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
								
								$hd('select.child_col').parent().find('span.child_tag').remove();
								$hd('select.child_table, select.child_col').html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
								
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
					$hd(this_ref).parent().find('span.child_tag').remove();
					
					$hd('select.xml_field').parent().find('span.child_tag').remove();
					$hd('select.xml_field').html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
					
					$hd('select.child_col').parent().find('span.child_tag').remove();
					$hd('select.child_table, select.child_col').html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
				}
				return false;
			}
			else{
				root = $hd(this_ref).parent().parent().find('select.root option:selected').val();
				$hd(this_ref).parent().find('span.child_tag').remove();
			}
			
		}
		
		$hd.ajax({
			url:'index.php',
			type: 'POST',
			dataType: 'json',
			data: {"option":"com_vdata", "view":"profiles", "task":"plugintaskajax", "plugin":"custom.loadXmlTags", "<?php echo JSession::getFormToken(); ?>":1, "root":root, 'isRoot':1},
			beforeSend: function()	{
				$hd(".loading").show();
			},
			complete: function()	{
				$hd(".loading").hide();
			},
			success: function(res)	{
				if(res.result == "success"){
					if(selected){
						var index = $hd(this_ref).attr('data-idx');
						
						$hd('select.child_field'+index).parent().find('span.child_tag').remove();
						$hd('select.child_field'+index).html(default_opt+res.html).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
						
					}
					else{
						
						$hd('select.xml_field').parent().find('span.child_tag').remove();
						$hd('select.xml_field').html(default_opt+res.html).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
						
						var parent_opt = default_opt+'<optgroup label="Parent"><option value="'+$hd(this_ref).parent().parent().find('> select.root option:selected').val()+'">'+$hd(this_ref).parent().parent().find('> select.root option:selected').text()+'</option></optgroup>'+res.html;
						
						$hd('select.child_table').html(parent_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
						$hd("select.child_table option[value='custom_path']").remove();
						
						$hd('select.child_table').children().remove('optgroup[label="Attribute"]');
						$hd('select.child_table').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
						
						$hd('select.child_col').parent().find('span.child_tag').remove();
						$hd('select.child_col').html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
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
	
	$hd(document).off('change.get_childs').on('change.get_childs', 'select.get_childs', function(event){
		var this_ref = this;
		if($hd(this_ref).val()==""){
			$hd(this_ref).parent().find('span.child_tag').remove();
			return false;
		}
		else if($hd(this_ref).val()=="custom_path"){
			$hd(this_ref).parent().find('span.child_tag').remove();
			$hd(this_ref).parent().append('<span class="child_tag"><input type="text" name="'+$hd(this).attr('name')+'" value=""/></span>');
			return false;
		}
		var default_opt = '<option value=""><?php echo JText::_('SELECT_FIELD');?></option>';
		var selected = $hd("option:selected", this).closest('optgroup').attr('label');
		if(selected!=undefined && selected=='Childs'){
			
			if( ($hd("option:selected", this).attr('data-child')=='yes') || ($hd("option:selected", this).attr('data-attr')=='yes') ){
				
				var select_name = $hd(this).attr('name');
				var root = $hd("option:selected", this).val();
				
				$hd.ajax({
					url:'index.php',
					type: 'POST',
					dataType: 'json',
					data: {"option":"com_vdata", "view":"profiles", "task":"plugintaskajax", "plugin":"custom.loadXmlTags", "<?php echo JSession::getFormToken(); ?>":1, "root":root},
					beforeSend: function()	{
						$hd(".loading").show();
					},
					complete: function()	{
						$hd(".loading").hide();
					},
					success: function(res)	{
						if(res.result == "success"){
							
							var html = '<span class="child_tag"><select name="'+select_name+'" class="get_childs">'+default_opt+res.html+'</select></span>';
							$hd(this_ref).parent().find('span.child_tag').remove();
							$hd(this_ref).parent().append(html);
							$hd(this_ref).parent().find('span.child_tag>select.get_childs').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
							
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
				$hd(this_ref).parent().find('span.child_tag').remove();
			}
			
		}
		else{
			$hd(this_ref).parent().find('span.child_tag').remove();
		}
		
	});
	
	$hd(document).off('change.child_table').on('change.child_table', 'select.child_table', function(){
		var this_ref = this;
		var root = $hd("option:selected", this).val();
		var default_opt = '<option value=""><?php echo JText::_('SELECT_FIELD');?></option>';
		
		if($hd(this_ref).val()==""){
			$hd(this_ref).parent().find('span.child_tag').remove();
			var index = $hd(this_ref).attr('data-idx');
			$hd('select.child_field'+index).parent().find('span.child_tag').remove();
			$hd('select.child_field'+index).html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
		}
		
		var label = $hd("option:selected", this).closest('optgroup').attr('label');
		if(label=='Parent'){
			var index = $hd(this_ref).attr('data-idx');
			$hd.ajax({
				url:'index.php',
				type: 'POST',
				dataType: 'json',
				data: {"option":"com_vdata", "view":"profiles", "task":"plugintaskajax", "plugin":"custom.loadXmlTags", "<?php echo JSession::getFormToken(); ?>":1, "root":root, "isRoot":1},
				beforeSend: function()	{
					$hd(".loading").show();
				},
				complete: function()	{
					$hd(".loading").hide();
				},
				success: function(res)	{
					if(res.result == "success"){
						$hd(this_ref).parent().find('span.child_tag').remove();
						$hd('select.child_field'+index).parent().find('span.child_tag').remove();
						$hd('select.child_field'+index).html(default_opt+res.html).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
					}
					else{
						alert(res.error);
					}
				},
				error: function(jqXHR, textStatus, errorThrown)	{
					alert(textStatus);				  
				}
			});
			return false;
		}
		
		if( ($hd("option:selected", this).attr('data-child')=='yes') || ($hd("option:selected", this).attr('data-attr')=='yes') ){
		var select_name = $hd(this).attr('name');
		
			$hd.ajax({
				
				url:"index.php",
				type:"POST",
				dataType:"json",
				data:{"option":"com_vdata", "view":"profiles", "task":"plugintaskajax", "plugin":"custom.loadChildXmlTags", "<?php echo JSession::getFormToken(); ?>":1, "root":root},
				beforeSend: function()	{
					$hd(".loading").show();
				},
				complete: function()	{
					$hd(".loading").hide();
				},
				success: function(res)	{
					if(res.result == "success"){
						
						if(res.roots!=""){
							var index = $hd(this_ref).attr('data-idx');
							var rootTags = default_opt+res.roots;
							var html = '<span class="child_tag"><select name="'+select_name+'" class="root" data-idx="'+index+'">'+rootTags+'</select></span>';
							
							$hd(this_ref).parent().find('span.child_tag').remove();
							$hd(this_ref).parent().append(html);
							$hd(this_ref).parent().find('span.child_tag>select.root').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
							
							
							$hd('select.child_field'+index).parent().find('span.child_tag').remove();
							$hd('select.child_field'+index).html(default_opt).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
						}
						else if(res.childs!=""){
							var index = $hd(this_ref).attr('data-idx');
							var childTags = default_opt+res.childs;
							$hd(this_ref).parent().find('span.child_tag').remove();
							
							$hd('select.child_field'+index).parent().find('span.child_tag').remove();
							$hd('select.child_field'+index).html(childTags).chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}).trigger('liszt:updated');
							
							
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
	
	
});

function custom_quick(){
	var ioquick = $hd('input[name="quick"]:checked').length;
	if(ioquick == 1){
		$hd('.col_block').html('<tr><td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_FIELDS');?></td><tr>');
	}
	else{
		var root = '';
		$hd('select[name="base[]"] :selected').each(function(index, value){
			if($hd(this).val()=='load_child'){
				return false;
			}
			root = $hd(this).val();
		});
		
		$hd.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_xml_columns', 'profileid' : <?php echo $this->profile->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'root':root, 'isRoot':1},
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
			<td><label class="hasTip" title="<?php echo JText::_('SELECT_ROOT_TAG_DESC');?>"><?php echo JText::_('SELECT_ROOT_TAG');?></label></td>
			<td>
				<select name="base[]" class="root">
					<option value=""><?php echo JText::_('SELECT_TAG');?></option>
					<?php ?>
					<?php foreach($xmlfields as $key=>$tag){?>
						<option value="<?php echo $tag;?>" data-child="yes" <?php if(isset($base) && ($base==$tag)){echo 'selected="selected"';}?>><?php echo $key;?></option>
					<?php }?>
				</select>
				<?php if($baseTag && count($baseTag)>1){?>
					<span class="child_tag">
						<?php for($i=1;$i<count($baseTag);$i++){?>
							<span class="child_tag">
								<select name="base[]" class="root">
									<option value=""><?php echo JText::_('SELECT_TAG');?></option>
									<?php if($baseTag[$i]=='load_child'){?>
										<option value="load_child" selected="selected"><?php echo JText::_('LOAD_CHILD');?></option>
									<?php }?>
									<?php 
										$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $baseTag[$i-1], $baseTag[$i], false)); 
										if(!empty($res[0]->childs)){
											echo $res[0]->childs;
										}
									?>
								</select>
							</span>
						<?php }?>
					</span>
				<?php }?>
			</td>
		</tr>
		<?php if($this->profile->quick && $operation!=3){?>
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
					<select name="xmlfield[<?php echo $key;?>][]" class="xml_field load_attr get_childs">
						<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<?php 
							$selected_tags = isset($xmlfield->{$key})?$xmlfield->{$key}:false;
							$selected = ($selected_tags)? $selected_tags[0]:false;
							$loadDirectPath = false;
						?>
						<?php 
							if(!empty($selected_tags) && (count($selected_tags)==2) && ($selected_tags[0]=='custom_path')){
								$loadDirectPath = true;
							
						?>
							<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
						<?php 
							}
							$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $base, $selected)); 
							if(!empty($res[0]->childs)){
								echo $res[0]->childs;
							}
						 ?>
					</select>
				</td>
			</tr>
		<?php }}?>
		
        <?php foreach($this->profile->params->fields as $column=>$params) { ?>
        <?php if($params->data == "file") { ?>
		  <tr>
			<td width="200"><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $column);?>"><?php echo $column; ?></label></td>
			<td>
				<select name="xmlfield[<?php echo $column; ?>][]" class="xml_field load_attr get_childs">
				<?php 
					$selected_tags = isset($xmlfield->{$column})?$xmlfield->{$column}:false;
					$selected = ($selected_tags)? $selected_tags[0]:false;
					$loadDirectPath = false;
				?>
				<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
				<?php 
					if(!empty($selected_tags) && (count($selected_tags)==2) && ($selected_tags[0]=='custom_path')){
						$loadDirectPath = true;
					
				?>
					<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
				<?php 
					}
					$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $base, $selected)); 
					if(!empty($res[0]->childs)){
						echo $res[0]->childs;
					}
				 ?>
				</select>
				<?php if($selected_tags && count($selected_tags)>1){?>
					<span class="child_tag">
						<?php if($loadDirectPath){?>
						<input type="text" name="xmlfield[<?php echo $column; ?>][]" value="<?php echo $selected_tags[count($selected_tags)-1];?>"/>
						<?php }else{for($i=1;$i<count($selected_tags);$i++){?>
						<span class="child_tag">
							<select name="xmlfield[<?php echo $column; ?>][]" class="xml_field load_attr get_childs">
								<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
								<?php 
									$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $selected_tags[$i-1], $selected_tags[$i])); 
									if(!empty($res[0]->childs)){
										echo $res[0]->childs;
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
								<select name="xmlfield[<?php echo $column;?>][<?php echo $ref;?>][]" class="xml_field load_attr get_childs">
									<?php  
										$selected_tags = isset($xmlfield->$column->$ref)?$xmlfield->$column->$ref:false;
										$selected = ($selected_tags)? $selected_tags[0]:false;
										$loadDirectPath = false;
									?>
									<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
									<?php 
										if(!empty($selected_tags) && (count($selected_tags)==2) && ($selected_tags[0]=='custom_path')){
											$loadDirectPath = true;
										
									?>
										<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
									<?php 
										}
										$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $base, $selected)); 
										if(!empty($res[0]->childs)){
											echo $res[0]->childs;
										}
									?>
								</select>
								<?php if($selected_tags && count($selected_tags)>1){?>
									<span class="child_tag">
										<?php if($loadDirectPath){?>
										<input type="text" name="xmlfield[<?php echo $column;?>][<?php echo $ref;?>][]" value="<?php echo $selected_tags[count($selected_tags)-1];?>"/>
										<?php 
										}else{for($i=1;$i<count($selected_tags);$i++){?>
										<span class="child_tag">
											<select name="xmlfield[<?php echo $column;?>][<?php echo $ref;?>][]" class="xml_field load_attr get_childs">
												<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
												<?php 
													$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $selected_tags[$i-1], $selected_tags[$i])); 
													
													if(!empty($res[0]->childs)){
														echo $res[0]->childs;
													}
												?>
											</select>
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
			
			if (strpos(strtolower($components), "com_") ===false){
			$components = "com_".strtolower($components); 	
			}	
			?>
					
				<div class = "field_option">
					<label class="hasTip" title="<?php echo JText::_('Component Name');?>"><?php echo JText::_('Component Name');?></label>
					<span class="field">
						<select name="xmlfield[<?php echo $column;?>][component]">
							<option value="<?php echo $params->table?>"><?php echo JText::_($components); ?></option>
							</select>
						
					</span>
				</div>
				<div class = "field_option"> 
					<label class="hasTip" title="<?php echo JText::_('Table For');?>"><?php echo JText::_('Table For');?></label>
					<span class="field">
						<select name="xmlfield[<?php echo $column;?>][on]">
							<option value="<?php echo $params->on?>"><?php echo JText::_($params->on); ?></option>
							</select>
						
					</span>
				</div>
				<div class = "field_option"> 
					<label class="hasTip" title="<?php echo JText::_('RULES');?>"><?php echo JText::_('RULES');?></label>
					<span class="field">
						<select name="xmlfield[<?php echo $column;?>][rules][]" class="xml_field load_attr get_childs">
							<?php 
								$selected_tags = isset($xmlfield->$column->rules)?$xmlfield->$column->rules:false;
								$selected = ($selected_tags)? $selected_tags[0]:false;
								$loadDirectPath = false;
							?>
							<option value=""><?php echo JText::_("SELECT_RULES"); ?></option>
							<?php 
								if(!empty($selected_tags) && (count($selected_tags)==2) && ($selected_tags[0]=='custom_path')){
									$loadDirectPath = true;
								
							?>
								<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
							<?php 
								}
								$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $base, $selected)); 
								if(!empty($res[0]->childs)){
									echo $res[0]->childs;
								}
							?>
						</select>
						<?php if($selected_tags && count($selected_tags)>1){?>
							<span class="child_tag">
								<?php if($loadDirectPath){?>
									<input type="text" name="xmlfield[<?php echo $column;?>][rules][]" value="<?php echo $selected_tags[count($selected_tags)-1];?>"/>
								<?php 
								}else{for($i=1;$i<count($selected_tags);$i++){?>
								<span class="child_tag">
									<select name="xmlfield[<?php echo $column;?>][rules][]" class="xml_field load_attr get_childs">
										<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
										<?php 
											$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $selected_tags[$i-1], $selected_tags[$i])); 
											if(!empty($res[0]->childs)){
												echo $res[0]->childs;
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
		
		<?php 	
		$joins = isset($this->profile->params->joins) ? $this->profile->params->joins : null;
		if(!empty($joins)){
		$joins->table1 = isset($joins->table1) ? $joins->table1 : array();
			for($i=0; $i<count($joins->table1); $i++) {
		?>
		
		<?php if( isset($joins->columns[$i]) && !empty($joins->columns[$i]) ){ ?>
			<tr>
				<td>
					<label class="hasTip" title="<?php echo JText::sprintf('CHILD_ROOT_TO_IMPORT', $joins->table2[$i]);?>"><?php echo $joins->table2[$i];?></label>
				</td>
				<td>
					<select name="root[<?php echo $joins->table2[$i];?>][]" class="root<?php echo $i;?> child_table" data-idx="<?php echo $i;?>">
						<?php
							$selected_root= isset($root->{$joins->table2[$i]})?$root->{$joins->table2[$i]}:false;
							$direct = ($selected_root && count($selected_root)>1)?false:true;
							$selected = ($selected_root)? $selected_root[0]:false;
						 ?>
						<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<?php $parent_tag = array_search($base, $xmlfields);
							if($parent_tag!=FALSE){ ?>
							<optgroup label="Parent">
							<option value="<?php echo $base;?>" <?php if($selected && ($selected==$base)){echo ' selected="selected"';}?>><?php echo $parent_tag;?></option>
							</optgroup>
						<?php }?>
						
						 <?php
							$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $base, $selected, false)); 
							if(!empty($res[0]->childs)){
								echo $res[0]->childs;
							}
						?>
					</select>
					<?php if($selected_root && count($selected_root)>1){?>
						<span class="child_tag">
							<select name="root[<?php echo $joins->table2[$i];?>][]" class="root<?php echo $i;?> child_table" data-idx="<?php echo $i;?>">
								<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
								<?php 
									$res = $dispatcher->trigger('getChildXmlTags', array($session->get('file_path', ''), $selected_root[0], $selected_root[1])); 
									if(!empty($res[0]->roots)){
										echo $res[0]->roots;
									}
									
								?>
							</select>
						</span>
					<?php }?>
				</td>
				
			</tr>
				<?php foreach($joins->columns[$i] as $key=>$column){?>
				<?php if($column->data == "file"){?>
				<tr>
					<td><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $key);?>"><?php echo $key;?></label></td>
					<td>
					<select name="xmlfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][]" class="child_field<?php echo $i;?> child_col get_childs">
						<?php 
							$selected_child_tags = isset($xmlfield->{$joins->table2[$i]}->{$key})?$xmlfield->{$joins->table2[$i]}->{$key}:false;
							$selected = ($selected_child_tags)? $selected_child_tags[0]:false;
							$loadDirectPath = false;
						?>
						<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<?php 
							if(!empty($selected_child_tags) && (count($selected_child_tags)==2) && ($selected_child_tags[0]=='custom_path')){
								$loadDirectPath = true;
							
						?>
						<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
						<?php
							}
							if($direct){
								$res = $dispatcher->trigger('getChildXmlTags', array($session->get('file_path', ''), $selected_root[0], $selected)); 
								if(!empty($res[0]->childs)){
									echo $res[0]->childs;
								}
							}
							else{
								$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $selected_root[1], $selected));
								if(!empty($res[0]->childs)){
									echo $res[0]->childs;
								}
							}
						?>
					</select>
						<?php if($selected_child_tags && count($selected_child_tags)>1){?>
							<span class="child_tag">
							<?php if($loadDirectPath){?>
									<input type="text" name="xmlfield[<?php echo $joins->table2[$j];?>][<?php echo $key;?>][]" value="<?php echo $selected_child_tags[count($selected_child_tags)-1];?>"/>
							<?php 
							}else{ for($j=1;$j<count($selected_child_tags);$j++){?>
								<span class="child_tag">
								<select name="xmlfield[<?php echo $joins->table2[$j];?>][<?php echo $key;?>][]" class="child_field<?php echo $j;?> child_col get_childs">
									<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
										<?php 
											$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $selected_child_tags[$j-1], $selected_child_tags[$j])); 
											if(!empty($res[0]->childs)){
												echo $res[0]->childs;
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
					<td><label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $key);?>"><?php echo $key;?></label></td>
					<td>
						<?php foreach($column->reftext as $ref){ ?>
						<div class = "field_option">
						<label class="hasTip" title="<?php echo JText::sprintf('FIELD_TO_IMPORT', $ref);?>"><?php echo $ref;?></label>
						<span class="field">
							<select name="xmlfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][<?php echo $ref;?>][]" class="child_field<?php echo $i;?> child_col get_childs">
								<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
								<?php 
									$selected_child_tags = isset($xmlfield->{$joins->table2[$i]}->{$key}->{$ref})?$xmlfield->{$joins->table2[$i]}->{$key}->{$ref}:false;
									$selected = ($selected_child_tags)? $selected_child_tags[0]:false;
									$loadDirectPath = false;
								?>
								<?php 
									if(!empty($selected_child_tags) && (count($selected_child_tags)==2) && ($selected_child_tags[0]=='custom_path')){
										$loadDirectPath = true;
									
								?>
								<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
								<?php
									}
									if($direct){
										$res = $dispatcher->trigger('getChildXmlTags', array($session->get('file_path', ''), $selected_root[0], $selected)); 
										if(!empty($res[0]->childs)){
											echo $res[0]->childs;
										}
									}
									else{
										$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $selected_root[1], $selected));
										if(!empty($res[0]->childs)){
											echo $res[0]->childs;
										}
									}
								?>
							</select>
							<?php if($selected_child_tags && count($selected_child_tags)>1){?>
								<span class="child_tag">
								<?php if($loadDirectPath){?>
									<input type="text" name="xmlfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][<?php echo $ref;?>][]" value="<?php echo $selected_child_tags[count($selected_child_tags)-1];?>"/>
								<?php 
									}else{ for($j=1;$j<count($selected_child_tags);$j++){?>
									<span class="child_tag">
									<select name="xmlfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][<?php echo $ref;?>][]" class="child_field<?php echo $j;?> child_col get_childs">
										<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
											<?php 
												$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $selected_child_tags[$j-1], $selected_child_tags[$j])); 
												if(!empty($res[0]->childs)){
													echo $res[0]->childs;
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
						$query->where($db->quoteName('name') . ' = ' . $db->quote($column->table));
						$db->setQuery($query);
						$components = $db->loadResult();  
						
						if (strpos(strtolower($components), "com_") ===false){
						$components = "com_".strtolower($components); 	
						}
						?>
								
							<div class = "field_option">
								<label class="hasTip" title="<?php echo JText::_('Component Name');?>"><?php echo JText::_('Component Name');?></label>
								<span class="field">
									<select name="xmlfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][component]">
										<option value="<?php echo $column->table?>"><?php echo JText::_($components); ?></option>
									</select>
								</span>
							</div>
							<div class = "field_option"> 
								<label class="hasTip" title="<?php echo JText::_('Table For');?>"><?php echo JText::_('Table For');?></label>
								<span class="field">
									<select name="xmlfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][on]">
										<option value="<?php echo $column->on?>"><?php echo JText::_($column->on); ?></option>
										</select>
									
								</span>
							</div>
							<div class = "field_option"> 
								<label class="hasTip" title="<?php echo JText::_('RULES');?>"><?php echo JText::_('RULES');?></label>
								<span class="field">
									<select name="xmlfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][rules][]" class="child_field<?php echo $i;?> child_col get_childs">
										<option value=""><?php echo JText::_("SELECT_RULES"); ?></option>
										<?php 
											$selected_child_tags = isset($xmlfield->{$joins->table2[$i]}->{$key}->rules)?$xmlfield->{$joins->table2[$i]}->{$key}->rules:false;
											$selected = ($selected_child_tags)? $selected_child_tags[0]:false;
											$loadDirectPath = false;
										?>
										<?php 
											if(!empty($selected_child_tags) && (count($selected_child_tags)==2) && ($selected_child_tags[0]=='custom_path')){
												$loadDirectPath = true;
											
										?>
										<option value="custom_path" <?php if($loadDirectPath){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_LOAD_NODE_FROM_PATH');?></option>
										<?php
											}
											if($direct){
												$res = $dispatcher->trigger('getChildXmlTags', array($session->get('file_path', ''), $selected_root[0], $selected)); 
												if(!empty($res[0]->childs)){
													echo $res[0]->childs;
												}
											}
											else{
												$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $selected_root[1], $selected));
												if(!empty($res[0]->childs)){
													echo $res[0]->childs;
												}
											}
										?>
									</select>
									<?php if($selected_child_tags && count($selected_child_tags)>1){?>
										<span class="child_tag">
										<?php if($loadDirectPath){?>
										<input type="text" name="xmlfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][rules][]" value="<?php echo $selected_child_tags[count($selected_child_tags)-1];?>"/>
										<?php 
											}else{ for($j=1;$j<count($selected_child_tags);$j++){
										?>
											<span class="child_tag">
											<select name="xmlfield[<?php echo $joins->table2[$i];?>][<?php echo $key;?>][rules][]" class="child_field<?php echo $j;?> child_col get_childs">
												<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
													<?php 
														$res = $dispatcher->trigger('getXmlTags', array($session->get('file_path', ''), $selected_child_tags[$j-1], $selected_child_tags[$j])); 
														if(!empty($res[0]->childs)){
															echo $res[0]->childs;
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
				<tr>
				<td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_FIELDS');?></td>
				</tr>
			</table>
        <?php }?>
		</table>
	</fieldset>
</div>