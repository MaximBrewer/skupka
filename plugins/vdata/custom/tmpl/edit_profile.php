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
$db  = JFactory::getDbo();
	$query = $db->getQuery(true);
	$query->select('extension_id, name');
	$query->from($db->quoteName('#__extensions'));
	$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
	$db->setQuery($query);
	$components = $db->loadObjectList();

?>

<script type="text/javascript">
    
var columns = new Array();

function validateit()	{
		
	if($hd('select[name="params[table]"]').val() == "")	{
		alert("<?php echo JText::_('PLZ_SELECT_TABLE'); ?>");
		return false;
	}
	var operation = $hd('select[name="params[operation]"]').val();
	console.log(operation);
	
	if(operation!=1){
		if($hd('select[name="params[unqkey][]"]').val()==null || ($hd('select[name="params[unqkey][]"]').val()=='')){
			alert('<?php echo JText::_('VDATA_PROFILE_SELECT_PRIMARY_KEY');?>');
			return false;
		}
		if($hd('#unqkey').val()!=''){
			var selectedPrimary=$hd('#unqkey').val();
			for(var un=0;un<selectedPrimary.length;un++)
			{
				var arrayPrimary=selectedPrimary[un];
				if($hd('select[name="params[fields]['+arrayPrimary+'][data]"]').val()=='skip')
				{
				alert('<?php echo JText::_('VDATA_PROFILE_SELECT_PRIMARY_KEY_FIELD');?>');
				return false;
				}
			}
		}
		
		/* var msgpr="";
		$hd('tr.primaryKey').find('.field_type').each(function(index)	{
		var prval=$hd('tr.primaryKey').find(this).val();
		if(prval=="skip")
		{
			msgpr='skip';
			return false;
		}
		});
		if(msgpr=='skip')	{
			alert("<?php echo JText::_('VDATA_PROFILE_SELECT_PRIMARY_KEY'); ?>");
			return false;		
		} */
	}
	if(operation==3){
		return true;
	}
	var ioquick = $hd('input[name="quick"]').val();
	if(ioquick==0){
		var blank = true;
		$hd('select.field_type').each(function(index)	{
			if($hd(this).val() != "skip")	{
				blank=false;
				return false;
			}
		});
		
		if(blank==true)	{
			alert("<?php echo JText::_('PLZ_SELECT_FIELD_TO_IMPORT_DATA'); ?>");
			return false;		
		}
	}
	return true;
}

$hd(function()	{
	
	$hd(document).off('change.paramstable').on('change.paramstable', 'select[name="params[table]"]', function(event)	{
		loadcustom_columns();
		var table = $hd('select[name="params[table]"]').val();
		var ioquick = $hd('input[name="quick"]').val();
		if( (ioquick == 1) && (table != '')){
			load_fields();
		}
		if(table == ""){
			$hd('#unqkey').html('<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>');
			$hd('select#unqkey').trigger('liszt:updated');
			$hd('.col_block').html('');
		}
	});
	
	$hd(document).off('change.quick_check').on('change.quick_check', 'input[name="quick_check"]', function(event)	{
		var table = $hd('select[name="params[table]"]').val();
		var ioquick = $hd('input[name="quick_check"]:checked').length;
		if(ioquick == 0){
			$hd('input[name="quick"]').val("0");
		}
		else{
			$hd('input[name="quick"]').val("1");
		}
		loadcustom_columns();
		
		if(ioquick == 1 && table != ''){
			$hd('input[name="quick"]').val("1");
			$hd('.col_block').html('');
			load_fields();
		}
		
		
		if(table == ''){
			$hd('#unqkey').html('<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>');
		}
	});
	
	$hd(document).off('change.field_type').on('change.field_type', 'select.field_type', function(event)	{
		
		var html = '';
		
		var column = $hd(this).parent().parent().data('column');
		var hasId = false;
		if(typeof $hd(this).attr('data-vid')!='undefined' && $hd(this).attr('data-vid').length){
			var hasId = $hd(this).attr('data-vid');
		}
		
		if($hd(this).val()!='reference'){
			var baseTable = $hd('select[name="params[table]"]').val();
			var previousTable = $hd(this).parent().find('select[name="params[fields][id][table]"]').attr('data-previous');
			if(previousTable!=undefined){
				$hd('select[name="params[filters][column][]"]').each(function(selIdx){
				// remove optgroup
				if( (previousTable!='') && (previousTable!=baseTable) ){
					$hd(this).find('optgroup[label="'+previousTable+'"]').remove().end().trigger('liszt:updated');
				}
				});
			}
		}
		
		switch ($hd(this).val())	{
			
			case 'file':
				reqImage = false;
				reqImage = $hd(this).attr('id');
				
				html += '<span class="format_block"><select '+(hasId ? 'name="params[joins][columns]['+hasId+']['+column+'][format]" data-vid="'+hasId+'"' : 'name="params[fields]['+column+'][format]"')+' class="field_format">'
							+'<option value="string"><?php echo JText::_('STRING'); ?></option>';
							if(typeof reqImage!='undefined')
								html += '<option value="image"><?php echo JText::_('IMAGE'); ?></option>';
							html += '<option value="date"><?php echo JText::_('DATE'); ?></option>'
							+'<option value="number"><?php echo JText::_('NUMBER'); ?></option>'
							+'<option value="urlsafe"><?php echo JText::_('URLSAFE'); ?></option>'
							+'<option value="encrypt"><?php echo JText::_('ENCRYPTION');?></option>'
							+'<option value="email"><?php echo JText::_('VDATA_VALIDATE_EMAIL');?></option>'
						+'</select><span class="field_val chosen">'
						+'<select '+(hasId ? 'name="params[joins][columns]['+hasId+']['+column+'][type]" data-vid="'+hasId+'"' : 'name="params[fields]['+column+'][type]"')+' class="field_str">'
						+'<option value=""><?php echo JText::_('AS_IT_IS'); ?></option>'
						+'<option value="striptags"><?php echo JText::_('STRIP_TAGS'); ?></option>'
						+'<option value="chars"><?php echo JText::_('CHAR_LIMIT'); ?></option>'
						+'</select><span class="char_limit"></span>'
						+'</span></span>';
			break;
				
			case 'defined':
				html += '<span class="default_block"><input name="'+(hasId ? 'params[joins][columns]['+hasId+']['+column+'][default]' : 'params[fields]['+column+'][default]')+'" id="fields'+column+'default" value="" class="hdglobal" />';
				/* if(column=='images'){
					html+='<input name="'+(hasId ? 'params[joins][columns]['+hasId+']['+column+'][image_ftp]' : 'params[fields]['+column+'][image_ftp]')+'" id="fields'+column+'image_ftp" value="ftp_host:demo host|ftp_port:21|ftp_user:demo|ftp_pass:demo|ftp_directory:." class="hdglobal" />';
				} */
				html+='</span>';
			break;
			
			case 'reference':
				html += '<span class="table_block"><select class="field_table" data-column="'+column+'"'+(hasId ? ' name="params[joins][columns]['+hasId+']['+column+'][table]" data-vid="'+hasId+'"' : 'name="params[fields]['+column+'][table]"')+'><option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>';
				
				html += '<?php
	
					for($i=0;$i<count($tables);$i++)	:
					
						$table = str_replace($dbprefix, '#__', $tables[$i]);
					
						echo '<option value="'.$table.'">'.$table.'</option>';
					
				endfor;	?>';
				
				html += '</select></span>';
				
                html += ' <span class="hd_label"><?php echo JText::_('ON'); ?></span><span class="leftcolumn_block"><span class="field"><select name="'+(hasId ? 'params[joins][columns]['+hasId+']['+column+'][on]' : 'params[fields]['+column+'][on]')+'" class="field_table_join_on"></select></span></span>';
                                
				html += ' <span class="hd_label"><?php echo JText::_('SELECT_COLUMNS'); ?></span><span class="refcolumn_block"><span class="field"><select name="'+(hasId ? 'params[joins][columns]['+hasId+']['+column+'][reftext][]' : 'params[fields]['+column+'][reftext][]')+'" multiple="multiple" size="5" class="field_table_join_cols"></select></span></span>';
			
			break;
			
			case 'asset_reference':
			html += '<span class="table_block"><select class="field_table component" data-column="'+column+'"'+(hasId ? ' name="params[joins][columns]['+hasId+']['+column+'][table]" data-vid="'+hasId+'"' : 'name="params[fields]['+column+'][table]"')+'><option value=""><?php echo JText::_('SELECT_COMPONENT'); ?></option>';
				html += '<?php
				for($i=0;$i<count($components);$i++)	:
					
						$component = $components[$i]; 
					
						// echo '<option value="'.$component->extension_id.'">'.JText::_($component->name).'</option>';
						echo '<option value="'.$component->name.'">'.JText::_($component->name).'</option>';
					
				endfor;
						?>';
				html += '</select></span>';
				html += ' <span class="hd_label"><?php echo JText::_('Table File'); ?></span><span class="leftcolumn_block"><span class="field"><select name="'+(hasId ? 'params[joins][columns]['+hasId+']['+column+'][on]' : 'params[fields]['+column+'][on]')+'" class="field_table_join_on"></select></span></span>';
			break;
			
			default :
				
			break;
		
		}
		
		$hd(this).parent().find('div.field_options').html(html);
		$hd('select.field_format,select.field_str,select.field_table, select.field_table_join_on, select.field_table_join_cols').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	});
	
	$hd(document).off('change.field_format').on('change.field_format', 'select.field_format', function(event)	{
		var html = '';
		var column = $hd(this).closest('tr').data('column');
		
		var hasId = false;
		if(typeof $hd(this).attr('data-vid')!='undefined' && $hd(this).attr('data-vid').length){
			var hasId = $hd(this).attr('data-vid');
		}
		
		switch ($hd(this).val()){
			case 'date':
				html += '<input type="text" name="'+(hasId ? 'params[joins][columns]['+hasId+']['+column+'][type]' : 'params[fields]['+column+'][type]')+'" value="<?php echo 'Y-m-d H:i:s';?>" />';
			break;
			case 'image':
				html += '<input type="text" name="'+(hasId ? 'params[joins][columns]['+hasId+']['+column+'][location]' : 'params[fields]['+column+'][location]')+'" value="<?php echo 'file/targetdirectory/';?>" />';
			break;
			case 'string':
				html += '<select '+(hasId ? 'name="params[joins][columns]['+hasId+']['+column+'][type]" data-vid="'+hasId+'"' : 'name="params[fields]['+column+'][type]"')+' class="field_str">'
					+'<option value=""><?php echo JText::_('AS_IT_IS'); ?></option>'
					+'<option value="striptags"><?php echo JText::_('STRIP_TAGS'); ?></option>'
					+'<option value="chars"><?php echo JText::_('CHAR_LIMIT'); ?></option>'
					+'</select><span class="char_limit"></span>';
			break;
			case 'encrypt':
				html += '<select name="'+(hasId ? 'params[joins][columns]['+hasId+']['+column+'][type]' : 'params[fields]['+column+'][type]')+'" class="field_enc">'
					+'<option value="plain"><?php echo JText::_('AS_IT_IS'); ?></option>'
					+'<option value="bcrypt"><?php echo JText::_('BCRYPT'); ?></option>'
					+'<option value="sha"><?php echo JText::_('SHA'); ?></option>'
					+'<option value="crypt"><?php echo JText::_('CRYPT'); ?></option>'
					+'<option value="crypt-des"><?php echo JText::_('CRYPT-DES'); ?></option>'
					+'<option value="crypt-md5"><?php echo JText::_('CRYPT-MD5'); ?></option>'
					+'<option value="crypt-blowfish"><?php echo JText::_('CRYPT-BLOWFISH'); ?></option>'
					+'<option value="ssha"><?php echo JText::_('SSHA'); ?></option>'
					+'<option value="smd5"><?php echo JText::_('SMD5'); ?></option>'
					+'<option value="aprmd5"><?php echo JText::_('APRMD5'); ?></option>'
					+'<option value="sha256"><?php echo JText::_('SHA256'); ?></option>'
					+'<option value="md5-hex"><?php echo JText::_('MD5-HEX'); ?></option>'
					+'</select>';
			break;
			default :
				
			break;
		}
		$hd(this).parent().find('span.field_val').html(html);
		$hd('select.field_str,select.field_enc').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	});
	
	$hd(document).off('change.component_value').on('change.component_value', 'select.component_value', function(){
		var ref = this;
		var component = $hd(this).val();
		var default_option = '<option value=""><?php echo JText::_('SELECT_TABLE');?></option>';
		if(component==''){
			$hd(this).parent().parent().find('select.component_table').html(default_option).trigger('liszt:updated');
			$hd('tr#primary').show();
			return false;
		}
		$hd.ajax({
			url:'index.php',
			type:'POST',
			dataType:'json',
			data:{'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_comonent_tables', 'component':component, "<?php echo JSession::getFormToken(); ?>":1},
			 beforeSend: function()	{
				$hd(".loading").show();
			},
			complete: function()	{
				$hd(".loading").hide();
			},
			success: function(res){
				if(res.result == "success") {
					$hd(ref).parent().parent().find('select.component_table').html(res.html).trigger('liszt:updated');
					if($hd('select[name="params[operation]"]').val()==1){
						$hd('tr#primary').hide();
						$hd('tr.primaryKey').find('.field_type').find('option[value="skip"]').attr('Selected', 'Selected');
						$hd('tr.primaryKey').hide();
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
	
	$hd(document).off('change.paramsoperation').on('change.paramsoperation','select[name="params[operation]"]', function(){
		var component = $hd('select[name="params[component][value]"]').val();
		var component_table = $hd('select[name="params[component][table]"]').val();
		if( ($hd(this).val()==1) && (component!="")) {
			$hd('tr#primary').hide();
			$hd('tr.primaryKey').find('.field_type').find('option[value="skip"]').attr('Selected', 'Selected');
			$hd('tr.primaryKey').hide();
			// $hd('select[name="params[unqkey][]"]').find('option:selected').removeAttr("selected").trigger('liszt:updated');
			$hd('select[name="params[unqkey][]"] option:selected').removeAttr("selected").trigger('liszt:updated');
			// $hd('select[name="params[unqkey][]"]').find('option:selected').prop("selected", false).trigger('liszt:updated');
			// $hd('select[name="params[unqkey][]"] option:selected').prop("selected", false).trigger('liszt:updated');
		}
		else{
			$hd('tr#primary').show();
			$hd('tr.primaryKey').show();
		}
		
		//#delete operation
		var iotype = $hd('select[name="iotype"]').val();
		var operation = $hd('select[name="params[operation]"]').val();
		var isOpVisible = $hd('tr#imp_op').is(":visible");
		/* if(iotype==0 && isOpVisible && operation==3){
			$hd('.col_block').html('');
		}
		else{
			loadcustom_columns();
		} */
		if(iotype==0 && isOpVisible){
			loadcustom_columns();
		}
	});
	
	
	$hd(document).off('change.field_str').on('change.field_str', 'select.field_str', function(){
		var hasId = false;
		if(typeof $hd(this).attr('data-vid')!='undefined' && $hd(this).attr('data-vid').length){
			var hasId = $hd(this).attr('data-vid');
		}
		var html = '';
		if($hd(this).val()=='chars'){
			var column = $hd(this).closest('tr').data('column');
			html += '<input type="text" name="'+(hasId ? 'params[joins][columns]['+hasId+']['+column+'][val]' : 'params[fields]['+column+'][val]')+'" value="" />';
		}
		$hd(this).parent().find('span.char_limit').html(html);
		
	});
	
	
	$hd(document).off('change.field_table').on('change.field_table', 'select.field_table', function(event, changeObj)	{
		
		var that = this;
		
		var component = '';
		if($hd(this).hasClass('component')){
			component = $hd(this).val();
		}
		
		var column = $hd(this).parent().parent().parent().parent().data('column');
		var table = $hd(this).val();
		
		var hasId = -1;
		if(typeof $hd(this).attr('data-vid')!='undefined' && $hd(this).attr('data-vid').length){
			var hasId = $hd(this).attr('data-vid');
		}
		
		$hd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType: "json",
			  data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_refer_columns', 'table':table, 'component':component, 'column':column, 'profileid':<?php echo (int)$this->profile->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'index':hasId},
			  beforeSend: function()	{
				$hd(".loading").show();
			  },
			  complete: function()	{
				$hd(".loading").hide();
			  },
			  success: function(res)	{
				
				if(res.result = "success")  {
					if(component=='')
					{
					$hd(that).parent().parent().find('span.refcolumn_block>span.field').html(res.reftext);
                    $hd(that).parent().parent().find('span.leftcolumn_block>span.field').html(res.ontext);
					//reference columns as filter field
						var removePrevious = false;
						var previousTable = '';
						var baseTable = $hd('select[name="params[table]"]').val();
						if($hd(that).attr('data-previous')!==undefined){
							previousTable = $hd(that).attr('data-previous');
							removePrevious = true;
						}
						
						var childTable = $hd(that).closest('td.iocolumns').closest('tr').prev('tr').find('select.righttable').val()
						
						$hd('select[name="params[filters][column][]"]').each(function(selIdx){
							
							// remove optgroup
							if( removePrevious && (previousTable!='') && (previousTable!=baseTable) ){
								///
								if(hasId>-1){
									$hd(this).find('optgroup[label="child:ref:'+previousTable+'"]').remove();
								}
								else{
									$hd(this).find('optgroup[label="ref:'+previousTable+'"]').remove();
								}
							}
							
							var hasGroup = false;
							var optgrouplbl = (hasId>-1)?'child:ref:'+table:'ref:'+table;
							$hd(this).children('optgroup').each(function(optIdx){
								///
								// var targetLabel = (hasId>-1)?'child:ref:'+$hd(this).attr('label'):'ref:'+$hd(this).attr('label');
								var targetLabel = $hd(this).attr('label');
								if(optgrouplbl==targetLabel){
									hasGroup = true;
									return false;
								}
							});
							if(hasGroup==true){
								// $hd(that).removeAttr('data-previous');
								return false;
							}
							
							var optGroupOptions = '';
							$hd.each(res.columns,function(idx,val){
								///
								if(hasId>-1){
									optGroupOptions += '<option value="'+baseTable+'.'+childTable+'.'+table+'.'+val+'">'+val+'</option>';
								}
								else{
									optGroupOptions += '<option value="'+table+'.'+val+'">'+val+'</option>';
								}
								
							});
							
							//add optgroup
							///
							if(hasId>-1){
								var refOptGroup = '<optgroup label="child:ref:'+table+'" data-count="1">'+optGroupOptions+'</optgroup>';
							}
							else{
								var refOptGroup = '<optgroup label="ref:'+table+'" data-count="1">'+optGroupOptions+'</optgroup>';
							}
							
							$hd(this).append(refOptGroup).trigger('liszt:updated');
							// $hd(this).append(optGroupOptions).trigger('liszt:updated');
							
						});
						//add memory element
						$hd(that).attr('data-previous', table);
						
                    }
					else if(component!=''){
					$hd(that).parent().parent().find('span.leftcolumn_block>span.field').html(res.ontext); 
					}
                    
                    $hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
				}
				else
					alert(res.error);
				
			  },
			  error: function(jqXHR, textStatus, errorThrown)	{
				  alert(textStatus);				  
			  }
		});
		
	});
        
    $hd(document).off('click.add_filter').on('click.add_filter', '.add_filter', function(event){
        
		if(columns.length < 1)  {
			alert('<?php echo JText::_('NO_FILTER'); ?>');
			return;
		}
		
		var idx = $hd('table.filter_table>tbody>tr').length;
		
		var html = '<tr><td colspan="2">';
		if(idx==0){
			html += '<span class="filter_block"><select name="params[filters][column][]">';
			html += '<optgroup label="'+$hd('select[name="params[table]"]').val()+'">';		
			for(var i=0;i<columns.length;i++)   {
				html += '<option value="'+columns[i]+'">'+columns[i]+'</option>';
			}
			html += '</optgroup>';
			html += '</select></span>';
		}
		else{
			html += '<span class="filter_block"><select name="params[filters][column][]">';
			html += $hd('table.filter_table>tbody tr:last select[name="params[filters][column][]"]').html();
			html += '</select></span>';
		}
		
		html += '<span class="op_block"><select name="params[filters][cond][]" class="oplist" data-idx="'+idx+'"><option value="=">=</option><option value="<>">!=</option><option value="<">&lt;</option><option value=">">&gt;</option><option value="in">IN</option><option value="notin">NOT IN</option><option value="between">BETWEEN</option><option value="notbetween">NOT BETWEEN</option><option value="like">LIKE</option><option value="notlike">NOT LIKE</option><option value="regexp">REGEXP</option></select></span>';
		
		html += '<span class="value_block"><input id="paramsfiltersvalue" class="inputbox filterval" type="text" size="50" value="" name="params[filters][value][]"></span>';			
		
		html += ' <span class="remove_filter btn btn-danger"><i class="icon-delete"></i> <?php echo JText::_('REMOVE'); ?></span></td></tr>';
		
		$hd('.filter_table').append(html);
            
		$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
		
    });
        
    $hd(document).off('change.oplist').on('change.oplist', '.oplist', function(event){
            
		var length = $hd(this).parent().next().find('input').length;
		var idx = $hd(this).attr('data-idx');
		
		if(($hd(this).val() == "between" || $hd(this).val() == "notbetween") && length == 1) {
			var val = $hd(this).parent().next().find('input:first').val();
			$hd(this).parent().next().html('<input id="paramsfiltersvalue" class="inputbox filterval" type="text" size="50" value="'+val+'" name="params[filters][value1][]"> <input id="paramsfiltersvalue" class="inputbox filterval" type="text" size="50" value="" name="params[filters][value2][]">');
		}
		else if($hd(this).val() != "between" && $hd(this).val() != "notbetween" && length==2){
			var val = $hd(this).parent().next().find('input:first').val();
			$hd(this).parent().next().html('<input id="paramsfiltersvalue" class="inputbox filterval" type="text" size="50" value="'+val+'" name="params[filters][value][]">');
		}
            
    });
        
    $hd(document).off('click.remove_filter').on('click.remove_filter', '.remove_filter', function(event){
        $hd(this).parent().parent().remove();
    });
        
    $hd(document).off('click.add_join').on('click.add_join', '.add_join', function(event){
        
		if($hd('.join_tables>tbody select.lefttable:last option:selected').val()==''){
			alert('<?php echo JText::_('SELECT_LEFT_TABLE');?>');
			return false;
		}
		
		if($hd('.join_tables>tbody select.righttable:last option:selected').val()==''){
			alert('<?php echo JText::_('SELECT_RIGHT_TABLE');?>');
			return false;
		}
		
		
		var length = $hd('.join_tables>tbody>tr').length;
		
		length = length>0?(length/2):0;
		
		var html = '<tr><td colspan="2">';
		
		if(length){
			
			html += '<select name="params[joins][table1][]" class="lefttable">';
			html += '<option value=""><?php echo JText::_('SELECT_TABLE');?></option>';
			html += '<option value="'+$hd("#paramstable option:selected").val()+'">'+$hd("#paramstable option:selected").text()+'</option>';
			var rtbls = [$hd("#paramstable option:selected").val()];
			$hd('.join_tables>tbody select.righttable option:selected').each(function(index, value){
				var tbl = this.value;
				var isNew = true;
				$hd.each(rtbls, function(key, val){
					if(tbl==val){
						isNew=false;
					}
				});
				if(isNew){
					rtbls.push(tbl);
					html += '<option value="'+tbl+'">'+tbl+'</option>';
				}
			});
			html += '</select>';
			
		}
		else{
			
			html += '<select name="params[joins][table1][]" class="lefttable">';
			html += '<option value="'+$hd("#paramstable option:selected").val()+'">'+$hd("#paramstable option:selected").text()+'</option>';
			html += '</select>';
			
		}
		
		html += '<span class="hd_label"><select name="params[joins][join][]" class="join">';
		html += '<option value="join"><?php echo JText::_('JOIN');?></option>';
		html += '<option value="left_join"><?php echo JText::_('LEFT_JOIN');?></option>';
		html += '<option value="right_join"><?php echo JText::_('RIGHT_JOIN');?></option>';
		html += '</select></span> ';
		
		html += '<select name="params[joins][table2][]" class="righttable">';
				
		var tmp_paramstable = $hd($hd('#paramstable').clone().find(':selected').removeAttr("selected").end()).html();
		html += tmp_paramstable;
		
		html += '</select> <label><span class="hd_label"><?php echo JText::_('ON'); ?></label></label>';
		
		if(length){
			html += '<select name="params[joins][column1][]" class="leftcolumn"><option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option></select>';
		}
		else{
			html += '<select name="params[joins][column1][]" class="leftcolumn">'+$hd('select#unqkey').html()+'</select>';
		}
		
		html += ' = <select name="params[joins][column2][]" class="righttcolumn"><option value=""><?php echo JText::_('SELECT_COLUMN'); ?></option></select>';
		
		var iotype = $hd('select[name="iotype"]').val();
		if(iotype==0){
			html += '<div class="field_options">';
			html += '<span class="table_block">';
				html += '<select class="component_value" name="params[joins][component][value][]">';
				html += '<option value=""><?php echo JText::_('SELECT_COMPONENT'); ?></option>';
				html += '<?php for($i=0;$i<count($components);$i++)	{
							$component = $components[$i]; 
							echo '<option value="'.$component->name.'">'.JText::_($component->name).'</option>';
						}?>';
				html += '</select>';
			html += '</span>';
			html += '<span class="hd_label"><?php echo JText::_('Table File'); ?></span>';
			html += '<span class="field">';
				html += '<select name="params[joins][component][table][]" class="component_table">';
					html += '<option value=""><?php echo JText::_('SELECT_COMPONENT_TABLE');?></option>';
				html += '</select>';
			html += '</span>';
			html += '</div>';
		}
		
		html += '</td></tr><tr><td width="200"><span class="hd_label"><?php echo JText::_('COLUMNS_TO_IMPORT_EXPORT'); ?></span></td><td class="iocolumns"></td></tr>';
		
		if($hd('tr.remove_join').length<1)
			$hd('.join_tables').parent().parent().after('<tr class="remove_join"><td colspan="2"><span class="remove_join btn btn-danger"><i class="icon-delete"></i> <?php echo JText::_('REMOVE'); ?></span></td></tr>');
		
		$hd('.join_tables').append(html);
		
        $hd('select.lefttable, select.join, select.righttable, select.leftcolumn, select.righttcolumn, select.exportcolumns,select.component_value, select.component_table').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
    });
        
    $hd(document).off('click.remove_join').on('click.remove_join', 'span.remove_join', function(event){
		$hd('.join_tables>tbody>tr').slice(-2).remove();
    });
        
    $hd(document).off('change.lefttable').on('change.lefttable', 'select.lefttable', function(event)	{
		
		var that = this;
		var table = $hd(this).val();
		if(table==''){
			$hd(that).parent().find('select.leftcolumn').html('<option value=""><?php echo JText::_('SELECT_COLUMN');?></option>').trigger('liszt:updated');
			return false;
		}
		$hd.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_table_columns', 'table':table, 'profileid':<?php echo (int)$this->profile->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
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
					
					$hd(that).parent().find('select.leftcolumn').html(html);
					$hd('select.leftcolumn').trigger('liszt:updated');
				}
				else
					alert(res.error);

			},
			error: function(jqXHR, textStatus, errorThrown)	{
				  alert(textStatus);				  
			}
		});
		
    });

    $hd(document).off('change.righttable').on('change.righttable', 'select.righttable', function(event)	{
		
		var that = this;
		var table = $hd(this).val();
		var iotype = $hd('select[name="iotype"]').val();
		
		var idx = ($hd(this).parent().parent().index()/2);
		
		$hd(that).parent().parent().next().nextAll().remove();
		
		if(table==''){
			$hd(that).parent().find('select.righttcolumn').html('<option value=""><?php echo JText::_('SELECT_COLUMN');?></option>').trigger('liszt:updated');
			$hd(that).parent().parent().next().find('td.iocolumns').html('');
			return false;
		}
		$hd.ajax({
			url: "index.php",
			type: "POST",
			dataType: "json",
			// data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_table_columns', 'table':table, 'profileid':<?php echo (int)$this->profile->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
			data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_table_fields', 'table':table, 'profileid':<?php echo (int)$this->profile->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'index':idx, 'iotype':iotype},
			
			beforeSend: function()	{
					$hd(".loading").show();
			  },
			complete: function()	{
					$hd(".loading").hide();
			  },
			success: function(res)	{

				if(res.result == "success")  {
						
					var columns = '';
					for(var i=0;i<res.columns.length;i++){
						columns += '<option value="'+res.columns[i]+'">'+res.columns[i]+'</option>';
					}
						
					// $hd(that).parent().find('select.righttcolumn').html(html);
					// $hd(that).parent().parent().next().find('select.exportcolumns').html(html);
					$hd(that).parent().find('select.righttcolumn').html(columns);
					$hd(that).parent().parent().next().find('td.iocolumns').html(res.html);
					
					// var tbl_lbl = table+'<input type="hidden" name="params[joins][table1][]" value="'+table+'" />';//
					// $hd(that).parent().parent().next().next().find('span.lefttable').html(tbl_lbl);//
					// $hd(that).parent().parent().next().next().find('select.leftcolumn').html(html);//
						
					$hd('select.righttcolumn').trigger('liszt:updated');
					$hd('table.join_tables'+idx+' select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
					
						var removePrevious = false;
						var previousTable = '';
						var baseTable = $hd('select[name="params[table]"]').val();
						if($hd(that).attr('data-previous')!==undefined){
							previousTable = $hd(that).attr('data-previous');
							removePrevious = true;
						}
						$hd('select[name="params[filters][column][]"]').each(function(selIdx){
							
							// remove optgroup
							if( removePrevious && (previousTable!='') && (previousTable!=baseTable) ){
							$hd(this).find('optgroup[label="child:'+previousTable+'"]').remove();
							}
							
							var hasGroup = false;
							$hd(this).children('optgroup').each(function(optIdx){
								if('child:'+table==$hd(this).attr('label')){
									hasGroup = true;
									return false;
								}
							});
							if(hasGroup==true){
								$hd(that).removeAttr('data-previous');
								return false;
							}
							var optGroupOptions = '';
							$hd.each(res.columns,function(idx,val){
								optGroupOptions += '<option value="'+baseTable+'.'+table+'.'+val+'">'+val+'</option>';
							});
							
							//add optgroup
							var childOptGroup = '<optgroup label="child:'+table+'" data-count="0">'+optGroupOptions+'</optgroup>';
							$hd(this).append(childOptGroup).trigger('liszt:updated');
						});
						//add memory element
						$hd(that).attr('data-previous', table);
					
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

function triggercustom()
{
	
	loadcustom_columns();
	
}

function editprofilecustom()
{
	
	loadcustom_columns();
	
}

function load_fields()
{
	var table = $hd('select[name="params[table]"]').val();
    var ioquick = $hd('input[name="quick_check"]:checked').length;
	var iotype = $hd('select[name="iotype"]').val();
		$hd.ajax({
			url:"index.php",
			type: "POST",
			dataType: "json",
			data:{'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_fields', 'table':table, 'iotype':iotype, 'profileid':<?php echo (int)$this->profile->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
			beforeSend: function(){
				$hd(".loading").show();
			},
			complete: function(){
				$hd(".loading").hide();
			},
			success: function(res){
				if(res.result == "success")  {
					var htm = '<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>';
					columns = res.fields;
					for(var j=0;j<(res.fields.length);j++){
						htm += '<option value="'+res.fields[j]+'"';
						if($hd.inArray(res.fields[j], res.primary)!==-1){
							htm += 'selected="selected"';
						}
						htm += ' >'+res.fields[j]+'</option>';
					}
					$hd("select#unqkey").html(htm);
					$hd("select#unqkey").trigger('liszt:updated');//trigger("chosen:updated")
					
				}
				else
					alert(res.error);
			},
			error: function(jqXHR, textStatus, errorThrown){
				alert(textStatus);
			}
		});
}

function loadcustom_columns()	{
    console.log('reached');
    var table = $hd('select[name="params[table]"]').val();
    
	var ioquick = 0;
    var quick = $hd('input[name="quick_check"]').is(":visible");
	if(quick){
		ioquick = $hd('input[name="quick"]').val();
	}
	
	try{
		var params = JSON.parse('<?php echo addslashes(json_encode($this->profile->params));?>');
	}
	catch(e){
		console.log("error: "+e);
		return false;
	}
	var remote = <?php echo $remote = JFactory::getApplication()->input->getInt('remote', 0);?>;
	
	var iotype = $hd('select[name="iotype"]').val();
    //#delete operation
	var operation = $hd('select[name="params[operation]"]').val();
	var isOpVisible = $hd('tr#imp_op').is(":visible");
	
	if(table == "" || ioquick == 1)	{

	}
	else{
		
		$hd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType: "json",
			  data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':'custom.load_columns', 'table':table, 'iotype':iotype, 'profileid':<?php echo (int)$this->profile->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'remote':remote, 'params':params, 'operation':operation},
			  beforeSend: function()	{
				$hd(".loading").show();
			  },
			  complete: function()	{
				$hd(".loading").hide();
			  },
			  success: function(res)	{
				
				if(res.result == "success")  {
					console.log(res.fi);
                    columns = res.columns;	
					$hd("#unqkey").html(res.unqkey_html);
					$hd("#unqkey").trigger('liszt:updated');//trigger("chosen:updated")
					//#delete operation
					// if(iotype==1 || (iotype==0 && isOpVisible && operation!=3)){
						$hd('.col_block').html(res.html);
					// }
					$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
                }
				else{
					alert(res.error);
					$hd('.col_block').html(res.error);
				}
			  },
			  error: function(jqXHR, textStatus, errorThrown)	{
				  alert(textStatus);				  
			  }
		});
		
	}
	
}


</script>
<table class="adminform table table-striped">
	<tr>
    <td width="200"><?php //$inst = JTable::getInstance('import', 'Table');print_r($inst);?><label class="hasTip required" title="<?php echo JText::_('TABLE_DESC');?>"><?php echo JText::_('TABLE'); ?></label></td>
    <td><select name="params[table]" id="paramstable">
    	<option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>
	<?php
	
		for($i=0;$i<count($tables);$i++)	{
		
		$table = str_replace($dbprefix, '#__', $tables[$i]);
		
	?>
        
        <option value="<?php echo $table; ?>" <?php if($table===$this->profile->params->table) echo 'selected="selected"'; ?>><?php echo $table; ?></option>
	
	<?php	}	?>
	</select>
	<?php if(JFactory::getApplication()->input->getInt('iotype')==0){?>
		<span class="field_options">
			<span class="table_block">
				<select class="component_value" name="params[component][value]">
					<option value=""><?php echo JText::_('SELECT_COMPONENT'); ?></option>
					<?php for($i=0;$i<count($components);$i++)	{
						$component = $components[$i]; 
					
						// echo '<option value="'.$component->extension_id.'"'.( (isset($this->profile->params->component->value)) && ($this->profile->params->component->value==$component->extension_id ) ? ' selected="selected"': '').'>'.JText::_($component->name).'</option>';
						echo '<option value="'.$component->name.'"'.( (isset($this->profile->params->component->value)) && ($this->profile->params->component->value==$component->name ) ? ' selected="selected"': '').'>'.JText::_($component->name).'</option>';
						
					} ?>
				</select>
			</span>
			<span class="hd_label"><?php echo JText::_('Table File'); ?></span>
			<span class="field">
				<select name="params[component][table]" class="component_table">
					<option value=""><?php echo JText::_('SELECT_TABLE'); ?></option>
					<?php if(!empty($component_tables)) echo $component_tables;?>
				</select>
			</span>
		</span>
	<?php }?>
    </td>
  </tr>
  <tr id="primary" <?php if( isset($this->profile->params->operation) && ($this->profile->params->operation==1) && isset($this->profile->params->component->value) && !empty($this->profile->params->component->value) ){echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('UNIQUE_KEY_DESC');?>"><?php echo JText::_('UNIQUE_KEY'); ?></label></td>
	<td>
		<select name="params[unqkey][]" id="unqkey" multiple="multiple">
			<option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
				<?php 
					$primaryKeys = array();
					if(isset($this->profile->params->unqkey)){
						$primaryKeys = (isset($this->profile->params->unqkey) && is_array($this->profile->params->unqkey))?$this->profile->params->unqkey:array($this->profile->params->unqkey);
					}
					
				foreach($columns as $column){?>
			<option value="<?php echo $column;?>" <?php if(property_exists($this->profile->params,'unqkey') && in_array($column, $primaryKeys)){echo 'selected="selected"';}?>><?php echo $column;?></option>
				<?php }?>
		</select>
	</td>
  </tr>
  
   <tr id="imp_op" <?php if(JFactory::getApplication()->input->getInt('iotype')==1){echo 'style="display:none"';}?>>
  	<td><label class="hasTip required" title="<?php echo JText::_('OPERATION_DESC');?>"><?php echo JText::_('OPERATION'); ?></label></td>
    <td>
		<select name="params[operation]" id="paramsoperation" <?php if(JFactory::getApplication()->input->getInt('iotype')==1){echo 'disabled="disabled"';}?>>
        <option value="1" <?php if(property_exists($this->profile->params,'operation') && $this->profile->params && $this->profile->params->operation == 1){echo 'selected="selected"';}?>><?php echo JText::_('INSERT');?></option>
        <option value="0" <?php if(property_exists($this->profile->params,'operation') && $this->profile->params->operation == 0){echo 'selected="selected"';}?>><?php echo JText::_('UPDATE');?></option>
		<option value="2" <?php if(property_exists($this->profile->params,'operation') && $this->profile->params->operation == 2){echo 'selected="selected"';}?>><?php echo JText::_('SYNCHRONIZE');?></option>
		<option value="3" <?php if(property_exists($this->profile->params,'operation') && $this->profile->params->operation==3){echo 'selected="selected"';}?>><?php echo JText::_('VDATA_PROFILE_DELETE_OPERATION');?></option>
        </select>
    </td>
  </tr>
  
  <?php  
	$before = array();
	$after = array();
	$before['Content'] = array( "content:onContentBeforeSave" );
	$after['Content'] = array("content:onContentAfterSave","content:onContentChangeState" );//, "content:onCategoryChangeState"
	$before['User'] = array( "user:onUserBeforeSave" );
	$after['User'] = array( "user:onUserAfterSave" );
	$before['Finder'] = array( "finder:onFinderBeforeSave" );
	$after['Finder'] = array( "finder:onFinderAfterSave", "finder:onFinderChangeState" );//, "finder:onFinderCategoryChangeState"
	
  ?>
	<tr <?php if(JFactory::getApplication()->input->getInt('iotype')==1){echo 'style="display:none"';}?> id="ievent">
		<td><label class="hasTip required" title="<?php echo JText::_('EVENTS_DESC');?>"><?php echo JText::_('EVENTS');?></label></td>
		<td>
			<span class="hd_label" id="before"><?php echo JText::_('BEFORE');?></span>
			<select name="params[events][before][]" id="events" multiple="multiple">
				<optgroup label="">
					<option value="" disabled="disabled"><?php echo JText::_('SELECT_EVENTS'); ?></option>
				</optgroup>
				<?php foreach($before as $key=>$ops){?>
					<optgroup label="<?php echo $key;?>">
						<?php foreach($ops as $op){?>
							<option value="<?php echo $op;?>" <?php if( property_exists($this->profile->params,'events') && property_exists($this->profile->params->events, 'before') && in_array($op,$this->profile->params->events->before) ){echo 'selected="selected"';}?>><?php $option = explode(':',$op);echo $option[1];?></option>
						<?php }?>
					</optgroup>
				<?php }?>
			</select>
			
			<span class="hd_label" id="after"><?php echo JText::_('AFTER');?></span>
			<select name="params[events][after][]" id="events" multiple="multiple">
				<optgroup label="">
					<option value="" disabled="disabled"><?php echo JText::_('SELECT_EVENTS'); ?></option>
				</optgroup>
				<?php foreach($after as $key=>$ops){?>
					<optgroup label="<?php echo $key;?>">
						<?php foreach($ops as $op){?>
							<option value="<?php echo $op;?>" <?php if( property_exists($this->profile->params,'events') && property_exists($this->profile->params->events, 'after') && in_array($op,$this->profile->params->events->after) ){echo 'selected="selected"';}?>><?php $option = explode(':',$op);echo $option[1];?></option>
						<?php }?>
					</optgroup>
				<?php }?>
			</select>
			
		</td>
	</tr>

	<table class="adminform table table-striped col_block">
		<tr style="display:none;">
		<td colspan="2"><?php echo JText::_('SELECT_CUSTOMIZED_COLUMNS');?></td>
		</tr>
	</table>
</table>