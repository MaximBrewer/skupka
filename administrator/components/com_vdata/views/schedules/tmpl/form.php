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
$input = JFactory::getApplication()->input;
$user = JFactory::getUser();

$session = JFactory::getSession();
$iotype = JRequest::getInt('iotype', 0);
if($iotype){
	$pms = $session->get('exportitem', null);
}
else{
	$pms = $session->get('importitem', null);
}
?>

<script type="text/javascript">
	
	Joomla.submitbutton = function(task) {
		if (task == 'cancel') {
			$hd('input[name=view]').val('schedules');
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			var form = document.adminForm;
			if(form.title.value == "")	{
				alert("<?php echo JText::_('PLZ_ENTER_TITLE'); ?>");
				return false;
			}
			if(form.iotype.value== -1){
				alert("<?php echo JText::_('PLZ_IO_TYPE'); ?>");
				return false;
			}
			
			var selected = $hd("input[name=type]:checked");
			selectedVal = 1;
			if (selected.length > 0) {
				selectedVal = selected.val();
			}
			var iptype = $hd('select[name=iotype] option:selected').val();
			if(selectedVal==2 && iptype==0){
				
			}
			else if(form.source.value== ""){
				alert("<?php echo JText::_('PLZ_SELECT_FORMAT'); ?>");
				return false;
			}
			else if(form.source.value == "remote"){
				if(!$hd('#db_loc').is(':checked')){
					
					if(form.driver.value == "")	{
						alert("<?php echo JText::_('PLZ_ENTER_DRIVER'); ?>");
						return false;
					}
					
					if(form.host.value == "")	{
						alert("<?php echo JText::_('PLZ_ENTER_HOST'); ?>");
						return false;
					}
					
					if(form.user.value == "")	{
						alert("<?php echo JText::_('PLZ_ENTER_USER'); ?>");
						return false;
					}
					
					if(form.database.value == "")	{
						alert("<?php echo JText::_('PLZ_ENTER_DATABASE'); ?>");
						return false;
					}
					
				}
			}
			
			Joomla.submitform(task, document.getElementById('adminForm'));
			
		}
	}
$hd(function(){
	
	$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
	
	$hd(document).on('change', 'input[type=radio][name=type]', function(){
		if($hd('select[name=iotype] option:selected').val()!=-1){
			$hd('select[name=iotype] option[value=-1]').attr('selected', 'selected').trigger('liszt:updated');
		}
		if($hd(this).val()==2){
			$hd('tr.mailfilestatus').hide();
			$hd('tr.mailfile').hide();
		}
		else{
			$hd('tr.mailfilestatus').show();
			if($hd('select[name="sendfile[status]"]').val()==1){
				$hd('tr.mailfile').show();
			}
		}
		
		$hd('span#or').show();
		$hd('input[name=uid]').val('');
		$hd('tr#feed_access').remove();
		$hd('select[name=profileid]').find('option:gt(1)').remove().trigger('liszt:updated');
		$hd('select[name=profileid]').trigger('liszt:updated');
		$hd('tr.format_block').hide();
		$hd('tr.rd_block').hide();
		$hd('tr.rss2').hide();
		$hd('tr.rss1').hide();
		$hd('tr.atom').hide();
		$hd('tr.op_block').hide();
		$hd('select[name=source]').html('<option value=""><?php echo JText::_('SELECT_DATA_SOURCE');?></option><option value="csv">CSV</option><option value="json">JSON</option><option value="xml">XML</option><option value="remote"><?php echo JText::_('REMOTE_DATABASE'); ?></option>').trigger('liszt:updated'); 
	});

	
	<?php 
		$st_cols = $session->get('columns', null);
		if(!empty($st_cols)){echo "laodiocronprofiles();";} 
	?>
	
	<?php if( (($input->get('iotype', -1)==0) || ($input->get('iotype', -1)==1) || ($input->get('iotype', -1)==2)) && empty($st_cols) ){echo "laodioprofiles();";}?>
	
	$hd('select#iotype').on('change', function(event){
		
		laodioprofiles();
	});
	
	$hd(document).on('change', 'select#source', function(event){
		
		if($hd(this).val()==""){
			$hd('tr.format_block').hide();
			$hd('tr.rd_block').hide();
			$hd('tr.rss2').hide();
			$hd('tr.rss1').hide();
			$hd('tr.atom').hide();
			$hd('tr.op_block').hide();
			$hd('tr.mailfilestatus').show();
			if($hd('select[name="sendfile[status]"]').val()==1){
				$hd('tr.mailfile').show();
			}
		}
		else if($hd(this).val()=="remote")   {
			$hd('tr.format_block').hide();
			$hd('tr.rd_block').show();
			$hd('tr.rss2').hide();
			$hd('tr.rss1').hide();
			$hd('tr.atom').hide();
			var iotype = $hd('select#iotype').val();
			if((iotype==1)){
				$hd('tr.op_block').show();
			}
			//disable file email in remove case
			$hd('tr.mailfilestatus').hide();
			$hd('tr.mailfile').hide();
			// $hd('')
			
		}
		else if($hd(this).val()=='RSS2'){
			$hd('tr.rss2').show();
			$hd('tr.rss1').hide();
			$hd('tr.atom').hide();
			$hd('tr.mailfilestatus').hide();
			$hd('tr.mailfile').hide();
		}
		else if($hd(this).val()=='RSS1'){
			$hd('tr.rss1').show();
			$hd('tr.rss2').hide();
			$hd('tr.atom').hide();
			$hd('tr.mailfilestatus').hide();
			$hd('tr.mailfile').hide();
		}
		else if($hd(this).val()=='ATOM'){
			$hd('tr.atom').show();
			$hd('tr.rss2').hide();
			$hd('tr.rss1').hide();
			$hd('tr.mailfilestatus').hide();
			$hd('tr.mailfile').hide();
		}
		else{
			$hd('tr.format_block').show();
			$hd('tr.rd_block').hide();
			$hd('tr.rss2').hide();
			$hd('tr.rss1').hide();
			$hd('tr.atom').hide();
			$hd('tr.op_block').hide();
			$hd('tr.mailfilestatus').show();
			if($hd('select[name="sendfile[status]"]').val()==1){
				$hd('tr.mailfile').show();
			}
		}
		var type = ($hd("input[name=type]:checked").length)?$hd("input[name=type]:checked").val():1;
		if(type=='2'){
			$hd('tr.format_block').hide();
			$hd('tr.rd_block').hide();
		}
    });
	
	$hd(document).on('change', 'select#server', function(event){
		if($hd(this).val()=="local"){
			$hd('span.rel_ab').show();
			$hd('span.localpath').show();
			$hd('span.remotepath').hide();
		}
		else if($hd(this).val()=="absolute"){
			$hd('span.rel_ab').hide();
			$hd('span.localpath').show();
			$hd('span.remotepath').hide();
		}
		else if($hd(this).val()=="remote"){
			$hd('span.localpath').hide();
			$hd('span.remotepath').show();
		}
    });
	
	$hd('#db_loc').change(function(event){
		if($hd(this).is(":checked")) {
			$hd('.rd_block').not('tr#check_db').hide();
		}
		else{
			$hd('.rd_block').show();
		}
	});
	
	$hd(document).on('change', 'select#profileid', function(event){
		var iotype = $hd('#iotype').val();
		var href = "<?php echo JRoute::_(JURI::base().'index.php?option=com_vdata&view=profiles&task=edit&type=2');?>";
		if(iotype==0){
			href = "<?php echo JRoute::_(JURI::base().'index.php?option=com_vdata&view=profiles&task=edit&iotype=0&type=2');?>";
		}
		else if(iotype==1){
			href = "<?php echo JRoute::_(JURI::base().'index.php?option=com_vdata&view=profiles&task=edit&iotype=1&type=2');?>";
		}
		else if(iotype==2){
			href = "<?php echo JRoute::_(JURI::base().'index.php?option=com_vdata&view=profiles&task=edit&iotype=2&type=2');?>";
		}
		if($hd(this).val()=="-1"){
			window.location.href = href;
			//window.location.href = "<?php echo JRoute::_(JURI::base().'index.php?option=com_vdata&view=profiles&task=edit');?>";
			// window.location.assign("<?php echo JRoute::_(JURI::base().'index.php?option=com_vdata&view=profiles&task=edit&iotype=0');?>");
			// window.location.replace("<?php echo JRoute::_(JURI::base().'index.php?option=com_vdata&view=profiles&task=edit&iotype=0');?>");
		}
	});
	
	$hd(document).on('change', 'select[name="sendfile[status]"]', function(event){
		if($hd(this).val()==0){
			$hd('tr.mailfile').hide();
		}
		else{
			if($hd('select[name="source"]').val()!='remote' && $hd('input[type=radio][name=type]:checked').val()!=2){
				$hd('tr.mailfile').show();
			}
		}
	});
	
});

function laodioprofiles(){
	var iptype = $hd('select#iotype').val();
	var selected = $hd("input[name=type]:checked");
	selectedVal = 1;
	if (selected.length > 0) {
		selectedVal = selected.val();
	}
	//feed
	if(selectedVal==2){
		// $hd('table#cron_tbl tr#feed_access').remove();
		if($hd('table#cron_tbl tr#feed_access').length==0){
			<?php 
				$access = '<select name="access">'; 
				foreach($this->viewLevels as $key=>$level){
					$access .= '<option value="'.$level->id.'"';
					if($level->id==$this->item->access){
						$access .= ' selected="selected"';
					}
					$access .= '>'.$level->title.'<//option>';
				}
				$access .= '<//select>';
			?>
			
			var access_html = '<?php echo $access; ?>';
			var access_tr = '<tr id="feed_access"><td width="200"><label class="hasTip required" title="<?php echo JText::_("ACCESS_DESC");?>"><?php echo JText::_("ACCESS"); ?></label></td>';
			access_tr += '<td>'+access_html+'</td>';
			access_tr += '</tr>';
			$hd("tr#unqid").after(access_tr);
			$hd('select[name="access"]').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
			
			jQuery('.hasTip').each(function() {
				var title = jQuery(this).attr('title');
				if (title) {
					var parts = title.split('::', 2);
					jQuery(this).data('tip:title', parts[0]);
					jQuery(this).data('tip:text', parts[1]);
				}
			});
			var JTooltips = new Tips(jQuery('.hasTip').get(), {"maxTitleChars": 50,"fixed": false});
			if($hd('input[name="access"]').length>0){
				$hd('input[name="access"]').remove();
			}
		}
		
		if(iptype==1){
			if( $hd('select#source option[value="remote"]').length==1 ){
				$hd('select#source option[value="remote"],option[value="csv"],option[value="json"],option[value="xml"]').remove();
				var op = '<option value="SITEMAP">SITEMAP</option><option value="RSS2">RSS2</option><option value="RSS1">RSS1</option><option value="ATOM">ATOM</option><option value="csv">CSV</option><option value="xml">XML</option><option value="json">JSON</option>';
				$hd('select#source').append(op);
				$hd('select#source').trigger('liszt:updated');
				$hd('tr.rd_block').hide();
			}
			$hd('span#or').show();
			$hd('span#mode').show();
			$hd('tr.format_block').hide();
			$hd('input[name=task],input[name=view]').val("export");
			$hd('form#adminForm').attr('action','index.php?option=com_vdata&view=export');
			$hd('div#toolbar-apply button').attr('onclick', 'Joomla.submitbutton("exportready")');
			$hd('tr.op_block').hide();
			$hd('tr#ds_block').show();
		}
		else if(iptype==0){
			$hd('span#or').hide();
			$hd('span#mode').hide();
			$hd('input[name=task],input[name=view]').val("import");
			$hd('form#adminForm').attr('action','index.php?option=com_vdata&view=import');
			$hd('div#toolbar-apply button').attr('onclick', 'Joomla.submitbutton("importready")');
			$hd('tr.op_block').hide();
			$hd('tr.rss2').hide();
			$hd('tr.rss1').hide();
			$hd('tr.atom').hide();
			$hd('tr#ds_block').hide();
		}
		else {
			$hd('span#or').show();
			$hd('span#mode').show();
			$hd('tr.format_block').show();
			$hd('tr.op_block').hide();
		}
		//hide email options
		$hd('tr.mailfile').hide();
	}
	else{//cron
		if( $hd('select#source option[value="remote"]').length!=1 ){
			$hd('select#source option[value="SITEMAP"],option[value="RSS2"],option[value="ATOM"],option[value="csv"],option[value="xml"],option[value="json"],option[value="RSS1"]').remove();
			$hd('select#source').append('<option value="csv">CSV</option><option value="json">JSON</option><option value="xml">XML</option><option value="remote"><?php echo JText::_('REMOTE_DATABASE'); ?></option>');
			$hd('select#source').trigger('liszt:updated');
			$hd('tr.mailfile').hide();
		}
		//super user access for import/export
		if($hd('input[name="access"]').length==0){
			$hd('form#adminForm').append('<input type="hidden" name="access" value="6" />');
		}
		$hd('table#cron_tbl tr#feed_access').remove();
		$hd('tr#ds_block').show();
	
		if(iptype==0) {
			$hd('span#or').hide();
			$hd('span#mode').hide();		
			$hd('input[name=task],input[name=view]').val("import");
			$hd('form#adminForm').attr('action','index.php?option=com_vdata&view=import');
			$hd('div#toolbar-apply button').attr('onclick', 'Joomla.submitbutton("importready")');
			$hd('tr.op_block').hide();
			$hd('tr.rss2').hide();
			$hd('tr.rss1').hide();
			$hd('tr.atom').hide();
			//disbale file email options
			$hd('tr.mailfilestatus').hide();
			$hd('tr.mailfile').hide();
		}
		else if(iptype==1) {
			$hd('span#or').show();
			$hd('span#mode').show();
			$hd('input[name=task],input[name=view]').val("export");
			$hd('form#adminForm').attr('action','index.php?option=com_vdata&view=export');
			$hd('div#toolbar-apply button').attr('onclick', 'Joomla.submitbutton("exportready")');
			if($hd('select#source').val()=='remote')
				$hd('tr.op_block').show();
			$hd('tr.rss2').hide();
			$hd('tr.rss1').hide();
			$hd('tr.atom').hide();
			$hd('tr.mailfilestatus').show();
			if($hd('select[name="sendfile[status]"]').val()==1 && $hd('select[name="source"]').val()!='remote' ){
				$hd('tr.mailfile').show();
			}
			
		}
		else {
			$hd('span#or').show();
			$hd('span#mode').show();
			$hd('tr.format_block').show();
			$hd('tr.op_block').hide();
			$hd('tr.mailfilestatus').show();
			if($hd('select[name="sendfile[status]"]').val()==1 && $hd('select[name="source"]').val()!='remote'){
				$hd('tr.mailfile').show();
			}
		}
	}
		
	$hd.ajax({
		url:'index.php',
		type:'POST',
		dataType:'json',
		data:{'option':'com_vdata', 'view':'schedules', 'task':'getProfiles','iotype':iptype, "<?php echo JSession::getFormToken(); ?>":1<?php if(($input->get('profileid', 0))){echo ",profileid:".$input->get('profileid');}?>},
		beforeSend: function()	{
			$hd(".loading").show();
		},
		complete: function()	{
			$hd(".loading").hide();
		},
		success:function(res){
			if(res.result == 'success'){
				$hd('select#profileid').html(res.html);
				$hd('select#profileid').trigger('liszt:updated');
			}
		},
		error: function(jqXHR, textStatus, errorThrown)	{
			alert(textStatus);				  
		}
	});
}

function laodiocronprofiles(){
	var iptype = $hd('#iotype').val();
	if(iptype==2){
		if( $hd('select#source option[value="remote"]').length==1 ){
			$hd('select#source option[value="remote"],option[value="csv"],option[value="json"],option[value="xml"]').remove();
			var op = '<option value="SITEMAP">SITEMAP</option><option value="RSS2">RSS2</option><option value="RSS1">RSS1</option><option value="ATOM">ATOM</option><option value="CSV">CSV</option><option value="XML">XML</option><option value="JSON">JSON</option>';
			$hd('select#source').append(op);
			$hd('select#source').trigger('liszt:updated');
			$hd('tr.rd_block').hide();
		}
		//access visible for feed only
		<?php 
			$access = '<select name="access">'; 
			foreach($this->viewLevels as $key=>$level){
				$access .= '<option value="'.$level->id.'"';
				if($level->id==$this->item->access){
					$access .= ' selected="selected"';
				}
				$access .= '>'.$level->title.'<//option>';
			}
			$access .= '<//select>';
		?>
		var access_html = '<?php echo $access; ?>';
		var access_tr = '<tr id="feed_access"><td width="200"><label class="hasTip required" title="<?php echo JText::_("ACCESS_DESC");?>"><?php echo JText::_("ACCESS"); ?></label></td>';
		access_tr += '<td>'+access_html+'</td>';
		access_tr += '</tr>';
		$hd("tr#unqid").after(access_tr);
		$hd('select[name="access"]').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
		
		jQuery('.hasTip').each(function() {
			var title = jQuery(this).attr('title');
			if (title) {
				var parts = title.split('::', 2);
				jQuery(this).data('tip:title', parts[0]);
				jQuery(this).data('tip:text', parts[1]);
			}
		});
		var JTooltips = new Tips(jQuery('.hasTip').get(), {"maxTitleChars": 50,"fixed": false});
		if($hd('input[name="access"]').length>0){
			$hd('input[name="access"]').remove();
		}
	}
	else{
		if( $hd('select#source option[value="remote"]').length!=1 ){
			$hd('select#source option[value="SITEMAP"],option[value="RSS2"],option[value="ATOM"],option[value="csv"],option[value="xml"],option[value="json"],option[value="RSS1"]').remove();
			$hd('select#source').append('<option value="csv">CSV</option><option value="json">JSON</option><option value="xml">XML</option><option value="remote"><?php echo JText::_('REMOTE_DATABASE'); ?></option>');
			$hd('select#source').trigger('liszt:updated');
		}
		//super user access for import/export
		if($hd('input[name="access"]').length==0){
			$hd('form#adminForm').append('<input type="hidden" name="access" value="6" />');
		}
		$hd('table#cron_tbl tr#feed_access').remove();
	}
	if(iptype==0) {
		$hd('span#or').hide();
		$hd('span#mode').hide();			
		$hd('tr.op_block').hide();
		$hd('tr.rss2').hide();
		$hd('tr.rss1').hide();
		$hd('tr.atom').hide();
	}
	else if(iptype==1) {
		$hd('span#or').show();
		$hd('span#mode').show();
		if($hd('select#source').val()=='remote')
			$hd('tr.op_block').show();
		$hd('tr.rss2').hide();
		$hd('tr.rss1').hide();
		$hd('tr.atom').hide();
	}
	else if(iptype==2) {
		iptype = 1;
		$hd('span#or').show();
		$hd('span#mode').show();
		$hd('tr.format_block').hide();
		$hd('tr.op_block').hide();
	}
	else {
		$hd('span#or').show();
		$hd('span#mode').show();
		$hd('tr.format_block').show();
		$hd('tr.op_block').hide();
	}
	
	$hd.ajax({
		url:'index.php',
		type:'POST',
		dataType:'json',
		data:{'option':'com_vdata', 'view':'schedules', 'task':'getProfiles','iotype':iptype, "<?php echo JSession::getFormToken(); ?>":1<?php if(($input->get('profileid', 0))){echo ",profileid:".$input->get('profileid');}?>},
		beforeSend: function()	{
			$hd(".loading").show();
		},
		complete: function()	{
			$hd(".loading").hide();
		},
		success:function(res){
			if(res.result == 'success'){
				$hd('select#profileid').html(res.html);
				$hd('select#profileid').trigger('liszt:updated');
			}
		},
		error: function(jqXHR, textStatus, errorThrown)	{
			alert(textStatus);				  
		}
	});
}
</script>

<div id="vdatapanel">

<form action="<?php if($this->item->iotype==0) echo 'index.php?option=com_vdata&view=import';else echo 'index.php?option=com_vdata&view=export';?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'SCHEDULE_DETAILS' ); ?></legend>
<table id="cron_tbl" class="adminform table table-striped">
<tr>
    <td><label class="hasTip required" title="<?php echo JText::_('TITLE_DESC');?>"><?php echo JText::_('TITLE'); ?></label></td>
    <td><input type="text" name="title" id="title" class="inputbox required" value="<?php echo $this->item->title; ?>" size="50" /></td>
</tr>
<tr>
	<td><label class="hasTip required" title="<?php echo JText::_('FEED_TYPE_DESC');?>"><?php echo JText::_('FEED_TYPE'); ?></label></td>
	<td>
		<input type="radio" name="type" class="cron_type typeof" value="1" <?php if($this->item->type==1){echo 'checked="checked"';}?> /><?php echo JText::_('CRON');?>
		<input type="radio" name="type" class="feed_type typeof" value="2" <?php if($this->item->type==2){echo 'checked="checked"';}?> /><?php echo JText::_('FEED');?>
	</td>
</tr>
<tr>
	<td>
		<label class="hasTip required" title="<?php echo JText::_('TYPE_DESC');?>"><?php echo JText::_('TYPE'); ?></label>
	</td>
	<td>
		<select name="iotype" id="iotype">
			<option value="-1"<?php if($this->item->iotype==-1) echo 'selected="selected"';?>><?php echo JText::_('SELECT_IO_TYPE');?></option>
			<option value="0"<?php if($this->item->iotype==0){echo 'selected="selected"';}elseif($input->get('iotype',-1)==0){echo 'selected="selected"';}?>><?php echo JText::_('IMPORT');?></option>
			<option value="1"<?php if($this->item->iotype==1){echo 'selected="selected"';}elseif($input->get('iotype',-1)==1){echo 'selected="selected"';}?>><?php echo JText::_('EXPORT');?></option>
			<?php /*?><option value="2"<?php if($this->item->iotype==2) {echo 'selected="selected"';}elseif($input->get('iotype',-1)==2){echo 'selected="selected"';}?>><?php echo JText::_('FEEDS');?></option><?php */?>
		</select>
	</td>
</tr>
<tr>
	<td width="200"><label class="hasTip required" title="<?php echo JText::_('PROFILE_DESC');?>"><?php echo JText::_('PROFILE'); ?></label></td>
	<td>
		<select name="profileid" id="profileid">
			<option value="0"><?php echo JText::_('SELECT_PROFILE'); ?></option>
			<option value="-1"><?php echo JText::_('CREATE_PROFILE');?></option>
			<?php for($i=0;$i<count($this->item->profiles);$i++) :?>
				<option value="<?php echo $this->item->profiles[$i]->id; ?>" <?php if($this->item->profiles[$i]->id==$this->item->profileid) echo 'selected="selected"'; ?>>
				<?php echo $this->item->profiles[$i]->title; ?>
				</option>
			<?php endfor; ?>
			
		</select>
		
		<span id="or" <?php if($this->item->iotype==0){ echo ' style="display:none;"';} ?>>
			<?php echo JText::_('OR');?>
			<textarea name="qry" rows="5" cols="50" placeholder="<?php echo JText::_('COM_VDATA_EXPORT_QRY_PLACEHOLDER');?>"><?php if(!empty($this->item->qry)) {echo $this->item->qry;}elseif(!empty($this->qry)){echo $this->qry;}?></textarea>
		</span>
    </td>
</tr>
<tr id="unqid">
	<td width="200"><label class="hasTip required" title="<?php echo JText::_('UNIQUE_ID_DESC');?>"><?php echo JText::_('UNIQUE_ID'); ?></label></td>
	<td>
		<input type="text" name="uid" value="<?php echo $this->item->uid;?>" />
	</td>
</tr>

<?php if($this->item->type==2){//$this->item->type=2 && $this->item->iotype==1?>
<tr id="feed_access">
	<td width="200"><label class="hasTip required" title="<?php echo JText::_('ACCESS_DESC');?>"><?php echo JText::_('ACCESS'); ?></label></td>
	<td>
		<?php echo JHtmlAccess::level('access', $this->item->access, '', false);?>
	</td>
</tr>
<?php }?>

<?php if( $user->authorise('core.edit.state', 'com_vdata') ){?>
<tr>
	<td width="200"><label class="hasTip required" title="<?php echo JText::_('STATUS_DESC');?>"><?php echo JText::_('STATUS');?></label></td>
	<td>
		<select name="state">
			<option value="0" <?php if($this->item->state==0) echo 'selected="selected"';?>><?php echo JText::_('DISABLE');?></option>
			<option value="1" <?php if($this->item->state==1) echo 'selected="selected"';?>><?php echo JText::_('ENABLE');?></option>
		</select>
	</td>
</tr>
<?php }?>
<tr id="ds_block" <?php if($this->item->type==2 && $this->item->iotype==0){echo ' style="display:none;"';}?>>
  <td><label class="hasTip required" title="<?php echo JText::_('DATA_FORMAT_DESC');?>"><?php echo JText::_('DATA_FORMAT'); ?></label></td>
  <td>
	<select name="source" id="source">
    	<option value=""><?php echo JText::_('SELECT_DATA_SOURCE'); ?></option>
		<?php if($this->item->type!=2){?>
		
		<option value="csv" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='csv'){echo 'selected="selected"';} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='csv')){echo 'selected="selected"';}?>>CSV</option>
        <option value="json" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='json'){echo 'selected="selected"';} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='json')){echo 'selected="selected"';}?>>JSON</option>
        <option value="xml" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='xml'){echo 'selected="selected"';} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='xml')){echo 'selected="selected"';}?>>XML</option>
        <option value="remote" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='remote'){echo 'selected="selected"';} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='remote')){echo 'selected="selected"';}?>><?php echo JText::_('REMOTE_DATABASE'); ?></option>
		
		<?php }else{?>
		
		<option value="SITEMAP" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='SITEMAP') echo 'selected="selected"';?>><?php echo JText::_('SITEMAP');?></option>
		<option value="RSS2" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='RSS2') echo 'selected="selected"';?>><?php echo JText::_('RSS2');?></option>
		<option value="RSS1" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='RSS1') echo 'selected="selected"';?>><?php echo JText::_('RSS1');?></option>
		<option value="ATOM" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='ATOM') echo 'selected="selected"';?>><?php echo JText::_('ATOM');?></option>
		<?php /*?><option value="SPEED" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='SPEED') echo 'selected="selected"';?>><?php echo JText::_('SPEED');?></option><?php */?>
		<option value="csv" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='csv') echo 'selected="selected"';?>><?php echo JText::_('CSV');?></option>
		<option value="xml" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='xml') echo 'selected="selected"';?>><?php echo JText::_('XML');?></option>
		<option value="json" <?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='json') echo 'selected="selected"';?>><?php echo JText::_('JSON');?></option>
		<?php }?>
	</select>
  </td>
</tr>
<tr class="format_block"<?php if(!empty($st_cols) && !empty($pms) && ($pms->source!='remote')){} elseif(empty($this->item->params) || ($this->item->params->source=='remote') || ($this->item->type==2) ) echo ' style="display:none;"'; ?>>
<!-- !empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=="remote"-->
    <td>
	<label class="hasTip required" title="<?php echo JText::_('ENTER_PATH_DESC'); ?>"><?php echo JText::_('ENTER_PATH'); ?></label>
	</td>
    <td>
        <select name="server" id="server">
			<option value="local"<?php if(!empty($this->item->params) && property_exists($this->item->params,'server') && $this->item->params->server=='local'){echo 'selected="selected"';} elseif(!empty($st_cols) && !empty($pms) && (($pms->server=='local') || ($pms->server=='write_local'))){echo 'selected="selected"';}?>>
				<?php echo JText::_('LOCAL_SERVER'); ?>
			</option>
			
			<option value="absolute"<?php if(!empty($this->item->params) && property_exists($this->item->params,'server') && $this->item->params->server=='absolute'){echo 'selected="selected"';} elseif(!empty($st_cols) && !empty($pms) && (($pms->server=='absolute') || ($pms->server=='write_local'))){echo 'selected="selected"';}?>>
				<?php echo JText::_('LOCAL_SERVER_ABSOLUTE'); ?>
			</option>
			
			<option value="remote"<?php if(!empty($this->item->params) && property_exists($this->item->params,'server') && $this->item->params->server=='remote'){echo 'selected="selected"';} elseif(!empty($st_cols) && !empty($pms) && (($pms->server=='remote') || ($pms->server=='write_remote'))){echo 'selected="selected"';}?>>
				<?php echo JText::_('REMOTE_SERVER'); ?>
			</option>

		</select><br />
        <span class="localpath"<?php if(!empty($this->item->params) && property_exists($this->item->params,'server') && ($this->item->params->server=='remote')) echo ' style="display:none;"';?>>
		<span class="rel_ab" <?php if(!empty($this->item->params) && property_exists($this->item->params,'server') && $this->item->params->server != 'local') {echo ' style="display:none;"';} elseif(!empty($st_cols) && !empty($pms) && (($pms->server!='local') || ($pms->server!='write_local'))){echo ' style="display:none;"';}?>><?php echo JPATH_ROOT.'/'.'';?></span>
			<input type="text" name="path" id="path" class="inputbox required" size="50" value="<?php if(!empty($this->item->params) && property_exists($this->item->params,'path')){echo $this->item->params->path;} elseif(!empty($st_cols) && !empty($pms) && ($pms->source!='remote')){echo $pms->path;}?>" />
			<span id="mode"<?php if($this->item->iotype==0) echo ' style="display:none;"';?>>
				<select name="mode" >
					<option value="w" <?php if(!empty($this->item->params) && property_exists($this->item->params, 'mode') && $this->item->params->mode == 'w') {echo 'selected="selected"';} elseif(!empty($st_cols) && !empty($pms) && ($pms->mode=='w')){echo 'selected="selected"';}?>><?php echo JText::_('CREATE');?></option>
					<option value="a" <?php if(!empty($this->item->params) && property_exists($this->item->params, 'mode') && $this->item->params->mode == 'a') {echo 'selected="selected"';}elseif(!empty($st_cols) && !empty($pms) && ($pms->mode=='a')){echo 'selected="selected"';}?>><?php echo JText::_('APPEND');?></option>
				</select>
			</span>
		</span>
		<span class="remotepath"<?php if( !isset($this->item->params->server) || ($this->item->params->server != "remote") || empty($this->item->params->server) ) echo ' style="display:none;"';?>>
			<div>
				<?php echo JText::_('VDATA_FTP_HOST');?>
				<input type="text" name="ftp[ftp_host]" value="<?php if(isset($this->item->params->ftp->ftp_host)){echo $this->item->params->ftp->ftp_host;}?>" />
			</div>
			<div>
				<?php echo JText::_('VDATA_FTP_PORT');?>
				<input type="text" name="ftp[ftp_port]" value="<?php if(isset($this->item->params->ftp->ftp_port)){echo $this->item->params->ftp->ftp_port;}?>" />
			</div>
			<div>
				<?php echo JText::_('VDATA_FTP_USER');?>
				<input type="text" name="ftp[ftp_user]" value="<?php if(isset($this->item->params->ftp->ftp_user)){echo $this->item->params->ftp->ftp_user;}?>" />
			</div>
			<div>
				<?php echo JText::_('VDATA_FTP_PASSWORD');?>
				<input type="text" name="ftp[ftp_pass]" value="<?php if(isset($this->item->params->ftp->ftp_pass)){echo $this->item->params->ftp->ftp_pass;}?>" />
			</div>
			<div>
				<?php echo JText::_('VDATA_FTP_ROOT_DIRECTORY');?>
				<input type="text" name="ftp[ftp_directory]" value="<?php if(isset($this->item->params->ftp->ftp_directory)){echo $this->item->params->ftp->ftp_directory;}?>" />
			</div>
			<div>
				<?php echo JText::_('VDATA_FTP_FILE');?>
				<input type="text" name="ftp[ftp_file]" value="<?php if(isset($this->item->params->ftp->ftp_file)){echo $this->item->params->ftp->ftp_file;}?>" />
			</div>
		</span>
    </td>
</tr>

<tr class="op_block"<?php //if(empty($this->item->params) || ($this->item->params->source!='remote') || ($this->item->profileid!=0) || ($this->item->iotype!=1) )  echo ' style="display:none;"';?><?php if(!empty($this->item->params) && ($this->item->params->source=='remote') && ($this->item->iotype==1) ){} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='remote') && ($iotype==1) ){} else { echo ' style="display:none;"';}?> id="operation">
	<td width="200"><label class="hasTip" title="<?php echo JText::_('OPERATION_DESC');?>"><?php echo JText::_('OPERATION');?></label></td>
	<td>
		<select name="operation">
			<option value="1"<?php if(!empty($this->item->params) && property_exists($this->item->params, 'operation') && ($this->item->params->operation==1) ) {echo 'selected="selected"';} elseif( !empty($st_cols) && !empty($pms) && ($pms->source=='remote') && property_exists($pms, 'operation') && ($pms->operation==1) ){echo 'selected="selected"';}?>><?php echo JText::_('INSERT');?></option>
			<option value="0"<?php if(!empty($this->item->params) && property_exists($this->item->params, 'operation') && ($this->item->params->operation==0) ) {echo 'selected="selected"';}  elseif( !empty($st_cols) && !empty($pms) && ($pms->source=='remote') && property_exists($pms, 'operation') && ($pms->operation==0) ){echo 'selected="selected"';}?>><?php echo JText::_('UPDATE');?></option>
			<option value="2" <?php if(!empty($this->item->params) && property_exists($this->item->params, 'operation') && ($this->item->params->operation==2) ) {echo 'selected="selected"';}  elseif( !empty($st_cols) && !empty($pms) && ($pms->source=='remote') && property_exists($pms, 'operation') && ($pms->operation==2) ){echo 'selected="selected"';}?>><?php echo JText::_('SYNCHRONIZE');?></option>
		</select>
	</td>
</tr>

<tr class="rd_block" id="check_db"<?php //if(empty($this->item->params) || $this->item->params->source<>'remote') echo ' style="display:none;"';?> <?php if(!empty($this->item->params) && ($this->item->params->source=='remote')) {} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='remote') ){} else {echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('LOCAL_DB_DESC');?>"><?php echo JText::_('LOCAL_DB');?></label></td>
	<td><input type="checkbox" id="db_loc" name="db_loc" value="localdb" <?php  if(!empty($this->item->params) && property_exists($this->item->params, 'local_db') && ($this->item->params->local_db==1)) {echo 'checked="checked"';} elseif(!empty($st_cols) && !empty($pms) && property_exists($pms, 'db_loc') && ($pms->db_loc=='localdb')){ echo 'checked="checked"';}?>/></td>
</tr>

<tr class="rd_block"<?php  //if( empty($this->item->params) || ($this->item->params->source!='remote') || ($this->item->params->local_db==1) )  echo ' style="display:none;"'; ?><?php  if( !empty($this->item->params) && ($this->item->params->source=='remote') && ($this->item->params->local_db!=1) ){} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='remote') && !property_exists($pms, 'db_loc') ){} else {echo ' style="display:none;"';} ?>>
<!--!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source<>"remote"-->
    <td><label class="hasTip required" title="<?php echo JText::_('DRIVER_DESC');?>"><?php echo JText::_('DRIVER'); ?></label></td>
    <td><input type="text" name="driver" id="driver" class="inputbox required" size="50" value="<?php if(!empty($this->item->params) && property_exists($this->item->params, 'driver')) {echo $this->item->params->driver;} elseif(!empty($st_cols) && !empty($pms) && !property_exists($pms, 'db_loc') && property_exists($pms, 'driver') ){echo $pms->driver;}?>" /> 
    </td>
</tr>
<tr class="rd_block"<?php  //if( empty($this->item->params) || ($this->item->params->source!='remote')  || ($this->item->params->local_db==1) )  echo ' style="display:none;"'; ?><?php  if( !empty($this->item->params) && ($this->item->params->source=='remote') && ($this->item->params->local_db!=1) ){} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='remote') && !property_exists($pms, 'db_loc') ){} else{ echo ' style="display:none;"';} ?>>
<!--!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source<>"remote"-->
    <td><label class="hasTip required" title="<?php echo JText::_('HOSTNAME_DESC');?>"><?php echo JText::_('HOSTNAME'); ?></label></td>
    <td><input type="text" name="host" id="host" class="inputbox required" size="50" value="<?php if(!empty($this->item->params) && property_exists($this->item->params, 'host')) {echo $this->item->params->host;} elseif(!empty($st_cols) && !empty($pms) && !property_exists($pms, 'db_loc') && property_exists($pms, 'host') ){echo $pms->host;} ?>" /> 
    </td>
</tr>
<tr class="rd_block"<?php //if( empty($this->item->params) || ($this->item->params->source!='remote') || ($this->item->params->local_db==1) ) echo ' style="display:none;"'; ?> <?php if( !empty($this->item->params) && ($this->item->params->source=='remote') && ($this->item->params->local_db!=1) ){} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='remote') && !property_exists($pms, 'db_loc') ){} else{echo ' style="display:none;"';} ?>>
<!--!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source<>"remote"-->
    <td><label class="hasTip required" title="<?php echo JText::_('USERNAME_DESC');?>"><?php echo JText::_('USERNAME'); ?></label></td>
    <td><input type="text" name="user" id="user" class="inputbox required" size="50" value="<?php if(!empty($this->item->params) && property_exists($this->item->params,'user')) {echo $this->item->params->user;} elseif(!empty($st_cols) && !empty($pms) && !property_exists($pms, 'db_loc') && property_exists($pms, 'user') ){echo $pms->user;}?>" /> 
    </td>
</tr>
<tr class="rd_block"<?php  //if(empty($this->item->params) || ($this->item->params->source!='remote') || ($this->item->params->local_db==1))  echo ' style="display:none;"'; ?><?php  if( !empty($this->item->params) && ($this->item->params->source=='remote') && ($this->item->params->local_db!=1) ){} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='remote') && !property_exists($pms, 'db_loc') ){} else{ echo ' style="display:none;"';} ?>>
<!--!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source<>"remote"-->
    <td><label class="hasTip required" title="<?php echo JText::_('PASSWORD_DESC');?>"><?php echo JText::_('PASSWORD'); ?></label></td>
    <td><input type="password" name="password" id="password" class="inputbox required" size="50" value="<?php if(!empty($this->item->params) && property_exists($this->item->params, 'password')) {echo $this->item->params->password;} elseif(!empty($st_cols) && !empty($pms) && !property_exists($pms, 'db_loc') && property_exists($pms, 'password') ){echo $pms->password;}?>" /> 
    </td>
</tr>
<tr class="rd_block"<?php  //if(empty($this->item->params) || ($this->item->params->source!='remote') || ($this->item->params->local_db==1))  echo ' style="display:none;"'; ?><?php  if( !empty($this->item->params) && ($this->item->params->source=='remote') && ($this->item->params->local_db!=1) ){} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='remote') && !property_exists($pms, 'db_loc') ){} else { echo ' style="display:none;"';} ?>>
<!--!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source<>"remote"-->
    <td><label class="hasTip required" title="<?php echo JText::_('DATABASE_NAME_DESC');?>"><?php echo JText::_('DATABASE_NAME'); ?></label></td>
    <td><input type="text" name="database" id="database" class="inputbox required" size="50" value="<?php if(!empty($this->item->params) && property_exists($this->item->params, 'database')) {echo $this->item->params->database;} elseif(!empty($st_cols) && !empty($pms) && !property_exists($pms, 'db_loc') && property_exists($pms, 'database') ){echo $pms->database;}?>" /> 
    </td>
</tr>
<tr class="rd_block"<?php //if(empty($this->item->params) || ($this->item->params->source!='remote') || ($this->item->params->local_db==1))  echo ' style="display:none;"';?><?php if( !empty($this->item->params) && ($this->item->params->source=='remote') && ($this->item->params->local_db!=1) ){} elseif(!empty($st_cols) && !empty($pms) && ($pms->source=='remote') && !property_exists($pms, 'db_loc') ){} else { echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('DATABASE_TBL_PREFIX_DESC');?>"><?php echo JText::_('DATABASE_PREFIX'); ?></label></td>
	<td><input type="text" name="dbprefix" id="dbprefix" class="inputbox required" size="50" value="<?php if(!empty($this->item->params) && property_exists($this->item->params, 'dbprefix')) {echo $this->item->params->dbprefix;} elseif(!empty($st_cols) && !empty($pms) && !property_exists($pms, 'db_loc') && property_exists($pms, 'dbprefix') ){echo $pms->dbprefix;} ?>" /> 
    </td>
</tr>



<tr class="rss2"<?php if( !empty($this->item->params) && property_exists($this->item->params,'source') && ($this->item->params->source=='RSS2') ) { } else  { echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('RSS2_FEED_TITLE_DESC');?>"><?php echo JText::_('RSS2_FEED_TITLE');?></label></td>
	<td>
		<input type="text" name="rss2_title" value="<?php if(!empty($this->item->params) && property_exists($this->item->params,'rss2_title') ){echo $this->item->params->rss2_title;}?>" />
	</td>
</tr>
<tr class="rss2"<?php if( !empty($this->item->params) && property_exists($this->item->params,'source') && ($this->item->params->source=='RSS2') ) { } else  { echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('RSS2_FEED_LINK_DESC');?>"><?php echo JText::_('RSS2_FEED_LINK');?></label></td>
	<td>
		<input type="text" name="rss2_link" value="<?php if(!empty($this->item->params) && property_exists($this->item->params,'rss2_link') ){echo $this->item->params->rss2_link;}?>" />
	</td>
</tr>
<tr class="rss2"<?php if( !empty($this->item->params) && property_exists($this->item->params,'source') && ($this->item->params->source=='RSS2') ) { } else  { echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('RSS2_FEED_DESCRIPTION_DESC');?>"><?php echo JText::_('RSS2_FEED_DESCRIPTION');?></label></td>
	<td>
		<textarea cols="10" rows="5" name="rss2_desc"><?php if(!empty($this->item->params) && property_exists($this->item->params,'rss2_desc') ){echo $this->item->params->rss2_desc;}?></textarea>
	</td>
</tr>

<tr class="rss2"<?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='RSS2') { } else  { echo ' style="display:none;"';}?>>
<td colspan="2"><a href="https://validator.w3.org/feed/docs/rss2.html" target="_blank">RSS2.0</a></td>
</tr>

<tr class="rss1"<?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='RSS1') { } else { echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('RSS1_FEED_TITLE_DESC');?>"><?php echo JText::_('RSS1_FEED_TITLE');?></label></td>
	<td>
		<input type="text" name="rss1_title" value="<?php if(!empty($this->item->params) && property_exists($this->item->params,'rss1_title') ){echo $this->item->params->rss1_title;}?>" />
	</td>
</tr>
<tr class="rss1"<?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='RSS1') { } else { echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('RSS1_FEED_LINK_DESC');?>"><?php echo JText::_('RSS1_FEED_LINK');?></label></td>
	<td>
		<input type="text" name="rss1_link" value="<?php if(!empty($this->item->params) && property_exists($this->item->params,'rss1_link') ){echo $this->item->params->rss1_link;}?>" />
	</td>
</tr>
<tr class="rss1"<?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='RSS1') { } else { echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('RSS1_FEED_DESCRIPTION_DESC');?>"><?php echo JText::_('RSS1_FEED_DESCRIPTION');?></label></td>
	<td>
		<textarea cols="10" rows="5" name="rss1_desc"><?php if(!empty($this->item->params) && property_exists($this->item->params,'rss1_desc') ){echo $this->item->params->rss1_desc;}?></textarea>
	</td>
</tr>
<tr class="rss1"<?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='RSS1') { } else { echo ' style="display:none;"';}?>>
<td colspan="2"><a href="http://web.resource.org/rss/1.0/spec" target="_blank">RSS1.0</a></td>
</tr>

<tr class="atom"<?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='ATOM') { } else  { echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('ATOM_TITLE_DESC');?>"><?php echo JText::_('ATOM_TITLE');?></label></td>
	<td>
		<input type="text" name="atom_title" value="<?php if(!empty($this->item->params) && property_exists($this->item->params,'atom_title') ){echo $this->item->params->atom_title;}?>" />
	</td>
</tr>
<tr class="atom"<?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='ATOM') { } else  { echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('ATOM_CATEGORY_DESC');?>"><?php echo JText::_('ATOM_CATEGORY');?></label></td>
	<td>
		<input type="text" name="atom_category" value="<?php if(!empty($this->item->params) && property_exists($this->item->params,'atom_category') ){echo $this->item->params->atom_category;}?>" />
	</td>
</tr>
<tr class="atom"<?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='ATOM') { } else  { echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('ATOM_AUTHOR_NAME_DESC');?>"><?php echo JText::_('ATOM_AUTHOR_NAME');?></label></td>
	<td>
		<input type="text" name="atom_author_name" value="<?php if(!empty($this->item->params) && property_exists($this->item->params,'atom_author_name') ){echo $this->item->params->atom_author_name;}?>" />
	</td>
</tr>
<tr class="atom"<?php if(!empty($this->item->params) && property_exists($this->item->params,'source') && $this->item->params->source=='ATOM') { } else  { echo ' style="display:none;"';}?>>
	<td><label class="hasTip required" title="<?php echo JText::_('ATOM_AUTHOR_EMAIL_DESC');?>"><?php echo JText::_('ATOM_AUTHOR_EMAIL');?></label></td>
	<td>
		<input type="text" name="atom_author_email" value="<?php if(!empty($this->item->params) && property_exists($this->item->params,'atom_author_email') ){echo $this->item->params->atom_author_email;}?>" />
	</td>
</tr>
	<?php $mailfilestatus = (($this->item->type==1) && ($this->item->iotype==1))?true:false;?>
	<tr class="mailfilestatus" <?php if(!$mailfilestatus){echo ' style="display:none;"';}?>>
		<td>
			<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_DESC');?>"><?php echo JText::_('VDATA_EXPORT_FILE_LABEL');?></label>
		</td>
		<td>
			<select name="sendfile[status]">
				<option value="0" <?php if(isset($this->item->params->sendfile->status) && ($this->item->params->sendfile->status==0)){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_DISABLE');?></option>
				<option value="1" <?php if(isset($this->item->params->sendfile->status) && ($this->item->params->sendfile->status==1)){echo ' selected="selected"';}?>><?php echo JText::_('VDATA_ENABLE');?></option>
			</select>
		</td>
	</tr>
	<?php 
		$emailStatus = ( isset($this->item->params->sendfile->status) && ($this->item->params->sendfile->status==1) && (isset($this->item->params->source) && ($this->item->params->source!='remote')) && ($this->item->type!=2) )?true:false;
	?>
	<tr class="mailfile" <?php if(!$emailStatus){echo ' style="display:none;"';}?>>
		<td>
			<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_ADDRESSES_DESC');?>"><?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_ADDRESSES_LABEL');?></label>
		</td>
		<td>
			<input type="text" name="sendfile[emails]" value="<?php if(isset($this->item->params->sendfile->emails)){echo $this->item->params->sendfile->emails;}?>" />
		</td>
	</tr>
	<tr class="mailfile" <?php if(!$emailStatus){echo ' style="display:none;"';}?>>
		<td>
			<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_CC_LABEL');?>"><?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_CC_DESC');?></label>
		</td>
		<td>
			<input type="text" name="sendfile[cc]" value="<?php if(isset($this->item->params->sendfile->cc)){echo $this->item->params->sendfile->cc;}?>" />
		</td>
	</tr>
	<tr class="mailfile" <?php if(!$emailStatus){echo ' style="display:none;"';}?>>
		<td>
			<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_BCC_LABEL');?>"><?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_BCC_DESC');?></label>
		</td>
		<td>
			<input type="text" name="sendfile[bcc]" value="<?php if(isset($this->item->params->sendfile->bcc)){echo $this->item->params->sendfile->bcc;}?>" />
		</td>
	</tr>
	<tr class="mailfile" <?php if(!$emailStatus){echo ' style="display:none;"';}?>>
		<td>
			<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_SUBJECT_LABEL');?>"><?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_SUBJECT_DESC');?></label>
		</td>
		<td>
			<input type="text" name="sendfile[subject]" value="<?php if(isset($this->item->params->sendfile->subject)){echo $this->item->params->sendfile->subject;}?>" />
		</td>
	</tr>
	<tr class="mailfile" <?php if(!$emailStatus){echo ' style="display:none;"';}?>>
		<td>
			<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_BODY_LABEL');?>"><?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_BODY_DESC');?></label>
		</td>
		<td>
			<?php 
				$typeEditor = JFactory::getConfig()->get('editor');
				$editor = JEditor::getInstance($typeEditor);
				
				$editorHtml = isset($this->item->params->sendfile->tmpl)? $this->item->params->sendfile->tmpl:'';
				echo $editor->display( 'sendfile[tmpl]', $editorHtml, '200', '200', '10', '10', false ,'sendfile_tmpl');
			?>
		</td>
	</tr>

</table>
</fieldset>
</div>
<?php if($this->item->type!=2){?>
	<input type="hidden" name="access" value="6" />
<?php }?>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="<?php if($this->item->iotype==0){echo 'import';}elseif(($this->item->iotype==1)){echo 'export';}else{echo 'schedules';}?>" />
<input type="hidden" name="st" value="1" />
</form>

</div>