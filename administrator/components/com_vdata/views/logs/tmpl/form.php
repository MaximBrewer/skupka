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
JHTML::_('behavior.tooltip');

?>
<div id="vdatapanel">
<form action="index.php?option=com_vdata&view=logs" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col100">
<legend><?php echo JText::_('LOG_DETAILS'); ?></legend>
<div id="editcell">
<table class="adminlist table table-striped table-hover">
<tr>
	<td><label class="hasTip" title="<?php echo JText::_('LOG_IOTYPE_DESC');?>"><?php echo JText::_('IOTYPE');?></td>
	<td><?php if($this->log->iotype==0){echo JText::_("IMPORT");} elseif($this->log->iotype==1){echo JText::_("EXPORT");}elseif($this->log->iotype==2){echo JText::_('FEED');}?></label></td>
</tr>

<tr>
	<td>
		<label class="hasTip" title="<?php echo JText::_('LOG_PROFILE_TITLE_DESC');?>"><?php echo JText::_('PROFILE_TITLE');?></label>
	</td>
	<td><?php if(!empty($this->log->profile)) echo  $this->log->profile->title;?></td>
</tr>
<tr>
	<td><label class="hasTip" title="<?php echo JText::_('LOG_STATUS_DESC');?>"><?php echo JText::_('STATUS');?></label></td>
	<td><?php echo  $this->log->status;?></td>
</tr>
<tr>
	<td><label class="hasTip" title="<?php echo JText::_('LOG_MESSAGE_DESC');?>"><?php echo JText::_('MESSAGE');?></label></td>
	<td><?php echo $this->log->message;?></td>
</tr>
<tr>
	<td><label class="hasTip" title="<?php echo JText::_('LOG_TABLE_DESC');?>"><?php echo JText::_('TABLE');?></label></td>
	<td><?php echo  $this->log->table;?></td>
</tr>
<tr>
	<td><label class="hasTip" title="<?php echo JText::_('LOG_OP_START_DESC');?>"><?php echo JText::_('OP_START');?></label></td>
	<td><?php echo  $this->log->op_start;?></td>
</tr>
<tr>
	<td><label class="hasTip" title="<?php echo JText::_('LOG_OP_END_DESC');?>"><?php echo JText::_('OP_END');?></label></td>
	<td><?php echo  $this->log->op_end;?></td>
</tr>
<?php 
$time_one = new DateTime( $this->log->op_end );
$time_two = new DateTime( $this->log->op_start );
$difference = $time_one->diff( $time_two );

?>
<tr>
	<td><label class="hasTip" title="<?php echo JText::_('LOG_TIME_TAKEN_DESC');?>"><?php echo JText::_('TIME_TAKEN');?></label></td>
	<td><?php echo $difference->format('%h hours %i minutes %s seconds');?></td></tr>
<tr>
	<td><label class="hasTip" title="<?php echo JText::_('LOG_USER_DESC');?>"><?php echo JText::_('USER');?></label></td>
	<td>
		<?php if($this->log->user){$user = JFactory::getUser($this->log->user); echo $user->username;} else echo "guest";?>
	</td>
</tr>
<tr>
	<td><label class="hasTip" title="<?php echo JText::_('LOG_SIDE_DESC');?>"><?php echo JText::_('SIDE');?></label></td>
	<td><?php echo  $this->log->side;?></td>
</tr>
<tr>
	<td><label class="hasTip" title="<?php echo JText::_('LOG_LOG_FILE_DESC');?>"><?php echo JText::_('LOG_FILE');?></label></td>
	<td>
		<?php 
			$config = JFactory::getConfig();
			$logfile = $config->get('log_path').DIRECTORY_SEPARATOR.$this->log->logfile;
			
			$cong_log_path = $config->get('log_path');
			$log_file_path = str_replace(JPATH_ROOT.DIRECTORY_SEPARATOR, JURI::root(), $cong_log_path);
			
			$loguri = JRoute::_($log_file_path.DIRECTORY_SEPARATOR.$this->log->logfile);
			
			if(!empty($this->log->logfile) && file_exists($logfile))
				echo "<a href='".$loguri."' target='_blank'>".$this->log->logfile."</a>";
			else
				echo $this->log->logfile;
		?>
	</td>
</tr>

</table>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>

<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="logs" />
</div>
</form>
</div>