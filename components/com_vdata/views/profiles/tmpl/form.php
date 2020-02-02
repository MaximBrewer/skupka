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
JHtml::_('behavior.tooltip');
$user = JFactory::getUser();

$canViewDashboard = $user->authorise('core.access.dashboard', 'com_vdata');
$canViewProfiles = $user->authorise('core.access.profiles', 'com_vdata');
$canViewImport = $user->authorise('core.access.import', 'com_vdata');
$canViewExport = $user->authorise('core.access.export', 'com_vdata');
$canViewCronFeed = $user->authorise('core.access.cron', 'com_vdata');

$custom_plg = '';
$custom_plg_name = '';

$lang = JFactory::getLanguage();
$plugins = array(); 
for($i=0;$i<count($this->plugins);$i++){
	$extension = 'plg_' . $this->plugins[$i]->folder . '_' . $this->plugins[$i]->element;
	$lang->load($extension . '.sys', JPATH_SITE.'/plugins/vdata/'.$this->plugins[$i]->element, null, false, false);
	$m_cache = new JRegistry($this->plugins[0]->manifest_cache);
	$plugins[] = '{"value":"'.$this->plugins[$i]->extension_id.':'.$this->plugins[$i]->element.'", "label":"'.JText::_($this->plugins[$i]->name).'", "desc":"'.JText::_($m_cache->get('description', '')).'"}';
	
	if($this->plugins[$i]->element=='custom'){
		$custom_plg = $this->plugins[$i]->extension_id.':'. $this->plugins[$i]->element;
		$custom_plg_name = $this->plugins[$i]->element;
	}
	
}

$isCustomProfile = (JFactory::getApplication()->input->get('profile', '')=='custom')?true:false;

?>

<script type="text/javascript">
	
	Joomla.submitbutton = function(task) {
		if (task == 'cancel') {
			
			Joomla.submitform(task, document.getElementById('adminForm'));
		} 
		else if(task == 'schedule'){
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else {
			
			var form = document.adminForm;
		
			if(form.title.value == "")	{
				alert("<?php echo JText::_('PLZ_ENTER_TITLE'); ?>");
				return false;
			}
			
			if((form.pluginid.value == 0) || (form.pluginid.value == ''))	{
				alert("<?php echo JText::_('PLZ_SELECT_PLUGIN'); ?>");
				return false;
			}
			
			if(typeof(validateit) == 'function')	{
                
				if(!validateit())
					return false;
				
			}
						
			Joomla.submitform(task, document.getElementById('adminForm'));
			
		}
	}
	
$hd(function()	{
	$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
	edit_profile();
        
        $hd('select[name="iotype"]').off('change').on('change', function(event)	{
            
			if($hd(this).val() == 0){
				//$hd('select#events').closest('tr').show();
				$hd('#ievent').show();
				// $hd('select#paramsoperation').show();
				$hd('select#paramsoperation').prop("disabled", false);
				$hd('select#paramsoperation').trigger('liszt:updated');
				$hd('tr#imp_op').show();
			}
			else{
				//$hd('select#events').closest('tr').hide();
				$hd('#ievent').hide();
				// $hd('select#paramsoperation').hide();
				$hd('select#paramsoperation').prop("disabled", true);
				$hd('select#paramsoperation').trigger('liszt:updated');
				$hd('tr#imp_op').hide();
			}
			
			var plugin = $hd('#pluginid').val();
            // var plugin = $hd('select[name="pluginid"]').val();
            
			var quick = $hd('input[name="quick_check"]').is(":visible");
			if(!quick){
				$hd('.plugin_block').html('<tr><td colspan="2"><?php echo JText::_('NO_PLUGIN_SELECTED');?></td><tr>');
				$hd('input[name="quick_check"]').closest('tr').show();
				$hd('input[name="quick"]').val("1");
				// $hd('input[name="quick_check"]').attr('checked', false);
				$hd('input[name="quick_check"]').prop('checked', true);
				$hd('input[name="plg"], input[name="pluginid"]').val('');
				return false;
			}
			
            plugin = plugin.split(':');
            
            var triggerit = 'editprofile'+plugin[1];
                
            if(typeof(window[triggerit]) == 'function')	{

                window[triggerit]();
                return;

            }
            
            edit_profile();
	
        });
	
	var gvar = {source:["@vdLocal:", "@vdSql:", "@vdPhp:"], minLength:0};
	$hd(document).on('focus', '.hdglobal', function(){
         $hd(this).autocomplete(gvar).focus(function(){
			$hd(this).autocomplete("search",$hd(this).val());
		});
    });
	
	//filter combo box
	var options = {
		source: ["@vdSql:NOW()", "@vdSql:CURDATE()", "@vdSql:DATE_SUB(NOW(), INTERVAL 10 DAY)", "@vdPhp:date(\"Y-m-d H:i:s\")"],
		minLength: 0
	};
	$hd(document).on('focus', '.filterval', function(){
         $hd(this).autocomplete(options).focus(function(){
			$hd(this).autocomplete("search",$hd(this).val());
		});
    });
	
	var import_profile_cache = [<?php 
							$top_import_profiles = array();
							if($this->topProfiles){
								if(property_exists($this->topProfiles, 'import')){
									foreach($this->topProfiles->import as $i=>$ip){
										$top_import_profiles[] = json_encode($ip);
									}
								}
								
							} 
							echo implode(',', array_merge($plugins, $top_import_profiles));
							?>];
	
	var export_profile_cache = [<?php 
							$top_export_profiles = array();
							if($this->topProfiles){
								if(property_exists($this->topProfiles, 'export')){
									foreach($this->topProfiles->export as $i=>$op){
										$top_export_profiles[] = json_encode($op);
									}
								}
								
							} 
							echo implode(',', array_merge($plugins, $top_export_profiles));
							?>];
	
	// plugin combo box
	var cache = [<?php 
				echo implode(',', $plugins);
			?>];
	var maintain_session = 1;
	$hd('#plg').autocomplete({
		source:function( request, response ) {
			var term = request.term;
			if(term == ''){
				if($hd('select[name="iotype"]').val()==0){
					response(import_profile_cache);
				}
				if($hd('select[name="iotype"]').val()==1){
					response(export_profile_cache);
				}
				 // response(cache);
				 return;								 
			}
			/* else if(term in cache){
				response(cache[term]);
				return;
			} */
			else{
				var iotype = $hd('select[name="iotype"]').val();
				$hd.ajax({
				  url: "index.php",
				  type: "POST",
				  dataType: "json",
				  data: {
					"option":"com_vdata",
					"view":"profiles",
					"task":"getRemoteProfiles",
					"iotype": iotype,
					"term": request.term,
					"<?php echo JSession::getFormToken(); ?>":1,
					"session_setting": maintain_session
				  },
				  success: function( res ) {
					if(res.result == "success"){
						maintain_session = 0;
						//cache[ term ] = res.html;
						response( res.html );
					}
					else
						alert(res.error);
				  }
				});
			}
			
		},
		open:function(event, ui){
			
		},
		close:function(event, ui){
			
		},
		minLength: 0,
		select: function(event, ui){

			var isRemote = ui.item.hasOwnProperty('params') ? true : false;
			
			if(isRemote){
				var selected_keyword = ui.item.value;
				$hd('#plg').val(ui.item.label);
				$hd('#pluginid').val(ui.item.pluginid);
				var plugin = $hd('#pluginid').val();
				plugin = plugin.split(':');
				var iotype = $hd('select[name="iotype"]').val();
				var params = JSON.parse(ui.item.params);
				
				$hd('input[name="quick_check"]').closest('tr').hide();
				$hd('input[name="quick"]').val("0");
				// $hd('input[name="quick_check"]').attr('checked', false);
				$hd('input[name="quick_check"]').prop('checked', false);
				event.preventDefault();
				$hd.ajax({
					url: "index.php",
					type: "POST",
					data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':plugin[1]+'.onEditProfile', 'iotype':iotype, 'ioquick':0, 'profileid':0, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'remote':1, 'params':params},
					beforeSend: function()	{
						$hd(".loading").show();
					},
					complete: function()	{
						$hd(".loading").hide();
					},
					success: function(res)	{
						$hd.ajax({
							url: "index.php",
							type: "POST",
							data: {'option':'com_vdata', 'view':'vdata', 'task':'update_selected_keyword', 'type':"profile", 'keyword':selected_keyword, "<?php echo JSession::getFormToken(); ?>":1}	
						});
						$hd('.plugin_block').html(res);
						var triggerit = 'trigger'+plugin[1];
						if(typeof(window[triggerit]) == 'function')	{
							if(!window[triggerit]())
								return false;
						}
					},
					error: function(jqXHR, textStatus, errorThrown)	{
						  alert(textStatus);				  
					}
				});
				
			}
			else{
				$hd('#plg').val(ui.item.label);
				$hd('#pluginid').val(ui.item.value);
				// $hd('input[name="quick"]').val("1");
				// $hd('input[name="quick_check"]').attr('checked', true);
				// $hd('input[name="quick_check"]').prop('checked', true);
				$hd('input[name="quick_check"]').closest('tr').show();
				
				edit_profile();
			}
			
			$hd('#plg').blur();
			return false;
		}
	}).focus(function(){
		$hd(this).autocomplete("search",$hd(this).val());
	}).data("ui-autocomplete")
	._renderItem = function(ul, item) {
        var listItem = $hd("<li class='hasTip' title='"+item.desc+"'></li>")
            .data("item.autocomplete", item)
            .append("<a>" + item.label + "</a>")
            .appendTo(ul);
		var JTooltips = new Tips(jQuery('.hasTip').get(), {"maxTitleChars": 50,"fixed": false});
        return listItem;
    };
	
	$hd(document).on('change', 'input[name="quick_check"]', function(event)	{
		var ioquick = $hd('input[name="quick_check"]:checked').length;
		if(ioquick == 0){
			$hd('input[name="quick"]').val("0");
		}
		else{
			$hd('input[name="quick"]').val("1");
		}
	});
	
});

function edit_profile()	{
	var plugin = $hd('#pluginid').val();
    // var plugin = $hd('select[name="pluginid"]').val();
    var iotype = $hd('select[name="iotype"]').val();
    var ioquick = 0;
    var quick = $hd('input[name="quick_check"]').is(":visible");
	if(quick){
		ioquick = $hd('input[name="quick"]').val();
	}
	
	<?php if($this->remote==1){?>
		var remote = 1;
		var params = JSON.parse('<?php echo json_encode($this->item->params);?>');
	<?php }else{?>
		var remote = 0;
		var params = '';
	<?php }?>
	
	if(plugin == 0)	{
		// $hd('.plugin_block').html('');
	}
	else{
            
        plugin = plugin.split(':');
		
		$hd.ajax({
			  url: "index.php",
			  type: "POST",
			  data: {'option':'com_vdata', 'view':'profiles', 'task':'plugintaskajax', 'plugin':plugin[1]+'.onEditProfile', 'iotype':iotype, 'ioquick':ioquick, 'profileid':<?php echo (int)$this->item->id; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'remote':remote, 'params':params},
			  beforeSend: function()	{
				$hd(".loading").show();
			  },
			  complete: function()	{
				$hd(".loading").hide();
			  },
			  success: function(res)	{
				
				$hd('.plugin_block').html(res);
				jQuery('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
				
				jQuery('.hasTip').each(function() {
				var title = jQuery(this).attr('title');
				if (title) {
					var parts = title.split('::', 2);
					jQuery(this).data('tip:title', parts[0]);
					jQuery(this).data('tip:text', parts[1]);
				}
				});
				var JTooltips = new Tips(jQuery('.hasTip').get(), {"maxTitleChars": 50,"fixed": false});
				
				var triggerit = 'trigger'+plugin[1];
				if(typeof(window[triggerit]) == 'function')	{
                
					if(!window[triggerit]())
						return false;
					
				}
				
			  },
			  error: function(jqXHR, textStatus, errorThrown)	{
				  alert(textStatus);				  
			  }
		});
		
	}
	
}
	
</script>
<div id="vdatapanel">
<div id="toolbar" class="toolbar-btn dash-btn">
<span class="hx_title"><img src="<?php echo JURI::root();?>/media/com_vdata/images/vdata-logo.png" alt="vData"> <span class="hx_main_title"><?php echo JText::_( 'VDATA_TITLE' );?><br><span class="hx_subtitle"><?php echo JText::_( 'VDATA_SUBTITLE_DESC' );?></span></span></span>
<div class="hx_dash_button">
<?php if($canViewDashboard){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=vdata" 
    class="btn btn-small btn-widget">
       <span class="icon-new icon-dashboard"></span><?php echo JText::_( 'VDATA_DASHBOARD' );?>
</a>
<?php }?>
<?php if($canViewImport){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=import" 
    class="btn btn-small btn-import">
       <span class="icon-import icon-white"></span><?php echo JText::_( 'VDATA_IMPORT' );?>
</a>
<?php }?>
<?php if($canViewExport){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=export" 
    class="btn btn-small btn-export">
       <span class="icon-out-2 icon-white"></span><?php echo JText::_( 'VDATA_EXPORT' );?>
</a>
<?php }?>
<?php if($canViewProfiles){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=profiles" 
    class="btn btn-small btn-profile active">
       <span class="icon-stack icon-white"></span><?php echo JText::_( 'VDATA_PROFILE' );?>
</a>
<?php }?>
<?php if($canViewCronFeed){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=schedules" 
    class="btn btn-small btn-feed active">
       <span class="icon-feed icon-white"></span><?php echo JText::_( 'VDATA_SCHEDULES' );?>
</a>
<?php }?>
</div>
</div>
<div id="toolbar" class="toolbar-btn">
<?php 
	$isNew	= ($this->item->id < 1);
	
?>
<span class="hx_title"><?php echo JText::_( 'DETAILS' ); ?></span>
<?php if(!$isNew){?>
<button class="btn btn-small cron-feed" onclick="Joomla.submitbutton('schedule');"><span class="icon-feed"></span><?php echo JText::_('CRON_FEED');?></button>
<button class="btn btn-small save-copy" onclick="Joomla.submitbutton('save2copy');"><span class="icon-save-copy"></span><?php echo JText::_('SAVE2COPY');?></button>
<?php }?>
<button class="btn btn-small cancel" onclick="Joomla.submitbutton('cancel');"><span class="icon-cancel"></span><?php echo JText::_('CANCEL');?></button>
<button class="btn btn-small save-close" onclick="Joomla.submitbutton('save');"><span class="icon-apply"></span><?php echo JText::_('SAVE_N_CLOSE');?></button>
<button class="btn btn-small btn-success" onclick="Joomla.submitbutton('apply');"><span class="icon-apply"></span><?php echo JText::_('SAVE');?></button>
	
	
	
</div>


<form action="index.php?option=com_vdata&view=profiles" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col100">
	<fieldset class="adminform">
<table class="adminform table table-striped">
<tr>
    <td><label class="required hasTip" title="<?php echo JText::_('PROFILE_TITLE_DESC');?>"><?php echo JText::_('TITLE'); ?></label></td>
    <td><input type="text" name="title" id="title" class="inputbox required" value="<?php echo $this->item->title; ?>" size="50" /></td>
  </tr>
<tr>
    <td><label class="required hasTip" title="<?php echo JText::_('TYPE_DESC');?>"><?php echo JText::_('TYPE'); ?></label></td>
    <td>
        <select name="iotype" id="iotype" class="inputbox required">
            <option value="0"><?php echo JText::_('IMPORT'); ?></option>
            <option value="1"<?php if($this->item->iotype==1) echo 'selected="selected"'; elseif( (JFactory::getApplication()->input->getInt('iotype', 0)==1) || (JFactory::getApplication()->input->getInt('iotype', 0)==2) ) echo 'selected="selected"';?>><?php echo JText::_('EXPORT'); ?></option>
        </select>
    </td>
</tr>

<tr>
    <td><label class="required hasTip" title="<?php echo JText::_('QUICK_IO_DESC');?>"><?php echo JText::_('QUICK_IO'); ?></label></td>
    <td>
	<input type="checkbox" name="quick_check" id="quick_check" class="inputbox required" value="1" <?php if(!isset($this->item->id) && $isCustomProfile){}elseif( ($this->item->quick==1) || ($this->item->quick===null) ) echo 'checked="checked"'; ?> />
	<input type="hidden" name="quick" value="<?php if(!isset($this->item->id) && $isCustomProfile){echo "0";}elseif($this->item->quick!==null){echo $this->item->quick;}else{echo "1";}?>" />
	</td>
</tr>
<tr>
	<td width="200"><label class="required hasTip" title="<?php echo JText::_('PLUGIN_COMBO_DESC');?>"><?php echo JText::_('PLUGIN_COMBO'); ?></label></td>
	<td>
		<input type="text" placeholder="<?php echo JText::_('PROFILE_PLUGIN_COMBO_PLACEHOLDER');?>" name="plg" id="plg" value="<?php if(!empty($this->item->plugin)) {echo JText::_($this->item->plugin);}elseif(!isset($this->item->id) && $isCustomProfile && !empty($custom_plg_name)){echo $custom_plg_name;}?>" />
		<input type="hidden" name="pluginid" id="pluginid" value="<?php if( !empty($this->item->pluginid) && !empty($this->item->element) ) {echo $this->item->pluginid.':'. $this->item->element;}elseif(!isset($this->item->id) && $isCustomProfile && !empty($custom_plg)){echo $custom_plg;}?>" />
	</td>
</tr> 

	<table class="adminform table table-striped plugin_block">
		<tr><td colspan="2"><?php echo JText::_('NO_PLUGIN_SELECTED');?></td><tr>
	</table>
</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="profiles" />
<input type="hidden" name="iptype" value="<?php echo JFactory::getApplication()->input->getInt('iotype', -1);?>" />
<input type="hidden" name="type" value="<?php echo JFactory::getApplication()->input->getInt('type', -1);?>" />
</form>

</div>