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
$user = JFactory::getUser();

$canViewDashboard = $user->authorise('core.access.dashboard', 'com_vdata');
$canViewProfiles = $user->authorise('core.access.profiles', 'com_vdata');
$canViewImport = $user->authorise('core.access.import', 'com_vdata');
$canViewExport = $user->authorise('core.access.export', 'com_vdata');
$canViewCronFeed = $user->authorise('core.access.cron', 'com_vdata');


?>

<script type="text/javascript">
	
	Joomla.submitbutton = function(task) {
		if (task == 'cancel') {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			var form = document.adminForm;
			if(form.profileid.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_PROFILE'); ?>");
				return false;
			}
			if(form.source.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_DATA_SOURCE'); ?>");
				return false;
			}
			if(form.source.value == "remote")	{
				//var checked = $hd('input[name="db_loc"]:checked').length;
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
			else{
				if($hd('input[name="file"]').val() == "" && $hd('input[name="path"]').val() == ""){
					alert("<?php echo JText::_('PLZ_SELECT_FILE_PATH'); ?>");
					return false;				
				}
			}
			
			Joomla.submitform(task, document.getElementById('adminForm'));
			
		}
	}
        
    $hd(function()	{
        
		$hd('select').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
		
        $hd(document).on('change', 'select#source', function(event){
            
            if($hd(this).val()=="remote")   {
                $hd('tr.format_block').hide();
				$hd('#db_loc').prop('checked', false);
                $hd('tr.rd_block').css('display', 'table-row');
            }
            else{
                $hd('tr.rd_block').hide();
                $hd('tr.format_block').css('display', 'table-row');
            }
            
        });
        
        $hd(document).on('change', 'select#server', function(event){
            if($hd(this).val()=="remote")   {
                $hd('span.localpath').hide();
				$hd('span.remotepath').show();
            }
            else{
				if($hd(this).val()=="absolute"){
					$hd('span.rel_ab').hide();
				}
				else{
					$hd('span.rel_ab').show();
				}
                $hd('span.localpath').show();
				$hd('span.remotepath').hide();
            } 
        });
		
		$hd('#db_loc').change(function(event){
			//var checked = $hd('input[name="db_loc"]:checked').length;
			if($hd(this).is(":checked")) {
				$hd('tr.rd_block').not('#check_db').hide();
			}
			else{
				$hd('tr.rd_block').show();
			}
		
		});
		
		$hd(document).on('change', 'select#profileid', function(event){
			if($hd(this).val()=="-1"){
				window.location.href = "<?php echo JRoute::_(JURI::base().'index.php?option=com_vdata&view=profiles&task=edit&iotype=0&type=0');?>";
			}
			
		});
		$hd("#icon_desc_toggle").mouseover(function(){
			$hd( "#desc_toggle" ).show( 'slide', 800);
		});	
		$hd( ".sub_desc .close" ).on( "click", function() {
			$hd( "#desc_toggle" ).hide( 'slide', 800);
		});		
		
    });
</script>
	
<div id="vdatapanel">

<div class="toolbar-btn dash-btn">
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
    class="btn btn-small btn-import active">
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
    class="btn btn-small btn-profile">
       <span class="icon-stack icon-white"></span><?php echo JText::_( 'VDATA_PROFILE' );?>
</a>
<?php }?>
<?php if($canViewCronFeed){?>
<a 
    href="<?php echo Juri::root();?>index.php?option=com_vdata&view=schedules" 
    class="btn btn-small btn-feed">
       <span class="icon-feed icon-white"></span><?php echo JText::_( 'VDATA_SCHEDULES' );?>
</a>
<?php }?>
</div>
</div>

<div id="toolbar" class="toolbar-btn">
<div class="hxd_title">
	<span class="hx_title">
	<?php echo JText::_('VDATA_IMPORT'); ?> <span class="icon-info" id="icon_desc_toggle"></span>
		<div class="sub_desc" id="desc_toggle" style="display:none;"><a class="close" href="javascript:void(0);">×</a><?php echo JText::_('VDATA_IMPORT_DESC_TT'); ?></div>
	</span>
<button class="btn btn-small cancel" onclick="Joomla.submitbutton('cancel');"><span class="icon-cancel"></span><?php echo JText::_('CANCEL');?></button>
	<button class="btn btn-small btn-success" onclick="Joomla.submitbutton('importready');"><span class="icon-ok"></span><?php echo JText::_('CONTINUE');?></button>
</div>
</div>
<form action="<?php echo JRoute::_('index.php?option=com_vdata&view=import');?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col100">
	<fieldset class="adminform">
<table class="adminform table table-striped">
<tr>
  <td width="180">
    <label class="hasTip required" title="<?php echo JText::_('PROFILE_IMPORT_DESC');?>"><?php echo JText::_('PROFILE'); ?></label></td>
    <td><select name="profileid" id="profileid">
    	<option value=""><?php echo JText::_('SELECT_PROFILE'); ?></option>
		<option value="-1"><?php echo JText::_('CREATE_PROFILE');?></option>
	<?php	
		$pid = JFactory::getApplication()->input->getInt('profileid', 0);
		for($i=0;$i<count($this->profiles);$i++)	{	?>
		<option value="<?php echo $this->profiles[$i]->id; ?>" <?php if(($this->item->profileid==$this->profiles[$i]->id) || ($pid==$this->profiles[$i]->id)) echo 'selected="selected"'; ?>><?php echo $this->profiles[$i]->title; ?></option>
	<?php } ?>
		
	</select>
  </td>
  </tr>
  <tr>
  <td>
    <label class="hasTip required" title="<?php echo JText::_('DATA_SOURCE_DESC');?>"><?php echo JText::_('DATA_SOURCE'); ?></label></td>
    <td><select name="source" id="source">
    	<option value=""><?php echo JText::_('SELECT_DATA_SOURCE'); ?></option>	
	<option value="csv" <?php if($this->item->source=='csv') echo 'selected="selected"'; ?>><?php  echo JText::_('CSV');?></option>
        <option value="json" <?php if($this->item->source=='json') echo 'selected="selected"'; ?>><?php  echo JText::_('JSON');?></option>
        <option value="xml" <?php if($this->item->source=='xml') echo 'selected="selected"'; ?>><?php  echo JText::_('XML');?></option>
        <option value="remote" <?php if($this->item->source=='remote') echo 'selected="selected"'; ?>><?php echo JText::_('REMOTE_DATABASE'); ?></option>
	</select>
  </td>
  </tr>
  <tr class="format_block"<?php if($this->item->source=="remote") echo ' style="display:none;"'; ?>>
    <td><label class="hasTip required" title="<?php echo JText::_('UPLOAD_FILE_DESC');?>"><?php echo JText::_('UPLOAD_FILE'); ?></label></td>
    <td>
	<input type="file" name="file[]" id="file" class="inputbox required" size="50" multiple="multiple"/> <?php echo JText::_('OR'); ?>
    </td>
  </tr>
  <tr class="format_block"<?php if($this->item->source=="remote") echo ' style="display:none;"'; ?>>
    <td><label class="hasTip required" title="<?php echo JText::_('TT_ENTER_PATH'); ?>"><?php echo JText::_('ENTER_PATH'); ?></label></td>
    <td>
        <select name="server" id="server">
			<option value="local" <?php if($this->item->server=='local') echo 'selected="selected"'; ?>><?php echo JText::_('LOCAL_SERVER'); ?></option>
			<option value="absolute" <?php if($this->item->server=='absolute') echo 'selected="selected"'; ?>><?php echo JText::_('LOCAL_SERVER_ABSOLUTE'); ?></option>
			<option value="remote" <?php if($this->item->server=='remote') echo 'selected="selected"'; ?>><?php echo JText::_('REMOTE_SERVER'); ?></option>
		</select><br />
        <span class="localpath" <?php if($this->item->server=='remote'){echo ' style="display:none;"';}?>>
			<span class="rel_ab" <?php if(isset($this->item->server) && $this->item->server!='local'){echo ' style="display:none;"';}?>><?php echo JPATH_SITE.DIRECTORY_SEPARATOR.''; ?></span>
			<input type="text" name="path" id="path" class="inputbox required" size="50" value="<?php echo $this->item->path; ?>" /> 
		</span>
		<span class="remotepath"<?php if($this->item->server != "remote" || empty($this->item->server)) echo ' style="display:none;"';?>>
			<div>
				<?php echo JText::_('VDATA_FTP_HOST');?>
				<input type="text" name="ftp[ftp_host]" value="<?php if(isset($this->item->ftp['ftp_host'])){echo $this->item->ftp['ftp_host'];}?>" />
			</div>
			<div>
				<?php echo JText::_('VDATA_FTP_PORT');?>
				<input type="text" name="ftp[ftp_port]" value="<?php if(isset($this->item->ftp['ftp_port'])){echo $this->item->ftp['ftp_port'];}?>" />
			</div>
			<div>
				<?php echo JText::_('VDATA_FTP_USER');?>
				<input type="text" name="ftp[ftp_user]" value="<?php if(isset($this->item->ftp['ftp_user'])){echo $this->item->ftp['ftp_user'];}?>" />
			</div>
			<div>
				<?php echo JText::_('VDATA_FTP_PASSWORD');?>
				<input type="text" name="ftp[ftp_pass]" value="<?php if(isset($this->item->ftp['ftp_pass'])){echo $this->item->ftp['ftp_pass'];}?>" />
			</div>
			<div>
				<?php echo JText::_('VDATA_FTP_ROOT_DIRECTORY');?>
				<input type="text" name="ftp[ftp_directory]" value="<?php if(isset($this->item->ftp['ftp_directory'])){echo $this->item->ftp['ftp_directory'];}?>" />
			</div>
			<div>
				<?php echo JText::_('VDATA_FTP_FILE');?>
				<input type="text" name="ftp[ftp_file]" value="<?php if(isset($this->item->ftp['ftp_file'])){echo $this->item->ftp['ftp_file'];}?>" />
			</div>
		</span>
    </td>
  </tr>
  
  <tr class="rd_block" id="check_db"<?php if( $this->item->source<>"remote" ) echo ' style="display:none;"';?>>
	<td><label class="hasTip required" title="<?php echo JText::_('LOCAL_DB_DESC');?>"><?php echo JText::_('LOCAL_DB');?></label></td>
	<td><input type="checkbox" id="db_loc" name="db_loc" value="localdb" <?php  ?>/></td>
  </tr>
  
  <tr class="rd_block"<?php if($this->item->source<>"remote") echo ' style="display:none;"'; ?>>
    <td><label class="hasTip required" title="<?php echo JText::_('DRIVER_DESC');?>"><?php echo JText::_('DRIVER'); ?></label></td>
    <td>
	<input type="text" name="driver" id="driver" class="inputbox required" size="50" value="<?php echo $this->item->driver; ?>" /> 
    </td>
  </tr>
  <tr class="rd_block"<?php if($this->item->source<>"remote") echo ' style="display:none;"'; ?>>
    <td><label class="hasTip required" title="<?php echo JText::_('HOSTNAME_DESC');?>"><?php echo JText::_('HOSTNAME'); ?></label></td>
    <td>
	<input type="text" name="host" id="host" class="inputbox required" size="50" value="<?php echo $this->item->host; ?>" /> 
    </td>
  </tr>
  <tr class="rd_block"<?php if($this->item->source<>"remote") echo ' style="display:none;"'; ?>>
    <td><label class="hasTip required" title="<?php echo JText::_('USERNAME_DESC');?>"><?php echo JText::_('USERNAME'); ?></label></td>
    <td>
	<input type="text" name="user" id="user" class="inputbox required" size="50" value="<?php echo $this->item->user; ?>" /> 
    </td>
  </tr>
  <tr class="rd_block"<?php if($this->item->source<>"remote") echo ' style="display:none;"'; ?>>
    <td><label class="hasTip required" title="<?php echo JText::_('PASSWORD_DESC');?>"><?php echo JText::_('PASSWORD'); ?></label></td>
    <td>
	<input type="password" name="password" id="password" class="inputbox required" size="50" value="<?php echo $this->item->password; ?>" /> 
    </td>
  </tr>
  <tr class="rd_block"<?php if($this->item->source<>"remote") echo ' style="display:none;"'; ?>>
    <td><label class="hasTip required" title="<?php echo JText::_('DATABASE_NAME_DESC');?>"><?php echo JText::_('DATABASE_NAME'); ?></label></td>
    <td>
	<input type="text" name="database" id="database" class="inputbox required" size="50" value="<?php echo $this->item->database; ?>" /> 
    </td>
  </tr>
  <?php ?><tr class="rd_block"<?php if($this->item->source<>"remote") echo ' style="display:none;"';?>>
	<td><label class="hasTip required" title="<?php echo JText::_('DATABASE_TBL_PREFIX_DESC');?>"><?php echo JText::_('DATABASE_TBL_PREFIX');?></label></td>
	<td>
		<input type="text" name="dbprefix" id="dbprefix" class="inputbox" value="<?php if(!empty($this->item->dbprefix)) echo $this->item->dbprefix;?>" />
	</td>
  </tr><?php ?>
</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="import" />
</form>

</div>