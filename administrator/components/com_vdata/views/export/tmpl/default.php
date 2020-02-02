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

$profileid = JFactory::getApplication()->input->getInt('profileid', 0);

?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
	else {
		var form = document.adminForm;
		if(form.profileid.value == "")	{
			alert("<?php echo JText::_('PLZ_SELECT_PROFILE'); ?>");
			return false;
		}
		if(form.source.value == "")	{
			alert("<?php echo JText::_('PLZ_SELECT_DATA_FORMAT'); ?>");
			return false;
		}
		if(form.server.value == 'write_local' && form.path.value == ''){
			alert("<?php echo JText::_('PLZ_ENTER_FILEPATH'); ?>");
			return false;
		}
		//var checked = $hd('input[name="db_loc"]:checked').length;
		if( (form.source.value == "remote") && !$hd('#db_loc').is(':checked') )	{
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
		if(form.server.value == 'write_remote'){
			if( (form['ftp[ftp_host]'].value=='') || (form['ftp[ftp_user]'].value=='') || (form['ftp[ftp_pass]'].value=='') ){
				alert('<?php echo JText::_('VDATA_FTP_VALIDATION_ENTER_DETAILS');?>');
				return false;
			}
		}
		
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
	
$hd(function(){
    
	$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
	
    $hd(document).on('change', 'select#source', function(event){
		if($hd(this).val()=="remote")   {
			$hd('tr.format_block').hide();
			$hd('#db_loc').prop('checked', false);
			$hd('tr.rd_block').show();
			//disable file email for remote case
			$hd('tr.mailfile').hide();
			$hd('tr.mailfilestatus').hide();
		}
		else{
			$hd('tr.format_block').show();
			$hd('tr.rd_block').hide();
			$hd('tr.mailfilestatus').show();
			if($hd('select[name="sendfile[status]"]').val()==1){
				$hd('tr.mailfile').show();
			}
		}
    });
        
    $hd(document).on('change', 'select#server', function(event){
		if($hd(this).val()=="down")   {
			$hd('span.localpath').hide();
			$hd('span.remotepath').hide();
		}
		else if($hd(this).val()=="write_local"){
			$hd('span.localpath').show();
			$hd('span.rootpath').show();
			$hd('span.remotepath').hide();
		}
		else if($hd(this).val()=="write_remote"){
			$hd('span.remotepath').show();
			$hd('span.rootpath').hide();
			$hd('span.localpath').hide();
		}
    });
		
	$hd('#db_loc').change(function(event){
		//var checked = $hd('input[name="db_loc"]:checked').length;
		if($hd(this).is(":checked")) {
			$hd('.rd_block').not('tr#check_db,tr#imp_op').hide();
		}
		else{
			$hd('.rd_block').show();
		}
	});
	
	$hd(document).on('change', 'select#profileid', function(event){
		if($hd(this).val()=="-1"){
			window.location.href = "<?php echo JRoute::_(JURI::base().'index.php?option=com_vdata&view=profiles&task=edit&iotype=1&type=1');?>";
		}
	});
	$hd("#icon_desc_toggle").mouseover(function(){
		$hd( "#desc_toggle" ).show( 'slide', 800);
	});	
	$hd( ".sub_desc .close" ).on( "click", function() {
		$hd( "#desc_toggle" ).hide( 'slide', 800);
	});	
	$hd(document).on('change', 'select[name="sendfile[status]"]', function(event){
		if($hd(this).val()==0){
			$hd('tr.mailfile').hide();
		}
		else{
			if($hd('select[name="source"]').val()!='remote'){
				$hd('tr.mailfile').show();
			}
		}
	});
	
	
});
</script>

<div class="adminform_box vdata_export">
<form action="index.php?option=com_vdata&view=export" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_('PROFILE_EXPORT'); ?> <span class="icon-info" id="icon_desc_toggle"></span>
			<div class="sub_desc" id="desc_toggle" style="display:none;"><a class="close" href="javascript:void(0);">×</a><?php echo JText::_('PROFILE_EXPORT_DESC_TT'); ?></div>
		</legend>
		<table class="adminform table table-striped">
			<!-- export_profile::Start -->
			<tr>
				<td width="200">
					<label class="hasTip required" title="<?php echo JText::_('PROFILE_DESC');?>"><?php echo JText::_('PROFILE'); ?></label>
				</td>
				<td>
					<select name="profileid" id="profileid">
						<option value=""><?php echo JText::_('SELECT_PROFILE'); ?></option>
						<option value="-1"><?php echo JText::_('CREATE_PROFILE');?></option>
						<?php	
						$pid = JFactory::getApplication()->input->getInt('profileid', 0);
						for($i=0;$i<count($this->profiles);$i++){ ?>
							<option value="<?php echo $this->profiles[$i]->id; ?>" <?php if( ($this->item->profileid==$this->profiles[$i]->id) || ($pid==$this->profiles[$i]->id) ) echo 'selected="selected"'; ?>><?php echo $this->profiles[$i]->title; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<!-- export_profile::End -->
			
			<!-- export_format::Start -->
			<tr>
				<td>
				<label class="hasTip required" title="<?php echo JText::_('DATA_FORMAT_DESC');?>"><?php echo JText::_('DATA_FORMAT'); ?></label>
				</td>
				<td>
					<select name="source" id="source">
						<option value=""><?php echo JText::_('SELECT_DATA_SOURCE'); ?></option>	
						<option value="csv" <?php if($this->item->source=='csv') echo 'selected="selected"'; ?>>CSV</option>
						<option value="json" <?php if($this->item->source=='json') echo 'selected="selected"'; ?>>JSON</option>
						<option value="xml" <?php if($this->item->source=='xml') echo 'selected="selected"'; ?>>XML</option>
						<option value="remote" <?php if($this->item->source=='remote') echo 'selected="selected"'; ?>><?php echo JText::_('REMOTE_DATABASE'); ?></option>
					</select>
				</td>
			</tr>
			<!-- export_format::End --> 
			
			<!-- export_output::Start -->
			<tr class="format_block"<?php if($this->item->source=="remote") echo ' style="display:none;"'; ?>>
				<td><label class="hasTip required" title="<?php echo JText::_('EXPORT_ENTER_PATH'); ?>"><?php echo JText::_('EXPORT_OUTPUT'); ?></label></td>
				<td>
					<select name="server" id="server">
						<option value="down" <?php if($this->item->server=='down'){echo 'selected="selected"';}?>><?php echo JText::_('DOWN_FILE'); ?></option>
						<option value="write_local"<?php if($this->item->server=='write_local'){echo 'selected="selected"';}?>><?php echo JText::_('WRITE_TO_LOCAL_SERVER'); ?></option>
						<option value="write_remote"<?php if($this->item->server=='write_remote'){echo 'selected="selected"';}?>><?php echo JText::_('WRITE_TO_REMOTE_SERVER'); ?></option>
					</select>
					<span class="localpath"<?php if($this->item->server != "write_local" || empty($this->item->server)) echo ' style="display:none;"';?>>
						<span class="rootpath" <?php if($this->item->server != 'write_local') echo ' style="display:none;"';?>><?php echo JPATH_SITE.DIRECTORY_SEPARATOR.'';?></span>
						<input type="text" name="path" id="path" class="inputbox required" size="50" value="<?php echo $this->item->path;?>" />
						<select name="mode">
							<option value="w" <?php if($this->item->mode == 'w') echo 'selected="selected"';?>><?php echo JText::_('CREATE');?></option>
							<option value="a" <?php if($this->item->mode == 'a') echo 'selected="selected"';?>><?php echo JText::_('APPEND');?></option>
						</select>
					</span>
					<span class="remotepath"<?php if($this->item->server != "write_remote" || empty($this->item->server)) echo ' style="display:none;"';?>>
						<div class="rote_path vdata_ftp_host">
							<label><?php echo JText::_('VDATA_FTP_HOST');?></label>
							<input type="text" name="ftp[ftp_host]" value="<?php if(isset($this->item->ftp['ftp_host'])){echo $this->item->ftp['ftp_host'];}?>" />
						</div>
						<div class="rote_path vdata_ftp_port">
							<label><?php echo JText::_('VDATA_FTP_PORT');?></label>
							<input type="text" name="ftp[ftp_port]" value="<?php if(isset($this->item->ftp['ftp_port'])){echo $this->item->ftp['ftp_port'];}?>" />
						</div>
						<div class="rote_path vdata_ftp_user">
							<label><?php echo JText::_('VDATA_FTP_USER');?></label>
							<input type="text" name="ftp[ftp_user]" value="<?php if(isset($this->item->ftp['ftp_user'])){echo $this->item->ftp['ftp_user'];}?>" />
						</div>
						<div class="rote_path vdata_ftp_pass">
							<label><?php echo JText::_('VDATA_FTP_PASSWORD');?></label>
							<input type="text" name="ftp[ftp_pass]" value="<?php if(isset($this->item->ftp['ftp_pass'])){echo $this->item->ftp['ftp_pass'];}?>" />
						</div>
						<div class="rote_path vdata_ftp_directory">
							<label><?php echo JText::_('VDATA_FTP_ROOT_DIRECTORY');?></label>
							<input type="text" name="ftp[ftp_directory]" value="<?php if(isset($this->item->ftp['ftp_directory'])){echo $this->item->ftp['ftp_directory'];}?>" />
						</div>
						<div class="rote_path vdata_ftp_file">
							<label><?php echo JText::_('VDATA_FTP_FILE');?></label>
							<input type="text" name="ftp[ftp_file]" value="<?php if(isset($this->item->ftp['ftp_file'])){echo $this->item->ftp['ftp_file'];}?>" />
						</div>
					</span>
				</td>
			</tr>
			<!-- export_output::End -->
			
			<!-- remote_database_export::Start -->
			<tr class="rd_block" id="check_db"<?php if( $this->item->source<>"remote" ) echo ' style="display:none;"';?>>
				<td><label class="hasTip required" title="<?php echo JText::_('LOCAL_DB_DESC');?>"><?php echo JText::_('LOCAL_DB');?></label></td>
				<td><input type="checkbox" id="db_loc" name="db_loc" value="localdb" <?php  ?>/></td>
			</tr>
			<tr class="rd_block" id="imp_op" <?php  if( $this->item->source<>"remote" )  echo ' style="display:none;"'; ?>>
				<td><label class="hasTip required" title="<?php echo JText::_('OPERATION_DESC');?>"><?php echo JText::_('OPERATION'); ?></label></td>
				<td>
					<select name="operation" id="operation">
						<option value="1" <?php //if(){echo 'selected="selected"';}?>><?php echo JText::_('INSERT');?></option>
						<option value="0" <?php //if(){echo 'selected="selected"';}?>><?php echo JText::_('UPDATE');?></option>
						<option value="2" <?php ?>><?php echo JText::_('SYNCHRONIZE');?></option>
					</select>
				</td>
			</tr>
			<tr class="rd_block"<?php  if($this->item->source<>"remote")  echo ' style="display:none;"'; ?>>
				<td><label class="hasTip required" title="<?php echo JText::_('DRIVER_DESC');?>"><?php echo JText::_('DRIVER'); ?></label></td>
				<td><input type="text" name="driver" id="driver" class="inputbox required" size="50" value="<?php echo $this->item->driver; ?>" /> 
				</td>
			</tr>
			<tr class="rd_block"<?php  if($this->item->source<>"remote")  echo ' style="display:none;"'; ?>>
				<td><label class="hasTip required" title="<?php echo JText::_('HOSTNAME_DESC');?>"><?php echo JText::_('HOSTNAME'); ?></label></td>
				<td><input type="text" name="host" id="host" class="inputbox required" size="50" value="<?php echo $this->item->host; ?>" /> 
				</td>
			</tr>
			<tr class="rd_block"<?php  if($this->item->source<>"remote")  echo ' style="display:none;"'; ?>>
				<td><label class="hasTip required" title="<?php echo JText::_('USERNAME_DESC');?>"><?php echo JText::_('USERNAME'); ?></label></td>
				<td><input type="text" name="user" id="user" class="inputbox required" size="50" value="<?php echo $this->item->user; ?>" /> 
				</td>
			</tr>
			<tr class="rd_block"<?php  if($this->item->source<>"remote")  echo ' style="display:none;"'; ?>>
				<td><label class="hasTip required" title="<?php echo JText::_('PASSWORD_DESC');?>"><?php echo JText::_('PASSWORD'); ?></label></td>
				<td><input type="password" name="password" id="password" class="inputbox required" size="50" value="<?php echo $this->item->password; ?>" /> 
				</td>
			</tr>
			<tr class="rd_block"<?php  if($this->item->source<>"remote")  echo ' style="display:none;"'; ?>>
				<td><label class="hasTip required" title="<?php echo JText::_('DATABASE_NAME_DESC');?>"><?php echo JText::_('DATABASE_NAME'); ?></label></td>
				<td><input type="text" name="database" id="database" class="inputbox required" size="50" value="<?php echo $this->item->database; ?>" /> 
				</td>
			</tr>
			<tr class="rd_block"<?php  if($this->item->source<>"remote")  echo ' style="display:none;"'; ?>>
				<td><label class="hasTip required" title="<?php echo JText::_('DATABASE_TABLE_PREFIX');?>"><?php echo JText::_('TABLE_PREFIX'); ?></label></td>
				<td><input type="text" name="dbprefix" id="dbprefix" class="inputbox required" size="50" value="<?php echo $this->item->dbprefix; ?>" /> 
				</td>
			</tr>
			<!-- remote_database_export::End -->
			
			<!-- email_import_export_file::Start -->
			<?php $filestatus = (isset($this->item->sendfile) && ($this->item->sendfile==1)?true:false);?>
			<tr class="mailfilestatus">
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_DESC');?>"><?php echo JText::_('VDATA_EXPORT_FILE_LABEL');?></label>
				</td>
				<td>
					<select name="sendfile[status]">
						<option value="0" <?php ?>><?php echo JText::_('VDATA_DISABLE');?></option>
						<option value="1" <?php ?>><?php echo JText::_('VDATA_ENABLE');?></option>
					</select>
				</td>
			</tr>
		  
			<tr class="mailfile" <?php if(!$filestatus){echo ' style="display:none;"';}?>>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_ADDRESSES_DESC');?>"><?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_ADDRESSES_LABEL');?></label>
				</td>
				<td>
					<input type="text" name="sendfile[emails]" value="<?php if(isset($this->item->sendfile->email)){echo $this->item->sendfile->email;}?>" />
				</td>
			</tr>
			<tr class="mailfile" <?php if(!$filestatus){echo ' style="display:none;"';}?>>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_CC_LABEL');?>"><?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_CC_DESC');?></label>
				</td>
				<td>
					<input type="text" name="sendfile[cc]" value="<?php if(isset($this->item->sendfile->cc)){echo $this->item->sendfile->cc;}?>" />
				</td>
			</tr>
			<tr class="mailfile" <?php if(!$filestatus){echo ' style="display:none;"';}?>>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_BCC_LABEL');?>"><?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_BCC_DESC');?></label>
				</td>
				<td>
					<input type="text" name="sendfile[bcc]" value="<?php if(isset($this->item->sendfile->bcc)){echo $this->item->sendfile->bcc;}?>" />
				</td>
			</tr>
			<tr class="mailfile" <?php if(!$filestatus){echo ' style="display:none;"';}?>>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_SUBJECT_LABEL');?>"><?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_SUBJECT_DESC');?></label>
				</td>
				<td>
					<input type="text" name="sendfile[subject]" value="<?php if(isset($this->item->sendfile->subject)){echo $this->item->sendfile->subject;}?>" />
				</td>
			</tr>
			<tr class="mailfile" <?php if(!$filestatus){echo ' style="display:none;"';}?>>
				<td>
					<label class="hasTip" title="<?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_BODY_LABEL');?>"><?php echo JText::_('VDATA_EXPORT_FILE_EMAIL_BODY_DESC');?></label>
				</td>
				<td>
					<?php 
						$typeEditor = JFactory::getConfig()->get('editor');
						$editor = JEditor::getInstance($typeEditor);
						
						$editorHtml = isset($this->item->sendfile->tmpl)? $this->item->sendfile->tmpl:'';
						echo $editor->display( 'sendfile[tmpl]', $editorHtml, '200', '200', '10', '10', false ,'sendfile_tmpl');
					?>
				</td>
			</tr>
			<!-- email_import_export_file::End -->
		</table>
	</fieldset>
</div>
<div class="clr"></div>
</div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="export" />
</form>
</div>