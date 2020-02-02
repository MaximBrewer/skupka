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
$exportitem = $session->get('exportitem', null);

if( isset($schedule) && ($schedule->profileid==$exportitem->profileid) ){

$base = (!empty($schedule_columns) && property_exists($schedule_columns, 'table'))?$schedule_columns->table:null;
$unkey = (!empty($schedule_columns) && property_exists($schedule_columns, 'unkey'))?$schedule_columns->unkey:null;
$fields = (!empty($schedule_columns) && property_exists($schedule_columns, 'fields'))?json_decode(json_encode($schedule_columns->fields), true):null;
$join_table = (!empty($schedule_columns) && property_exists($schedule_columns, 'join_table'))?$schedule_columns->join_table:null;

}
else{
	$base = $unkey = $fields = $join_table = null;
}

?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	
	if (task == 'close' || task == 'close_st') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} 
	else if(task == 'export_start'){
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
		
		function batchExport(offset){

				var values, index;
				values = $hd("#adminForm").serializeArray();
				addOption('task', 'export_start', values);
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
					console.log('export continue...');
					$hd('div#hd_progress').show();
				},
				complete: function()	{
				},
				error: function(jqXHR, textStatus, errorThrown)	{
					alert(textStatus);
				}
			}).done(function(resp){
				if(resp.result == 'success') {
					console.log(resp.offset);
					$hd('div#hd_progress').show();
					var percentage = Math.round((resp.offset/resp.size)*100);
					var prog = percentage>100 ? 100 : percentage;
					$hd('div#hd_progress').width(prog+"%");
					batchExport(resp.offset);
				}
				else if(resp.result == 'error' && resp.hasOwnProperty("qty")) {
					console.log('export complete...');
					console.log(resp.error);
					$hd('div#hd_progress').hide();
					var jmsgs = [resp.success_msg];
					Joomla.renderMessages({'message': jmsgs });
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
	else {
		
		var form = document.adminForm;
		
		if(form.table.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_TABLE'); ?>");
				return false;
			}
			
		var ioquick = $hd('input[name="quick"]:checked').length;
		if(ioquick == 0) {
			 var blank = false;
			 $hd('select[name^="fields"]').each(function(index){
					if($hd(this).val() == "")	{
                            blank=true;
                            return false;
                    }									 
				});
			 if(blank == true)	{
					alert("<?php echo JText::_('PLZ_SELECT_FIELD_TO_EXPORT_DATA'); ?>");
					return false;		
				}
			}
		Joomla.submitform(task, document.getElementById('adminForm'));
		return false;
					
	}
}

$hd(function()	{
    
	$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	
    var columns = new Array();

	<?php if(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && ($this->profile->quick==1)){echo "custom_quick();";}?>
	
	$hd(document).on('change','input[name="quick"]', function(event)	{
		custom_quick(event);
	});
	
	$hd(document).on('change', 'select.base', function(event)	{
		var that = this;
		var hasIdx = ($hd(that).attr('data-idx')!=undefined && $hd(that).attr('data-idx').length)? true: false;
		var table = $hd(this).val();
		var default_opt = '<option value=""><?php echo JText::_('SELECT_FIELD');?></option>';
		if(table==""){
			if(hasIdx){
				var index = $hd(that).attr('data-idx');
				$hd('select.expo_columns'+index).html(default_opt).trigger('liszt:updated');
			}
			else{
				$hd('select.expo_columns, select#unkey').html(default_opt).trigger('liszt:updated');
			}
			return false;
		}
		$hd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType: "json",
			  data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_column_options', 'table':table, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'remote_db':1},
			  beforeSend: function()	{
					$hd(".loading").show();
			  },
			  complete: function()	{
					$hd(".loading").hide();
			  },
			  success: function(res)	{
				if(res.result == "success")  {
					var options = default_opt+res.html;
					if(hasIdx){
						var index = $hd(that).attr('data-idx');
						$hd('select.expo_columns'+index).html(options).trigger('liszt:updated');
					}
					else{
						$hd('select.expo_columns, select#unkey').html(options).trigger('liszt:updated');
					}
				}
				else
					alert(res.error);
			  },
			  error: function(jqXHR, textStatus, errorThrown)	{
				alert(textStatus);				  
			  }
		});
    });

	$hd(document).on('change','select#ref_table',function(event){
			
		var that = this;
		var table = $hd(this).val();
		var default_opt = '<option value=""><?php echo JText::_('SELECT_FIELD');?></option>';
		if(table==''){
			$hd(that).parent().find('.ref_column').html(default_opt).trigger('liszt:updated');
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
				else
					alert(res.error);
			},
			error: function(jqXHR, textStatus, errorThrown)	{
				alert(textStatus);				  
			}
		});
		
	});
	
	$hd(document).on('change', 'td > select.ref_value', function(event)	{
		
		var that = this;
		if($hd(this).val() == 'reference'){
		var html = '';
		var column = $hd(this).parent().parent().data('column');			
		html += ' <span class="table_block"><span class="label"><?php echo JText::_('LEFT JOIN'); ?></span><select class="field_table" name="fields['+column+'][table]"><option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>';
		html += '<?php
		foreach($this->remotetables as $table) :
			echo '<option value="'.$table.'">'.$table.'</option>';
		endforeach;	?>';
		html += '</select></span>';
		
		var opt = '';
		for(var i=0;i<columns.length;i++){
			opt += '<option value="'+columns[i]+'">'+columns[i]+'</option>';
		}
		
		html += ' <select name="fields['+column+'][column1]" class="leftcolumn">'+opt+'</select> = ';
        html += ' <span class="leftcolumn_block"> <span class="label"><?php echo JText::_('ON'); ?></span><span class="field"><select name="fields['+column+'][column2]" class="rightcolumn"><option value=""><?php echo JText::_('SELECT_COLUMN'); ?></select></span>';    
		html += '<?php echo JText::_('COLUMN_TO_IMPORT'); ?><span class="field"><select name="fields['+column+'][column]" class="importcolumns"><option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option></select></span></span>';
		$hd(this).parent().find('div.ref_field_options').html(html);
		}
		else {
            $hd(that).parent().find('div.ref_field_options').html('');
        }
	});
	
	
	$hd(document).on('change', 'select.field_table', function(event) {
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
                                
								$hd('select.rightcolumn').html(html);
								$hd('select.importcolumns').html(html);
								 
								 /* $hd(that).parent().parent().find('select.rightcolumn').html(html);
								 $hd(that).parent().parent().find('select.importcolumns').html(html);
								 
								 $hd('select.rightcolumn').trigger('liszt:updated');
								 $hd('select.importcolumns').trigger('liszt:updated'); */
                          }
				else
					alert(res.error);
				
			  },
			  error: function(jqXHR, textStatus, errorThrown)	{
				  alert(textStatus);				  
			  }
		});
		
	});
	
});


function custom_quick(event){
	
	event = event||{};
	var element = $hd('input[name="quick"]');
	var ioquick = $hd('input[name="quick"]:checked').length;
	var table = $hd('select[name="table"]').val();
	if(ioquick == 0 && table == ''){
		$hd(element).prop('checked', true);
		alert("<?php echo JText::_('PLZ_SELECT_TABLE'); ?>");
		event.stopPropagation();
		event.preventDefault();
		return false;
	}
	if(table == "" || ioquick == 1)	{
		$hd('.col_block').html('<tr><td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_COLUMNS');?></td><tr>');
	}
	else{
		$hd.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_remote_table_columns','profileid' : <?php if(!empty($this->profile->id)){echo $this->profile->id;}else{echo "0";} ?>, "<?php echo JSession::getFormToken(); ?>":1,'table':table},
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
</script>

<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'PROFILE_EXPORT_DETAILS' ); ?></legend>
        <table class="adminform table table-striped adminformlist">

             <tr>
                <td width="200"><label class="hasTip" title="<?php echo JText::_('EXPORT_TABLE_DESC');?>"><?php echo JText::_('TABLE'); ?></label></td>
                <td>
                    <select name="table" id="table" class="base">
                        <option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>
                        <?php foreach($this->remotetables as $table) { ?>
                        <option value="<?php echo $table; ?>" <?php if( $base==$table ){echo 'selected="selected"';}?>><?php echo $table; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
			<tr>
				<td>
					<label class="hasTip" title="<?php echo JText::_('UNIQUE_KEY_DESC');?>"><?php echo JText::_('UNIQUE_KEY'); ?></label>
				</td>
				<td>
					<select name="unkey" id="unkey">
						<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<?php 
							$selected = isset($unkey)?$unkey:'';
							$res = $dispatcher->trigger('getColumnOptions', array($base, $selected, $db)); 
							if(!empty($res[0]->options)){
								echo $res[0]->options;
							} 
						?>
					</select>
				</td>
			</tr>
			
			<?php if($profileid!=0){?>
			
			<?php if($this->profile->quick){?>
            <tr>
            	<td width="200"><label class="hasTip" title="<?php echo JText::_('QUICK_EXPORT_DESC');?>"><?php echo JText::_('QUICK_EXPORT'); ?></label></td>
                <td>
                	<input type="checkbox" value="1" class="inputbox required" id="quick" name="quick" <?php if( !empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==1) ){echo "checked='checked'";} elseif(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && ($this->profile->id==$schedule->profileid)){} elseif(  $this->profile->quick){echo "checked='checked'";} ?>/>
                </td>
            </tr>	
            <?php }?>
            
        <?php foreach($this->profile->params->fields as $column=>$params) : if($params->data != 'skip' && $params->data != 'defined'){?>
          <tr data-column="<?php echo $column; ?>">
            <td width="200"><label class="hasTip" title="<?php echo JText::sprintf('EXPORT_COLUMN_TO', $column);?>"><?php echo $column; ?></label></td>
            <td>
                <?php if($params->data == 'include'){?>
                    <select name="fields[<?php echo $column; ?>]" class="expo_columns">
						<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
						<?php 
							$selected = isset($fields[$column])?$fields[$column]:'';
							$res = $dispatcher->trigger('getColumnOptions', array($base, $selected, $db)); 
							if(!empty($res[0]->options)){
								echo $res[0]->options;
							} 
						?>
                    </select>
                    <div class="ref_field_options"></div>
                <?php } elseif($params->data == 'reference' && count($params->reftext)){?>
                    <div>
						<?php 
							$ref_table = isset($fields[$column]['table'])?:null;
						?>
                        <select name="fields[<?php echo $column; ?>][table]" id="ref_table">
                            <option value=""><?php echo JText::_('Select Table'); ?></option>
                            <?php foreach($this->remotetables as $table) : ?>
                            <option value="<?php echo $table; ?>" <?php if($ref_table==$table) echo 'selected="selected"';?>><?php echo $table; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php foreach($params->reftext as $ref){?>
                            <div class="field_option">
                                <label class="hasTip" title="<?php echo JText::sprintf('EXPORT_COLUMN_TO', $ref);?>"><?php echo $ref;?></label>
                                <span class="field">
                                    <select name="fields[<?php echo $column; ?>][refcol][<?php echo $ref;?>]" class="ref_column">
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
                <?php }?>
            </td>
          </tr> 
        <?php } endforeach; ?>
			<?php 	$joins = isset($this->profile->params->joins) ? $this->profile->params->joins : null;
					if(!empty($joins)){
					$joins->table1 = isset($joins->table1) ? $joins->table1 : array();
					for($i=0; $i<count($joins->table1); $i++) {
						
			?>
			<?php if( isset($joins->columns[$i]) && !empty($joins->columns[$i]) ){ ?>
            	<tr>
                	<td>
                    	<label class="hasTip" title="<?php echo JText::sprintf('EXPORT_CHILD_TABLE', $joins->table2[$i]);?>"><?php echo $joins->table2[$i];?></label>
                    </td>
					<td>
						<?php $cur_join_table = isset($join_table[$i])?$join_table[$i]:null;?>
						<select name="join_table[]" class="base" data-idx ="<?php echo $i;?>">
                            <option value=""><?php echo JText::_('Select Table'); ?></option>
                            <?php foreach($this->remotetables as $table) : ?>
                            <option value="<?php echo $table; ?>"<?php if($cur_join_table==$table) echo 'selected="selected"';?>><?php echo $table; ?></option>
                            <?php endforeach; ?>
                        </select>
					</td>
				</tr>
				<?php if(isset($joins->columns[$i])){foreach($joins->columns[$i] as $column=>$params){ ?>
					<?php if(($params->data == "include") || ($params->data=='defined')){?>
                    <tr>
						<td><label class="hasTip" title="<?php echo JText::sprintf('EXPORT_COLUMN_TO', $column);?>"><?php echo $column; ?></label></td>
						<td>
							<select name="fields[<?php echo $joins->table2[$i];?>][<?php echo $column; ?>]" class="expo_columns<?php echo $i;?>">
								<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
								<?php 
									$selected = isset($fields[$joins->table2[$i]][$column])?$fields[$joins->table2[$i]][$column]:'';
									$res = $dispatcher->trigger('getColumnOptions', array($cur_join_table, $selected, $db)); 
									if(!empty($res[0]->options)){
										echo $res[0]->options;
									}
								?>
							</select>
							<div class="ref_field_options"></div>
						</td>
					</tr>
					<?php } elseif($params->data=="reference" && count($params->reftext)){?>
					<tr>
						<td>
						<label class="hasTip" title="<?php echo JText::sprintf('EXPORT_COLUMN_TO', $column);?>"><?php echo $column; ?></label>
						</td>
						<td>
							<?php $cur_ref_table = isset($fields[$joins->table2[$i]][$column]['table'])?$fields[$joins->table2[$i]][$column]['table']:null;?>
							<select name="fields[<?php echo $joins->table2[$i];?>][<?php echo $column; ?>][table]" id="ref_table">
								<option value=""><?php echo JText::_('Select Table'); ?></option>
								<?php foreach($this->remotetables as $table) : ?>
								<option value="<?php echo $table; ?>" <?php if($cur_ref_table==$table){echo 'selected="selected"';}?>><?php echo $table; ?></option>
								<?php endforeach; ?>
							</select>
							<?php foreach($params->reftext as $ref){?>
								<div class="field_option">
									<label class="hasTip" title="<?php echo JText::sprintf('EXPORT_COLUMN_TO', $ref);?>"><?php echo $ref;?></label>
									<span class="field">
										<select name="fields[<?php echo $joins->table2[$i];?>][<?php echo $column;?>][refcol][<?php echo $ref;?>]" class="ref_column">
											<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
											<?php 
												$selected = isset($fields[$joins->table2[$i]][$column]['refcol'][$ref])?$fields[$joins->table2[$i]][$column]['refcol'][$ref]:'';
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
					<?php }?>
				<?php }}?>
				<?php }?>
            <?php }}?>
			
			<?php }?>
			<?php if($st && !empty($qry) && ($profileid==0) ) { ?>
				<tr>
					<td colspan="2"><?php echo JText::_('COLUMNS');?></td>
				</tr>
				<?php if(!empty($sfields)){ foreach($sfields as $key=>$sfield){?>
					<tr>
						<td width="200">
						<label class="hasTip" title="<?php echo JText::sprintf('EXPORT_COLUMN_TO', $key)?>"><?php echo $key;?></label></td>
						<td>
							 <select name="fields[<?php echo $key; ?>]" class="expo_columns">
								<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
								<?php 
									$selected = isset($fields[$key])?$fields[$key]:'';
									$res = $dispatcher->trigger('getColumnOptions', array($base, $selected, $db)); 
									if(!empty($res[0]->options)){
										echo $res[0]->options;
									}
								?>
							</select>
						</td>
					</tr>
				<?php }}?>
			<?php } ?>
			<?php if($this->profile->quick){?>
				<table class="adminform table table-striped col_block">
					<tr style="display:none;">
					<td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_COLUMNS');?></td>
					</tr>
				</table>
			<?php } ?>
        </table>
	</fieldset>
</div>